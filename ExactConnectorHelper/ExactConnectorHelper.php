<?php

namespace ExactConnectorHelper;

use Shopware\Components\Plugin;

class ExactConnectorHelper extends Plugin
{

    /**
     * Subscribe events
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorVersion'
                => 'onGetConnectorVersionApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorShippingMethods'
                => 'onGetConnectorShippingMethodsApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorOrderStatuses'
                => 'onGetConnectorOrderStatusesApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorProductAttributes'
                => 'onGetConnectorProductAttributesApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorProductAttributesOptions'
            => 'onGetConnectorProductAttributesOptionsApiController'
        ];
    }

    /**
     * @return string
     */
    public function onGetConnectorVersionApiController()
    {
        return $this->getPath() . '/Controllers/Api/ConnectorVersion.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorShippingMethodsApiController()
    {
        return $this->getPath() . '/Controllers/Api/ConnectorShippingMethods.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorOrderStatusesApiController()
    {
        return $this->getPath() . '/Controllers/Api/ConnectorOrderStatuses.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorProductAttributesApiController()
    {
        return $this->getPath() . '/Controllers/Api/ConnectorProductAttributes.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorProductAttributesOptionsApiController()
    {
        return $this->getPath() . '/Controllers/Api/ConnectorProductAttributesOptions.php';
    }

}