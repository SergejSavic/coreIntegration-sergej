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
     * @return array|Entity[]
     * @throws \PrestaShopDatabaseException
     */
    public function select(QueryFilter $filter = null)
    {
        $query = '
            SELECT *
            FROM ' . static::getTableName() . ' ';

        $query .= $this->where($filter);
        $query .= $this->orderBy($filter);
        $query .= $this->limit($filter);

        $records = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return ($records !== false && !empty($this->transformEntities($records))) ? $this->transformEntities($records) : array();
    }

    /**
     * @param QueryFilter|null $filter
     * @return Entity|mixed|null
     * @throws \PrestaShopDatabaseException
     */
    public function selectOne(QueryFilter $filter = null)
    {
        $filter->setLimit(1);
        $record = $this->select($filter);

        return !empty($record) ? $record[0] : null;
    }

    /**
     * @param QueryFilter|null $filter
     * @param array $columns
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function selectSpecificColumns(QueryFilter $filter = null, array $columns)
    {
        $query = 'SELECT ' . implode(',', $columns) . ' FROM ' . static::getTableName() . ' ';

        $query .= $this->where($filter);
        $query .= $this->orderBy($filter);
        $query .= $this->limit($filter);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * @param Entity $entity
     * @return int|null
     * @throws \PrestaShopDatabaseException
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException
     */
    public function save(Entity $entity)
    {
        $properties = $this->getDataForInsertOrUpdate($entity);
        $tableName = $this->getTableWithoutPrefix();
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert($tableName, $properties);
        $id = null;

        if ($result) {
            $filter = new QueryFilter();
            $filter->orderBy('id', QueryFilter::ORDER_DESC);
            $id = ($this->selectOne($filter))->getId();
        }

        return (int)$id;
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
            FROM ' . static::getTableName() . ' ';

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
        $indexes = IndexHelper::mapFieldsToIndexes(new $this->entity);
        $query = '';

        if (!empty($conditions)) {
            $query .= ' WHERE ';

            foreach ($conditions as $index => $condition) {
                if ($index !== 0) {
                    $query .= $condition->getChainOperator() . ' ';
                }
                $query .= ' '. 'index_' . $indexes[$condition->getColumn()]
                    . ' ' . $condition->getOperator();

                if ($condition->getValue() !== "") {
                    $query .= "'" . $condition->getValue() . "'";
                } else {
                    $query .= "'" . "'";
                }
            }
        }

        return $query;
    }

    /**
     * @param $columnName
     * @param $data
     * @return string
     */
    protected function whereNotIn($columnName, $data)
    {
        return $columnName . ' NOT IN(' . $this->getStringFromArray('', $data, true) . ') ';
    }

    /**
     * @param $columnName
     * @param $data
     * @return string
     */
    protected function whereIn($columnName, $data)
    {
        return $columnName . ' IN(' . $this->getStringFromArray('', $data, true) . ') ';
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

        $properties['data'] = addslashes(json_encode($entity->toArray()));

        return $properties;
    }

    /**
     * @param array $records
     * @return array
     */
    protected function transformEntities(array $records)
    {
        $entities = array();

        foreach ($records as $record) {
            $entity = $this->transformEntity($record['data']);
            if ($entity !== null) {
                $entity->setId((int)$record['id']);
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * @param string $data
     * @return Entity|null
     */
    protected function transformEntity($data)
    {
        $jsonEntity = json_decode($data, true);
        $jsonEntity['class_name'] = '\\' . $jsonEntity['class_name'];

        if (empty($jsonEntity)) {
            return null;
        }

        if (array_key_exists('class_name', $jsonEntity)) {
            $entity = new $jsonEntity['class_name'];
        } else {
            $entity = new $this->entity;
        }

        /** @var Entity $entity */
        $entity->inflate($jsonEntity);

        return $entity;
    }

    /**
     * @return false|string
     */
    protected function getTableWithoutPrefix()
    {
        return substr(static::getTableName(), 3);
    }


    /**
     * @param string $optionalString
     * @param $dataArray
     * @return string
     */
    protected function getStringFromArray($stringHelper, $dataArray, $isString)
    {
        $string = '';
        $iterator = 1;
        foreach ($dataArray as $index => $data) {
            if ($isString) {
                $string .= "'";
            }
            $string .= $stringHelper . $data;

            if ($isString) {
                $string .= "'";
            }

            if ($iterator !== count($dataArray)) {
                $string .= ',';
            }

            $iterator++;
        }

        return $string;
    }


    /**
     * @param $arrayOfArrays
     * returns array of data from array of arrays
     * @return array
     */
    protected function getArray($arrayOfArrays)
    {
        $arrayOfData = array();

        foreach ($arrayOfArrays as $array) {
            foreach ($array as $data) {
                $arrayOfData[] = $data;
            }
        }

        return $arrayOfData;
    }
}