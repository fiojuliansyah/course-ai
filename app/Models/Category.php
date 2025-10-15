<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    // Relasi: Satu Kategori punya banyak Course
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}