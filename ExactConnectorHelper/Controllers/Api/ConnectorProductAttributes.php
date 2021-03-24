<?php

/**
 * Class Shopware_Controllers_Api_ConnectorProductAttributes
 */
class Shopware_Controllers_Api_ConnectorProductAttributes extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ConnectorProductAttributes
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ConnectorProductAttributes');
    }

    /**
     * GET Request on /api/ConnectorProductAttributes
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
     * Get one attribute group
     *
     * GET /api/ConnectorProductAttributes/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        $option = $this->Request()->getParam('options');

        if ($option) {
            $productAttributes = $this->resource->getOneIncludeOptions($id, $this->container);
        }
        else {
            $productAttributes = $this->resource->getOne($id);
        }

        $this->View()->assign(['data' => $productAttributes, 'success' => true]);
    }

}