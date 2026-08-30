<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['name', 'address', 'latitude', 'longitude', 'description', 'status'];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function getLatAttribute(): float
    {
        return (float) ($this->latitude ?: 12.3325);
    }

    public function getLngAttribute(): float
    {
        return (float) ($this->longitude ?: 12.3468);
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps/search/?api=1&query=" . urlencode("{$this->lat},{$this->lng}");
    }

    public function getGoogleMapsDirectionsUrlAttribute(): string
    {
        return "https://www.google.com/maps/dir/?api=1&destination=" . urlencode("{$this->lat},{$this->lng}");
    }
}
