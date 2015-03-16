<?php
class Pas_Responsys_Helper_Product extends Pas_Responsys_Helper_Abstract
{
    const XML_PATH_RESPONSYS_PRODUCTS_TABLEFOLDER    = 'responsys/products/table_folder';
    const XML_PATH_RESPONSYS_PRODUCTS_TABLE          = 'responsys/products/table';

    /**
     * Attribute used to load product collection.
     */
    const DEFAULT_ATTR_SYNC = 'responsys_sync';

    public function getTableFolder()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_PRODUCTS_TABLEFOLDER);
    }

    public function getTable()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_PRODUCTS_TABLE);
    }

    public function getSyncAttribute()
    {
        return self::DEFAULT_ATTR_SYNC;
    }

    public function getResponsysKey()
    {
        return 'PRODUCT_ID';
    }

    public function getMagentoKey()
    {
        return 'sku';
    }

    /**
     * Gets list of Responsys product fields and their Magento equivalents.
     *
     * @return array
     */
    public function getResponsysMapping()
    {
        $responsysKey   = $this->getResponsysKey();
        $magentoKey     = $this->getMagentoKey();

        return array(
            $responsysKey       => $magentoKey,
            'PRODUCT_NAME'      => 'name',
            'PRODUCT_CATEGORY'  => 'category',
            'PRODUCT_PRICE'     => 'price',
            'PRODUCT_DESC'      => 'description',
            'PRODUCT_LINK'      => 'url',
            'PRODUCT_IMAGE'     => 'image'
        );
    }

    public function getResponsysColumns()
    {
        return array_keys($this->getResponsysMapping());
    }

    public function getMagentoColumns()
    {
        return array_values($this->getResponsysMapping());
    }
}
