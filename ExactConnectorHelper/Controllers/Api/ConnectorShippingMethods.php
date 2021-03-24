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
        $limit  = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $filter = $this->Request()->getParam('filter', []);
        $sort   = $this->Request()->getParam('sort', []);

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
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

        $this->View()->assign(['data' => $shippingMethod, 'success' => true]);
    }

}