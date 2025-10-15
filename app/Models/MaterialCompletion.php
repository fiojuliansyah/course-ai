<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialCompletion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'material_id', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(ModuleMaterial::class, 'material_id');
    }
}