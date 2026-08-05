<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model
{
    protected $fillable = [
        'timeline_id',
        'year',
        'title',
        'description',
        'order',
    ];
}
