<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Components\Api\Exception as ApiException;

class ConnectorShippingMethods extends Resource
{

    /**
     * @return \Shopware\Models\Dispatch\Repository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(Dispatch::class);
    }

    /**
     * Get shipping methods
     *
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 25, array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('shippingMethods');

        // Create builder on requested filter and sort
        $builder->addFilter($criteria)
                ->addOrderBy($orderBy)
                ->setFirstResult($offset)
                ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->resultMode);

        $paginator = $this->getManager()->createPaginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns shipping methods data
        $shippingMethods = $paginator->getIterator()->getArrayCopy();

        return ['data' => $shippingMethods, 'total' => $totalResult];
    }

    /**
     * Get one shipping method
     *
     * @param $id
     * @return mixed
     * @throws ApiException\NotFoundException
     * @throws ApiException\ParameterMissingException
     * @throws ApiException\PrivilegeException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOne($id)
    {
        $this->checkPrivilege('read');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $builder = $this->getRepository()
            ->createQueryBuilder('shippingMethod')
            ->select('shippingMethod')
            ->where('shippingMethod.id =?1')
            ->setParameter(1, $id);

        $shippingMethod = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$shippingMethod) {
            throw new ApiException\NotFoundException("Shipping method by id $id not found");
        }

        return $shippingMethod;
    }

}