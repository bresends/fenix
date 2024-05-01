<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Military extends Model
{
    use HasFactory;

    protected $fillable = [
        'rg',
        'name',
        'email',
        'rank',
        'division',
        'blood_type',
    ];
}
