<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Property\Option as Option;
use Shopware\Components\Api\Exception as ApiException;


class ConnectorProductAttributes extends Resource
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(Option::class);
    }

    /**
     * Get Product Attributes
     *
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 25, array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('filter');

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

        //returns attribute groups data
        $groups = $paginator->getIterator()->getArrayCopy();

        return ['data' => $groups, 'total' => $totalResult];
    }

    /**
     * Get one attribute group
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
            ->createQueryBuilder('filter')
            ->select('filter')
            ->where('filter.id =?1')
            ->setParameter(1, $id);

        $group = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$group) {
            throw new ApiException\NotFoundException("Attribute group by id $id not found");
        }

        return $group;
    }

    /**
     * Get one attribute group with values
     *
     * @param $id
     * @return mixed
     * @throws ApiException\NotFoundException
     * @throws ApiException\ParameterMissingException
     * @throws ApiException\PrivilegeException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneIncludeOptions($id) {

        $this->checkPrivilege('read');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $optionData = $this->getOne($id);

        $valueData = $this->getRepository()
            ->createQueryBuilder('option')
            ->select('values.id, values.value, values.position, values.mediaId')
            ->join('option.values', 'values')
            ->where('values.optionId = :parameter')
            ->setParameter('parameter', $id)
            ->groupBy('values.id')
            ->getQuery()
            ->getResult();

        $data = array(
            "option" => $optionData,
            "values" => $valueData
        );

        if (!$data) {
            throw new ApiException\NotFoundException("Attribute group by id $id not found");
        }

        return $data;
    }

}