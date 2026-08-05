<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    public function items()
    {
        return $this->hasMany(HotspotItem::class, 'hotspot_id')->orderBy('order', 'asc');
    }
}
