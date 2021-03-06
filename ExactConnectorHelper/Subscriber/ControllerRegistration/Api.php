<?php

namespace ExactConnectorHelper\Subscriber\ControllerRegistration;


use Enlight\Event\SubscriberInterface;

class Api implements SubscriberInterface
{

    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @param string $pluginDirectory
     */
    public function __construct($pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }

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
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorOrderStatusesHistory'
            => 'onGetConnectorOrderStatusesHistoryApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorProductAttributes'
            => 'onGetConnectorProductAttributesApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorProductAttributesOptions'
            => 'onGetConnectorProductAttributesOptionsApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorVatCodes'
            => 'onGetConnectorVatCodesApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorCurrencies'
            => 'onGetConnectorCurrenciesApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorPropertyValues'
            => 'onGetConnectorPropertyValuesApiController',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ConnectorFreeFields'
            => 'onGetConnectorFreeFieldsApiController',
        ];
    }

    /**
     * @return string
     */
    public function onGetConnectorVersionApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorVersion.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorShippingMethodsApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorShippingMethods.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorOrderStatusesApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorOrderStatuses.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorOrderStatusesHistoryApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorOrderStatusesHistory.php';
    }


    /**
     * @return string
     */
    public function onGetConnectorProductAttributesApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorProductAttributes.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorProductAttributesOptionsApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorProductAttributesOptions.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorVatCodesApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorVatCodes.php';
    }
    /**
     * @return string
     */
    public function onGetConnectorCurrenciesApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorCurrencies.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorPropertyValuesApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorPropertyValues.php';
    }

    /**
     * @return string
     */
    public function onGetConnectorFreeFieldsApiController()
    {
        return $this->pluginDirectory . '/Controllers/Api/ConnectorFreeFields.php';
    }

}