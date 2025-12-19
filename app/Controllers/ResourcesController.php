<?php

namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller para servir assets de desenvolvimento a partir do diretório 'resources'.
 * ATENÇÃO: Este controller SÓ DEVE ser acessível em ambiente de desenvolvimento.
 */
class ResourcesController extends Controller
{
    /**
     * Serve um arquivo do diretório 'resources'.
     *
     * @param string ...$segments Segmentos da URL que formam o caminho do arquivo.
     *
     * @return ResponseInterface
     */
    public function serve(...$segments): ResponseInterface
    {
        // 1. MEDIDA DE SEGURANÇA: Bloquear em produção
        if (ENVIRONMENT === 'production') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 2. Montar o caminho do arquivo a partir dos segmentos da URL
        $path = implode('/', $segments);

        // 3. MEDIDA DE SEGURANÇA: Validar e normalizar o caminho para evitar Path Traversal
        // Garante que o caminho não contém '..' e resolve para um caminho real
        $realPath = realpath(APPPATH . 'Resources/' . $path);

        // Verifica se o caminho é válido e se está dentro do diretório 'resources'
        if ($realPath === false || strpos($realPath, realpath(APPPATH . 'Resources')) !== 0) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 4. MEDIDA DE SEGURANÇA: Verificar se é um arquivo real
        if (!is_file($realPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 5. Servir o arquivo
        try {
            $mime = mime_content_type($realPath);

            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setBody(file_get_contents($realPath))
                ->setStatusCode(200);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500, 'Could not serve the file.');
        }
    }
}
