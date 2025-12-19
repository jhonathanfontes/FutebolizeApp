<?php

namespace App\Services;

class AssetManager
{
    protected $base_url;

    public function __construct()
    {
        $this->base_url = rtrim(config('App')->baseURL, '/');
    }

    /**
     * Gera as tags de CSS global.
     * Em desenvolvimento, carrega arquivos individuais.
     * Em produção, carrega o arquivo minificado e versionado.
     */
    public function globalCss(): string
    {
        if (ENVIRONMENT === 'development') {
            $path = APPPATH . 'Resources/development/assets/css/';
            $urls = [];

            if (!is_dir($path)) {
                return '';
            }
            
            $files = glob($path . '*.css');
            sort($files);

            $basePathToRemove = APPPATH . 'Resources/';

            foreach ($files as $css) {
                // Get the file path relative to app/Resources/
                $relativeFilePath = str_ireplace($basePathToRemove, '', $css);
                
                // Normalize slashes for the URL
                $urlPath = str_replace('\\', '/', $relativeFilePath);

                // Build the final URL, mapping app/Resources to /resources
                $finalUrl = "/resources/{$urlPath}";

                $urls[] = "<link rel=\"stylesheet\" href=\"{$finalUrl}\">";
            }

            return implode("\n", $urls);
        }

        // PRODUÇÃO (com cache)
        if (!$tag = cache('asset_global_css')) {
            $filePath = FCPATH . 'assets/css/app.min.css';
            if (!file_exists($filePath)) {
                return '<!-- Global CSS file not found -->';
            }
            $tag = "<link rel=\"stylesheet\" href=\"{$this->base_url}/assets/css/app.min.css?v=" . filemtime($filePath) . "\">";
            cache()->save('asset_global_css', $tag, 3600);
        }
        return $tag;
    }

    /**
     * Gera as tags de JS global.
     * Em desenvolvimento, carrega arquivos individuais.
     * Em produção, carrega o arquivo minificado e versionado.
     */
    public function globalJs(): string
    {
        if (ENVIRONMENT === 'development') {
            $path = APPPATH . 'Resources/development/assets/js/';
            $urls = [];

            if (!is_dir($path)) {
                return '';
            }

            $files = glob($path . '*.js');
            sort($files);

            $basePathToRemove = APPPATH . 'Resources/';

            foreach ($files as $js) {
                // Get the file path relative to app/Resources/
                $relativeFilePath = str_ireplace($basePathToRemove, '', $js);
                
                // Normalize slashes for the URL
                $urlPath = str_replace('\\', '/', $relativeFilePath);

                // Build the final URL, mapping app/Resources to /resources
                $finalUrl = "/resources/{$urlPath}";

                $urls[] = "<script src=\"{$finalUrl}\"></script>";
            }

            return implode("\n", $urls);
        }

        // PRODUÇÃO (com cache)
        if (!$tag = cache('asset_global_js')) {
            $filePath = FCPATH . 'assets/js/app.min.js';
            if (!file_exists($filePath)) {
                return '<!-- Global JS file not found -->';
            }
            $tag = "<script src=\"{$this->base_url}/assets/js/app.min.js?v=" . filemtime($filePath) . "\"></script>";
            cache()->save('asset_global_js', $tag, 3600);
        }
        return $tag;
    }

    /**
     * Gera a tag de CSS para um módulo específico (apenas em produção).
     */
    public function moduleCss(?string $module): string
    {
        if ($module === 'default' || empty($module) || ENVIRONMENT === 'development') {
            return '';
        }

        $cacheKey = 'asset_module_css_' . $module;
        if (!$tag = cache($cacheKey)) {
            $filePath = FCPATH . "assets/{$module}/css/{$module}.min.css";
            if (file_exists($filePath)) {
                $url = "{$this->base_url}/assets/{$module}/css/{$module}.min.css?v=" . filemtime($filePath);
                $tag = "<link rel=\"stylesheet\" href=\"{$url}\">";
                cache()->save($cacheKey, $tag, 3600);
            } else {
                return '';
            }
        }
        return $tag;
    }

    /**
     * Gera a tag de JS para um módulo específico (apenas em produção).
     */
    public function moduleJs(?string $module): string
    {
        if ($module === 'default' || empty($module) || ENVIRONMENT === 'development') {
            return '';
        }

        $cacheKey = 'asset_module_js_' . $module;
        if (!$tag = cache($cacheKey)) {
            $filePath = FCPATH . "assets/{$module}/js/{$module}.min.js";
            if (file_exists($filePath)) {
                $url = "{$this->base_url}/assets/{$module}/js/{$module}.min.js?v=" . filemtime($filePath);
                $tag = "<script src=\"{$url}\"></script>";
                cache()->save($cacheKey, $tag, 3600);
            } else {
                return '';
            }
        }
        return $tag;
    }

