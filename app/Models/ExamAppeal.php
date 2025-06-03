<?php

namespace App\Models;

use App\Enums\StatusExamEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAppeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam',
        'question',
        'discipline',
        'type',
        'motive',
        'bibliography',
        'accept_terms',
        'file',
        'status',
        'final_judgment_reason',
        'archived',
        'evaluated_by',
        'evaluated_at',
        'sei_number',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusExamEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
