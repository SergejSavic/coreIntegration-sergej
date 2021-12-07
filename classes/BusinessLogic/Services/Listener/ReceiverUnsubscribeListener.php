<?php

namespace CleverReachIntegration\BusinessLogic\Services\Listener;

use CleverReachIntegration\BusinessLogic\Repositories\PrestaShopRepository;

/**
 * Class ReceiverUnsubscribeListener
 * @package CleverReachIntegration\BusinessLogic\Services\Listener
 */
class ReceiverUnsubscribeListener
{
    /** @var string fully qualified name of the class */
    const CLASS_NAME = __CLASS__;

    /**
     * @param $event
     * @return void
     */
    public static function handle($event)
    {
        $receiverInfo = $event->getReceiverId();
        $email = $receiverInfo->getEmail();

        (new PrestaShopRepository())->update('customer', array('newsletter' => 0), 'email='. "'". $email . "'");
    }
}