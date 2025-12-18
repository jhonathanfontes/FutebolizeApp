<?php

namespace App\Libraries\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\Extension\StringLoaderExtension;
use Performing\TwigComponents\Configuration;

class TwigFactory
{
  
    private static ?Environment $instance = null;

    public static function get(): Environment
    {
        if (self::$instance === null) {

            // Base real da pasta /Templates
            $basePath = rtrim(APPPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'Templates';

            // Pasta de cache do Twig
            $cachePath = WRITEPATH . 'cache/twig';

            // Garante que a pasta existe
            if (!is_dir($cachePath)) {
                mkdir($cachePath, 0775, true);
            }

            $loader = new FilesystemLoader($basePath);

            self::$instance = new Environment($loader, [
                'debug'       => ENVIRONMENT === 'development',
                'cache'       => ENVIRONMENT === 'production' ? $cachePath : false,
                'auto_reload' => ENVIRONMENT === 'development',
            ]);

            // Extensões essenciais
            self::$instance->addExtension(new DebugExtension());
            self::$instance->addExtension(new StringLoaderExtension());
            self::$instance->addExtension(new TwigExtensionLib());
            TwigFunctionLib::register(self::$instance);

            // Performing components (se estiver usando)
            if (class_exists(Configuration::class)) {

                Configuration::make(self::$instance)
                    ->setTemplatesPath('components')
                    ->setTemplatesExtension('twig')
                    ->useCustomTags()
                    ->setup();
            }

            // Variáveis globais automáticas
            self::$instance->addGlobal('baseURL', config('App')->baseURL);
            self::$instance->addGlobal('env', ENVIRONMENT);
            self::$instance->addGlobal('assets', new \App\Services\AssetManager());

            // Versões opcionais do .env
            self::$instance->addGlobal('cssVersion', getenv('app.cssVersion') ?: '1.0.0');
            self::$instance->addGlobal('jsVersion',  getenv('app.jsVersion')  ?: '1.0.0');
        }

        return self::$instance;
    }
}
