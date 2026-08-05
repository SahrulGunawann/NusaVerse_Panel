<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'heritage_slug',
        'category',
        'title',
        'description',
        'passing_score',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id', 'id')->orderBy('order', 'asc');
    }
}
