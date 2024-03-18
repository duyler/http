<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

readonly class Error
{
    public function __construct(
        public string $content,
        public int $status = 500,
        public array $headers = [],
    ) {}
}
