<?php

namespace App\Services;

use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ResponseService
{
    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /* ==============================
     | SUCCESS RESPONSE
     ============================== */
    public function success(
        mixed $data = null,
        string $message = 'Operação realizada com sucesso',
        int $statusCode = 200,
        array $meta = []
    ): ResponseInterface {
        return $this->respond([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
            'code'    => $statusCode,
            'meta'    => $meta,
        ], $statusCode);
    }

    /* ==============================
     | ERROR RESPONSE
     ============================== */
    public function error(
        string $message = 'Ocorreu um erro inesperado',
        array|string|null $errors = null,
        int $statusCode = 400,
        ?Throwable $exception = null
    ): ResponseInterface {

        $payload = [
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => null,
            'code'    => $statusCode,
            'meta'    => [],
        ];

        // Ambiente de desenvolvimento → erro detalhado
        if (ENVIRONMENT === 'development') {
            $payload['errors'] = $errors ?? ($exception ? [
                'exception' => get_class($exception),
                'message'   => $exception->getMessage(),
                'file'      => $exception->getFile(),
                'line'      => $exception->getLine(),
                'trace'     => explode("\n", $exception->getTraceAsString()),
            ] : null);
        } else {
            // Produção → mensagem amigável
            $payload['errors'] = [
                'message'   => $exception->getMessage(),
            ];
        }

        return $this->respond($payload, $statusCode);
    }

    /* ==============================
     | VALIDATION RESPONSE
     ============================== */
    public function validation(
        array $errors,
        string $message = 'Erro de validação',
        int $statusCode = 422
    ): ResponseInterface {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
            'code'    => $statusCode,
            'meta'    => [],
        ], $statusCode);
    }

    /* ==============================
     | INTERNAL RESPONSE
     ============================== */
    protected function respond(array $payload, int $statusCode): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($payload);
    }
}
