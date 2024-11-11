<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Duyler\TwigWrapper\TwigConfig;
use Duyler\TwigWrapper\TwigWrapper;

final class ErrorRenderer
{
    private TwigWrapper $twig;

    public function __construct()
    {
        $viewPath = dirname(__DIR__, 2) . '/resources/views';
        $config = new TwigConfig($viewPath);
        $this->twig = new TwigWrapper($config);
    }

    public function render(string $view, array $content = []): string
    {
        $this->twig->content($content);
        return $this->twig->render($view);
    }
}
