<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

use Logeecom\Infrastructure\ORM\Entity;
use Logeecom\Infrastructure\ORM\Interfaces\RepositoryInterface;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\ORM\Utility\IndexHelper;
use RuntimeException;

class BaseRepository implements RepositoryInterface
{
    public static function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    public function setEntityClass($entityClass)
    {
        // TODO: Implement setEntityClass() method.
    }

    public function select(QueryFilter $filter = null)
    {
        // TODO: Implement select() method.
    }

    public function selectOne(QueryFilter $filter = null)
    {
        // TODO: Implement selectOne() method.
    }

    public function save(Entity $entity)
    {
        // TODO: Implement save() method.
    }

    public function update(Entity $entity)
    {
        // TODO: Implement update() method.
    }

    public function delete(Entity $entity)
    {
        // TODO: Implement delete() method.
    }

    public function count(QueryFilter $filter = null)
    {
        // TODO: Implement count() method.
    }

}