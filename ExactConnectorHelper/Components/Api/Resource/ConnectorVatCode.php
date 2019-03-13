<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Tax\Tax as Tax;
use Shopware\Components\Api\Exception as ApiException;

class ConnectorVatCode extends Resource
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(Tax::class);
    }

    /**
     * Get vat codes
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList(array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('vat');

        // Create builder on requested filter and sort
        $builder->addFilter($criteria)
            ->addOrderBy($orderBy);

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
     * Get one vat code
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
            ->createQueryBuilder('vatCode')
            ->select('vatCode')
            ->where('vatCode.id =?1')
            ->setParameter(1, $id);

        $vatCode = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$vatCode) {
            throw new ApiException\NotFoundException("Vat code by id $id not found");
        }

        return $vatCode;
    }

}