<?php

namespace ExactConnectorHelper\Components\Api\Resource;

use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Property\Value;
use Shopware\Models\Property\Option as Option;
use Shopware\Components\Api\Exception as ApiException;

class ConnectorPropertyValues extends Resource
{

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getOptionRepository() {
        return $this->getManager()->getRepository(Option::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getValueRepository() {
        return $this->getManager()->getRepository(Value::class);
    }

    /**
     * Get Shopware id's for the given group name and property value.
     * @param $attributeId
     * @param $groupName
     * @param $propertyValues
     * @return array
     * @throws ApiException\NotFoundException
     * @throws ApiException\OrmException
     * @throws ApiException\ValidationException
     */
    public function getPropertyIds($attributeId, $groupName, $propertyValues) {
        $groupId = $this->getGroupId($groupName);

        if ($groupId == null)
            throw new ApiException\NotFoundException("Group by name $groupName not found");

        $valueArray = explode(',', $propertyValues);
        $propertyIds = array();
        foreach ($valueArray as $propertyValue) {

            $propertyId = $this->getPropertyValues($groupId, $propertyValue);

            /**
             * If property value does not exist yet, create a new value for the given group.
             */
            if ($propertyId == null)
                $propertyId = $this->createPropertyValue($groupId, $propertyValue);

            array_push($propertyIds, $propertyId);
        }


        return ['data' => ["attributeId" => $attributeId, "groupId" => $groupId, "valueId" => $propertyIds]];
    }

    /**
     * Get group id by group name.
     * @param $groupName
     * @return array
     */
    private function getGroupId($groupName) {
        $groupRepository = $this->getOptionRepository();
        $group = $groupRepository->findOneBy(["name" => $groupName]);

        return ($group != null ?  $group->getId() : null);
    }

    /**
     * Get property value by value and optionId.
     * @param $groupId
     * @param $propertyValue
     * @return array
     */
    private function getPropertyValues($groupId, $propertyValue) {
        $valueRepository = $this->getValueRepository();
        $value = $valueRepository->findOneBy(["value" => $propertyValue, "optionId" => $groupId]);

        return ($value != null ?  $value->getId() : null);
    }

    /**
     * Create new property value for specific group.
     * @param $groupId
     * @param $propertyValue
     * @return int
     * @throws ApiException\OrmException
     * @throws ApiException\ValidationException
     */
    private function createPropertyValue($groupId, $propertyValue) {
        $optionRepository = $this->getOptionRepository();
        $option = $optionRepository->find($groupId);

        $value = new Value($option, $propertyValue);

        $violations = $this->getManager()->validate($value);

        /**
         * Handle Violation Errors
         */
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->getManager()->persist($value);
        $this->flush();

        return $value->getId();
    }

}