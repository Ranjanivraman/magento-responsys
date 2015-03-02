<?php
class Pas_Responsys_Model_Api extends Mage_Core_Model_Abstract
{
    /**
     * API call types
     */
    const INTERACT_MEMBER   = 1;
    const INTERACT_URL      = 2;
    const INTERACT_WELCOME  = 3;

    /**
     * @var Responsys_API
     */
    protected $_client;

    /**
     * @var Pas_Responsys_Helper_Data
     */
    protected $_helper;

    /**
     * Array of customer collections based on filter attribute.
     *
     * @var array
     */
    protected $_customers = array();

    protected function _construct()
    {
        $username = $this->getHelper()->getUsername();
        $password = $this->getHelper()->getPassword();

        $this->_client = new Responsys_API($username, $password);
    }

    public function syncCustomers($attribute = null, $resetFlag = true)
    {
	    // Get default sync attribute code if non given.
        if (!$attribute) {
            $attribute = $this->getHelper()->getSyncAttribute();
        }

	    /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = $this->getCustomerCollection($attribute);

	    // Build data array to be sent to responsys.
        $records = array();
        foreach ($collection as $customer) {
            $data = $customer->toArray(
                $this->getHelper()->getMagentoColumns()
            );
            $data = array_combine(
                $this->getHelper()->getResponsysColumns(),
                $data
            );
            $data = array_merge(
                $this->getHelper()->getResponsysDefaults(),
                $data
            );
            $records[] = $data;
        }

	    // Don't execute API call if nothing to send.
	    if(!count($records)) return $this;

        try {
            $this->_client->mergeListMembers(
                $this->getHelper()->getInteractFolder(self::INTERACT_MEMBER),
                $this->getHelper()->getInteractObject(self::INTERACT_MEMBER),
                $records,
                true,
                RESPONSYS_REPLACE_ALL,
                $this->getHelper()->getResponsysKey()
            );
            $this->_syncProfileExtensions($collection);
        }
        catch (Exception $e) {
            $this->getHelper()->log($e->getMessage());
            return $this;
        }

        if ($resetFlag) {
            $this->setFlags($collection, $attribute, false);
        }

        return $this;
    }

    public function sendWelcome($attribute = null, $resetFlag = true, $online = true)
    {
	    // Get default welcome attribute code if non given.
        if (!$attribute) {
            $attribute = $this->getHelper()->getWelcomeAttribute();
        }

	    /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = $this->getCustomerCollection($attribute);

	    // Build data array to be sent to responsys.
        $emails = array();
        foreach ($collection as $customer) {
            if ($customer->hasEmail()) {
                $emails[] = $customer->getEmail();
            }
        }

	    // Don't execute API call if nothing to send.
	    if(!count($emails)) return $this;

	    // Todo: Move in store event to separate module.
	    $event = $online ? $this->getHelper()->getWelcomeOnlineEvent() : $this->getHelper()->getWelcomeInStoreEvent();

        try {
            $this->_client->triggerCustomEvent(
                $this->getHelper()->getInteractFolder(self::INTERACT_WELCOME),
                $this->getHelper()->getInteractObject(self::INTERACT_WELCOME),
                $event,
                $emails
            );
        }
        catch (Exception $e) {
            $this->getHelper()->log($e->getMessage());
            return $this;
        }

        if ($resetFlag) {
            $this->setFlags($collection, $attribute, false);
        }

        return $this;
    }

    /**
     * @return Pas_Responsys_Helper_Data
     */
    public function getHelper()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('responsys');
        }
        return $this->_helper;
    }

    /**
     * Return customer collection filtered on primary key.
     *
     * @param string $filter
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function getCustomerCollection($filter)
    {
        if (!isset($this->_customers[$filter])) {
            $key = $this->getHelper()->getMagentoKey();
            $columns = $this->getHelper()->getMagentoColumns();

            $this->_customers[$filter] = Mage::getResourceModel('customer/customer_collection')
                ->addAttributeToFilter($filter, array('eq' => true))
                ->addAttributeToFilter($key, array('notnull' => true))
                ->addAttributeToSelect($columns)
                ->load();
        }

        return $this->_customers[$filter];
    }

    /**
     * Set flag for given attribute filter.
     *
     * @param Varien_Data_Collection $collection
     * @param string $attribute
     * @param bool $value
     * @return Pas_Responsys_Model_Api
     */
    public function setFlags(Varien_Data_Collection $collection, $attribute, $value = true)
    {
        foreach($collection as $customer) {
            $customer->setData($attribute, $value);
            $customer->_getResource()->saveAttribute($customer, $attribute);
        }

        return $this;
    }

    /**
     * Sync profile extension data for a collections of customers.
     *
     * Todo: Move to separate module.
     *
     * @param Varien_Data_Collection $collection
     * @return Pas_Responsys_Model_Api
     */
    protected function _syncProfileExtensions(Varien_Data_Collection $collection)
    {
	    $responsysKey   = $this->getHelper()->getResponsysKey();
	    $magentoKey     = $this->getHelper()->getMagentoKey();

        $urls = array();
        foreach ($collection as  $customer) {
            $urls[] = array(
	            $responsysKey => $customer->getData($magentoKey),
                'RESET_URL' => $this->_getCustomerActivationLink($customer)
            );
        }

        $this->_client->mergeIntoProfileExtension(
            $this->getHelper()->getInteractFolder(self::INTERACT_URL),
            $this->getHelper()->getInteractObject(self::INTERACT_URL),
            $urls
        );

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
        if ($customer->getData($attributeCheck) == true) {
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