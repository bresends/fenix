<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fo extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'military_id',
        'issuer',
        'reason',
        'excuse',
        'final_judgment',
        'final_judgment_reason',
        'paid',
        'date_issued',
    ];

    public function military()
    {
        return $this->belongsTo(Military::class, 'military_id');
    }
}
