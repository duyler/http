<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\Dto\Action;
use Duyler\Framework\Loader\LoaderServiceInterface;
use Duyler\Framework\Loader\PackageLoaderInterface;
use Duyler\Http\Action\CreateRequestAction;
use Duyler\Http\State\PrepareRequestStateHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Loader implements PackageLoaderInterface
{
    public function __construct(private ContainerInterface $container) {}

    public function load(LoaderServiceInterface $loaderService): void
    {
        $requestAction = new Action(
            id: 'Http.CreateRequest',
            handler: CreateRequestAction::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $loaderService->addAction($requestAction);

        $prepareRequest = $this->container->get(PrepareRequestStateHandler::class);
        $attributeHandler = $this->container->get(AttributeHandler::class);

        $loaderService->addStateHandler($prepareRequest);
        $loaderService->addAttributeHandler($attributeHandler);
    }
}
