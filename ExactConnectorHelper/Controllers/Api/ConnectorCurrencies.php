<?php

/**
 * Class Shopware_Controllers_Api_ConnectorVatCodes
 */
class Shopware_Controllers_Api_ConnectorCurrencies extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ConnectorCurrencies
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ConnectorCurrencies');
    }

    /**
     * GET Request on /api/ConnectorCurrencies
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
     * GET /api/ConnectorCurrencies/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $vatCode = $this->resource->getOne($id);
        $this->View()->assign(['data' => $vatCode, 'success' => true]);
    }

}