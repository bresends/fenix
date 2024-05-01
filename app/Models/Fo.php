<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fo extends Model
{
    use HasFactory;

    protected $fillable = [
        'aluno_id',
        'punished',
    ];

    public function military()
    {
        return $this->belongsTo(Military::class, 'aluno_id');
    }
}
