<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

use Logeecom\Infrastructure\ORM\Entity;
use Logeecom\Infrastructure\ORM\Interfaces\RepositoryInterface;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\ORM\Utility\IndexHelper;

class BaseRepository implements RepositoryInterface
{
    /**
     * Fully qualified name of this interface.
     */
    const THIS_CLASS_NAME = __CLASS__;
    /**
     * @var entity
     */
    protected $entity;

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return _DB_PREFIX_ . '_cleverreach_entity';
    }

    /**
     * @return string
     */
    public static function getClassName()
    {
        return static::THIS_CLASS_NAME;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entity = $entityClass;
    }

    /**
     * @param QueryFilter|null $filter
     * @return array|bool|Entity[]|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function select(QueryFilter $filter = null)
    {
        $query = '
            SELECT c.*
            FROM ' . static::getTableName() . ' c';

        $query .= $this->where($filter);
        $query .= $this->orderBy($filter);
        $query .= $this->limit($filter);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * @param QueryFilter|null $filter
     * @return array|bool|Entity|object|null
     * @throws \PrestaShopDatabaseException
     */
    public function selectOne(QueryFilter $filter = null)
    {
        $filter->setLimit(1);

        return ($this->select($filter))[0];
    }

    /**
     * @param Entity $entity
     * @return bool|int
     * @throws \PrestaShopDatabaseException
     */
    public function save(Entity $entity)
    {
        $properties = $this->getDataForInsertOrUpdate($entity);
        $tableName = $this->getTableWithoutPrefix();

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert($tableName, $properties);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function update(Entity $entity)
    {
        $properties = $this->getDataForInsertOrUpdate($entity);
        $tableName = $this->getTableWithoutPrefix();
        $where = 'id=' . $entity->getId();

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update($tableName, $properties, $where);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity)
    {
        $tableName = $this->getTableWithoutPrefix();
        $where = 'id=' . $entity->getId();

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->delete($tableName, $where);
    }

    /**
     * @param QueryFilter|null $filter
     * @return false|int|string
     */
    public function count(QueryFilter $filter = null)
    {
        $query = '
            SELECT count(*) as count
            FROM ' . static::getTableName() . ' c';

        $query .= $this->where($filter);

        return (int)(\Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query));
    }

    /**
     * @param QueryFilter|null $filter
     * @return string
     */
    protected function where(QueryFilter $filter = null)
    {
        $conditions = $filter->getConditions();

        $query = '';

        if (!empty($conditions)) {
            $query .= ' WHERE ';

            foreach ($conditions as $index => $condition) {
                if ($index !== 0) {
                    $query .= $condition->getChainOperator() . ' ';
                }
                $query .= $condition->getColumn()
                    . ' ' . $condition->getOperator()
                    . "'" . $condition->getValue() . "'";
            }
        }

        return $query;
    }

    /**
     * @param QueryFilter|null $filter
     * @return string
     */
    protected function orderBy(QueryFilter $filter = null)
    {
        $query = '';

        if ($filter->getOrderByColumn() !== null) {
            $query .= ' ORDER BY ' . $filter->getOrderByColumn() . ' ' . $filter->getOrderDirection();
        }

        return $query;
    }

    /**
     * @param QueryFilter|null $filter
     * @return string
     */
    protected function limit(QueryFilter $filter = null)
    {
        $query = '';

        if ($filter->getLimit() !== null) {
            $query .= ' limit ' . $filter->getLimit();

            if ($filter->getOffset() !== null) {
                $query .= ' offset ' . $filter->getOffset();
            }
        }

        return $query;
    }

    /**
     * @param Entity $entity
     * @return array
     */
    protected function getDataForInsertOrUpdate(Entity $entity)
    {
        $fields = IndexHelper::transformFieldsToIndexes($entity);
        $entityConfiguration = $entity->getConfig();
        $properties = array();
        $properties['type'] = $entityConfiguration->getType();

        foreach ($fields as $index => $field) {
            $properties['index_' . $index] = $field;
        }

        $properties['data'] = json_encode($entity->toArray());

        return $properties;
    }

    /**
     * @return false|string
     */
    protected function getTableWithoutPrefix()
    {
        return substr(static::getTableName(), 3);
    }
}