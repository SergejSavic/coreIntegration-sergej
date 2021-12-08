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
