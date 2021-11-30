<?php

namespace CleverReachIntegration\BusinessLogic\Services\Receiver;

use CleverReach\BusinessLogic\Receiver\DTO\Receiver;

/**
 * Class SubscriberService
 * @package CleverReachIntegration\BusinessLogic\Services\Receiver
 */
class SubscriberService extends ReceiverAbstractClass
{
    /**
     * Singleton instance of this class.
     * @var static
     */
    protected static $instance;
    /** @var string full name of this class */
    const THIS_CLASS_NAME = __CLASS__;

    /**
     * @param string $email
     * @param false $isServiceSpecificDataRequired
     * @return Receiver|null
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getReceiver($email, $isServiceSpecificDataRequired = false)
    {
        $record = $this->getPrestaShopRepository()->getPrestaShopSubscriber($email);

        if (count($record) > 0) {
            $subscriber = $record[0];
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($subscriber['id_customer']);
            return $this->createReceiver($orders, $subscriber);
        }

        return null;
    }

    /**
     * @param array $emails
     * @param false $isServiceSpecificDataRequired
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getReceiverBatch(array $emails, $isServiceSpecificDataRequired = false)
    {
        $records = $this->getPrestaShopRepository()->getPrestaShopSubscribers($emails);
        $subscribers = array();

        foreach ($records as $record) {
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($record['id_customer']);
            $subscriber = $this->createReceiver($orders, $record);
            $subscribers[] = $subscriber;
        }

        return $subscribers;
    }

    /**
     * @return array|string[]
     * @throws \PrestaShopDatabaseException
     */
    public function getReceiverEmails()
    {
        $emails = $this->getPrestaShopRepository()->getSubscribersEmails();
        return $this->getArrayFromPrestaShopReturn($emails);
    }

}