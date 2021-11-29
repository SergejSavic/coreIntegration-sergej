<?php

namespace CleverReachIntegration\Infrastructure;

use CleverReach\BusinessLogic\BootstrapComponent as BusinessLogicBootstrap;
use CleverReachIntegration\BusinessLogic\Repositories\QueueItemRepository;
use CleverReach\BusinessLogic\Configuration\Configuration;
use Logeecom\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use Logeecom\Infrastructure\TaskExecution\QueueItem;
use CleverReachIntegration\BusinessLogic\Repositories\ConfigRepository;
use CleverReachIntegration\BusinessLogic\Repositories\ProcessRepository;
use Logeecom\Infrastructure\Configuration\ConfigEntity;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use Logeecom\Infrastructure\ServiceRegister;
use CleverReachIntegration\BusinessLogic\Services\Logger\LoggerService;
use CleverReachIntegration\BusinessLogic\Services\DemoService;
use CleverReachIntegration\BusinessLogic\Services\ConfigService as ConfigurationService;
use CleverReachIntegration\BusinessLogic\Services\DemoServiceInterface;
use Logeecom\Infrastructure\Serializer\Concrete\JsonSerializer;
use Logeecom\Infrastructure\Serializer\Serializer;
use Logeecom\Infrastructure\TaskExecution\Process;
use CleverReachIntegration\BusinessLogic\Services\Authorization\AuthorizationService;
use CleverReach\BusinessLogic\Authorization\Contracts\AuthorizationService as BaseAuthService;
use CleverReachIntegration\BusinessLogic\Services\Group\GroupService;
use CleverReach\BusinessLogic\Group\Contracts\GroupService as GroupServiceInterface;

class BootstrapComponent extends BusinessLogicBootstrap
{

    /**
     * Initializes services,repositories,proxies,events,pipelines and webhook handlers
     */
    public static function init()
    {
        parent::init();
    }

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

        ServiceRegister::registerService(
            Configuration::CLASS_NAME,
            function () {
                return ConfigurationService::getInstance();
            }
        );

        ServiceRegister::registerService(
            ShopLoggerAdapter::CLASS_NAME,
            function () {
                return new LoggerService();
            }
        );

        ServiceRegister::registerService(
            Serializer::CLASS_NAME,
            function () {
                return new JsonSerializer();
            }
        );

        ServiceRegister::registerService(
            BaseAuthService::CLASS_NAME,
            function () {
                return new AuthorizationService();
            }
        );

        ServiceRegister::registerService(
            GroupServiceInterface::CLASS_NAME,
            function () {
                return new GroupService();
            }
        );
    }

    /**
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryClassException
     * Initializes repositories
     */
    public static function initRepositories()
    {
        parent::initRepositories();

        RepositoryRegistry::registerRepository(ConfigEntity::CLASS_NAME, ConfigRepository::getClassName());
        RepositoryRegistry::registerRepository(Process::CLASS_NAME, ProcessRepository::getClassName());
        RepositoryRegistry::registerRepository(QueueItem::CLASS_NAME, QueueItemRepository::getClassName());
    }


}