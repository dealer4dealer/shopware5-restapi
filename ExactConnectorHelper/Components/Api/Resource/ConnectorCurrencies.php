<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Shop\Currency as Currency;
use Shopware\Components\Api\Exception as ApiException;

class ConnectorCurrencies extends Resource
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(Currency::class);
    }

    /**
     * Get Currencies
     *
     * @param int   $offset
     * @param int   $limit
     * @param array $criteria
     * @param array $orderBy
     *
     * @return array
     */
    public function getList($offset = 0, $limit = 25, array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('currency');

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

        //returns the vat codes data
        $vatCodes = $paginator->getIterator()->getArrayCopy();

        return ['data' => $vatCodes, 'total' => $totalResult];
    }


    /**
     * Get one currency
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
            ->createQueryBuilder('currency')
            ->select('currency')
            ->where('currency.id =?1')
            ->setParameter(1, $id);

        $vatCode = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$vatCode) {
            throw new ApiException\NotFoundException("currency by id $id not found");
        }

        return $vatCode;
    }

}