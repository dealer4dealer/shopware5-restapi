<?php

/**
 * Class Shopware_Controllers_Api_ConnectorProductAttributesOptions
 */
class Shopware_Controllers_Api_ConnectorProductAttributesOptions extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ConnectorProductAttributesOptions
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ConnectorProductAttributesOptions');
    }

    /**
     * GET Request on /api/ConnectorProductAttributesOptions
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
     * Get one option value
     *
     * GET /api/ConnectorProductAttributesOptions/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $optionValue = $this->resource->getOne($id);

        $this->View()->assign(['data' => $optionValue, 'success' => true]);
    }

    /**
     * Create new Attribute option
     *
     * POST /api/ConnectorProductAttributesOptions
     */
    public function postAction()
    {
        $value = $this->resource->create($this->Request()->getPost());

        $location = $this->apiBaseUrl . 'ConnectorProductAttributesOptions/' . $value->getId();

        $data = [
            'id' => $value->getId(),
            'location' => $location,
        ];
        $this->View()->assign(['data' => $data, 'success' => true]);
        $this->Response()->setHeader('Location', $location);
    }

}