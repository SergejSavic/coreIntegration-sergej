<?php

namespace CleverReachIntegration\BusinessLogic\Services\Receiver;

use CleverReach\BusinessLogic\Receiver\DTO\Receiver;

/**
 * Class VisitorService
 * @package CleverReachIntegration\BusinessLogic\Services\Receiver
 */
class VisitorService extends ReceiverAbstractClass
{
    /**
     * Singleton instance of this class.
     * @var static
     */
    protected static $instance;
    /** @var int visitors group id */
    const VISITOR_GROUP_ID = 1;
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
        $record = $this->getPrestaShopRepository()->getPrestaShopCustomer(self::VISITOR_GROUP_ID, $email);

        if (count($record) > 0) {
            $visitor = $record[0];
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($visitor['id_customer']);
            return $this->createReceiver($orders, $visitor);
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
        $records = $this->getPrestaShopRepository()->getPrestaShopCustomers($emails, self::VISITOR_GROUP_ID);
        $visitors = array();

        foreach ($records as $record) {
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($record['id_customer']);
            $visitor = $this->createReceiver($orders, $record);
            $visitors[] = $visitor;
        }

        return $visitors;
    }

    /**
     * @return array|string[]
     * @throws \PrestaShopDatabaseException
     */
    public function getReceiverEmails()
    {
        return $this->getEmails(self::VISITOR_GROUP_ID);
    }

}