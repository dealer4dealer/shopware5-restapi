<?php

/**
 * Class Shopware_Controllers_Api_ConnectorVatCodes
 */
class Shopware_Controllers_Api_ConnectorVatCodes extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ConnectorVatCodes
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ConnectorVatCodes');
    }

    /**
     * GET Request on /api/ConnectorVatCodes
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
     * Get one vat code
     *
     * GET /api/ConnectorVatCodes/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $vatCode = $this->resource->getOne($id);
        $this->View()->assign(['data' => $vatCode, 'success' => true]);
    }

}