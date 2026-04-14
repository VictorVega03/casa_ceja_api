<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\EntryProduct;
use App\Models\OutputProduct;
use App\Models\ProductStock;
use App\Models\StockEntry;
use App\Models\StockOutput;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    use ApiResponse;

    // ────────────────────────────────────────────────────────────
    // POST /api/v1/inventory/outputs
    // Registra una salida/traspaso hacia otra sucursal.
    // Requiere conexión — no pasa por sync.
    // Resta stock en origen y crea una entrada TRANSFER pendiente en destino.
    // ────────────────────────────────────────────────────────────
    public function storeOutput(Request $request)
    {
        $data = $request->validate([
            'folio'                  => 'required|string|max:50',
            'destination_branch_id'  => 'required|integer',
            'user_id'                => 'required|integer',
            'total_amount'           => 'required|numeric|min:0',
            'output_date'            => 'required',
            'notes'                  => 'nullable|string',
            'products'               => 'required|array|min:1',
            'products.*.product_id'  => 'required|integer',
            'products.*.barcode'     => 'required|string',
            'products.*.product_name'=> 'required|string',
            'products.*.quantity'    => 'required|integer|min:1',
            'products.*.unit_cost'   => 'required|numeric|min:0',
            'products.*.line_total'  => 'required|numeric|min:0',
        ]);

        // Idempotencia — si el folio ya existe, responder OK sin reprocessar
        if (StockOutput::where('folio', $data['folio'])->exists()) {
            return $this->success(['folio' => $data['folio']], 'Salida ya registrada');
        }

        $originBranchId = $request->input('branch_id'); // viene del middleware

        DB::transaction(function () use ($data, $originBranchId) {
            // 1. Crear el registro de salida
            $output = StockOutput::create([
                'folio'                 => $data['folio'],
                'origin_branch_id'      => $originBranchId,
                'destination_branch_id' => $data['destination_branch_id'],
                'user_id'               => $data['user_id'],
                'type'                  => 'TRANSFER',
                'status'                => StockOutput::STATUS_PENDING,
                'total_amount'          => $data['total_amount'],
                'output_date'           => $data['output_date'],
                'notes'                 => $data['notes'] ?? null,
            ]);

            // 2. Crear el registro de entrada TRANSFER pendiente en destino
            $entry = StockEntry::create([
                'folio'        => $this->generateEntryFolio($data['destination_branch_id']),
                'folio_output' => $data['folio'],
                'entry_type'   => StockEntry::TYPE_TRANSFER,
                'branch_id'    => $data['destination_branch_id'],
                'supplier_id'  => null,
                'user_id'      => $data['user_id'],
                'total_amount' => $data['total_amount'],
                'entry_date'   => $data['output_date'],
                'notes'        => $data['notes'] ?? null,
            ]);

            foreach ($data['products'] as $prod) {
                // Detalle en la salida
                OutputProduct::create([
                    'output_id'    => $output->id,
                    'product_id'   => $prod['product_id'],
                    'barcode'      => $prod['barcode'],
                    'product_name' => $prod['product_name'],
                    'quantity'     => $prod['quantity'],
                    'unit_cost'    => $prod['unit_cost'],
                    'line_total'   => $prod['line_total'],
                ]);

                // Detalle en la entrada pendiente (cantidades originales enviadas)
                EntryProduct::create([
                    'entry_id'     => $entry->id,
                    'product_id'   => $prod['product_id'],
                    'barcode'      => $prod['barcode'],
                    'product_name' => $prod['product_name'],
                    'quantity'     => $prod['quantity'],
                    'unit_cost'    => $prod['unit_cost'],
                    'line_total'   => $prod['line_total'],
                ]);

                // 3. Restar stock en origen AHORA
                ProductStock::subtractStock($prod['product_id'], $originBranchId, $prod['quantity']);
                // NO se suma en destino — esperar confirmación
            }
        });

        return $this->success(['folio' => $data['folio']], 'Salida registrada. Stock restado en origen. Entrada pendiente creada en destino.');
    }

    // ────────────────────────────────────────────────────────────
    // GET /api/v1/inventory/pending-transfers?branch_id=X
    // Lista entradas TRANSFER pendientes de confirmar para una sucursal.
    // ────────────────────────────────────────────────────────────
    public function pendingTransfers(Request $request)
    {
        $branchId = $request->query('branch_id', $request->input('branch_id'));

        if (!$branchId) {
            return $this->error('branch_id requerido', 422);
        }

        $entries = StockEntry::where('branch_id', $branchId)
            ->where('entry_type', StockEntry::TYPE_TRANSFER)
            ->whereNull('confirmed_at')
            ->with(['products']) // relación para mostrar de dónde viene
            ->orderByDesc('entry_date')
            ->get();

        $data = $entries->map(function ($entry) {
            // Buscar la sucursal origen desde el folio_output
            $output = StockOutput::where('folio', $entry->folio_output)->first();
            $originBranchName = $output?->originBranch?->name ?? 'Desconocida';

            return [
                'id'                 => $entry->id,
                'folio'              => $entry->folio,
                'folio_output'       => $entry->folio_output,
                'origin_branch_id'   => $output?->origin_branch_id,
                'origin_branch_name' => $originBranchName,
                'entry_date'         => $entry->entry_date?->toIso8601String(),
                'total_amount'       => $entry->total_amount,
                'notes'              => $entry->notes,
                'products'           => $entry->products->map(fn($p) => [
                    'product_id'   => $p->product_id,
                    'barcode'      => $p->barcode,
                    'product_name' => $p->product_name,
                    'quantity'     => $p->quantity,  // cantidad que dice que llegó
                    'unit_cost'    => $p->unit_cost,
                ]),
            ];
        });

        return $this->success($data);
    }

    // ────────────────────────────────────────────────────────────
    // POST /api/v1/inventory/confirm-transfer/{id}
    // Sucursal B confirma la recepción con las cantidades reales.
    // El servidor suma el stock confirmado en B.
    // Las diferencias son merma — no se devuelven a A.
    // ────────────────────────────────────────────────────────────
    public function confirmTransfer(Request $request, int $id)
    {
        $data = $request->validate([
            'confirmed_by_user_id' => 'required|integer',
            'products'             => 'required|array|min:1',
            'products.*.product_id'=> 'required|integer',
            'products.*.quantity'  => 'required|integer|min:0',
        ]);

        $entry = StockEntry::where('id', $id)
            ->where('entry_type', StockEntry::TYPE_TRANSFER)
            ->first();

        if (!$entry) {
            return $this->error('Entrada no encontrada', 404);
        }

        // Idempotencia — si ya fue confirmada, responder OK
        if (!$entry->isPending()) {
            return $this->success(['folio' => $entry->folio], 'Entrada ya confirmada');
        }

        DB::transaction(function () use ($entry, $data) {
            $confirmedProducts = collect($data['products'])->keyBy('product_id');

            foreach ($entry->products as $entryProduct) {
                $confirmed = $confirmedProducts->get($entryProduct->product_id);
                $confirmedQty = $confirmed ? (int) $confirmed['quantity'] : 0;

                // Actualizar la cantidad real confirmada en el detalle
                $entryProduct->update(['quantity' => $confirmedQty]);

                // Sumar solo lo que realmente llegó
                // Si confirmedQty < quantity original → la diferencia es merma, no se devuelve a A
                if ($confirmedQty > 0) {
                    ProductStock::addStock($entryProduct->product_id, $entry->branch_id, $confirmedQty);
                }
            }

            // Recalcular total con cantidades reales
            $newTotal = $entry->products()->sum(DB::raw('quantity * unit_cost'));
            
            // Marcar entrada como confirmada
            $entry->update([
                'confirmed_by_user_id' => $data['confirmed_by_user_id'],
                'confirmed_at'         => now(),
                'total_amount'         => $newTotal,
            ]);

            // Marcar la salida correspondiente como confirmada también
            StockOutput::where('folio', $entry->folio_output)
                ->update([
                    'status'                => StockOutput::STATUS_CONFIRMED,
                    'confirmed_by_user_id'  => $data['confirmed_by_user_id'],
                    'confirmed_at'          => now(),
                ]);
        });

        return $this->success(['folio' => $entry->folio], 'Entrada confirmada. Stock actualizado en sucursal destino.');
    }

    // ────────────────────────────────────────────────────────────
    // GET /api/v1/inventory/unconfirmed-alerts?days=7
    // Para el módulo Admin — traspasos sin confirmar después de N días.
    // Solo lectura, sin acciones.
    // ────────────────────────────────────────────────────────────
    public function unconfirmedAlerts(Request $request)
    {
        $days = (int) $request->query('days', 7);

        $entries = StockEntry::where('entry_type', StockEntry::TYPE_TRANSFER)
            ->whereNull('confirmed_at')
            ->where('entry_date', '<=', now()->subDays($days))
            ->with('products')
            ->orderBy('entry_date')
            ->get();

        $data = $entries->map(function ($entry) {
            $output = StockOutput::where('folio', $entry->folio_output)->first();
            return [
                'id'                 => $entry->id,
                'folio'              => $entry->folio,
                'folio_output'       => $entry->folio_output,
                'destination_branch' => $entry->branch_id,
                'origin_branch'      => $output?->origin_branch_id,
                'entry_date'         => $entry->entry_date?->toIso8601String(),
                'days_pending'       => now()->diffInDays($entry->entry_date),
                'total_amount'       => $entry->total_amount,
                'products_count'     => $entry->products->count(),
            ];
        });

        return $this->success($data);
    }

    // ────────────────────────────────────────────────────────────
    // Genera un folio de entrada con el mismo formato que el cliente:
    // {branch:02d}{caja:02d}{fecha:dmY}E{seq:04d}
    // Caja siempre es 00 para entradas de inventario.
    // ────────────────────────────────────────────────────────────
    private function generateEntryFolio(int $branchId): string
    {
        $prefix = sprintf('%02d00%sE', $branchId, now()->format('dmY'));

        $last = StockEntry::where('folio', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTR(folio, 14, 4) AS UNSIGNED) DESC')
            ->value('folio');

        $seq = $last ? ((int) substr($last, 13, 4)) + 1 : 1;

        return $prefix . sprintf('%04d', $seq);
    }
}