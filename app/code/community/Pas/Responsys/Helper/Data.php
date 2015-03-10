<?php
class Pas_Responsys_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_RESPONSYS_GENERAL_ISENABLED      = 'responsys/general/is_enabled';
    const XML_PATH_RESPONSYS_GENERAL_APIUSERNAME    = 'responsys/general/api_username';
    const XML_PATH_RESPONSYS_GENERAL_APIPASSWORD    = 'responsys/general/api_password';

    protected $_interact = array(
        Pas_Responsys_Model_Api::INTERACT_MEMBER    => 'member',
        Pas_Responsys_Model_Api::INTERACT_URL       => 'url',
        Pas_Responsys_Model_Api::INTERACT_WELCOME   => 'welcome'
    );

    const XML_PATH_RESPONSYS_INTERACT_FOLDER    = 'responsys/interact/folder_';
    const XML_PATH_RESPONSYS_INTERACT_OBJECT    = 'responsys/interact/object_';

    const XML_PATH_RESPONSYS_EVENT_WELCOMEONLINE    = 'responsys/event/welcome_online';
    const XML_PATH_RESPONSYS_EVENT_WELCOMEINSTORE   = 'responsys/event/welcome_instore';

    const ATTR_SYNC     = 'responsys_sync';
    const ATTR_WELCOME  = 'responsys_welcome';

    /**
     * Log Responsys message.
     *
     * @param string $message
     * @return $this
     */
    public function log($message)
    {
        Mage::log($message, null, 'responsys.log');
        return $this;
    }

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_GENERAL_ISENABLED);
    }

    public function getUsername()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_GENERAL_APIUSERNAME);
    }

    public function getPassword()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_GENERAL_APIPASSWORD);
    }

    public function getInteractFolder($type)
    {
        if (isset($this->_interact[$type])) {
            return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_INTERACT_FOLDER . $this->_interact[$type]);
        }
        return false;
    }

    public function getInteractObject($type)
    {
        if (isset($this->_interact[$type])) {
            return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_INTERACT_OBJECT . $this->_interact[$type]);
        }
        return false;
    }

    public function getWelcomeOnlineEvent()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_EVENT_WELCOMEONLINE);
    }

    public function getWelcomeInStoreEvent()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_EVENT_WELCOMEINSTORE);
    }

    public function getSyncAttribute()
    {
        return self::ATTR_SYNC;
    }

    public function getWelcomeAttribute()
    {
        return self::ATTR_WELCOME;
    }

    public function getResponsysKey()
    {
        return 'CUSTOMER_ID_';
    }

    public function getMagentoKey()
    {
        return 'apparel21_person_id';
    }

    /**
     * Gets list of Responsys fields and their Magento equivalents.
     *
     * @return array
     */
    public function getResponsysMapping()
    {
        $responsysKey   = $this->getResponsysKey();
        $magentoKey     = $this->getMagentoKey();

        return array(
            $responsysKey       => $magentoKey,
            'EMAIL_ADDRESS_'    => 'email',
            'FIRST_NAME'        => 'firstname',
            'LAST_NAME'         => 'lastname'
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

    /**
     * Gets list of Responsys member defaults.
     *
     * @return array
     */
    public function getResponsysDefaults()
    {
        return array(
            'EMAIL_PERMISSION_STATUS_' => 'I'
        );
    }
}
