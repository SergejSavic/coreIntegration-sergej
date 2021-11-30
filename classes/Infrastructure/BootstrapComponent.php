<?php

namespace CleverReachIntegration\Infrastructure;

use CleverReach\BusinessLogic\BootstrapComponent as BusinessLogicBootstrap;
use CleverReach\BusinessLogic\DynamicContent\Contracts\DynamicContentService;
use CleverReach\BusinessLogic\Field\Contracts\FieldService as FieldServiceInterface;
use CleverReach\BusinessLogic\Form\FormEventsService;
use CleverReach\BusinessLogic\Language\Contracts\TranslationService as TranslationServiceInterface;
use CleverReach\BusinessLogic\Mailing\Contracts\DefaultMailingService;
use CleverReach\BusinessLogic\Receiver\ReceiverEventsService;
use CleverReach\BusinessLogic\Segment\Contracts\SegmentService as SegmentServiceInterface;
use CleverReachIntegration\BusinessLogic\Repositories\QueueItemRepository;
use CleverReach\BusinessLogic\Configuration\Configuration;
use CleverReachIntegration\BusinessLogic\Services\Mailing\MailingService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\CustomerService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\GuestService;
use CleverReachIntegration\BusinessLogic\Services\Segment\SegmentService;
use Logeecom\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use Logeecom\Infrastructure\ORM\Exceptions\RepositoryClassException;
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
use CleverReachIntegration\BusinessLogic\Services\Form\FormService;
use CleverReach\BusinessLogic\Form\Contracts\FormService as FormServiceInterface;
use CleverReachIntegration\BusinessLogic\Services\Receiver\VisitorService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\SubscriberService;
use CleverReachIntegration\BusinessLogic\Services\Translation\TranslationService;
use CleverReachIntegration\BusinessLogic\Services\DynamicContent\DynamicContentService as DynamicContent;
use CleverReachIntegration\BusinessLogic\Services\Field\FieldService;
use CleverReachIntegration\BusinessLogic\Services\ReceiverEvents\ReceiverEventsService as ReceiverEvents;
use CleverReachIntegration\BusinessLogic\Services\FormEvents\FormEventsService as FormEvents;

/**
 * Class BootstrapComponent
 * @package CleverReachIntegration\Infrastructure
 */
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

        ServiceRegister::registerService(
            FormServiceInterface::CLASS_NAME,
            function () {
                return new FormService();
            }
        );

        ServiceRegister::registerService(
            VisitorService::THIS_CLASS_NAME,
            function () {
                return VisitorService::getInstance();
            }
        );

        ServiceRegister::registerService(
            GuestService::THIS_CLASS_NAME,
            function () {
                return GuestService::getInstance();
            }
        );

        ServiceRegister::registerService(
            CustomerService::THIS_CLASS_NAME,
            function () {
                return CustomerService::getInstance();
            }
        );

        ServiceRegister::registerService(
            SubscriberService::THIS_CLASS_NAME,
            function () {
                return SubscriberService::getInstance();
            }
        );

        ServiceRegister::registerService(
            DefaultMailingService::CLASS_NAME,
            function () {
                return new MailingService();
            }
        );

        ServiceRegister::registerService(
            TranslationServiceInterface::CLASS_NAME,
            function () {
                return new TranslationService();
            }
        );

        ServiceRegister::registerService(
            DynamicContentService::CLASS_NAME,
            function () {
                return new DynamicContent();
            }
        );

        ServiceRegister::registerService(
            FieldServiceInterface::CLASS_NAME,
            function () {
                return new FieldService();
            }
        );

        ServiceRegister::registerService(
            SegmentServiceInterface::CLASS_NAME,
            function () {
                return new SegmentService();
            }
        );

        ServiceRegister::registerService(
            ReceiverEventsService::CLASS_NAME,
            function () {
                return new ReceiverEvents();
            }
        );

        ServiceRegister::registerService(
            FormEventsService::CLASS_NAME,
            function () {
                return new FormEvents();
            }
        );
    }

    /**
     * @throws RepositoryClassException
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