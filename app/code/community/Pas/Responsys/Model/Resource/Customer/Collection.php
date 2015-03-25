<?php
class Pas_Responsys_Model_Resource_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    protected $_filter;

    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('responsys/customer');
    }

    public function sync($filter = null, $resetFlag = true)
    {
        // Load default filter
        $filter = is_null($filter) ? Mage::helper('responsys/customer')->getSyncAttribute() : $filter;
        $folder = Mage::helper('responsys/customer')->getListFolder();
        $list   = Mage::helper('responsys/customer')->getList();

        $this->_setFilter($filter)->load();

        // Collect all required customer data.
        $records = array();
        foreach($this as $key => $customer) {
            $data = $customer->toArray(
                Mage::helper('responsys/customer')->getMagentoColumns()
            );
            $data = array_combine(
                Mage::helper('responsys/customer')->getResponsysColumns(),
                $data
            );
            $data = array_merge(
                Mage::helper('responsys/customer')->getResponsysDefaults(),
                $data
            );
            $records[$key] = $data;
        }

        Mage::getModel('responsys/api')->mergeList($folder, $list, $records);
        $this->_syncProfileExtensions();

        // If you want to reset the sync flag.
        if($resetFlag && $this->_getFilter()) {
            Mage::helper('responsys/customer')->setFlags($this, $this->_getFilter(), false);
        }

        return $this;
    }

    public function event($event, $filter = null, $resetFlag = true)
    {
        $filter = $filter ? $filter : Mage::helper('responsys/customer')->getWelcomeAttribute();
        $folder = Mage::helper('responsys/customer')->getEventListFolder($event);
        $list   = Mage::helper('responsys/customer')->getEventList($event);
        $event  = Mage::helper('responsys/customer')->getEventName($event);

        $this->_setFilter($filter)->load();

        // Collect all required customer data.
        $records = array();
        foreach($this as $key => $customer) {
            if ($customer->hasEmail()) {
                $records[$key] = $customer->getEmail();
            }
        }

        Mage::getModel('responsys/api')->triggerEvent($folder, $list, $event, $records);

        // If you want to reset the sync flag.
        if($resetFlag) {
            Mage::helper('responsys/customer')->setFlags($this, $this->_getFilter(), false);
        }

        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        $columns    = Mage::helper('responsys/customer')->getMagentoColumns();
        $primaryKey = Mage::helper('responsys/customer')->getMagentoKey();

        $this->addAttributeToSelect($columns)
            ->addAttributeToFilter($primaryKey, array('notnull' => true));

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

    /**
     * Sync profile extension data.
     *
     * Todo: Move to separate module.
     *
     * @return $this
     */
    protected function _syncProfileExtensions()
    {
        $folder     = Mage::helper('responsys/customer')->getExtensionFolder();
        $extension  = Mage::helper('responsys/customer')->getExtension();

        $responsysKey   = Mage::helper('responsys/customer')->getResponsysKey();
        $magentoKey     = Mage::helper('responsys/customer')->getMagentoKey();

        $records = array();
        foreach($this as $key => $customer) {
            $records[$key] = array(
                $responsysKey => $customer->getData($magentoKey),
                'RESET_URL' => $this->_getCustomerActivationLink($customer)
            );
        }

        Mage::getModel('responsys/api')->mergeExtension($folder, $extension, $records);

        return $this;
    }

    /**
     * Generates and returns a customer activation link.
     *
     * Todo: Move to separate module.
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return string
     */
    protected function _getCustomerActivationLink(Mage_Customer_Model_Customer $customer)
    {
        $attributeCheck = 'ali_welcome_email';

        $url = '';
        if($customer->getData($attributeCheck) == true) {
            $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
            $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);

            $url = Mage::getUrl('loyalty/account/setpassword', array(
                '_type' => Mage_Core_Model_Store::URL_TYPE_WEB,
                '_query' => array(
                    'id' => $customer->getId(),
                    'token' => $newResetPasswordLinkToken
                )
            ));
        }

        return $url;
    }
}
