<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DivisionEnum: string implements HasLabel
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
            self::QOC => 'QOC',
            self::QP_MUSICO => 'QP/Músico',
            self::QOA_ADMINISTRATIVO => 'QOA/Administrativo',
            self::QOA_MUSICO => 'QOA/Músico',
            self::QOS_DENTISTA => 'QOS/Dentista',
            self::QOS_MEDICO => 'QOS/Médico',
            self::QP_ESPECIAL => 'QP/Especial',
        };
    }
}
