<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Order\History;
use Shopware\Models\Order\Status;
use Shopware\Components\Api\Exception as ApiException;

class ConnectorOrderStatusesHistory extends Resource
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(History::class);
    }

    /**
     * Get order History Data
     *
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 25, array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('history');

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

        //returns the order history data
        $orderStatuses = $paginator->getIterator()->getArrayCopy();

        return ['data' => $orderStatuses, 'total' => $totalResult];
    }


    /**
     * Get one order status
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
            ->createQueryBuilder('orderStatus')
            ->select('orderStatus')
            ->where('orderStatus.id =?1')
            ->setParameter(1, $id);

        $orderStatus = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$orderStatus) {
            throw new ApiException\NotFoundException("Order status by id $id not found");
        }

        return $orderStatus;
    }

}