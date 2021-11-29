<?php

namespace CleverReachIntegration\BusinessLogic\Services\Form;

use CleverReach\BusinessLogic\Configuration\Configuration;
use CleverReachIntegration\BusinessLogic\Services\ConfigService as ConfigurationService;
use Logeecom\Infrastructure\ServiceRegister;
use CleverReach\BusinessLogic\Form\FormService as BaseService;

class FormService extends BaseService
{
    /** @var ConfigurationService $configService */
    private $configService;

    /**
     * @param ConfigurationService $configService
     * initializes configuration service
     */
    public function __construct()
    {
        $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
    }

    /**
     * @return string
     */
    public function getDefaultFormName()
    {
        return $this->configService->getIntegrationName();
    }

}