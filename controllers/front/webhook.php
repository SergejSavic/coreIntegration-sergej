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

use CleverReachIntegration\BusinessLogic\Services\HTTP\Request;
use CleverReachIntegration\Infrastructure\BootstrapComponent;
use CleverReachIntegration\BusinessLogic\Services\EventsHandler\ReceiverEventsHandler;
use Logeecom\Infrastructure\ServiceRegister;

/**
 * Class CoreWebhookModuleFrontController
 */
class CoreWebhookModuleFrontController extends ModuleFrontController
{
    /** @var ReceiverEventsHandler $eventsHandler */
    private $receiverEventsHandler;

    /**
     * Initializes bootstrap components
     */
    public function __construct()
    {
        $this->bootstrap = true;
        BootstrapComponent::init();
        $this->receiverEventsHandler = ServiceRegister::getService(ReceiverEventsHandler::CLASS_NAME);
        parent::__construct();
    }

    /**
     * @return void
     */
    public function initContent()
    {
        $request = new Request();
        $response = $this->receiverEventsHandler->handleRequest($request);
        self::diePlain($response['verificationToken'], $response['httpCode']);
    }

    /**
     * Sets response header content plaintext, echos $plainText and terminates the process
     *
     * @param string $plainText
     * @param int $httpCode
     */
    public static function diePlain($plainText = '', $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: text/plain');

        die($plainText);
    }
}
