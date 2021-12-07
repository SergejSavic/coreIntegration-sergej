<?php

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