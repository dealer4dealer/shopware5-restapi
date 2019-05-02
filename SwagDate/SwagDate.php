<?php

namespace SwagDate;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Article\Article;

class SwagDate extends Plugin
{
    public function install(InstallContext $context)
    {
        $attributeService = $this->container->get('shopware_attribute.crud_service');

        $attributeService->update(
            's_articles_attributes',
            'xcore_date',
            'datetime',
            [
                'translatable'     => true,
                'displayInBackend' => false,
                'custom'           => false,
            ],
            null, true
        );

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function uninstall(UninstallContext $context)
    {
        if ($context->keepUserData()) {
            return;
        }

        $attributeService = $this->container->get('shopware_attribute.crud_service');
        $attributeExists  = $attributeService->get('s_articles_attributes', 'xcore_date');

        if ($attributeExists) {
            $attributeService->delete(
                's_articles_attributes',
                'xcore_date'
            );
        }

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public static function getSubscribedEvents()
    {
        return [

            'Enlight_Controller_Action_PostDispatch_Backend_Article' => 'setDate',
        ];
    }

    public function setDate(\Enlight_Controller_EventArgs $args)
    {
        if ($args->getRequest()->has('id')) {
            $repostory = Shopware()->Models()->getRepository(Article::class);
            $id        = $args->getRequest()->getParam('id');

            $product = $repostory->find($id);
            $details = $product->getDetails();

            foreach ($details as $detail) {
                $detailID = $detail->getId();
                $now      = date('Y-m-d H:i:s');

                $sqlQuery =
                    'UPDATE `s_articles_attributes` 
                     SET `xcore_date` = ' . '"' . $now . '"' .
                    'WHERE `articledetailsID` = ' . $detailID;

                Shopware()->Db()->executeQuery($sqlQuery);
            }
        }
    }
}