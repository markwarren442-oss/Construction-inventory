<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = ['material_id', 'code', 'batch_number', 'qr_image_path', 'status', 'generated_by'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function generatedByUser()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'qr_code_id');
    }

    public static function generateUniqueCode()
    {
        do {
            $code = 'MAT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
