<?php

use CleverReach\BusinessLogic\Authorization\Tasks\Composite\ConnectTask;
use CleverReachIntegration\Infrastructure\BootstrapComponent;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use CleverReach\BusinessLogic\Authorization\Contracts\AuthorizationService as BaseAuthService;
use CleverReachIntegration\BusinessLogic\Services\Authorization\AuthorizationService;
use Logeecom\Infrastructure\TaskExecution\QueueService as BaseQueueService;
use CleverReach\BusinessLogic\Configuration\Configuration;

/**
 * Class CoreAuthorizationModuleFrontController
 */
class CoreAuthorizationModuleFrontController extends ModuleFrontController
{
    /**
     * Initializes bootstrap components
     */
    public function __construct()
    {
        $this->bootstrap = true;
        BootstrapComponent::init();
        parent::__construct();
    }

    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        /** @var Configuration $configService */
        $configService = ServiceRegister::getService(Configuration::CLASS_NAME);

        $code = Tools::getValue('code');

        if ($code) {
            /** @var AuthorizationService $authorizationService */
            $authorizationService = ServiceRegister::getService(BaseAuthService::CLASS_NAME);
            /** @var BaseQueueService $queueService */
            $queueService = ServiceRegister::getService(BaseQueueService::CLASS_NAME);
            try {
                $authorizationService->authorize($code);
                $queueService->enqueue($configService->getDefaultQueueName(), new ConnectTask());
            } catch (QueryFilterInvalidParamException $e) {
            } catch (QueueStorageUnavailableException $e) {
            }
        }
    }

}