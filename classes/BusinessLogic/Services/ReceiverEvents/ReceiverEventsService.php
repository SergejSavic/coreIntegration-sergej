<?php

namespace CleverReachIntegration\BusinessLogic\Services\ReceiverEvents;

use CleverReach\BusinessLogic\Receiver\ReceiverEventsService as BaseReceiverEventsService;

/**
 * Class ReceiverEventsService
 * @package CleverReachIntegration\BusinessLogic\Services\ReceiverEvents
 */
class ReceiverEventsService extends BaseReceiverEventsService
{
    /**
     * @return string
     */
    public function getEventUrl()
    {
        return 'http://c945-82-117-217-138.ngrok.io/en/module/core/webhook?XDEBUG_SESSION_START=debug';
    }

}