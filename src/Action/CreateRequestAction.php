<?php

declare(strict_types=1);

namespace Duyler\Http\Action;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Router\CurrentRoute;
use Psr\Http\Message\ServerRequestInterface;

class CreateRequestAction
{
    public function __construct(private ServerRequestInterface $request) {}

    public function __invoke(CurrentRoute $currentRoute): Result
    {
        if ($currentRoute->status) {
            foreach ($currentRoute->attributes as $key => $value) {
                $this->request = $this->request->withAttribute($key, $value);
            }

            $this->request = $this->request
                ->withAttribute('handler', $currentRoute->handler)
                ->withAttribute('scenario', $currentRoute->scenario)
                ->withAttribute('action', $currentRoute->action)
                ->withAttribute('language', $currentRoute->language)
            ;

            return new Result(
                status: ResultStatus::Success,
                data: $this->request
            );
        }

        return new Result(
            status: ResultStatus::Fail,
            data: $this->request
        );
    }
}
