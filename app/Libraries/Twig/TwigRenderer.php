<?php

namespace App\Libraries\Twig;

class TwigRenderer
{
    private string $basePath;
    private static array $pathCache = [];

    public function __construct()
    {
        $this->basePath = APPPATH . 'Templates';
    }

    public function render(string $view, array $data = [], bool $print = true)
    {
        $twig = TwigFactory::get();

        $data = array_merge(['URL' => config('App')->baseURL], $data);

        // Auto-detect module from view path if not provided
        if (empty($data['module'])) {
            $parts = explode('/', $view);
            $data['module'] = count($parts) > 1 ? $parts[0] : 'default';
        }

        $htmlTemplate = $this->resolveHtml($view);
        $cssTemplates = $this->resolveAssetsByLevel($view, 'css');
        $jsTemplates  = $this->resolveAssetsByLevel($view, 'js');

        $out = '';

        foreach ($cssTemplates as $css) {
            $out .= "<style>" . $twig->render($css, $data) . "</style>";
        }

        $out .= $twig->render($htmlTemplate, $data);

        foreach ($jsTemplates as $js) {
            $out .= "<script>" . $twig->render($js, $data) . "</script>";
        }

        if ($print) {
            echo $out;
            return;
        }

        return $out;
    }

    private function resolveHtml(string $view): string
    {
        if (isset(self::$pathCache["html:$view"])) {
            return self::$pathCache["html:$view"];
        }

        $fullPath = trim($view, '/');
        $segments = explode('/', $fullPath);
        $last = end($segments);

        $candidate = "{$fullPath}.html.twig";
        if ($this->exists($candidate)) {
            return self::$pathCache["html:$view"] = $candidate;
        }

        $candidate2 = "{$fullPath}/{$last}.html.twig";
        if ($this->exists($candidate2)) {
            return self::$pathCache["html:$view"] = $candidate2;
        }

        return self::$pathCache["html:$view"] = 'erro/erro.html.twig';
    }

    private function resolveAssetsByLevel(string $view, string $ext): array
    {
        $key = "{$ext}:{$view}";

        if (isset(self::$pathCache[$key])) {
            return self::$pathCache[$key];
        }

        $results = [];
        $segments = explode('/', trim($view, '/'));

        $acc = [];

        foreach ($segments as $seg) {
            $acc[] = $seg;
            $partial = implode('/', $acc);

            $cand1 = "{$partial}.{$ext}.twig";
            if ($this->exists($cand1)) {
                $results[] = $cand1;
            }

            $cand2 = "{$partial}/{$seg}.{$ext}.twig";
            if ($this->exists($cand2)) {
                $results[] = $cand2;
            }
        }

        return self::$pathCache[$key] = array_values(array_unique($results));
    }

    private function exists(string $templateRelative): bool
    {
        $full = $this->basePath . '/' . $templateRelative;
        return file_exists($full);
    }
}
