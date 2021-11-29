<?php

use CleverReachIntegration\Infrastructure\BootstrapComponent;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\TaskExecution\Interfaces\AsyncProcessService;
use Logeecom\Infrastructure\TaskExecution\AsyncProcessStarterService;

/**
 * Class CoreCallbackModuleFrontController
 */
class CoreCallbackModuleFrontController extends ModuleFrontController
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
        $guid = Tools::getValue('guid');

        if ($guid) {
            /** @var AsyncProcessStarterService $asyncProcessService */
            $asyncProcessService = ServiceRegister::getService(AsyncProcessService::CLASS_NAME);
            $asyncProcessService->runProcess($guid);
        }
    }

}