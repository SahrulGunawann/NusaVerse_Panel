<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Heritage extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'category_id',
        'category_name',
        'province_id',
        'province_name',
        'short_description',
        'full_description',
        'cover_image',
        'model_3d_url',
        'latitude',
        'longitude',
        'timeline_id',
        'hotspot_id',
        'opening_hours',
        'ticket_price',
        'is_featured',
        'additional_sections',
        'source_name',
        'source_url',
        'sources',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_featured' => 'boolean',
        'additional_sections' => 'array',
        'sources' => 'array',
    ];

    public function timeline()
    {
        return $this->hasOne(Timeline::class, 'heritage_id', 'id');
    }

    public function hotspot()
    {
        return $this->hasOne(Hotspot::class, 'heritage_id', 'id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'heritage_slug', 'slug');
    }
}
