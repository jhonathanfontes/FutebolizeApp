<?php

namespace App\Libraries\Twig;

use Twig\Environment;
use Twig\TwigFunction;

class TwigFunctionLib
{
    public static function register(Environment $twig): Environment
    {
        // Exemplo: gerar URL base do site
        $twig->addFunction(new TwigFunction(
            'base_url',
            fn($path = '') =>
            rtrim(config('App')->baseURL, '/') . '/' . ltrim($path, '/')
        ));

        // Exemplo: formatar datas
        $twig->addFunction(new TwigFunction('data_br', function ($data) {
            return date('d/m/Y', strtotime($data));
        }));

        // Exemplo: debug (var_dump mais limpo)
        $twig->addFunction(new TwigFunction('debug', function ($var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }));

        // Exemplo: site_url (CodeIgniter style)
        $twig->addFunction(new TwigFunction(
            'site_url',
            fn($path = '') =>
            site_url($path)
        ));

        // Adiciona a função old()
        $twig->addFunction(new TwigFunction('old', function ($key, $default = null) {
            return old($key, $default);
        }));

        // Adiciona a função session()
        $twig->addFunction(new TwigFunction('session', function ($key = null) {
            if ($key === null) {
                return session();
            }
            return session($key);
        }));

        return $twig;
    }
}
