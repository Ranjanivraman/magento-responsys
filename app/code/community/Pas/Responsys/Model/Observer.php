<?php
class Pas_Responsys_Model_Observer
{
    /**
     * Run customer sync.
     *
     * @return Pas_Responsys_Model_Observer
     */
    public function runCustomerCron()
    {
        // Don't run if module disabled.
        if (!Mage::helper('responsys')->isEnabled()) {
            return $this;
        }

        Mage::getResourceModel('responsys/customer_collection')
            ->sync()
            ->event('welcome');

        return $this;
    }

    /**
     * Run product sync.
     *
     * @return Pas_Responsys_Model_Observer
     */
    public function runProductCron()
    {
        // Don't run if module disabled.
        if (!Mage::helper('responsys')->isEnabled()) {
            return $this;
        }

        Mage::getResourceModel('responsys/product_collection')
            ->sync();

        return $this;
    }

    /**
     * responsys_new_customer
     *
     * @param Varien_Event_Observer $observer
     * @return Pas_Responsys_Model_Observer
     */
    public function flagCustomerSync(Varien_Event_Observer $observer)
    {
        $customer = $observer->getCustomer();
        $attributeSync = Mage::helper('responsys/customer')->getSyncAttribute();
        $attributeWelcome = Mage::helper('responsys/customer')->getWelcomeAttribute();
        Mage::helper('responsys/customer')
            ->setFlag($customer, $attributeSync)
            ->setFlag($customer, $attributeWelcome);

        // Don't run if module disabled.
        if (!Mage::helper('responsys')->isEnabled()) {
            return $this;
        }

        // Instant sync of customer data instead of waiting for cron.
        Mage::getResourceModel('responsys/customer_collection')
            ->addAttributeToFilter('entity_id', array('eq' => $customer->getId()))
            ->sync()
            ->event('welcome');

        return $this;
    }

    /**
     * catalog_product_save_before
     *
     * @param Varien_Event_Observer $observer
     * @return Pas_Responsys_Model_Observer
     */
    public function flagProductSync(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        $attribute = Mage::helper('responsys/product')->getSyncAttribute();
        Mage::helper('responsys/product')->setFlag($product, $attribute);

        return $this;
    }

    /**
     * customer_import_finish_before
     *
     * Todo: Move to separate module.
     *
     * @param Varien_Event_Observer $observer
     * @return Pas_Responsys_Model_Observer
     */
    public function importFinished(Varien_Event_Observer $observer)
    {
        // Don't run if module disabled.
        if (!Mage::helper('responsys')->isEnabled()) {
            return $this;
        }

        $attribute = $observer->getImportAttribute();
        Mage::getResourceModel('responsys/customer_collection')
            ->sync($attribute, false)
            ->event('instore_welcome', $attribute);

        return $this;
    }
}