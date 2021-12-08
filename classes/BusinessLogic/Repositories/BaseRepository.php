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

namespace CleverReachIntegration\BusinessLogic\Repositories;

use CleverReachIntegration\BusinessLogic\Services\Transformer\TransformerService;
use Logeecom\Infrastructure\ORM\Entity;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Logeecom\Infrastructure\ORM\Interfaces\RepositoryInterface;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\ORM\Utility\IndexHelper;

/**
 * Class BaseRepository
 * @package CleverReachIntegration\BusinessLogic\Repositories
 */
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
        return _DB_PREFIX_ . 'cleverreach_entities';
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
        $transformedEntities = $this->transformEntities($records);

        return ($records !== false && !empty($transformedEntities)) ? $transformedEntities : array();
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

        if ($filter !== null) {
            $query .= $this->where($filter);
            $query .= $this->orderBy($filter);
            $query .= $this->limit($filter);
        }

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * @param Entity $entity
     * @return int|null
     * @throws \PrestaShopDatabaseException
     * @throws QueryFilterInvalidParamException
     */
    public function save(Entity $entity)
    {
        $properties = $this->getDataForInsertOrUpdate($entity);
        $indexes = IndexHelper::mapFieldsToIndexes($entity);

        $query = 'INSERT INTO ' . static::getTableName() . '(type,' . $this->getStringFromArray('index_', $indexes, false) .
            ',data) VALUES(' . $this->getStringFromArray('', $properties, true) . ')';

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
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
     * @return int
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
                $value = $condition->getValue();
                if ($index !== 0) {
                    $query .= $condition->getChainOperator() . ' ';
                }

                if ($indexes[$condition->getColumn()] !== null) {
                    $query .= ' ' . 'index_' . $indexes[$condition->getColumn()]
                        . ' ' . $condition->getOperator();
                } else {
                    $query .= ' ' . $condition->getColumn() . ' ' . $condition->getOperator();
                }

                if ($value !== null) {
                    if ($condition->getValueType() === 'integer' && $condition->getColumn() !== 'id') {
                        $value = TransformerService::transformNumberToString($value);
                    }

                    if ($value !== "") {
                        if ($condition->getColumn() !== 'id') {
                            $query .= "'" . $value . "'";
                        } else {
                            $query .= $value . ' ';
                        }
                    } else {
                        $query .= "'" . "'";
                    }
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
        $indexes = IndexHelper::mapFieldsToIndexes(new $this->entity);
        $column = $filter->getOrderByColumn();
        $query = '';

        if ($column !== null) {
            if ($indexes[$column] !== null) {
                $query .= ' ORDER BY ' . 'index_' . $indexes[$column] . ' ' . $filter->getOrderDirection();
            } else {
                $query .= ' ORDER BY ' . $column . ' ' . $filter->getOrderDirection();
            }
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
        return \Tools::substr(static::getTableName(), 3);
    }


    /**
     * @param $stringHelper
     * @param $dataArray
     * @param $isString
     * @return string
     */
    protected function getStringFromArray($stringHelper, $dataArray, $isString)
    {
        $string = '';
        $iterator = 1;
        foreach ($dataArray as $index => $data) {
            if ($isString && $data !== null && $data !== '') {
                $string .= "'";
            }

            if ($data !== null) {
                if ($data === '') {
                    $string .= "''";
                } else {
                    $string .= $stringHelper . $data;
                }
            } else {
                $string .= "NULL";
            }

            if ($isString && $data !== null && $data !== '') {
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
