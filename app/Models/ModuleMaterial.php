<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleMaterial extends Model
{
    use HasFactory;
    
    protected $table = 'module_materials'; 

    protected $fillable = [
        'module_id', 
        'title', 
        'file_path', 
        'content',
        'order'
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }
}