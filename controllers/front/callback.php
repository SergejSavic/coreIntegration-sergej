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
        /** @var AsyncProcessStarterService $asyncProcessService */
        $asyncProcessService = ServiceRegister::getService(AsyncProcessService::CLASS_NAME);
        $guid = Tools::getValue('guid');

        if($guid) {
            $asyncProcessService->runProcess($guid);
        }
        /*
        $this->context->smarty->assign(
            array(
                'paymentId' => '1'
            ));

        $this->setTemplate('module:core/views/templates/front/file.tpl');
        */
    }

}