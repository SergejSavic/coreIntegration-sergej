<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use CleverReach\BusinessLogic\Configuration\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;

/**
 * Class ConfigService
 * @package CleverReachIntegration\BusinessLogic\Services
 */
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
        return 'zhYTmczOCA';
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return 'p0ZlXjkyvdjd23f5I2qSiZahSwurl62K';
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

    /**
     * @param string $guid
     * @return string
     */
    public function getAsyncProcessUrl($guid)
    {
        return Tools::getHttpHost(true) . __PS_BASE_URI__ . 'en/module/core/callback?guid='. $guid. '&XDEBUG_SESSION_START=debug';
    }

}