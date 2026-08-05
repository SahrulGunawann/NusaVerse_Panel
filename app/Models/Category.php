<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'name',
        'icon',
        'description',
    ];

    public function heritages()
    {
        return $this->hasMany(Heritage::class, 'category_id', 'id');
    }
}
