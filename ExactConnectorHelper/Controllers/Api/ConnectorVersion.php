<?php

/**
* Class Shopware_Controllers_Api_ConnectorVersion
*/
class Shopware_Controllers_Api_ConnectorVersion extends Shopware_Controllers_Api_Rest
{
    /**
     * GET Request on /api/ConnectorVersion
     */
    public function indexAction()
    {
        $pluginVersion = $this->container->get('shopware.plugin_manager')->getPluginByName('ExactConnectorHelper')->getVersion();

        $this->View()->assign([
            'data' => [
                'pluginVersion'=> $pluginVersion
            ],
            'success' => true
        ]);
    }

}