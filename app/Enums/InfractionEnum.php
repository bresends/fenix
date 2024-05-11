<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InfractionEnum: string implements HasLabel
{
    case ATRASAR_OU_FALTAR_SERVICO_ESCALA = 'Atrasar ou Faltar Serviço/Escala (Art. 142 da NE01, RDBM 4681/96 Anexo 01 Item 27)';
    case SEM_LUVA_E_IDENTIDADE = 'Sem Luva e Identidade (Art. 142 da NE01, RDBM 4681/96 Anexo 01 Item 85)';
    case CABELO_FORA_DO_PADRAO = 'Cabelo fora do Padrão (Art. 133 II da NE01)';
    case PE_DE_CABELO_E_BARBA_FORA_DO_PADRAO = 'Pé de Cabelo e Barba Fora do Padrão (Art. 133 V da NE01)';
    case UNIFORME_SUJO_OU_MAL_PASSADO_OU_DESALINHO = 'Uniforme Sujo ou Mal Passado ou em Desalinho (sem gorro) (Ar. 133 VIII da NE01)';
    case BOTA_SAPATO_COTURNO_NAO_ENGRAXADO_E_NAO_POLIDO = 'Bota/sapato/coturno não Engraxado e não Polido (Art. 133 IX da NE01)';
    case NAO_CUMPRE_HORARIO_PARA_ENTRAR_EM_FORMA_APOS_6_PIQUES = 'Não Cumpriu o Horário para entrar em forma após 6 piques (Art. 133 I da NE01 (horários). Art. 30 da NE01)';
    case USO_DE_OCULOS_ESCUROS_OU_TELEFONE_CELULAR_DURANTE_EXPEDIENTE_SEM_DEVIDA_AUTORIZACAO = 'Uso de óculos escuros ou Telefone Celular durante o expediente sem a devida autorização. Art. 133 XIV da NE01';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ATRASAR_OU_FALTAR_SERVICO_ESCALA => 'Atrasar ou Faltar Serviço/Escala (Art. 142 da NE01, RDBM 4681/96 Anexo 01 Item 27)',
            self::SEM_LUVA_E_IDENTIDADE => 'Sem Luva e Identidade (Art. 142 da NE01, RDBM 4681/96 Anexo 01 Item 85)',
            self::CABELO_FORA_DO_PADRAO => 'Cabelo fora do Padrão (Art. 133 II da NE01)',
            self::PE_DE_CABELO_E_BARBA_FORA_DO_PADRAO => 'Pé de Cabelo e Barba Fora do Padrão (Art. 133 V da NE01)',
            self::UNIFORME_SUJO_OU_MAL_PASSADO_OU_DESALINHO => 'Uniforme Sujo ou Mal Passado ou em Desalinho (sem gorro) (Ar. 133 VIII da NE01)',
            self::BOTA_SAPATO_COTURNO_NAO_ENGRAXADO_E_NAO_POLIDO => 'Bota/sapato/coturno não Engraxado e não Polido (Art. 133 IX da NE01)',
            self::NAO_CUMPRE_HORARIO_PARA_ENTRAR_EM_FORMA_APOS_6_PIQUES => 'Não Cumpriu o Horário para entrar em forma após 6 piques (Art. 133 I da NE01 (horários). Art. 30 da NE01)',
            self::USO_DE_OCULOS_ESCUROS_OU_TELEFONE_CELULAR_DURANTE_EXPEDIENTE_SEM_DEVIDA_AUTORIZACAO => 'Uso de óculos escuros ou Telefone Celular durante o expediente sem a devida autorização. Art. 133 XIV da NE01',
        };
    }
}
