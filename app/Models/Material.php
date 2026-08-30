<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name', 'category_id', 'supplier_id', 'unit', 'description',
        'minimum_stock_level', 'current_stock', 'location_id', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function updateStockStatus()
    {
        if ($this->current_stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->current_stock <= $this->minimum_stock_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }
        $this->save();
    }
}
