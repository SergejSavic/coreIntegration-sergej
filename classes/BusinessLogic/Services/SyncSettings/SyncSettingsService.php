<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2019 PrestaShop SA
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

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
