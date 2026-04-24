<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\CashClose;
use App\Models\CashMovement;
use App\Models\Credit;
use App\Models\CreditProduct;
use App\Models\CreditPayment;
use App\Models\Layaway;
use App\Models\LayawayProduct;
use App\Models\LayawayPayment;
use App\Models\StockEntry;
use App\Models\EntryProduct;
use App\Models\StockOutput;
use App\Models\OutputProduct;
use App\Models\ProductStock;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SyncPushService
{
    const MAX_BATCH_SIZE = 100;

    public function processSales(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            if (Sale::where('folio', $record['folio'])->exists()) {
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            $sale = Sale::create([
                'folio'            => $record['folio'],
                'branch_id'        => $branchId,
                'user_id'          => $record['user_id'],
                'subtotal'         => $record['subtotal'] ?? 0,
                'discount'         => $record['discount'] ?? 0,
                'total'            => $record['total'] ?? 0,
                'amount_paid'      => $record['amount_paid'] ?? 0,
                'change_given'     => $record['change_given'] ?? 0,
                'payment_method'   => $record['payment_method'] ?? null,
                'payment_summary'  => $record['payment_summary'] ?? null,
                'cash_close_folio' => $record['cash_close_folio'] ?? null,
                'sale_date'        => $this->parseTimestamp($record['sale_date']),
                'ticket_data'      => $record['ticket_data'] ?? null,
            ]);

            foreach ($record['products'] ?? [] as $prod) {
                SaleProduct::create([
                    'sale_id'               => $sale->id,
                    'product_id'            => $prod['product_id'],
                    'barcode'               => $prod['barcode'] ?? null,
                    'product_name'          => $prod['product_name'],
                    'quantity'              => $prod['quantity'],
                    'list_price'            => $prod['list_price'] ?? 0,
                    'final_unit_price'      => $prod['final_unit_price'] ?? 0,
                    'line_total'            => $prod['line_total'] ?? 0,
                    'total_discount_amount' => $prod['total_discount_amount'] ?? 0,
                    'price_type'            => $prod['price_type'] ?? null,
                    'discount_info'         => $prod['discount_info'] ?? null,
                    'pricing_data'          => $prod['pricing_data'] ?? null,
                ]);
            }

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processCashCloses(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            if (CashClose::where('folio', $record['folio'])->exists()) {
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            $close = CashClose::create([
                'folio'                 => $record['folio'],
                'branch_id'             => $branchId,
                'user_id'               => $record['user_id'],
                'opening_cash'          => $record['opening_cash'] ?? 0,
                'total_cash'            => $record['total_cash'] ?? 0,
                'total_debit_card'      => $record['total_debit_card'] ?? 0,
                'total_credit_card'     => $record['total_credit_card'] ?? 0,
                'total_checks'          => $record['total_checks'] ?? 0,
                'total_transfers'       => $record['total_transfers'] ?? 0,
                'layaway_cash'          => $record['layaway_cash'] ?? 0,
                'credit_cash'           => $record['credit_cash'] ?? 0,
                'credit_total_created'  => $record['credit_total_created'] ?? 0,
                'layaway_total_created' => $record['layaway_total_created'] ?? 0,
                'expenses'              => $record['expenses'] ?? null,
                'income'                => $record['income'] ?? null,
                'surplus'               => $record['surplus'] ?? 0,
                'expected_cash'         => $record['expected_cash'] ?? 0,
                'total_sales'           => $record['total_sales'] ?? 0,
                'notes'                 => $record['notes'] ?? null,
                'opening_date'          => $this->parseTimestamp($record['opening_date']),
                'close_date'            => $this->parseTimestamp($record['close_date'] ?? null),
            ]);

            foreach ($record['movements'] ?? [] as $mov) {
                CashMovement::create([
                    'cash_close_id' => $close->id,
                    'type'          => $mov['type'],
                    'concept'       => $mov['concept'] ?? null,
                    'amount'        => $mov['amount'],
                    'user_id'       => $mov['user_id'] ?? $record['user_id'],
                ]);
            }

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processCredits(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            $existing = Credit::where('folio', $record['folio'])->first();
            if ($existing) {
                // Actualizar campos que pueden cambiar: status, total_paid
                $existing->update([
                    'status'           => $record['status']           ?? $existing->status,
                    'total_paid'       => $record['total_paid']       ?? $existing->total_paid,
                    'cash_close_folio' => $record['cash_close_folio'] ?? $existing->cash_close_folio,
                ]);
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            $credit = Credit::create([
                'folio'         => $record['folio'],
                'branch_id'     => $branchId,
                'user_id'       => $record['user_id'],
                'customer_id'   => !empty($record['customer'])
                    ? $this->ensureCustomer($record['customer'])
                    : ($record['customer_id'] ?? null),
                'total'         => $record['total'] ?? 0,
                'total_paid'    => $record['total_paid'] ?? 0,
                'cash_close_folio' => $record['cash_close_folio'] ?? null,
                'months_to_pay' => $record['months_to_pay'] ?? 0,
                'credit_date'   => $this->parseTimestamp($record['credit_date']),
                'due_date'      => $this->parseTimestamp($record['due_date'] ?? null),
                'status'        => $record['status'] ?? 1,
                'notes'         => $record['notes'] ?? null,
                'ticket_data'   => $record['ticket_data'] ?? null,
            ]);

            foreach ($record['products'] ?? [] as $prod) {
                CreditProduct::create([
                    'credit_id'    => $credit->id,
                    'product_id'   => $prod['product_id'],
                    'barcode'      => $prod['barcode'] ?? null,
                    'product_name' => $prod['product_name'],
                    'quantity'     => $prod['quantity'],
                    'unit_price'   => $prod['unit_price'] ?? 0,
                    'line_total'   => $prod['line_total'] ?? 0,                   
                ]);
            }

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processCreditPayments(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            if (CreditPayment::where('folio', $record['folio'])->exists()) {
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            $credit = Credit::where('folio', $record['credit_folio'] ?? '')->first();
            if (!$credit) {
                return [
                    'status' => 'rejected',
                    'folio'  => $record['folio'],
                    'reason' => 'Crédito no encontrado: ' . ($record['credit_folio'] ?? 'null')
                ];
            }

            CreditPayment::create([
                'folio'            => $record['folio'],
                'credit_id'        => $credit->id,
                'branch_id'        => $branchId,
                'user_id'          => $record['user_id'],
                'amount_paid'      => $record['amount_paid'] ?? 0,
                'payment_method'   => $record['payment_method'] ?? null,
                'payment_date'     => $this->parseTimestamp($record['payment_date']),
                'cash_close_folio' => $record['cash_close_folio'] ?? null,
                'notes'            => $record['notes'] ?? null,
            ]);

            $newTotalPaid = $credit->payments()->sum('amount_paid');

            // Derivar status — el servidor es la fuente de verdad
            // Prioridad: Cancelado > Pagado > Vencido > Pendiente
            if ($credit->status == 4) {
                $newStatus = 4; // Cancelado: no se toca
            } elseif ($newTotalPaid >= $credit->total) {
                $newStatus = 2; // Pagado
            } elseif ($credit->due_date && now()->gt($credit->due_date)) {
                $newStatus = 3; // Vencido
            } else {
                $newStatus = 1; // Pendiente
            }

            $credit->update([
                'total_paid' => $newTotalPaid,
                'status'     => $newStatus,
            ]);

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processLayaways(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            $existing = Layaway::where('folio', $record['folio'])->first();
            if ($existing) {
                // Actualizar campos mutables — igual que processCredits
                $existing->update([
                    'status'           => $record['status']           ?? $existing->status,
                    'total_paid'       => $record['total_paid']       ?? $existing->total_paid,
                    'cash_close_folio' => $record['cash_close_folio'] ?? $existing->cash_close_folio,
                    'delivery_user_id' => $record['delivery_user_id'] ?? $existing->delivery_user_id,
                    'delivery_date'    => !empty($record['delivery_date'])
                                            ? $this->parseTimestamp($record['delivery_date'])
                                            : $existing->delivery_date,
                    'pickup_date'      => !empty($record['pickup_date'])
                                            ? $this->parseTimestamp($record['pickup_date'])
                                            : $existing->pickup_date,
                ]);
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            $layaway = Layaway::create([
                'folio'            => $record['folio'],
                'branch_id'        => $branchId,
                'user_id'          => $record['user_id'],
                'delivery_user_id' => $record['delivery_user_id'] ?? null,
                'customer_id'      => !empty($record['customer'])
                    ? $this->ensureCustomer($record['customer'])
                    : ($record['customer_id'] ?? null),
                'total'            => $record['total'] ?? 0,
                'total_paid'       => $record['total_paid'] ?? 0,
                'cash_close_folio' => $record['cash_close_folio'] ?? null,
                'layaway_date'     => $this->parseTimestamp($record['layaway_date']),
                'pickup_date'      => $this->parseTimestamp($record['pickup_date'] ?? null),
                'delivery_date'    => $this->parseTimestamp($record['delivery_date'] ?? null),
                'status'           => $record['status'] ?? 1,
                'notes'            => $record['notes'] ?? null,
                'ticket_data'      => $record['ticket_data'] ?? null,
            ]);

            foreach ($record['products'] ?? [] as $prod) {
                LayawayProduct::create([
                    'layaway_id'   => $layaway->id,
                    'product_id'   => $prod['product_id'],
                    'barcode'      => $prod['barcode'] ?? null,
                    'product_name' => $prod['product_name'],
                    'quantity'     => $prod['quantity'],
                    'unit_price'   => $prod['unit_price'] ?? 0,
                    'line_total'   => $prod['line_total'] ?? 0,                   
                ]);
            }

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processLayawayPayments(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            if (LayawayPayment::where('folio', $record['folio'])->exists()) {
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            $layaway = Layaway::where('folio', $record['layaway_folio'] ?? '')->first();
            if (!$layaway) {
                return [
                    'status' => 'rejected',
                    'folio'  => $record['folio'],
                    'reason' => 'Apartado no encontrado: ' . ($record['layaway_folio'] ?? 'null')
                ];
            }

            LayawayPayment::create([
                'folio'            => $record['folio'],
                'layaway_id'       => $layaway->id,
                'branch_id'        => $branchId,
                'user_id'          => $record['user_id'],
                'amount_paid'      => $record['amount_paid'] ?? 0,
                'payment_method'   => $record['payment_method'] ?? null,
                'payment_date'     => $this->parseTimestamp($record['payment_date']),
                'cash_close_folio' => $record['cash_close_folio'] ?? null,
                'notes'            => $record['notes'] ?? null,
            ]);

            $newTotalPaid = $layaway->payments()->sum('amount_paid');

            // Derivar status — el servidor es la fuente de verdad
            // Entregado (2) solo lo setea el cliente via MarkAsDeliveredAsync
            if ($layaway->status == 4) {
                $newStatus = 4; // Cancelado: no se toca
            } elseif ($layaway->status == 2) {
                $newStatus = 2; // Ya entregado: no se toca
            } elseif ($layaway->pickup_date && now()->gt($layaway->pickup_date)) {
                $newStatus = 3; // Vencido: pasó la fecha sin recogerse
            } else {
                $newStatus = 1; // Pendiente
            }

            $layaway->update([
                'total_paid' => $newTotalPaid,
                'status'     => $newStatus,
            ]);

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processStockEntries(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            // Idempotencia — si ya existe, aceptar sin reprocessar
            if (StockEntry::where('folio', $record['folio'])->exists()) {
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            // Solo se aceptan entradas tipo PURCHASE por sync
            // Las entradas tipo TRANSFER las crea el servidor internamente al procesar una salida
            $entryType = $record['entry_type'] ?? StockEntry::TYPE_PURCHASE;
            if ($entryType !== StockEntry::TYPE_PURCHASE) {
                return ['status' => 'rejected', 'folio' => $record['folio'], 'reason' => 'Solo se aceptan entradas tipo PURCHASE por sync'];
            }

            $entry = StockEntry::create([
                'folio'        => $record['folio'],
                'folio_output' => $record['folio_output'] ?? null,
                'entry_type'   => StockEntry::TYPE_PURCHASE,
                'branch_id'    => $branchId,
                'supplier_id'  => $record['supplier_id'] ?? null,
                'user_id'      => $record['user_id'],
                'total_amount' => $record['total_amount'] ?? 0,
                'entry_date'   => $this->parseTimestamp($record['entry_date']),
                'notes'        => $record['notes'] ?? null,
            ]);

            foreach ($record['products'] ?? [] as $prod) {
                EntryProduct::create([
                    'entry_id'     => $entry->id,
                    'product_id'   => $prod['product_id'],
                    'barcode'      => $prod['barcode'] ?? null,
                    'product_name' => $prod['product_name'],
                    'quantity'     => $prod['quantity'],
                    'unit_cost'    => $prod['unit_cost'] ?? 0,
                    'line_total'   => $prod['line_total'] ?? 0,
                ]);

                // Suma stock en la sucursal que recibió la mercancía
                ProductStock::addStock($prod['product_id'], $branchId, $prod['quantity']);
            }

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processStockOutputs(int $branchId, array $records): array
    {
        return $this->processBatch($records, function ($record) use ($branchId) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            // Idempotencia
            if (StockOutput::where('folio', $record['folio'])->exists()) {
                return ['status' => 'accepted', 'folio' => $record['folio']];
            }

            // NOTA: Las salidas tipo TRANSFER ya no pasan por sync/push.
            // Se registran vía POST /api/v1/inventory/outputs directamente (requiere conexión).
            // Este endpoint solo acepta salidas tipo OTHER (mermas, ajustes, etc.) sin destino.
            $output = StockOutput::create([
                'folio'                 => $record['folio'],
                'origin_branch_id'      => $branchId,
                'destination_branch_id' => $record['destination_branch_id'] ?? null,
                'user_id'               => $record['user_id'],
                'type'                  => $record['type'] ?? 'OTHER',
                'status'                => StockOutput::STATUS_CONFIRMED, // sin flujo de confirmación
                'total_amount'          => $record['total_amount'] ?? 0,
                'output_date'           => $this->parseTimestamp($record['output_date']),
                'notes'                 => $record['notes'] ?? null,
            ]);

            foreach ($record['products'] ?? [] as $prod) {
                OutputProduct::create([
                    'output_id'    => $output->id,
                    'product_id'   => $prod['product_id'],
                    'barcode'      => $prod['barcode'] ?? null,
                    'product_name' => $prod['product_name'],
                    'quantity'     => $prod['quantity'],
                    'unit_cost'    => $prod['unit_cost'] ?? 0,
                    'line_total'   => $prod['line_total'] ?? 0,
                ]);

                // Solo resta en origen — sin suma automática en destino
                ProductStock::subtractStock($prod['product_id'], $branchId, $prod['quantity']);
            }

            return ['status' => 'accepted', 'folio' => $record['folio']];
        });
    }

    public function processCustomers(array $records): array
    {
        return $this->processBatch($records, function ($record) {
            if (empty($record['folio'])) {
                return ['status' => 'rejected', 'folio' => 'unknown', 'reason' => 'Folio requerido'];
            }

            $existing = Customer::where('id', $record['id'] ?? 0)->first();

            if ($existing) {
                $existing->update([
                    'name'            => $record['name']            ?? $existing->name,
                    'rfc'             => $record['rfc']             ?? $existing->rfc,
                    'street'          => $record['street']          ?? $existing->street,
                    'exterior_number' => $record['exterior_number'] ?? $existing->exterior_number,
                    'interior_number' => $record['interior_number'] ?? $existing->interior_number,
                    'neighborhood'    => $record['neighborhood']    ?? $existing->neighborhood,
                    'postal_code'     => $record['postal_code']     ?? $existing->postal_code,
                    'city'            => $record['city']            ?? $existing->city,
                    'email'           => $record['email']           ?? $existing->email,
                    'phone'           => $record['phone']           ?? $existing->phone,
                    'active'          => $record['active']          ?? $existing->active,
                ]);

                return ['status' => 'accepted',
                'folio' => $record['folio'], 
                'server_id' => $existing->id];
            }

            $customer = Customer::create([
                'name'            => $record['name']            ?? '',
                'rfc'             => $record['rfc']             ?? null,
                'street'          => $record['street']          ?? null,
                'exterior_number' => $record['exterior_number'] ?? null,
                'interior_number' => $record['interior_number'] ?? null,
                'neighborhood'    => $record['neighborhood']    ?? null,
                'postal_code'     => $record['postal_code']     ?? null,
                'city'            => $record['city']            ?? null,
                'email'           => $record['email']           ?? null,
                'phone'           => $record['phone']           ?? null,
                'active'          => $record['active']          ?? true,
            ]);

            return [
                'status' => 'accepted', 
                'folio' => $record['folio'], 
                'server_id' => $customer->id];
        });
    }

    private function ensureCustomer(array $customerData): int
    {
        if (!empty($customerData['id'])) {
            $existing = Customer::find($customerData['id']);
            if ($existing) return $existing->id;
        }

        $customer = Customer::firstOrCreate(
            [
                'name'  => $customerData['name'] ?? 'Sin nombre',
                'phone' => $customerData['phone'] ?? null,
            ],
            [
                'rfc'             => $customerData['rfc'] ?? null,
                'street'          => $customerData['street'] ?? null,
                'exterior_number' => $customerData['exterior_number'] ?? null,
                'interior_number' => $customerData['interior_number'] ?? null,
                'neighborhood'    => $customerData['neighborhood'] ?? null,
                'postal_code'     => $customerData['postal_code'] ?? null,
                'city'            => $customerData['city'] ?? null,
                'email'           => $customerData['email'] ?? null,
                'active'          => true,
            ]
        );

        return $customer->id;
    }

    private function processBatch(array $records, callable $processor): array
    {
        $records  = array_slice($records, 0, self::MAX_BATCH_SIZE);
        $accepted = [];
        $rejected = [];

        foreach ($records as $record) {
            try {
                $result = DB::transaction(function () use ($processor, $record) {
                    return $processor($record);
                });

                if ($result['status'] === 'accepted') {
                    if (array_key_exists('server_id', $result)) {
                        $accepted[] = [
                            'folio'     => $result['folio'],
                            'server_id' => $result['server_id'],
                        ];
                    } else {
                        $accepted[] = $result['folio'];
                    }
                } else {
                    $rejected[] = ['folio' => $result['folio'], 'reason' => $result['reason'] ?? 'Rechazado'];
                }
            } catch (\Exception $e) {
                $rejected[] = [
                    'folio'  => $record['folio'] ?? 'unknown',
                    'reason' => 'Error interno: ' . $e->getMessage(),
                ];
            }
        }

        return ['accepted' => $accepted, 'rejected' => $rejected];
    }

    private function parseTimestamp($value): ?\Carbon\Carbon
    {
        if ($value === null || $value === '' || $value === 0) return null;

        if (is_numeric($value)) {
            $ts = (int) $value;
            if ($ts > 1000000000000) {
                $ts = (int) ($ts / 1000);
            }
            return Carbon::createFromTimestamp($ts);
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function processUsers(array $records): array
    {
        $accepted = [];
        $rejected = [];

        foreach ($records as $record) {
            try {
                $username = $record['username'] ?? null;

                if (empty($username)) {
                    $rejected[] = ['folio' => 'unknown', 'reason' => 'Username requerido'];
                    continue;
                }

                // Buscar por username (único y estable) en lugar de id
                // para evitar conflictos cuando el id local difiere del servidor
                $user = \App\Models\User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name'      => $record['name']      ?? '',
                        'email'     => $record['email']     ?? null,
                        'phone'     => $record['phone']     ?? null,
                        'password'  => $record['password']  ?? '',
                        'user_type' => $record['user_type'] ?? 3,
                        'branch_id' => $record['branch_id'] ?? null,
                        'active'    => $record['active']    ?? true,
                    ]
                );

                // Generar token si no existe — necesario para que el usuario pueda hacer login
                \App\Models\UserToken::getOrCreateForUser($user->id);

                $accepted[] = $username;

            } catch (\Exception $e) {
                $rejected[] = ['folio' => $record['username'] ?? 'unknown', 'reason' => $e->getMessage()];
            }
        }

        return ['accepted' => $accepted, 'rejected' => $rejected];
    }
}