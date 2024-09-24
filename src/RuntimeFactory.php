<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Http\Runtime\ReactPHPRuntime;
use Duyler\Http\Runtime\RoadRunnerRuntime;

final class RuntimeFactory
{
    public static function create(RuntimeType $type): RuntimeInterface
    {
        return match ($type) {
            RuntimeType::ReactPHP => new ReactPHPRuntime(),
            RuntimeType::RoadRunner => new RoadRunnerRuntime(),
        };
    }
}
