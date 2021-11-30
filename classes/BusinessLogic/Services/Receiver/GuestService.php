<?php

namespace CleverReachIntegration\BusinessLogic\Services\Receiver;

use CleverReach\BusinessLogic\Receiver\DTO\Receiver;
use CleverReach\BusinessLogic\Receiver\ReceiverService;

/**
 * Class GuestService
 * @package CleverReachIntegration\BusinessLogic\Services\Receiver
 */
class GuestService extends ReceiverAbstractClass
{
    /**
     * Singleton instance of this class.
     * @var static
     */
    protected static $instance;
    /** @var int guests group id */
    const GUEST_GROUP_ID = 2;
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
        $record = $this->getPrestaShopRepository()->getPrestaShopCustomer(self::GUEST_GROUP_ID, $email);

        if (count($record) > 0) {
            $guest = $record[0];
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($guest['id_customer']);
            return $this->createReceiver($orders, $guest);
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
        $guests = array();
        $records = $this->getPrestaShopRepository()->getPrestaShopCustomers($emails, self::GUEST_GROUP_ID);

        foreach ($records as $record) {
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($record['id_customer']);
            $guest = $this->createReceiver($orders, $record);
            $guests[] = $guest;
        }

        return $guests;
    }

    /**
     * @return array|string[]
     * @throws \PrestaShopDatabaseException
     */
    public function getReceiverEmails()
    {
        return $this->getEmails(self::GUEST_GROUP_ID);
    }

}