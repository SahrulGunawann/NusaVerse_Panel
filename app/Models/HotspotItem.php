<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotspotItem extends Model
{
    protected $guarded = [];

    public function hotspot()
    {
        return $this->belongsTo(Hotspot::class, 'hotspot_id');
    }
}
