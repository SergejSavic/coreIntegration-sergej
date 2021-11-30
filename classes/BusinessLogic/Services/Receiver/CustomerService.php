<?php

namespace CleverReachIntegration\BusinessLogic\Services\Receiver;

use CleverReach\BusinessLogic\Receiver\DTO\Receiver;

/**
 * Class CustomerService
 * @package CleverReachIntegration\BusinessLogic\Services\Receiver
 */
class CustomerService extends ReceiverAbstractClass
{
    /**
     * Singleton instance of this class.
     * @var static
     */
    protected static $instance;
    /** @var int customers group id */
    const CUSTOMER_GROUP_ID = 3;
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
        $record = $this->getPrestaShopRepository()->getPrestaShopCustomer(self::CUSTOMER_GROUP_ID, $email);

        if (count($record) > 0) {
            $customer = $record[0];
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($customer['id_customer']);
            return $this->createReceiver($orders, $customer);
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
        $customers = array();
        $records = $this->getPrestaShopRepository()->getPrestaShopCustomers($emails, self::CUSTOMER_GROUP_ID);

        foreach ($records as $record) {
            $orders = $this->getPrestaShopRepository()->getCustomerOrders($record['id_customer']);
            $customer = $this->createReceiver($orders, $record);
            $customers[] = $customer;
        }

        return $customers;
    }

    /**
     * @return array|string[]
     * @throws \PrestaShopDatabaseException
     */
    public function getReceiverEmails()
    {
        return $this->getEmails(self::CUSTOMER_GROUP_ID);
    }

}