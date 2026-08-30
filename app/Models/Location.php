<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['site_id', 'name', 'type', 'latitude', 'longitude', 'description', 'status'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function getFullNameAttribute(): string
    {
        return ($this->site ? $this->site->name . ' - ' : '') . $this->name;
    }

    public function getLatAttribute(): float
    {
        if (!empty($this->latitude)) {
            return (float) $this->latitude;
        }
        // Fall back to parent site coordinates, then a default
        return $this->site ? $this->site->getLatAttribute() : 12.3340;
    }

    public function getLngAttribute(): float
    {
        if (!empty($this->longitude)) {
            return (float) $this->longitude;
        }
        return $this->site ? $this->site->getLngAttribute() : 12.3465;
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
