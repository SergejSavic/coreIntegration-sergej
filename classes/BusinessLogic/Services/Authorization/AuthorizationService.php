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

namespace CleverReachIntegration\BusinessLogic\Services\Authorization;

use CleverReach\BusinessLogic\Authorization\AuthorizationService as BaseService;
use CleverReach\BusinessLogic\Authorization\Http\AuthProxy;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class AuthorizationService
 * @package CleverReachIntegration\BusinessLogic\Services\Authorization
 */
class AuthorizationService extends BaseService
{
    /**
     * @param false $isRefresh
     * @return string
     */
    public function getRedirectURL($isRefresh = false)
    {
        return \Tools::getHttpHost(true) . __PS_BASE_URI__ . 'en/module/core/authorization' . '?XDEBUG_SESSION_START=debug';
    }

    /**
     * @param $code
     * @throws QueryFilterInvalidParamException
     */
    public function authorize($code)
    {
        $authInfo = $this->getAuthProxy()->getAuthInfo($code, $this->getRedirectURL());
        $this->setAuthInfo($authInfo);
    }

    /**
     * @return object
     */
    private function getAuthProxy()
    {
        return ServiceRegister::getService(AuthProxy::CLASS_NAME);
    }
}
