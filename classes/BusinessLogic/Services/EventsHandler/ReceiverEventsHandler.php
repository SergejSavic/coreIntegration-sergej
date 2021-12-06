<?php

namespace CleverReachIntegration\BusinessLogic\Services\EventsHandler;

use CleverReach\BusinessLogic\WebHookEvent\Contracts\EventsService;
use CleverReach\BusinessLogic\Receiver\WebHooks\Handler as ReceiverHandler;
use CleverReach\BusinessLogic\WebHookEvent\DTO\WebHook;
use CleverReach\BusinessLogic\WebHookEvent\Exceptions\UnableToHandleWebHookException;
use CleverReachIntegration\BusinessLogic\Repositories\PrestaShopRepository;
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

        (new PrestaShopRepository())->update('customer', array('newsletter' => 1), 'email=' . "'" . $requestBody['email'] . "'");

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