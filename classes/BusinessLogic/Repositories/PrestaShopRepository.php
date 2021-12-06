<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

use PrestaShop\PrestaShop\Adapter\Entity\Context;
use PrestaShop\PrestaShop\Adapter\Entity\OrderState;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;

/**
 * Class PrestaShopRepository
 * @package CleverReachIntegration\BusinessLogic\Repositories
 */
class PrestaShopRepository
{
    /**
     * @return mixed
     */
    public function getShopName()
    {
        $tableName = 'shop';
        $query = 'SELECT `name` FROM `' . _DB_PREFIX_ . pSQL($tableName) . '`';

        return \Db::getInstance()->getValue($query);
    }

    /**
     * @param $emails
     * @param $groupId
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getPrestaShopCustomers($emails, $groupId)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
             SELECT p.`active`, p.`id_customer`, p.`email`, p.`firstname`, p.`lastname`, p.`date_add`, p.`id_shop`, p.`company`, p.`birthday`, p.`newsletter`,s.`name` as `shop`,
                   ad.`address1`,ad.`city`,ad.`phone`,ad.`postcode`,cnt.`name` as `country`, lng.`iso_code` as `language`, gen.`name` as `gender`
             FROM `' . _DB_PREFIX_ . 'customer` p LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (p.`id_shop` = s.`id_shop`)
             LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON (ad.`id_customer` = p.`id_customer`)
             LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cnt ON (ad.`id_country` = cnt.`id_country`)
             LEFT JOIN `' . _DB_PREFIX_ . 'customer_group` cgr ON (cgr.`id_customer` = p.`id_customer`)
             LEFT JOIN `' . _DB_PREFIX_ . 'lang` lng ON (lng.`id_lang` = p.`id_lang`)
             LEFT JOIN `' . _DB_PREFIX_ . 'gender_lang` gen ON (gen.`id_gender` = p.`id_gender`)
             WHERE (cnt.`id_lang` is null or cnt.`id_lang`=1) AND cgr.`id_group`="' . pSQL($groupId) . '" AND p.`email` IN (' . $this->convertArrayToString($emails) . ')
             AND (gen.`id_lang`=1 or gen.`id_lang` is null)
             GROUP BY p.`email`
             ORDER BY p.`id_customer` ASC'
        );
    }

    /**
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getPrestaShopCustomer($groupId, $email)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT p.`active`, p.`id_customer`, p.`email`, p.`firstname`, p.`lastname`, p.`date_add`, p.`id_shop`, p.`company`, p.`birthday`, p.`newsletter`,s.`name` as `shop`,
                   ad.`address1`,ad.`city`,ad.`phone`,ad.`postcode`,cnt.`name` as `country`, lng.`iso_code` as `language`, gen.`name` as `gender`
            FROM `' . _DB_PREFIX_ . 'customer` p LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (p.`id_shop` = s.`id_shop`)
            LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON (ad.`id_customer` = p.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cnt ON (ad.`id_country` = cnt.`id_country`)
            LEFT JOIN `' . _DB_PREFIX_ . 'customer_group` cgr ON (cgr.`id_customer` = p.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'lang` lng ON (lng.`id_lang` = p.`id_lang`)
            LEFT JOIN `' . _DB_PREFIX_ . 'gender_lang` gen ON (gen.`id_gender` = p.`id_gender`)
            WHERE (cnt.`id_lang` is null or cnt.`id_lang`=1) AND cgr.`id_group`="' . pSQL($groupId) . '"
            AND p.`email`="' . pSQL($email) . '"
            GROUP BY p.`email`
            ORDER BY p.`id_customer` ASC'
        );
    }

    /**
     * @param $emails
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getPrestaShopSubscribers($emails)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
             SELECT p.`active`, p.`id_customer`, p.`email`, p.`firstname`, p.`lastname`, p.`date_add`, p.`id_shop`, p.`company`, p.`birthday`, p.`newsletter`,s.`name` as `shop`,
                   ad.`address1`,ad.`city`,ad.`phone`,ad.`postcode`,cnt.`name` as `country`, lng.`iso_code` as `language`, gen.`name` as `gender`
             FROM `' . _DB_PREFIX_ . 'customer` p LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (p.`id_shop` = s.`id_shop`)
             LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON (ad.`id_customer` = p.`id_customer`)
             LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cnt ON (ad.`id_country` = cnt.`id_country`)
             LEFT JOIN `' . _DB_PREFIX_ . 'lang` lng ON (lng.`id_lang` = p.`id_lang`)
             LEFT JOIN `' . _DB_PREFIX_ . 'gender_lang` gen ON (gen.`id_gender` = p.`id_gender`)
             WHERE (cnt.`id_lang` is null or cnt.`id_lang`=1) AND p.`newsletter`=1 AND p.`email` IN (' . $this->convertArrayToString($emails) . ')
             AND (gen.`id_lang`=1 or gen.`id_lang` is null)
             GROUP BY p.`email`
             ORDER BY p.`id_customer` ASC'
        );
    }

    /**
     * @param $email
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getPrestaShopSubscriber($email)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT p.`active`, p.`id_customer`, p.`email`, p.`firstname`, p.`lastname`, p.`date_add`, p.`id_shop`, p.`company`, p.`birthday`, p.`newsletter`,s.`name` as `shop`,
                   ad.`address1`,ad.`city`,ad.`phone`,ad.`postcode`,cnt.`name` as `country`, lng.`iso_code` as `language`, gen.`name` as `gender`
            FROM `' . _DB_PREFIX_ . 'customer` p LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (p.`id_shop` = s.`id_shop`)
            LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON (ad.`id_customer` = p.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cnt ON (ad.`id_country` = cnt.`id_country`)
            LEFT JOIN `' . _DB_PREFIX_ . 'lang` lng ON (lng.`id_lang` = p.`id_lang`)
            LEFT JOIN `' . _DB_PREFIX_ . 'gender_lang` gen ON (gen.`id_gender` = p.`id_gender`)
            WHERE (cnt.`id_lang` is null or cnt.`id_lang`=1) AND p.`newsletter`=1
            AND p.`email`="' . pSQL($email) . '"
            GROUP BY p.`email`
            ORDER BY p.`id_customer` ASC'
        );
    }

    /**
     * @param $groupId
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getCustomerEmails($groupId)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT p.`email`
            FROM `' . _DB_PREFIX_ . 'customer` p
            LEFT JOIN `' . _DB_PREFIX_ . 'customer_group` cgr ON (cgr.`id_customer` = p.`id_customer`)
            WHERE cgr.`id_group`="' . pSQL($groupId) . '"
            GROUP BY p.`email`
            ORDER BY p.`id_customer` ASC'
        );
    }

    /**
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getSubscribersEmails()
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT p.`email`
            FROM `' . _DB_PREFIX_ . 'customer` p
            WHERE p.`newsletter`=1
            GROUP BY p.`email`
            ORDER BY p.`id_customer` ASC'
        );
    }

    /**
     * @param $customerId
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getCustomerGroups($customerId)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT `id_group`
            FROM `' . _DB_PREFIX_ . 'customer_group`
            WHERE `id_customer` = "' . pSQL($customerId) . '"'
        );
    }

    /**
     * @param $id
     * @return false|string
     */
    public function getGroupById($id)
    {
        $tableName = 'group_lang';
        $query = 'SELECT `name` FROM `' . _DB_PREFIX_ . pSQL($tableName) .
            '` WHERE `id_group` = "' . pSQL($id) . '" AND `id_lang` = 1';

        return \Db::getInstance()->getValue($query);
    }

