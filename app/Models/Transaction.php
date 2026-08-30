<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'material_id', 'qr_code_id', 'type', 'quantity',
        'from_location_id', 'to_location_id', 'performed_by',
        'remarks', 'reference_number'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class, 'qr_code_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function performedByUser()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public static function generateReferenceNumber(string $type): string
    {
        $prefix = strtoupper(substr($type, 0, 3));
        return $prefix . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
