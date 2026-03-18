<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    public $timestamps = false;
    protected $table = 'product_stock';
    protected $fillable = ['product_id', 'branch_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function addStock(int $productId, int $branchId, float $quantity): void
    {
        $stock = self::firstOrCreate(
            ['product_id' => $productId, 'branch_id' => $branchId],
            ['quantity' => 0]
        );
        $stock->increment('quantity', $quantity);
    }

    public static function subtractStock(int $productId, int $branchId, float $quantity): void
    {
        $stock = self::firstOrCreate(
            ['product_id' => $productId, 'branch_id' => $branchId],
            ['quantity' => 0]
        );
        $stock->decrement('quantity', $quantity);
    }
}