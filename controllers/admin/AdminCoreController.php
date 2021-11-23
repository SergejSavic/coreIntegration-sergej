<?php

use Logeecom\Infrastructure\Http\HttpClient;
use Logeecom\Infrastructure\ServiceRegister;
use CleverReachIntegration\BusinessLogic\Services\DemoServiceInterface;
use Logeecom\Infrastructure\Http;
use CleverReachIntegration\Infrastructure\BootstrapComponent;

/**
 * Class AdminCoreController
 */
class AdminCoreController extends ModuleAdminController
{
    /**
     * Initializes bootstrap and parent constructor
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryClassException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        BootstrapComponent::initServices();
        BootstrapComponent::initRepositories();
        parent::__construct();
    }

    /**
     * @throws SmartyException
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException
     */
    public function initContent()
    {
        $demoService = ServiceRegister::getService(DemoServiceInterface::CLASS_NAME);
        $msg = $demoService->getMessage();
        $configRepo = \Logeecom\Infrastructure\ORM\RepositoryRegistry::getRepository(\Logeecom\Infrastructure\Configuration\ConfigEntity::CLASS_NAME);
        $className = $configRepo::getClassName();
        $table = $className::getTableName();
        $this->setTemplateFile('origin.tpl', array());
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