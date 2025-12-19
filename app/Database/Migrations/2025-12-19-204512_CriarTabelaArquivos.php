<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaArquivos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'CODIGO' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'MODULO' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],

            'TIPO' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],

            'NOME_ORIGINAL' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'NOME_ARMAZENADO' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'VERSAO' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],

            'CAMINHO' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'CAMINHO RELATIVO DO ARQUIVO NO DIRETÓRIO DE UPLOADS',
            ],

            'MIME' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'TIPO MIME DO ARQUIVO',
            ],

            'TAMANHO' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],

            'CHECKSUM' => [
                'type'       => 'CHAR',
                'constraint' => 64,
                'null'       => true,
                'comment'    => 'HASH SHA256 PARA VERIFICAÇÃO DE INTEGRIDADE',
            ],

            'COD_USUARIO' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],

            'DATA_CADASTRO' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('CODIGO', true);
        $this->forge->addKey(['MODULO', 'TIPO'], false, false, 'IDX_MODULO_TIPO');
        $this->forge->addKey('COD_USUARIO', false, false, 'IDX_USUARIO');

        $this->forge->createTable('ARQUIVOS', true);
    }

    public function down()
    {
        $this->forge->dropTable('ARQUIVOS', true);
    }
}
