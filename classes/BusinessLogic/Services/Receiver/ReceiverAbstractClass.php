<?php

namespace CleverReachIntegration\BusinessLogic\Services\Receiver;

use CleverReach\BusinessLogic\Order\DTO\Category\Category;
use CleverReach\BusinessLogic\Order\DTO\OrderItem;
use CleverReach\BusinessLogic\Receiver\DTO\Receiver;
use CleverReach\BusinessLogic\Receiver\DTO\Tag\Tag;
use DateTime;
use PrestaShop\PrestaShop\Adapter\Entity\Order;
use CleverReach\BusinessLogic\Receiver\ReceiverService;
use CleverReachIntegration\BusinessLogic\Repositories\PrestaShopRepository;

/**
 * Class ReceiverAbstractClass
 * @package CleverReachIntegration\BusinessLogic\Services\Receiver
 */
abstract class ReceiverAbstractClass extends ReceiverService
{
    /** @var PrestaShopRepository $prestaShopRepository */
    protected $prestaShopRepository;

    /**
     * @return PrestaShopRepository
     */
    protected function getPrestaShopRepository()
    {
        if ($this->prestaShopRepository === null) {
            $this->prestaShopRepository = new PrestaShopRepository();
        }

        return $this->prestaShopRepository;
    }

    /**
     * @param $orders
     * @param $receiver
     * @return Receiver
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function createReceiver($orders, $receiver)
    {
        $receiverOrders = array();
        $totalSpent = $this->getTotalSpent($orders);
        $lastOrderDate = null;

        foreach ($orders as $index => $order) {
            if ($index === 0) {
                $lastOrderDate = $order['date_add'];
            }
            $receiverOrder = new Order($order['id_order']);
            $products = $receiverOrder->getProducts();
            foreach ($products as $product) {
                $receiverOrders[] = $this->createOrderItem($order, $product);
            }
        }

        $tags = $this->createTags($receiver);

        return $this->prepareReceiver($receiver, $receiverOrders, $tags, $totalSpent, $lastOrderDate);
    }

    /**
     * @param $receiver
     * @param $receiverOrders
     * @param $tags
     * @param $totalSpent
     * @param $lastOrderDate
     * @return Receiver
     */
    protected function prepareReceiver($receiver, $receiverOrders, $tags, $totalSpent, $lastOrderDate)
    {
        $receiverModel = new Receiver();
        $receiverModel->setId($receiver['id_customer']);
        $receiverModel->setEmail($receiver['email']);
        $receiverModel->setSource($receiver['shop']);
        $format = 'Y-m-d H:i:s';
        $date = DateTime::createFromFormat($format, $receiver['date_add']);
        $receiverModel->setActivated($date);
        $receiverModel->setRegistered($date);
        $receiverModel->setSalutation($receiver['gender']);
        $receiverModel->setFirstName($receiver['firstname']);
        $receiverModel->setLastName($receiver['lastname']);
        $streetAddress = $this->getStreetAddress($receiver['address1']);
        $receiverModel->setStreet($streetAddress['street']);
        $receiverModel->setStreetNumber($streetAddress['streetNumber']);
        $receiverModel->setZip($receiver['postcode']);
        $receiverModel->setCity($receiver['city']);
        $receiverModel->setCompany($receiver['company']);
        $receiverModel->setCountry($receiver['country']);
        $receiverModel->setBirthday($receiver['birthday']);
        $receiverModel->setPhone($receiver['phone']);
        $receiverModel->setShop($receiver['shop']);
        $receiverModel->setLanguage($receiver['language']);
        $receiverModel->setLastOrderDate($lastOrderDate);
        $receiverModel->setOrderCount(count($receiverOrders));
        $receiverModel->setTotalSpent($totalSpent);
        $receiverModel->setTags($tags);
        $receiverModel->setOrderItems($receiverOrders);
        $receiverModel->setActive($receiver['newsletter'] === '1');

        return $receiverModel;
    }

    /**
     * @param $order
     * @param $product
     * @return OrderItem
     * @throws \PrestaShopDatabaseException
     */
    public function createOrderItem($order, $product)
    {
        $orderItem = new OrderItem(
            $order['id_order'], $product['id_product'], $product['product_name']
        );
        $orderItem->setPrice($product['product_price']);
        $orderItem->setCurrency($order['iso_code']);
        $orderItem->setQuantity((int)$product['product_quantity']);
        $orderItem->setStamp(strtotime($order['date_add']));
        $manufacturer = $this->getPrestaShopRepository()->getManufacturerById($product['id_manufacturer']);
        $categories = $this->getPrestaShopRepository()->getProductCategories($product['id_product']);
        $categories = $this->getArrayFromPrestaShopReturn($categories);
        $orderItem->setVendor($manufacturer);
        $orderItem->setCategories($this->createCategories($categories));
        return $orderItem;
    }

    /**
     * @param $receiver
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    protected function createTags($receiver)
    {
        $tags = array();

        $groupIds = $this->getPrestaShopRepository()->getCustomerGroups($receiver['id_customer']);
        foreach ($groupIds as $groupId) {
            $groupName = $this->getPrestaShopRepository()->getGroupById((int)$groupId['id_group']);
            $tags[] = new Tag($receiver['shop'], $groupName);
        }

        if ($receiver['newsletter'] === "1") {
            $tags[] = new Tag($receiver['shop'], 'Subscriber');
        }

        return $tags;
    }

    /**
     * @param $categories
     * @return array
     */
    public function createCategories($categories)
    {
        $categoriesModel = array();

        foreach ($categories as $category) {
            $categoriesModel[] = new Category($category);
        }

        return $categoriesModel;
    }

    /**
     * @param $orders
     * @return string
     */
    protected function getTotalSpent($orders)
    {
        $totalSpent = 0;

        foreach ($orders as $order) {
            $totalSpent += $order['total_paid'];
        }

        return '' . $totalSpent . '';
    }

    /**
     * @param $street
     * @return array
     */
    protected function getStreetAddress($street)
    {
        $pieces = explode(" ", $street);
        $streetNumber = '';
        $street = '';

        foreach ($pieces as $piece) {
            if (preg_match('~\d+~', $piece)) {
                $streetNumber .= str_replace(',', '', $piece);
            } else {
                $street .= $piece;
            }
        }

        return array('street' => $street, 'streetNumber' => $streetNumber);
    }

    /**
     * @param $groupId
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    protected function getEmails($groupId)
    {
        $emails = $this->getPrestaShopRepository()->getCustomerEmails($groupId);
        return $this->getArrayFromPrestaShopReturn($emails);
    }

    /**
     * @param array $arr
     * @return array
     */
    protected function getArrayFromPrestaShopReturn(array $arr)
    {
        $response = array();
        foreach ($arr as $data) {
            foreach ($data as $value) {
                $response[] = $value;
            }
        }

        return $response;
    }
}