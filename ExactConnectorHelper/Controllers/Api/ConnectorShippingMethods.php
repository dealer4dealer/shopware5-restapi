<?php

/**
 * Class Shopware_Controllers_Api_ConnectorShippingMethods
 */
class Shopware_Controllers_Api_ConnectorShippingMethods extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ConnectorShippingMethods
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ConnectorShippingMethods');
    }

    /**
     * GET Request on /api/ConnectorShippingMethods
     */
    public function indexAction()
    {
        $filter = $this->Request()->getParam('filter', []);
        $sort = $this->Request()->getParam('sort', []);

        $result = $this->resource->getList($filter, $sort);

        $this->View()->assign(['success' => true, 'data' => $result]);
    }

    /**
     * Get one shipping method
     *
     * GET /api/ConnectorShippingMethods/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $shippingMethod = $this->resource->getOne($id);

        $this->View()->assign(['success' => true, 'data' => $shippingMethod]);
    }

}