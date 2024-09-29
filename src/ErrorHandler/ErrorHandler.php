<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Duyler\Http\Exception\BadRequestHttpException;
use Duyler\Http\Exception\ForbiddenHttpException;
use Duyler\Http\Exception\HttpException;
use Duyler\Http\Exception\InternalServerErrorHttpException;
use Duyler\Http\Exception\MethodNotAllowedHttpException;
use Duyler\Http\Exception\NotFoundHttpException;
use Duyler\Http\Exception\NotImplementedHttpException;
use Duyler\Http\Exception\UnauthorizedHttpException;
use Duyler\Http\Response\ResponseStatus;
use HttpSoft\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorHandler
{
    public function __construct(
        private ErrorHandlerProvider $errorHandlerProvider,
    ) {}

    public function handle(Throwable $t): ResponseInterface
    {
        foreach ($this->errorHandlerProvider->getHandlers() as $errorHandler) {
            if ($errorHandler->is($t)) {
                $error = $errorHandler->handle($t);
                return new HtmlResponse($error->content, $error->status, $error->headers);
            }
        }

        $message = $t->getMessage() . PHP_EOL . $t->getTraceAsString();

        return match (true) {
            $t instanceof BadRequestHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_BAD_REQUEST),
            $t instanceof ForbiddenHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_FORBIDDEN),
            $t instanceof MethodNotAllowedHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_METHOD_NOT_ALLOWED),
            $t instanceof NotImplementedHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_NOT_IMPLEMENTED),
            $t instanceof NotFoundHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_NOT_FOUND),
            $t instanceof UnauthorizedHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_UNAUTHORIZED),
            $t instanceof InternalServerErrorHttpException
                => new HtmlResponse($message, ResponseStatus::STATUS_INTERNAL_SERVER_ERROR),
            $t instanceof HttpException
                => new HtmlResponse($message, $t->statusCode),
            default => new HtmlResponse($message, ResponseStatus::STATUS_INTERNAL_SERVER_ERROR),
        };
    }
}
