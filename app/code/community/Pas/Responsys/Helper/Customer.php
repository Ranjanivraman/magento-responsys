<?php
class Pas_Responsys_Helper_Customer extends Pas_Responsys_Helper_Abstract
{
    /**
     * Customer config values.
     */
    const XML_PATH_RESPONSYS_CUSTOMERS_LISTFOLDER       = 'responsys/customers/list_folder';
    const XML_PATH_RESPONSYS_CUSTOMERS_LIST             = 'responsys/customers/list';
    const XML_PATH_RESPONSYS_CUSTOMERS_EXTENSIONFOLDER  = 'responsys/customers/extension_folder';
    const XML_PATH_RESPONSYS_CUSTOMERS_EXTENSION        = 'responsys/customers/extension';

    /**
     * Event config values.
     */
    const XML_PATH_RESPONSYS_EVENTS_LISTFOLDER  = 'responsys/events/list_folder_';
    const XML_PATH_RESPONSYS_EVENTS_LIST        = 'responsys/events/list_';
    const XML_PATH_RESPONSYS_EVENTS_NAME        = 'responsys/events/name_';

    /**
     * Attributes used to load product collection.
     */
    const DEFAULT_ATTR_SYNC     = 'responsys_sync';
    const DEFAULT_ATTR_WELCOME  = 'responsys_welcome';


    public function getListFolder()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_CUSTOMERS_LISTFOLDER);
    }

    public function getList()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_CUSTOMERS_LIST);
    }

    public function getExtensionFolder()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_CUSTOMERS_EXTENSIONFOLDER);
    }

    public function getExtension()
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_CUSTOMERS_EXTENSION);
    }

    public function getEventListFolder($event)
    {

        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_EVENTS_LISTFOLDER . strtolower($event));
    }

    public function getEventList($event)
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_EVENTS_LIST . strtolower($event));
    }

    public function getEventName($event)
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSYS_EVENTS_NAME . strtolower($event));
    }

    public function getSyncAttribute()
    {
        return self::DEFAULT_ATTR_SYNC;
    }

    public function getWelcomeAttribute()
    {
        return self::DEFAULT_ATTR_WELCOME;
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
     * Gets list of Responsys customer fields and their Magento equivalents.
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
            'LAST_NAME'         => 'lastname',
            'SIGNUP_CC'         => 'country_code'
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
