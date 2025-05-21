<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;
    
    protected $table = 'inventory';
    
    protected $fillable = [
        'product_id',
        'quantity',
        'minimum_stock',
        'last_restock_date'
    ];
    
    protected $casts = [
        'last_restock_date' => 'datetime'
    ];
    
    /**
     * Get the product that owns the inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
