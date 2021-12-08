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

namespace CleverReachIntegration\BusinessLogic\Services\EventsHandler;

use CleverReach\BusinessLogic\WebHookEvent\Contracts\EventsService;
use CleverReach\BusinessLogic\Receiver\WebHooks\Handler as ReceiverHandler;
use CleverReach\BusinessLogic\WebHookEvent\DTO\WebHook;
use CleverReach\BusinessLogic\WebHookEvent\Exceptions\UnableToHandleWebHookException;
use CleverReachIntegration\BusinessLogic\Services\HTTP\Request;
use Logeecom\Infrastructure\Logger\Logger;

/**
 * Class ReceiverEventsHandler
 * @package CleverReachIntegration\BusinessLogic\Services\ReceiverEventsHandler
 */
class ReceiverEventsHandler
{
    /** fully qualified name of the class */
    const CLASS_NAME = __CLASS__;
    /**
     * @var EventsService
     */
    private $eventsService;
    /**
     * @var ReceiverHandler
     */
    private $handler;

    /**
     * ReceiverEventsHandler constructor.
     *
     * @param EventsService $eventsService
     * @param $handler
     */
    public function __construct(EventsService $eventsService, $handler)
    {
        $this->eventsService = $eventsService;
        $this->handler = $handler;
    }

    /**
     * Handles request from CleverReach.
     *
     * @param Request $request
     *
     * @return array
     */
    public function handleRequest($request)
    {
        if ($request->getMethod() === 'GET') {
            $response = $this->register($request);
        } else {
            $response['httpCode'] = $this->handle($request);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function register($request)
    {
        $response = array();
        $secret = $request->getParam('secret');

        if ($secret === false) {
            $response['httpCode'] = 400;
        } else {
            $response['verificationToken'] = $this->eventsService->getVerificationToken() . ' ' . $secret;
            $response['httpCode'] = 200;
        }

        return $response;
    }

    /**
     * @param $request
     * @return int
     */
    private function handle($request)
    {
        $requestBody = json_decode($request->getRawBody(), true);

        if (!$this->validate($requestBody)) {
            return 400;
        }

        if ($requestBody['event'] === 'receiver.subscribed' || $requestBody['event'] === 'receiver.unsubscribed') {
            $webHook = new WebHook($requestBody['condition'], $requestBody['event'], $requestBody['payload']);

            try {
                $this->handler->handle($webHook);
            } catch (UnableToHandleWebHookException $e) {
                Logger::logError($e->getMessage(), 'Integration');
            }
        }

        return 200;
    }

    /**
     * @param $requestBody
     * @return bool
     */
    private function validate($requestBody)
    {
        return !empty($requestBody['payload'])
            && !empty($requestBody['event'])
            && !empty($requestBody['condition']);
    }
}
