<?php
class Pas_Responsys_Model_Observer
{
    /**
     * Run defaults.
     *
     * @return Pas_Responsys_Model_Observer
     */
    public function runCron()
    {
        /** @var Pas_Responsys_Model_Api $responsysApi */
        $responsysApi = Mage::getModel('responsys/api');

        // Don't run if module disabled.
        if (!$responsysApi->getHelper()->isEnabled()) {
            return $this;
        }

        $responsysApi
            ->syncCustomers()
            ->sendWelcome();

        return $this;
    }

    /**
     * responsys_new_customer
     *
     * @param Varien_Event_Observer $observer
     * @return Pas_Responsys_Model_Observer
     */
    public function newCustomer(Varien_Event_Observer $observer)
    {
        $customer = $observer->getCustomer();

        /** @var Pas_Responsys_Model_Api $responsysApi */
        $responsysApi = Mage::getModel('responsys/api');

        $attributeSync = $responsysApi->getHelper()->getSyncAttribute();
        $attributeWelcome = $responsysApi->getHelper()->getWelcomeAttribute();

        if($customer instanceof Mage_Customer_Model_Customer) {
            $collection = new Varien_Data_Collection();
            $collection->addItem($customer);
            $responsysApi
                ->setFlags($collection, $attributeSync, true)
                ->setFlags($collection, $attributeWelcome, true);
        }

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
	    /** @var Pas_Responsys_Model_Api $responsysApi */
	    $responsysApi = Mage::getModel('responsys/api');

	    // Don't run if module disabled.
	    if (!$responsysApi->getHelper()->isEnabled()) {
		    return $this;
	    }

        $attributeImport = $observer->getImportAttribute();

        $responsysApi
            ->syncCustomers($attributeImport, false) // Don't reset filter attribute until event fired.
            ->sendWelcome($attributeImport, true, false); // Send in store welcome event.

        return $this;
    }
}