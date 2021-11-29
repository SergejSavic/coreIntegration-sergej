<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

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
}