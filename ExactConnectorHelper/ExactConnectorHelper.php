<?php

namespace ExactConnectorHelper;

use Enlight_Controller_ActionEventArgs;
use Enlight_Hook_HookArgs;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Article\Article;
use Shopware\Models\Article\Detail;

class ExactConnectorHelper extends Plugin
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

        $this->setDateOnInstall();
        $this->generateEntity();
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

        $this->generateEntity();
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public static function getSubscribedEvents()
    {
        return [

            'Enlight_Controller_Action_PostDispatch_Backend_Article'                        => 'setDate',
            'Shopware_Controllers_Backend_Article::saveAction::after'                       => 'setDateWhenSaveProduct',
            'Shopware_Controllers_Backend_Article::createConfiguratorVariantsAction::after' => 'setDateWhenSaveProduct',
            'Shopware_Controllers_Backend_Article::saveDetailAction::after'                 => 'setDateWhenUpdateVariant',
        ];
    }

    public function setDate(Enlight_Controller_ActionEventArgs $args)
    {
        if ($args->getRequest()->getParam('id')) {
            $this->setDateWhenUpdateProduct($args);
        }
    }

    public function setDateWhenSaveProduct()
    {
        $now      = date('Y-m-d H:i:s');
        $sqlQuery =
            "UPDATE `s_articles_attributes`
                        SET `xcore_date` =  " . "'" . $now . "'" .
            "WHERE `xcore_date` IS NULL ";

        Shopware()->Db()->executeQuery($sqlQuery);
    }

    private function setDateOnInstall()
    {
        //get all the products' ids from s_articles.
        $sql = "SELECT `id` FROM `s_articles`";
        $ids = Shopware()->Db()->executeQuery($sql);

        foreach ($ids as $id) {
            //for each product's id get both last changed date, and the Product's details.
            explode(", ", $id);
            $repostory  = Shopware()->Models()->getRepository(Article::class);
            $product    = $repostory->find($id);
            $details    = $product->getDetails();
            $changeDate = $product->getChanged()->getTimestamp();
            //convert the date.
            $date = date("Y-m-d H:i:s", $changeDate);
            foreach ($details as $detail) {
                $detailID = $detail->getId();
                $sqlQuery =
                    "UPDATE `s_articles_attributes`
                        SET `xcore_date` =  " . "'" . $date . "'" .
                    "WHERE `articledetailsID` = " . $detailID .
                    " AND  `xcore_date` IS NULL";
                Shopware()->Db()->executeQuery($sqlQuery);
            }
        }
    }

    private function generateEntity()
    {
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
    }

    private function setDateWhenUpdateProduct(Enlight_Controller_ActionEventArgs $args)
    {
        $repostory = Shopware()->Models()->getRepository(Article::class);
        $id        = $args->getRequest()->getParam('id');
        $now       = date('Y-m-d H:i:s');

        $product = $repostory->find($id);
        $details = $product->getDetails();

        foreach ($details as $detail) {
            $detailID = $detail->getId();
            $sqlQuery =
                "UPDATE `s_articles_attributes`
                        SET `xcore_date` =  " . "'" . $now . "'" .
                "WHERE `articledetailsID` = " . $detailID;

            Shopware()->Db()->executeQuery($sqlQuery);
        }
    }

    public function setDateWhenUpdateVariant(Enlight_Hook_HookArgs $args)
    {
        $args      = $args->getArgs();
        $repostory = Shopware()->Models()->getRepository(Detail::class);

        if (isset($args['id']) && $id = $args['id']) {
            $detail   = $repostory->find($id);
            $now      = date('Y-m-d H:i:s');
            $detailId = $detail->getId();
            $sqlQuery =
                "UPDATE `s_articles_attributes`
                        SET `xcore_date` =  " . "'" . $now . "'" .
                "WHERE `articledetailsID` = " . $detailId;

            Shopware()->Db()->executeQuery($sqlQuery);
        }
    }
}