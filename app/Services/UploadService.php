<?php

namespace App\Services;

use App\Exceptions\ForbiddenException;
use App\Models\ArquivoModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

class UploadService
{
    protected string $basePath;
    protected ArquivoModel $model;

    protected array $mimePermitidos = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    protected int $tamanhoMaximo = 5_242_880; // 5MB

    public function __construct()
    {
        $this->basePath = realpath(WRITEPATH . 'uploads');
        $this->model    = new ArquivoModel();
    }

    public function upload(
        UploadedFile $arquivo,
        string $modulo,
        string $tipo,
        ?int $usuarioId = null
    ): array {
        if (! $arquivo->isValid()) {
            throw new RuntimeException('Upload inválido');
        }

        if ($arquivo->getSize() > $this->tamanhoMaximo) {
            throw ForbiddenException::forForbidden();
        }

        if (! in_array($arquivo->getMimeType(), $this->mimePermitidos, true)) {
            throw ForbiddenException::forForbidden();
        }

        $diretorio = "{$this->basePath}/{$modulo}/{$tipo}";
        is_dir($diretorio) || mkdir($diretorio, 0775, true);

        $nomeBase  = pathinfo($arquivo->getClientName(), PATHINFO_FILENAME);
        $extensao  = $arquivo->getClientExtension();

        $versao = $this->proximaVersao($modulo, $tipo, $nomeBase);

        $nomeArmazenado = "{$nomeBase}_V{$versao}.{$extensao}";
        $arquivo->move($diretorio, $nomeArmazenado);

        $caminho = "{$modulo}/{$tipo}/{$nomeArmazenado}";

        $dados = [
            'MODULO'          => $modulo,
            'TIPO'            => $tipo,
            'NOME_ORIGINAL'   => $arquivo->getClientName(),
            'NOME_ARMAZENADO' => $nomeArmazenado,
            'VERSAO'          => $versao,
            'CAMINHO'         => $caminho,
            'MIME'            => $arquivo->getMimeType(),
            'TAMANHO'         => $arquivo->getSize(),
            'CHECKSUM'        => hash_file('sha256', $diretorio . '/' . $nomeArmazenado),
            'COD_USUARIO'     => $usuarioId,
        ];

        $this->model->insert($dados);

        return $dados;
    }

    protected function proximaVersao(string $modulo, string $tipo, string $nomeBase): int
    {
        return (int) $this->model
            ->where('MODULO', $modulo)
            ->where('TIPO', $tipo)
            ->like('NOME_ARMAZENADO', $nomeBase . '_V', 'after')
            ->countAllResults() + 1;
    }
}
