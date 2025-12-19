<?php

namespace App\Models;

use CodeIgniter\Model;

class ArquivoModel extends Model
{
    protected $table      = 'ARQUIVOS';
    protected $primaryKey = 'CODIGO';

    protected $allowedFields = [
        'MODULO',
        'TIPO',
        'NOME_ORIGINAL',
        'NOME_ARMAZENADO',
        'VERSAO',
        'CAMINHO',
        'MIME',
        'TAMANHO',
        'CHECKSUM',
        'COD_USUARIO',
        'DATA_CADASTRO',
    ];

    protected $useTimestamps = false;
}