    /**
     * Em ambiente de desenvolvimento, carrega CSS de um módulo específico.
     */
    public function devModuleCss(?string $module): string
    {
        if (ENVIRONMENT !== 'development' || $module === 'default' || empty($module)) {
            return '';
        }

        $output = [];
        $basePath = "development/assets/{$module}/";

        // Carregar CSS do módulo
        $cssPath = FCPATH . $basePath . 'css/';
        if (is_dir($cssPath)) {
            foreach (glob($cssPath . '*.css') as $css) {
                $name = basename($css);
                $output[] = "<link rel=\"stylesheet\" href=\"{$this->base_url}/{$basePath}css/{$name}?v=" . filemtime($css) . "\">";
            }
        }

        return implode("\n", $output);
    }

    /**
     * Em ambiente de desenvolvimento, carrega JS de um módulo específico.
     */
    public function devModuleJs(?string $module): string
    {
        if (ENVIRONMENT !== 'development' || $module === 'default' || empty($module)) {
            return '';
        }

        $output = [];
        $basePath = "development/assets/{$module}/";

        // Carregar JS do módulo
        $jsPath = FCPATH . $basePath . 'js/';
        if (is_dir($jsPath)) {
            foreach (glob($jsPath . '*.js') as $js) {
                $name = basename($js);
                $output[] = "<script src=\"{$this->base_url}/{$basePath}js/{$name}?v=" . filemtime($js) . "\"></script>";
            }
        }

        return implode("\n", $output);
    }

    /**
     * Carrega o CSS de tema para uma empresa específica.
     */
    public function themeCss(?string $companyIdentifier): string
    {
        if (empty($companyIdentifier)) {
            return '';
        }

        if (ENVIRONMENT === 'development') {
            $path = FCPATH . "development/assets/{$companyIdentifier}/css/";
            $urls = [];

            if (is_dir($path)) {
                foreach (glob($path . '*.css') as $css) {
                    $name = basename($css);
                    $urls[] = "<link rel=\"stylesheet\" href=\"{$this->base_url}/development/assets/{$companyIdentifier}/css/{$name}?v=" . filemtime($css) . "\">";
                }
            }
            return implode("\n", $urls);
        }

        // PRODUÇÃO (com cache)
        $cacheKey = 'asset_theme_css_' . $companyIdentifier;
        if (!$tag = cache($cacheKey)) {
            $filePath = FCPATH . "assets/{$companyIdentifier}/css/theme.min.css";
            if (file_exists($filePath)) {
                $url = "{$this->base_url}/assets/{$companyIdentifier}/css/theme.min.css?v=" . filemtime($filePath);
                $tag = "<link rel=\"stylesheet\" href=\"{$url}\">";
                cache()->save($cacheKey, $tag, 3600);
            } else {
                return '';
            }
        }
        return $tag;
    }

    /**
     * Busca e injeta o CSS crítico (critical.css) de forma inline.
     */
    public function inlineCss(?string $module = null, ?string $companyIdentifier = null): string
    {
        $cacheKey = 'critical_css_' . ($module ?? 'global') . '_' . ($companyIdentifier ?? 'none');
        
        if (ENVIRONMENT === 'production' && $cachedCss = cache($cacheKey)) {
            return $cachedCss;
        }

        $criticalCss = '';
        $basePath = ENVIRONMENT === 'development' ? 'development/assets/' : 'assets/';
        $fileName = ENVIRONMENT === 'development' ? 'critical.css' : 'critical.min.css';

        // Global critical CSS
        $globalPath = FCPATH . $basePath . 'css/' . $fileName;
        if (file_exists($globalPath)) {
            $criticalCss .= file_get_contents($globalPath);
        }

        // Module critical CSS
        if ($module && $module !== 'default') {
            $modulePath = FCPATH . $basePath . $module . '/css/' . $fileName;
            if (file_exists($modulePath)) {
                $criticalCss .= file_get_contents($modulePath);
            }
        }

        // Company critical CSS
        if ($companyIdentifier) {
            $companyPath = FCPATH . $basePath . $companyIdentifier . '/css/' . $fileName;
            if (file_exists($companyPath)) {
                $criticalCss .= file_get_contents($companyPath);
            }
        }

        if (empty($criticalCss)) {
            return '';
        }

        $output = "<style>{$criticalCss}</style>";

        if (ENVIRONMENT === 'production') {
            cache()->save($cacheKey, $output, 3600);
        }

        return $output;
    }
}