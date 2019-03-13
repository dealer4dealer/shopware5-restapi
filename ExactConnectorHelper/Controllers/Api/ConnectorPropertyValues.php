<?php

/**
 * Class Shopware_Controllers_Api_ConnectorPropertyValues
 */
class Shopware_Controllers_Api_ConnectorPropertyValues extends Shopware_Controllers_Api_Rest
{

    /**
     * @var Shopware\Components\Api\Resource\ConnectorPropertyValues
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource("ConnectorPropertyValues");
    }

    /**
     * GET Request on /api/ConnectorPropertyValues
     */
    public function indexAction()
    {
        $attribute = $this->Request()->getParam('attribute');
        $group = $this->Request()->getParam('group');
        $property = $this->Request()->getParam('property');

        $result = $this->resource->getPropertyIds($attribute, $group, $property);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

}