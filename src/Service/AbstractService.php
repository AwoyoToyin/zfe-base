<?php

/**
 * Description of AbstractService
 *
 * @author: Awoyo Oluwatoyin Stephen alias awoyotoyin <awoyotoyin@gmail.com>
 */
namespace Zfe\Common\Service;

use Zfe\Common\Exception\AppException;
use Zfe\Common\Provider\AbstractProvider;
use Zfe\Common\Entity\EntityInterface;

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
            $entity = $this->provider->createEntity();
        } else {
            $entity = $this->read((int) $data['id']);
            if (!$entity) {
                throw new AppException('No record found', 404);
            }
        }

        return $this->provider->save($entity);
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
