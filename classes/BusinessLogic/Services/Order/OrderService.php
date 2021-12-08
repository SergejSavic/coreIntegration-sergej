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

namespace CleverReachIntegration\BusinessLogic\Services\Order;

use CleverReach\BusinessLogic\Order\Contracts\OrderService as OrderServiceInterface;
use CleverReachIntegration\BusinessLogic\Repositories\PrestaShopRepository;
use CleverReachIntegration\BusinessLogic\Services\Receiver\VisitorService;
use Logeecom\Infrastructure\ServiceRegister;
use PrestaShop\PrestaShop\Adapter\Entity\Order;

/**
 * Class OrderService
 * @package CleverReachIntegration\BusinessLogic\Services\Order
 */
class OrderService implements OrderServiceInterface
{
    /**
     * @return bool
     */
    public function canSynchronizeOrderItems()
    {
        return true;
    }

    /**
     * @param int|string $orderId
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getOrderItems($orderId)
    {
        $orderItems = array();
        $order = new Order($orderId);
        $iso_code = (new PrestaShopRepository())->getCurrencyById($order->id_currency);
        $orderArray = array('id_order' => (string)$order->id, 'date_add' => $order->date_add, 'iso_code' => $iso_code);
        $products = $order->getProducts();

        foreach ($products as $product) {
            $orderItems[] = $this->createOrderItem($orderArray, $product);
        }

        return $orderItems;
    }

    /**
     * @param mixed $orderId
     * @return string
     */
    public function getOrderSource($orderId)
    {
        return (new PrestaShopRepository())->getShopName() . '-order no.' . $orderId;
    }

    /**
     * @param $order
     * @param $product
     * @return mixed
     */
    private function createOrderItem($order, $product)
    {
        $receiverClass = ServiceRegister::getService(VisitorService::THIS_CLASS_NAME);
        return $receiverClass->createOrderItem($order, $product);
    }
}
