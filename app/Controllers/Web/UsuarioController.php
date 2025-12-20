<?php

namespace App\Controllers\Web;

class UsuarioController extends BaseController
{
    public function login(): string
    {
        return $this->render('usuario/login', [
            'title' => 'Login',
            'module' => 'usuario',
        ]);
    }

    public function criarConta(): string
    {
        return $this->render('usuario/criar_conta', [
            'title' => 'Criar Conta',
            'module' => 'usuario',
        ]);
    }

    public function logout()
    {
        // Lógica de logout aqui (ex: destruir sessão)
        return redirect()->to('/login');
    }
}
