<?php

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