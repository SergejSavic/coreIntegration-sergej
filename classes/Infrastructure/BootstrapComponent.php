<?php

namespace CleverReachIntegration\Infrastructure;

use CleverReach\BusinessLogic\BootstrapComponent as BusinessLogicBootstrap;
use CleverReachIntegration\BusinessLogic\Repositories\QueueItemRepository;
use Logeecom\Infrastructure\TaskExecution\QueueItem;
use CleverReachIntegration\BusinessLogic\Repositories\BaseRepository;
use CleverReachIntegration\BusinessLogic\Repositories\ConfigRepository;
use CleverReachIntegration\BusinessLogic\Repositories\ProcessRepository;
use Logeecom\Infrastructure\Configuration\ConfigEntity;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use Logeecom\Infrastructure\ServiceRegister;
use CleverReachIntegration\BusinessLogic\Services\DemoService;
use CleverReachIntegration\BusinessLogic\Services\DemoServiceInterface;
use Logeecom\Infrastructure\TaskExecution\Process;

class BootstrapComponent extends BusinessLogicBootstrap
{
    /**
     * Initializes services and utilities.
     */
    public static function initServices()
    {
        parent::initServices();

        ServiceRegister::registerService(
            DemoServiceInterface::CLASS_NAME,
            function () {
                return new DemoService();
            }
        );
    }

    /**
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryClassException
     */
    public static function initRepositories()
    {
        parent::initRepositories();

        RepositoryRegistry::registerRepository(ConfigEntity::CLASS_NAME, ConfigRepository::getClassName());
        RepositoryRegistry::registerRepository(Process::CLASS_NAME, ProcessRepository::getClassName());
        RepositoryRegistry::registerRepository(QueueItem::CLASS_NAME, QueueItemRepository::getClassName());
    }


}