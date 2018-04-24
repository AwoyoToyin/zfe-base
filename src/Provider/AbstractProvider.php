<?php

/**
 * Description of AbstractProvider
 *
 * @author: Awoyo Oluwatoyin Stephen alias AwoyoToyin <awoyotoyin@gmail.com>
 */
namespace Zfe\Common\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Zfe\Common\Provider\ProviderInterface;
use Zfe\Common\Entity\EntityInterface;
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Name of Entity class
     *
     * @var string
     */
    protected $entityClass = null;

    /**
     * An alias to use in queries
     *
     * @var String
     */
    protected $entityAlias = 'entity';

    /**
     * Injects the  entity manager into the provider
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Provides all entities
     */
    public function fetchAll(): QueryBuilder
    {
        $selection = $this->selectAll();
        return $this->query($selection);
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::selectAll()
     * @param array $filters
     * @param array $orderBy
     * @param array $groupBy
     * @return QueryBuilder
     */
    public function selectAll(
        array $filters = [],
        array $orderBy = [],
        array $groupBy = []
    ) {
        $selection = $this->getEntityManager()->createQueryBuilder();

        $selection->select([$this->entityAlias])
            ->from($this->entityClass, $this->entityAlias);

        return $this->filterFurther($filters, $orderBy, $groupBy, $selection, 0, 0);
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::selectAllPaginate()
     * @param int $first
     * @param int $max
     * @param array $filters
     * @param array $orderBy
     * @param array $groupBy
     * @param boolean $fetchJoinCollection
     * @return Tools\Pagination\Paginator
     */
    public function selectAllPaginate(
        $first = 0,
        $max = 20,
        array $filters = [],
        array $orderBy = [],
        array $groupBy = [],
        $fetchJoinCollection = true
    ) {
        $selection = $this->getEntityManager()->createQueryBuilder();

        $selection->select([$this->entityAlias])
            ->from($this->entityClass, $this->entityAlias);

        $selection = $this->filterFurther($filters, $orderBy, $groupBy, $selection, $first, $max);

        return new Paginator($selection, $fetchJoinCollection);
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::selectJoin()
     * @param int $first
     * @param int $max
     * @param array $filters
     * @param array $joins
     * @param array $orderBy
     * @param array $groupBy
     * @return QueryBuilder
     */
    public function selectJoin(
        $first = 0,
        $max = 20,
        array $filters = [],
        array $joins = [],
        array $orderBy = [],
        array $groupBy = []
    ) {
        $selection = $this->getEntityManager()->createQueryBuilder();
        $aliases = [];
        $aliases[] = $this->entityAlias;
        foreach ($joins as $table => $attributes) {
            $aliases[] = $attributes['alias'];
        }
        $selection->select($aliases)
            ->from($this->entityClass, $this->entityAlias);
        foreach ($joins as $table => $attributes) {
            $selection->Join(
                $table,
                $attributes['alias'],
                \Doctrine\ORM\Query\Expr\Join::WITH,
                "{$attributes['main_table_field']} = {$attributes['alias']}.{$attributes['join_table_field']}"
            );
        }
        return $this->filterFurther($filters, $orderBy, $groupBy, $selection, 0, 0);
    }

    /**
     * Retrieves the entities based on the selection
     *
     * @param $selection
     */
    public function query($selection)
    {
        return $selection->getQuery();
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::findById()
     * @param $id
     * @return mixed|null|object
     */
    public function findById($id)
    {
        $entity = $this->getEntityManager()->getRepository($this->entityClass);
        return $entity->find($id);
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::save()
     * @param EntityInterface $entity
     * @return mixed|void
     */
    public function save(EntityInterface $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::createEntity()
     */
    public function createEntity()
    {
        $entity = new $this->entityClass();
        return $entity;
    }

    /**
     * (non-PHPdoc)
     * @see ProviderInterface::delete()
     * @param $id
     * @return mixed|void
     */
    public function delete($id)
    {
        $entity = $this->findById($id);
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $first
     * @param $max
     * @param array $filters
     * @param array $orderBy
     * @param array $groupBy
     * @param QueryBuilder $selection
     * @return Selection|QueryBuilder
     */
    protected function filterFurther(
        array $filters,
        array $orderBy,
        array $groupBy,
        $selection,
        $first,
        $max
    ) {
        if (!empty($filters)) {
            $pc = 1;
            $params = [];
            foreach ($filters as $field => $specs) {
                switch ($specs['strategy']) {
                    default:
                    case 'Equals':
                        $part = "{$this->entityAlias}.{$field} = ?{$pc}";
                        $params[$pc] = $specs['value'];
                        break;
                    case 'NotEquals':
                        $part = "{$this->entityAlias}.{$field} <> ?{$pc}";
                        $params[$pc] = $specs['value'];
                        break;
                    case 'Contains':
                        $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                        $params[$pc] = "%{$specs['value']}%";
                        break;
                    case 'StartsWith':
                        $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                        $params[$pc] = "%{$specs['value']}";
                        break;
                    case 'EndsWith':
                        $part = "{$this->entityAlias}.{$field} Like ?{$pc}";
                        $params[$pc] = "{$specs['value']}%";
                        break;
                }
                $selection->andWhere($part);
                $pc++;
            }
            $selection->setParameters($params);
        }

        // group data
        if (!empty($groupBy)) {
            foreach ($groupBy as $field) {
                $selection->groupBy($this->entityAlias . '.' . $field);
            }
        }

        // Order data
        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $selection->orderBy($field, $dir);
            }
        }

        // Paginate data
        if ($max > 0) {
            $selection->setFirstResult($first)
                ->setMaxResults($max);
        }
        return $selection;
    }

    /**
     * Gets the instance of an EntityManagerInterface
     *
     * @return EntityManagerInterface
     */

    protected function getEntityManager()
    {
        return $this->em;
    }
}
