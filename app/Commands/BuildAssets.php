<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use MatthiasMullie\Minify;

class BuildAssets extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Development';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'build:assets';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Concatena e minifica os assets de CSS e JS para o ambiente de produção.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'build:assets';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Iniciando o build dos assets de produção...', 'yellow');

        $this->buildCss();
        $this->buildJs();

        CLI::write('Build dos assets concluído com sucesso!', 'green');
    }

    private function buildCss()
    {
        $sourcePath = APPPATH . 'Resources/development/assets/css/';
        $destinationFile = FCPATH . 'assets/css/app.min.css';

        CLI::write('Buscando arquivos CSS em: ' . $sourcePath);

        $files = glob($sourcePath . '*.css');
        sort($files);

        if (empty($files)) {
            CLI::write('Nenhum arquivo CSS encontrado. Pulando etapa.', 'light_yellow');
            return;
        }

        $minifier = new Minify\CSS();
        foreach ($files as $file) {
            CLI::write('Adicionando: ' . basename($file));
            $minifier->add($file);
        }

        // Garante que o diretório de destino exista
        if (!is_dir(dirname($destinationFile))) {
            mkdir(dirname($destinationFile), 0755, true);
        }

        $minifier->minify($destinationFile);
        CLI::write('Arquivo CSS minificado criado em: ' . $destinationFile, 'green');
    }

    private function buildJs()
    {
        $sourcePath = APPPATH . 'Resources/development/assets/js/';
        $destinationFile = FCPATH . 'assets/js/app.min.js';

        CLI::write('Buscando arquivos JS em: ' . $sourcePath);
        
        $files = glob($sourcePath . '*.js');
        sort($files);

        if (empty($files)) {
            CLI::write('Nenhum arquivo JS encontrado. Pulando etapa.', 'light_yellow');
            return;
        }

        $minifier = new Minify\JS();
        foreach ($files as $file) {
            CLI::write('Adicionando: ' . basename($file));
            $minifier->add($file);
        }

        // Garante que o diretório de destino exista
        if (!is_dir(dirname($destinationFile))) {
            mkdir(dirname($destinationFile), 0755, true);
        }

        $minifier->minify($destinationFile);
        CLI::write('Arquivo JS minificado criado em: ' . $destinationFile, 'green');
    }
}
