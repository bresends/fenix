<?php

namespace App\Models;

use App\Enums\FoStatusEnum;
use App\Enums\MakeUpExamStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MakeUpExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'discipline_name',
        'exam_date',
        'type',
        'motive',
        'file',
        'status',
        'date_back',
        'final_judgment_reason',
        'archived',
    ];

    protected function casts(): array
    {
        return [
            'status' => FoStatusEnum::class,
            'type' => MakeUpExamStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
