<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use CleverReach\BusinessLogic\Configuration\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;

class ConfigService extends Configuration
{
    /**
     * @return string
     */
    public function getDefaultQueueName()
    {
        return 'Sergej-CoreIntegrationQueue';
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return 'rbUPpLYzJh';
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return 'mk2yKjaXbomIcYpf0xqlRrohADIWm2YS';
    }

    /**
     * @return string
     */
    public function getSystemUrl()
    {
        return Tools::getHttpHost(true) . __PS_BASE_URI__;
    }

    /**
     * @return string
     */
    public function getIntegrationName()
    {
        return 'Core Integration';
    }

    public function getAsyncProcessUrl($guid)
    {
        // TODO: Implement getAsyncProcessUrl() method.
    }

}