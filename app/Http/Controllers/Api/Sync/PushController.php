<?php

namespace App\Http\Controllers\Api\Sync;

use App\Http\Controllers\Controller;
use App\Models\SyncLog;
use App\Services\SyncPushService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PushController extends Controller
{
    use ApiResponse;

    private SyncPushService $pushService;

    public function __construct(SyncPushService $pushService)
    {
        $this->pushService = $pushService;
    }

    public function sales(Request $request)
    {
        return $this->handlePush($request, 'sales', function ($branchId, $records) {
            return $this->pushService->processSales($branchId, $records);
        });
    }

    public function cashCloses(Request $request)
    {
        return $this->handlePush($request, 'cash_closes', function ($branchId, $records) {
            return $this->pushService->processCashCloses($branchId, $records);
        });
    }

    public function credits(Request $request)
    {
        return $this->handlePush($request, 'credits', function ($branchId, $records) {
            return $this->pushService->processCredits($branchId, $records);
        });
    }

    public function creditPayments(Request $request)
    {
        return $this->handlePush($request, 'credit_payments', function ($branchId, $records) {
            return $this->pushService->processCreditPayments($branchId, $records);
        });
    }

    public function layaways(Request $request)
    {
        return $this->handlePush($request, 'layaways', function ($branchId, $records) {
            return $this->pushService->processLayaways($branchId, $records);
        });
    }

    public function layawayPayments(Request $request)
    {
        return $this->handlePush($request, 'layaway_payments', function ($branchId, $records) {
            return $this->pushService->processLayawayPayments($branchId, $records);
        });
    }

    public function stockEntries(Request $request)
    {
        return $this->handlePush($request, 'stock_entries', function ($branchId, $records) {
            return $this->pushService->processStockEntries($branchId, $records);
        });
    }

    public function stockOutputs(Request $request)
    {
        return $this->handlePush($request, 'stock_outputs', function ($branchId, $records) {
            return $this->pushService->processStockOutputs($branchId, $records);
        });
    }

    private function handlePush(Request $request, string $entity, callable $processor)
    {
        $startTime = microtime(true);
        $branchId  = $request->input('branch_id');
        $records   = $request->input('records', []);

        if (!$branchId) {
            return $this->error('Token inválido', 401);
        }

        if (empty($records)) {
            return $this->error('No se enviaron registros', 422);
        }

        if (count($records) > SyncPushService::MAX_BATCH_SIZE) {
            return $this->error(
                'Máximo ' . SyncPushService::MAX_BATCH_SIZE . ' registros por batch. Enviados: ' . count($records),
                422
            );
        }

        try {
            $result     = $processor($branchId, $records);
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            $accepted   = $result['accepted'];
            $rejected   = $result['rejected'];
            $status     = empty($rejected) ? 'success' : (empty($accepted) ? 'error' : 'partial');

            SyncLog::logPush(
                $branchId,
                $entity,
                count($records),
                count($accepted),
                count($rejected),
                $status,
                !empty($rejected) ? json_encode(array_slice($rejected, 0, 5)) : null,
                $durationMs
            );

            if (empty($rejected)) {
                return $this->success([
                    'accepted' => $accepted,
                    'rejected' => [],
                ], 'Todos los registros aceptados');
            }

            return response()->json([
                'status'  => 'partial',
                'message' => 'Algunos registros fueron rechazados',
                'data'    => [
                    'accepted' => $accepted,
                    'rejected' => $rejected,
                ],
            ], 207);

        } catch (\Exception $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            SyncLog::logPush($branchId, $entity, count($records), 0, count($records), 'error', $e->getMessage(), $durationMs);

            return $this->error('Error procesando batch: ' . $e->getMessage(), 500);
        }
    }
}