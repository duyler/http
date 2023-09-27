<?php

declare(strict_types=1);

namespace Duyler\Http\Action;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Router\Result as RoutingResult;
use HttpSoft\ServerRequest\ServerRequestCreator;

class CreateRequestAction
{
    public function __invoke(RoutingResult $result): Result
    {
        $request = ServerRequestCreator::create();

        if ($result->status) {
            foreach ($result->attributes as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            $request = $request
                ->withAttribute('handler', $result->handler)
                ->withAttribute('scenario', $result->scenario)
                ->withAttribute('action', $result->action)
                ->withAttribute('language', $result->language);

            return new Result(
                status: ResultStatus::Success,
                data: $request
            );
        }

        return new Result(
            status: ResultStatus::Fail,
            data: $request
        );
    }
}
