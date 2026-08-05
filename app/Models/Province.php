<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'name',
        'island',
    ];

    public function heritages()
    {
        return $this->hasMany(Heritage::class, 'province_id', 'id');
    }
}
