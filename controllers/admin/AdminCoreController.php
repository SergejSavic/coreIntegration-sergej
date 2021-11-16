<?php

use Logeecom\Infrastructure\Http\HttpClient;
use Logeecom\Infrastructure\ServiceRegister;
use CleverReachIntegration\BusinessLogic\DemoService;
use Logeecom\Infrastructure\Http;
use CleverReachIntegration\Infrastructure\BootstrapComponent;

/**
 * Class AdminCoreController
 */
class AdminCoreController extends ModuleAdminController
{
    /**
     * Initialize bootstrap and parent constructor
     */
    public function __construct()
    {
        $this->bootstrap = true;
        BootstrapComponent::initServices();
        parent::__construct();
    }

    /**
     * @throws SmartyException
     */
    public function initContent()
    {
        $demoService = ServiceRegister::getService(DemoService::CLASS_NAME);
        $this->setTemplateFile('origin.tpl', array("message" => $demoService->getMessage()));
    }

    /**
     * @param $templateName
     * @param $variables
     */
    private function setTemplateFile($templateName, $variables)
    {
        $template = $this->context->smarty->createTemplate($this->getTemplatePath() . $templateName, $this->context->smarty);
        $template->assign($variables);
        $this->content .= $template->fetch();
        parent::initContent();
    }

}