    /**
     * @param $id
     * @return false|string
     */
    public function getCurrencyById($id)
    {
        $tableName = 'currency';
        $query = 'SELECT `iso_code` FROM `' . _DB_PREFIX_ . pSQL($tableName) .
            '` WHERE `id_currency` = ' . pSQL($id);

        return \Db::getInstance()->getValue($query);
    }

    /**
     * @param $id
     * @return false|string
     */
    public function getManufacturerById($id)
    {
        $tableName = 'manufacturer';
        $query = 'SELECT `name` FROM `' . _DB_PREFIX_ . pSQL($tableName) .
            '` WHERE `id_manufacturer` = "' . pSQL($id) . '"';

        return \Db::getInstance()->getValue($query);
    }

    /**
     * @param string $table
     * @param array $data
     * @param string $where
     * @return bool
     */
    public function update($table, $data, $where)
    {
        return \Db::getInstance()->update($table, $data, $where);
    }

    /**
     * @param string $table
     * @param array $data
     * @return bool
     * @throws \PrestaShopDatabaseException
     */
    public function insert($table, $data)
    {
        return \Db::getInstance()->insert($table, $data);
    }

    /**
     * @param $id
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getProductCategories($id)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT cl.`name`
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`id_category` = cp.`id_category`)
            WHERE (cl.`id_lang` is null or cl.`id_lang`=1) AND cp.`id_product`="' . pSQL($id) . '"
            ORDER BY cl.`id_category` ASC'
        );
    }

    /**
     * @param $id_customer
     * @param false $show_hidden_status
     * @param Context|null $context
     * @return array|bool|\mysqli_result|\PDOStatement|resource
     * @throws \PrestaShopDatabaseException
     */
    public function getCustomerOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $orderStates = OrderState::getOrderStates((int)$context->language->id, false);
        $indexedOrderStates = array();
        foreach ($orderStates as $orderState) {
            $indexedOrderStates[$orderState['id_order_state']] = $orderState;
        }

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT o.*,cu.`iso_code`,
          (SELECT SUM(od.`product_quantity`) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products,
          (SELECT oh.`id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh
           LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
           WHERE oh.`id_order` = o.`id_order` ' .
            (!$show_hidden_status ? ' AND os.`hidden` != 1' : '') .
            ' ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC LIMIT 1) id_order_state
            FROM `' . _DB_PREFIX_ . 'orders` o
            LEFT JOIN `' . _DB_PREFIX_ . 'currency` cu ON (cu.`id_currency` = o.`id_currency`)
            WHERE o.`id_customer` = ' . (int)$id_customer .
            Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
            GROUP BY o.`id_order`
            ORDER BY o.`date_add` DESC');

        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $orderState = !empty($val['id_order_state']) ? $indexedOrderStates[$val['id_order_state']] : null;
            $res[$key]['order_state'] = $orderState['name'] ?: null;
            $res[$key]['invoice'] = $orderState['invoice'] ?: null;
            $res[$key]['order_state_color'] = $orderState['color'] ?: null;
        }

        return $res;
    }

    /**
     * @param $arr
     * @return string
     */
    private function convertArrayToString($arr)
    {
        $string = '';

        foreach ($arr as $index => $data) {
            $string .= "'" . $data . "'";

            if ($index + 1 !== count($arr)) {
                $string .= ',';
            }
        }

        return $string;
    }
}