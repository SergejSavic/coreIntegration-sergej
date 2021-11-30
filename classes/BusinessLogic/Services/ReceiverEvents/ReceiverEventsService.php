<?php

namespace CleverReachIntegration\BusinessLogic\Services\ReceiverEvents;

use CleverReach\BusinessLogic\Receiver\ReceiverEventsService as BaseReceiverEventsService;

class ReceiverEventsService extends BaseReceiverEventsService
{
    /**
     * @return string
     */
    public function getEventUrl()
    {
        return '';
    }

}