<?php

namespace CleverReachIntegration\BusinessLogic\Services\SyncSettings;

use CleverReach\BusinessLogic\Receiver\DTO\Config\SyncService;
use CleverReach\BusinessLogic\SyncSettings\SyncSettingsService as BaseSyncSettingsService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\CustomerService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\GuestService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\SubscriberService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\VisitorService;

/**
 * Class SyncSettingsService
 * @package CleverReachIntegration\BusinessLogic\Services\SyncSettings
 */
class SyncSettingsService extends BaseSyncSettingsService
{
    /**
     * @return array
     */
    public function getAvailableServices()
    {
        $services = array();
        $services[] = new SyncService('service-' . VisitorService::THIS_CLASS_NAME, 2, VisitorService::THIS_CLASS_NAME);
        $services[] = new SyncService('service-' . GuestService::THIS_CLASS_NAME, 2, GuestService::THIS_CLASS_NAME);
        $services[] = new SyncService('service-' . CustomerService::THIS_CLASS_NAME, 2, CustomerService::THIS_CLASS_NAME);
        $services[] = new SyncService('service-' . SubscriberService::THIS_CLASS_NAME, 2, SubscriberService::THIS_CLASS_NAME);

        return $services;
    }

}