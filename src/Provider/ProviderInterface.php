<?php

/**
 * Description of ProviderInterface
 *
 * @author: Awoyo Oluwatoyin Stephen alias AwoyoToyin <awoyotoyin@gmail.com>
 */
namespace Common\Provider;

use Common\Entity\EntityInterface;

interface ProviderInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function findById($id);

    /**
     * @param EntityInterface $entity
     * @return mixed
     */
    public function save(EntityInterface $entity);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Selects all entities from the repository
     *
     * @param array $filters
     * @param array $orderBy
     * @param array $groupBy
     * @return Selection
     */
    public function selectAll(
        array $filters = [],
        array $orderBy = [],
        array $groupBy = []
    );

    /**
     * Selects all entities from the repository as paginated objects
     *
     * @param int $first
     * @param int $max
     * @param array $filters
     * @param array $orderBy
     * @param array $groupBy
     * @param boolean $fetchJoinCollection
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function selectAllPaginate(
        $first = 0,
        $max = 20,
        array $filters = [],
        array $orderBy = [],
        array $groupBy = [],
        $fetchJoinCollection = true
    );

    /**
     * @param int $first
     * @param int $max
     * @param array $filters
     * @param array $joins
     * @param array $orderBy
     * @param array $groupBy
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function selectJoin(
        $first = 0,
        $max = 20,
        array $filters = [],
        array $joins = [],
        array $orderBy = [],
        array $groupBy = []
    );

    /**
     * Queries the datastore for entities
     *
     * @param Selection|\Doctrine\ORM\QueryBuilder $selection
     */
    public function query($selection);

    /**
     * Creates an instance of the Enitity being provided
     */
    public function createEntity();
}
