<?php

namespace App\Libraries\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtensionLib extends AbstractExtension
{
    /**
     * Registra filtros personalizados do Twig.
     */
    public function getFilters(): array
    {
        return [

            // Exemplo: Uppercase PT-BR
            new TwigFilter('upper', fn($s) => mb_strtoupper($s)),
            
            // Exemplo: Lowercase PT-BR
            new TwigFilter('lower', fn($s) => mb_strtolower($s)),

            // Exemplo: moeda brasileira
            new TwigFilter('moeda', function ($valor) {
                return 'R$ ' . number_format($valor, 2, ',', '.');
            }),

            // Exemplo: limitar caracteres
            new TwigFilter('limitar', function ($texto, $limite = 50) {
                return mb_strlen($texto) > $limite
                    ? mb_substr($texto, 0, $limite) . '...'
                    : $texto;
            }),

        ];
    }

    /**
     * Registra funções especiais (diferentes da FunctionLib).
     * Ideal para funções mais complexas ou classes externas.
     */
    public function getFunctions(): array
    {
        return [

            // Exemplo: gerar slug
            new TwigFunction('slug', function ($texto) {
                $texto = mb_strtolower($texto);
                $texto = preg_replace('/[^a-z0-9]+/i', '-', $texto);
                return trim($texto, '-');
            }),

            // Exemplo: config() diretamente no Twig
            new TwigFunction('config', function ($key) {
                return config($key);
            }),

            // Exemplo: csrf token
            new TwigFunction('csrf_token', function () {
                return csrf_hash();
            }),

            // Exemplo: csrf field
            new TwigFunction('csrf_field', function () {
                return csrf_field();
            }),

        ];
    }
}
