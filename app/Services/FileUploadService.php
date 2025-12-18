<?php

namespace App\Services;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Files\File;

class FileUploadService
{
    protected RequestInterface $request;

    public function __construct()
    {
        $this->request = service('request');
    }

    /**
     * Realiza o upload de um arquivo.
     *
     * @param string $module O módulo ao qual o arquivo pertence (e.g., 'usuarios', 'produtos').
     * @param string $fieldName O nome do campo do formulário de upload.
     * @param string $type O tipo de arquivo ('imagem' or 'documento').
     * @param string|null $subtype O sub-tipo ou sub-pasta (opcional).
     * @return array
     */
    public function upload(string $module, string $fieldName, string $type, ?string $subtype = null): array
    {
        $file = $this->request->getFile($fieldName);

        if (!$file->isValid()) {
            return [
                'success' => false,
                'message' => $file->getErrorString() . '(' . $file->getError() . ')',
                'file'    => null,
                'path'    => null,
            ];
        }

        $validationRules = $this->getValidationRules($type);
        if (!$this->validate($file, $validationRules)) {
            return [
                'success' => false,
                'message' => 'Arquivo inválido. Verifique o tipo e o tamanho do arquivo.',
                'file'    => null,
                'path'    => null,
            ];
        }
        
        $relativePath = $this->buildRelativePath($module, $type, $subtype);
        $finalPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $relativePath;

        if (!$this->ensureDirectory($finalPath)) {
            return [
                'success' => false,
                'message' => 'Não foi possível criar o diretório de upload.',
                'file'    => null,
                'path'    => null,
            ];
        }
        
        $newName = $file->getRandomName();
        $file->move($finalPath, $newName);
        
        $savedFile = $relativePath . DIRECTORY_SEPARATOR . $newName;

        return [
            'success' => true,
            'message' => 'Upload realizado com sucesso.',
            'file'    => str_replace(DIRECTORY_SEPARATOR, '/', $savedFile),
            'path'    => $finalPath . DIRECTORY_SEPARATOR . $newName,
        ];
    }
    
    /**
     * Retorna um arquivo para download ou visualização inline.
     *
     * @param string $relativePath O caminho relativo do arquivo a partir de 'writable/uploads/'.
     * @return ResponseInterface|void
     */
    public function getFile(string $relativePath)
    {
        $fullPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (!is_file($fullPath)) {
            return service('response')->setStatusCode(404)->setBody('Arquivo não encontrado.');
        }

        $file = new File($fullPath);
        $mime = $file->getMimeType();

        return service('response')
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $file->getBasename() . '"')
            ->setBody(file_get_contents($fullPath));
    }

    /**
     * Remove um arquivo do sistema.
     *
     * @param string $relativePath O caminho relativo do arquivo a partir de 'writable/uploads/'.
     * @return bool
     */
    public function delete(string $relativePath): bool
    {
        $fullPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
    
    /**
     * Garante que a estrutura de diretórios exista.
     *
     * @param string $path O caminho completo do diretório.
     * @return bool
     */
    protected function ensureDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0775, true)) {
                return false;
            }
        }
        
        if (!is_file($path . DIRECTORY_SEPARATOR . 'index.html')) {
            file_put_contents($path . DIRECTORY_SEPARATOR . 'index.html', '');
        }

        return true;
    }

    /**
     * Constrói o caminho relativo para o upload.
     */
    private function buildRelativePath(string $module, string $type, ?string $subtype): string
    {
        $path = $module . DIRECTORY_SEPARATOR . $type;
        if ($subtype) {
            $path .= DIRECTORY_SEPARATOR . $subtype;
        }
        return $path;
    }
    
    /**
     * Retorna as regras de validação para um tipo de arquivo.
     */
    private function getValidationRules(string $type): array
    {
        if ($type === 'imagem') {
            return [
                'ext_in' => 'jpg,jpeg,png,bmp,webp',
                'max_size' => '5120', // 5MB in KB
            ];
        }

        if ($type === 'documento') {
            return [
                'ext_in' => 'pdf,doc,docx,xls,xlsx',
                'max_size' => '10240', // 10MB in KB
            ];
        }

        return [];
    }

    /**
     * Valida o arquivo.
     */
    private function validate(object $file, array $rules): bool
    {
        $exts = explode(',', $rules['ext_in']);
        if (!in_array($file->guessExtension(), $exts)) {
            return false;
        }

        if ($file->getSize() > ($rules['max_size'] * 1024)) {
            return false;
        }
        
        // Basic check for PHP files disguised as images
        if (strpos($file->getMimeType(), 'php') !== false) {
            return false;
        }

        return true;
    }
}
