<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Property\Value;
use Shopware\Models\Property\Option as Option;
use Shopware\Components\Api\Exception as ApiException;

class ConnectorProductAttributesOptions extends Resource
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository(Value::class);
    }

    /**
     * Get options values list
     * @param array $criteria
     * @param array $orderBy
     * @return mixed
     */
    public function getList(array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('values');

        // Create builder on requested filter and sort
        $builder->addFilter($criteria)
            ->addOrderBy($orderBy);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->resultMode);

        $paginator = $this->getManager()->createPaginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns options values data
        $values = $paginator->getIterator()->getArrayCopy();

        return ['data' => $values, 'total' => $totalResult];
    }

    /**
     * Get one option value
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
            ->createQueryBuilder('values')
            ->select('values')
            ->where('values.id =?1')
            ->setParameter(1, $id);

        $optionValue = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$optionValue) {
            throw new ApiException\NotFoundException("Option value by id $id not found");
        }

        return $optionValue;
    }

    /**
     * Create new Attribute Value
     *
     * @param array $params
     * @return Value
     * @throws ApiException\ValidationException
     * @throws ApiException\OrmException
     */
    public function create(array $params)
    {
        /** @var Value $value */
        $optionRepository = $this->getManager()->getRepository(Option::class);
        $option = $optionRepository->find("3");
        $value = new Value($option);

        $value->fromArray($params);

        $violations = $this->getManager()->validate($value);

        /**
         * Handle Violation Errors
         */
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->getManager()->persist($value);
        $this->flush();

        return $value;
    }

}