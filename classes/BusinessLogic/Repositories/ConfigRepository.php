<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

use Logeecom\Infrastructure\ORM\QueryFilter\Operators;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;

class ConfigRepository extends BaseRepository
{
    /**
     * Fully qualified name of this interface.
     */
    const THIS_CLASS_NAME = __CLASS__;

    /**
     * @return string
     */
    public static function getTableName()
    {
        return _DB_PREFIX_ . 'config_entities';
    }

    /**
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function enabledSyncServicesExist()
    {
        $filter = new QueryFilter();
        $filter->where('name', Operators::EQUALS, 'enabledSyncServices');

        $record = $this->selectOne($filter);
    }
}