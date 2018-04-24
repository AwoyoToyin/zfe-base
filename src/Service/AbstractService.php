<?php

/**
 * Description of AbstractService
 *
 * @author: Awoyo Oluwatoyin Stephen alias AwoyoToyin <awoyotoyin@gmail.com>
 */
namespace Common\Service;

use Common\Exception\AppException;
use Common\Provider\AbstractProvider;
use Common\Entity\EntityInterface;

abstract class AbstractService implements ServiceInterface
{
    /**
     * @var AbstractProvider
     */
    protected $provider;

    /**
     * Injects the  provider into the service
     *
     * @param AbstractProvider $provider
     */
    public function __construct(AbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Serves the index of all entity
     *
     * @param array $filters
     * @param array $orderBy
     * @param array $groupBy
     * @return EntityInterface
     */
    public function index(
        array $filters = [],
        $orderBy = [],
        $groupBy = []
    ) {
        $selection = $this->provider->selectAll($filters, $orderBy, $groupBy);
        $query = $this->provider->query($selection);
        return $query->getResult();
    }

    /**
     * Fetches an entity by Id
     *
     * @param string|integer $id
     * @return EntityInterface
     */
    public function read($id)
    {
        return $this->provider->findById($id);
    }

    /**
     * Saves an entity
     *
     * @param array $data
     * @return EntityInterface
     */
    public function save(array $data)
    {
        if (!isset($data['id']) || !$data['id']) {
            return $this->provider->createEntity();
        }

        $entity = $this->read((int) $data['id']);
        if (!$entity) {
            throw new AppException('No record found', 404);
        }
        return $entity;
    }

    /**
     * Deletes an entity
     *
     * @param [type] $id
     * @return EntityInterface
     */
    public function delete($id)
    {
        return $this->provider->delete($id);
    }
}
