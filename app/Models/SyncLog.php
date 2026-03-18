<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'branch_id', 'direction', 'entity',
        'records_sent', 'records_accepted', 'records_rejected',
        'status', 'error_message', 'duration_ms'
    ];

    public static function logPull(int $branchId, string $entity, int $count, int $durationMs): void
    {
        self::create([
            'branch_id'          => $branchId,
            'direction'          => 'PULL',
            'entity'             => $entity,
            'records_sent'       => 0,
            'records_accepted'   => $count,
            'records_rejected'   => 0,
            'status'             => 'success',
            'duration_ms'        => $durationMs,
        ]);
    }

    public static function logPush(int $branchId, string $entity, int $sent, int $accepted, int $rejected, string $status, ?string $errorMessage, int $durationMs): void
    {
        self::create([
            'branch_id'          => $branchId,
            'direction'          => 'PUSH',
            'entity'             => $entity,
            'records_sent'       => $sent,
            'records_accepted'   => $accepted,
            'records_rejected'   => $rejected,
            'status'             => $status,
            'error_message'      => $errorMessage,
            'duration_ms'        => $durationMs,
        ]);
    }
}