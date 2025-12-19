<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Errors extends ResourceController
{
    public function notFound()
    {
        $request = service('request');

        // Se for rota da API → JSON
        if (str_starts_with($request->getPath(), 'api')) {
            return $this->respond([
                'status'  => 404,
                'error'   => 'NOT_FOUND',
                'message' => 'Rota da API não encontrada',
                'path'    => $request->getPath(),
                'method'  => $request->getMethod(),
            ], 404);
        }

        // Caso contrário → comportamento padrão (web/app)
        return view(
            'errors/html/error_404',
            [
                'status'  => 404,
                'error'   => 'NOT_FOUND',
                'message' => 'Pagina não encontrada!',
                'path'    => $request->getPath(),
                'method'  => $request->getMethod(),
            ]
        );
    }
}
