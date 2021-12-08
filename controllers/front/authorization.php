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
     * enqueues connect task
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
