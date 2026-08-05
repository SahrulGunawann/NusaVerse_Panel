<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'heritage_id',
        'heritage_slug',
        'title',
    ];

    public function events()
    {
        return $this->hasMany(TimelineEvent::class, 'timeline_id', 'id')->orderBy('order', 'asc');
    }
}
