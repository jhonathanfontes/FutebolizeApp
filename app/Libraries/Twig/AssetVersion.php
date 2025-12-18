<?php

namespace App\Libraries\Twig;

class AssetVersion
{
    public static function version(string $file): string
    {
        return file_exists($file)
            ? substr(md5_file($file), 0, 10)
            : time();
    }

    public static function resolve(string $fileName): string
    {
        $baseURL  = rtrim(config('App')->baseURL, '/');
        $assetDir = FCPATH . 'assets/';

        $ext      = pathinfo($fileName, PATHINFO_EXTENSION);
        $name     = pathinfo($fileName, PATHINFO_FILENAME);

        $original = "{$assetDir}{$ext}/{$fileName}";
        $minFile  = "{$name}.min.{$ext}";
        $minFull  = "{$assetDir}{$ext}/{$minFile}";

        if (ENVIRONMENT === 'development') {
            return "{$baseURL}/assets/{$ext}/{$fileName}?v=" . time();
        }

        if (file_exists($minFull)) {
            return "{$baseURL}/assets/{$ext}/{$minFile}?v=" . self::version($minFull);
        }

        return "{$baseURL}/assets/{$ext}/{$fileName}?v=" . self::version($original);
    }

    /**
     * Retorna assets globais e modulares de acordo com o ambiente.
     */
    public static function getAssets(string $module, string $type): array
    {
        $base = rtrim(config('App')->baseURL, '/');

        $assets = [];

        // -----------------------------------------------
        // PRODUÇÃO: carregar bundles globais + modulares
        // -----------------------------------------------
        if (ENVIRONMENT === 'production') {

            // GLOBAL
            $globalBundle = "app.min.{$type}";
            $globalPath = FCPATH . "assets/{$type}/{$globalBundle}";
            $assets[] = "{$base}/assets/{$type}/{$globalBundle}?v=" . self::version($globalPath);

            // MODULAR (se existir)
            $moduleBundle = "{$module}.min.{$type}";
            $modulePath = FCPATH . "assets/{$module}/{$type}/{$moduleBundle}";

            if (file_exists($modulePath)) {
                $assets[] = "{$base}/assets/{$module}/{$type}/{$moduleBundle}?v=" . self::version($modulePath);
            }

            return $assets;
        }

        // -------------------------------------------------
        // DEVELOPMENT: carregar todos arquivos individuais
        // -------------------------------------------------

        //
        // 1) ASSETS GLOBAIS
        //
        $globalDevPath = ROOTPATH . "development/assets/{$type}";
        if (is_dir($globalDevPath)) {
            foreach (glob($globalDevPath . "/*.{$type}") as $file) {
                $name = basename($file);
                $assets[] = "{$base}/development/assets/{$type}/{$name}?v=" . time();
            }
        }

        //
        // 2) ASSETS DO MÓDULO
        //
        $moduleDevPath = ROOTPATH . "development/assets/{$module}/{$type}";
        if (is_dir($moduleDevPath)) {
            foreach (glob($moduleDevPath . "/*.{$type}") as $file) {
                $name = basename($file);
                $assets[] = "{$base}/development/assets/{$module}/{$type}/{$name}?v=" . time();
            }
        }

        return $assets;
    }
}
