<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = ['material_id', 'location_id', 'quantity', 'last_updated_by'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
