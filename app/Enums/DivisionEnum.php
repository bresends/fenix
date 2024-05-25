<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DivisionEnum: string implements HasColor, HasLabel
{
    case QP_COMBATENTE = 'QP/Combatente';
    case QOC = 'QOC';
    case QP_MUSICO = 'QP/Músico';
    case QOA_ADMINISTRATIVO = 'QOA/Administrativo';
    case QOA_MUSICO = 'QOA/Músico';
    case QOS_DENTISTA = 'QOS/Dentista';
    case QOS_MEDICO = 'QOS/Médico';

    case QP_ESPECIAL = 'QP/Especial';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::QP_COMBATENTE => 'QP/Combatente',
            self::QP_ESPECIAL => 'QP/Especial',
            self::QP_MUSICO => 'QP/Músico',
            self::QOC => 'QOC',
            self::QOS_MEDICO => 'QOS/Médico',
            self::QOS_DENTISTA => 'QOS/Dentista',
            self::QOA_ADMINISTRATIVO => 'QOA/Administrativo',
            self::QOA_MUSICO => 'QOA/Músico',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::QP_ESPECIAL, self::QP_COMBATENTE, self::QP_MUSICO => 'success',
            self::QOS_DENTISTA, self::QOS_MEDICO => 'gray',
            self::QOA_MUSICO, self::QOA_ADMINISTRATIVO => 'warning',
            self::QOC => 'danger',
        };

    }
}
