<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'quiz_id',
        'question',
        'options',
        'correct_index',
        'explanation',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_index' => 'integer',
    ];
}
