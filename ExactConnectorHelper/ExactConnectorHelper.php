<?php

namespace ExactConnectorHelper;

use Enlight_Controller_ActionEventArgs;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Article\Article;

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
            null,
            true
        );

        $this->generateEntity();
        $this->setDateOnInstall();
    }

    public function uninstall(UninstallContext $context)
    {
        if ($context->keepUserData()) {
            return;
        }

        $attributeService = $this->container->get('shopware_attribute.crud_service');

        if ($attributeExists = $attributeService->get('s_articles_attributes', 'xcore_date')) {
            $attributeService->delete(
                's_articles_attributes',
                'xcore_date'
            );
        }

        if ($translationExists = $attributeService->get('s_articles_translations', 'xcore_date')) {
            $attributeService->delete(
                's_articles_translations',
                'xcore_date'
            );
        }

        if ($configuratorExists = $attributeService->get('s_article_configurator_templates_attributes', 'xcore_date')) {
            $attributeService->delete(
                's_article_configurator_templates_attributes',
                'xcore_date'
            );
        }

        $this->generateEntity();
    }

    public static function getSubscribedEvents()
    {
        return [

            'Enlight_Controller_Action_PostDispatch_Backend_Article'                        => 'setDate',
            'Shopware_Controllers_Backend_Article::saveAction::after'                       => 'setDateWhenSaveProduct',
            'Shopware_Controllers_Backend_Article::createConfiguratorVariantsAction::after' => 'setDateWhenSaveProduct',
        ];
    }

    public function setDate(Enlight_Controller_ActionEventArgs $args)
    {
        if ($args->getRequest()->getParam('id')) {
            $this->setDateOnProduct($args);
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
            $repository = Shopware()->Models()->getRepository(Article::class);
            $product    = $repository->find($id);
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

    private function setDateOnProduct(Enlight_Controller_ActionEventArgs $args)
    {
        $argsId      = $args->getRequest()->getParam('id');
        $now         = date('Y-m-d H:i:s');
        $requestBody = json_decode($args->getRequest()->getRawBody());

        //situation when updating product that have no variant.
        $mainDetailId = $requestBody->mainDetailId ?: $requestBody->id;
        if (($mainDetailId === $argsId)) {
            $sqlQuery =
                "UPDATE `s_articles_attributes`
                        SET `xcore_date` =  " . "'" . $now . "'" .
                "WHERE `articledetailsID` = " . $mainDetailId;
            Shopware()->Db()->executeQuery($sqlQuery);
            Shopware()->Container()->get('pluginlogger')->info(
                sprintf('the xcore_date for product with detail id: %s  has been set to %s', $mainDetailId, $now)
            );
        } else {
            //situation when updating variant from articles overview.
            $repository = Shopware()->Models()->getRepository(Article::class);
            $product    = $repository->find($argsId);
            if ($product) {
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
        }
    }
}