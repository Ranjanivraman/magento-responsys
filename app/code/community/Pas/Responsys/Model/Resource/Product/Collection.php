<?php
class Pas_Responsys_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    protected $_filter;

    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('responsys/product');
    }

    public function sync($filter = null, $resetFlag = true)
    {
	    // Load default filter
        $filter = is_null($filter) ? Mage::helper('responsys/product')->getSyncAttribute() : $filter;
        $folder = Mage::helper('responsys/product')->getTableFolder();
        $table  = Mage::helper('responsys/product')->getTable();

        $this->_setFilter($filter)->load();

        // Load extra values.
        $this->_loadCategories()
            ->_loadLinks()
            ->_loadImages();

        // Collect all required product data.
        $records = array();
        foreach($this as $key => $product) {
            // Remove stock as its forced into the toArray() array.
            $product->unsetData('stock_item');

            $data = $product->toArray(
                Mage::helper('responsys/product')->getMagentoColumns()
            );
            $data = array_combine(
                Mage::helper('responsys/product')->getResponsysColumns(),
                $data
            );
            $records[$key] = $data;
        }

        Mage::getModel('responsys/api')->mergeTable($folder, $table, $records);

        // If you want to reset the sync flag.
        if($resetFlag && $this->_getFilter()) {
            Mage::helper('responsys/product')->setFlags($this, $this->_getFilter(), false);
        }

        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        $columns    = Mage::helper('responsys/product')->getMagentoColumns();
        $primaryKey = Mage::helper('responsys/product')->getMagentoKey();
        $enabled    = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        $visible    = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;

        $this->addAttributeToSelect($columns)
            ->addAttributeToFilter($primaryKey, array('notnull' => true))
            ->addAttributeToFilter('status', array('eq' => $enabled))
            ->addAttributeToFilter('visibility', array('neq' => $visible));

        // Sync filter
        if($filter = $this->_getFilter()) {
            $this->addAttributeToFilter($filter, array('eq' => true));
        }

        return parent::load($printQuery, $logQuery);
    }

    public function _setFilter($filter)
    {
        $this->_filter = $filter;

        return $this;
    }

    public function _getFilter()
    {
        if($this->_filter) {
            return $this->_filter;
        }

        return false;
    }

    protected function _loadCategories()
    {
        foreach($this as $product) {
            /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
            $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                ->addNameToResult()
                ->addAttributeToSelect(array('level', 'children_count'))
                ->addIdFilter($product->getCategoryIds())
                ->addOrderField('level')
                ->addOrderField('children_count');

            $category = $categoryCollection->getLastItem();
            $product->setCategory($category->getName());
        }

        return $this;
    }

    protected function _loadLinks()
    {
        // Set to default store. Fix bug with product URL when running shell script.
        Mage::app()->setCurrentStore(Mage::app()->getDefaultStoreView()->getCode());
        foreach($this as $product) {
            $product->setUrl($product->getProductUrl());
        }

        return $this;
    }

    protected function _loadImages()
    {
        foreach($this as $product) {
            $image = Mage::helper('catalog/image')->init($product, 'small_image')->resize(260, 347);
            $product->setSmallImage((string)$image);
        }

        return $this;
    }
}
