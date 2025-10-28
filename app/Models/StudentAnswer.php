<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'course_id',
        'question_id',
        'student_response',
        'ai_response',
        'score',
        'is_correct',
    ];

    protected $casts = [
        'student_response' => 'json',
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}