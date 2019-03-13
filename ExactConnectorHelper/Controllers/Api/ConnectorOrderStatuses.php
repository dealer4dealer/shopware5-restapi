<?php

/**
 * Class Shopware_Controllers_Api_ConnectorOrderStatuses
 */
class Shopware_Controllers_Api_ConnectorOrderStatuses extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ConnectorOrderStatuses
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ConnectorOrderStatuses');
    }

    /**
     * GET Request on /api/ConnectorOrderStatuses
     */
    public function indexAction()
    {
        $filter = $this->Request()->getParam('filter', []);
        $sort = $this->Request()->getParam('sort', []);

        $result = $this->resource->getList($filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    /**
     * Get one order status
     *
     * GET /api/ConnectorOrderStatuses/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $orderStatus = $this->resource->getOne($id);

        $this->View()->assign(['data' => $orderStatus, 'success' => true]);
    }

}