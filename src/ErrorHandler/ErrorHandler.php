<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Duyler\Config\ConfigInterface;
use Duyler\EventBus\Dto\Log;
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
use HttpSoft\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class ErrorHandler
{
    public function __construct(
        private ErrorHandlerProvider $errorHandlerProvider,
        private ErrorRenderer $renderer,
        private ConfigInterface $config,
    ) {}

    public function handle(Throwable $t, Log $log): ResponseInterface
    {
        return match (true) {
            $t instanceof BadRequestHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_BAD_REQUEST, $log),
            $t instanceof ForbiddenHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_FORBIDDEN, $log),
            $t instanceof MethodNotAllowedHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_METHOD_NOT_ALLOWED, $log),
            $t instanceof NotImplementedHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_NOT_IMPLEMENTED, $log),
            $t instanceof NotFoundHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_NOT_FOUND, $log),
            $t instanceof UnauthorizedHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_UNAUTHORIZED, $log),
            $t instanceof InternalServerErrorHttpException
                => $this->createResponse($t, ResponseStatus::STATUS_INTERNAL_SERVER_ERROR, $log),
            $t instanceof HttpException
                => $this->createResponse($t, $t->statusCode, $log),
            default => $this->createResponse($t, ResponseStatus::STATUS_INTERNAL_SERVER_ERROR, $log),
        };
    }

    private function createResponse(Throwable $t, int $status, Log $log): ResponseInterface
    {
        if ('DEV' === strtoupper($this->config->env('ENV'))) {
            $data = [];
            $data['actionLog'] = $log->mainEventLog;
            $data['successLog'] = $log->successLog;
            $data['failLog'] = $log->failLog;
            $data['retriesLog'] = $log->retriesLog;
            $data['suspendedLog'] = $log->suspendedLog;
            $data['beginAction'] = $log->beginAction;
            $data['errorAction'] = $log->errorAction;
            $data['message'] = $t->getMessage();
            $data['status'] = $status;
            $data['trace'] = $t->getTraceAsString();

            $message = $this->renderer->render('error', $data);
            return new HtmlResponse($message, $status);
        }

        foreach ($this->errorHandlerProvider->getHandlers() as $errorHandler) {
            if ($errorHandler->is($t)) {
                return $errorHandler->handle($t, $log);
            }
        }
        return new TextResponse($status . ' ' . ResponseStatus::ERROR_PHRASES[$status] ?? '', $status);
    }
}
