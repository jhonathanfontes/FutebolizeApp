<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller para servir arquivos privados de forma segura (ex: de writable/uploads).
 */
class FilesController extends Controller
{
    /**
     * Serve um arquivo do diretório 'writable/uploads'.
     *
     * @param string ...$segments Segmentos da URL que formam o caminho do arquivo.
     *
     * @return ResponseInterface
     */
    public function serve(...$segments): ResponseInterface
    {
        // 1. Montar o caminho do arquivo a partir dos segmentos da URL
        $path = implode('/', $segments);

        // 2. MEDIDA DE SEGURANÇA: Validar e normalizar o caminho para evitar Path Traversal
        // Define o diretório base de onde os arquivos podem ser servidos.
        $basePath = WRITEPATH . 'uploads';
        
        // Garante que o caminho não contém '..' e resolve para um caminho real
        $realPath = realpath($basePath . DIRECTORY_SEPARATOR . $path);

        // Verifica se o caminho é válido e se está dentro do diretório base ('writable/uploads')
        if ($realPath === false || strpos($realPath, $basePath) !== 0) {
            return $this->response->setStatusCode(403, 'Access denied. Path is outside the allowed directory.');
        }
        
        // 3. MEDIDA DE SEGURANÇA: Verificar se é um arquivo real
        if (!is_file($realPath) || !is_readable($realPath)) {
            return $this->response->setStatusCode(404, 'File not found or is not readable.');
        }

        // 4. Servir o arquivo
        try {
            $mime = mime_content_type($realPath);

            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setBody(file_get_contents($realPath))
                ->setStatusCode(200);
        } catch (\Throwable $th) {
            // Logar o erro pode ser uma boa ideia aqui
            // log_message('error', 'FilesController Error: ' . $th->getMessage());
            return $this->response->setStatusCode(500, 'Could not serve the file.');
        }
    }
}
