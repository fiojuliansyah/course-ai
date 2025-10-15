<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = ['enrollment_id', 'invoice_id', 'amount', 'payment_method', 'status'];
    
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}