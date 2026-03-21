<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'last_used',
    ];

    protected $casts = [
        'last_used' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /// <summary>
    /// Obtiene o crea el token para un usuario.
    /// Si ya existe lo devuelve, si no lo genera automáticamente.
    /// </summary>
    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['token'   => hash('sha256', Str::random(60))]
        );
    }
}