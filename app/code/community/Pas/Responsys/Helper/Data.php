<?php
class Pas_Responsys_Helper_Data extends Pas_Responsys_Helper_Abstract
{
    const XML_PATH_RESPONSYS_GENERAL_ISENABLED      = 'responsys/general/is_enabled';
    const XML_PATH_RESPONSYS_GENERAL_APIUSERNAME    = 'responsys/general/api_username';
    const XML_PATH_RESPONSYS_GENERAL_APIPASSWORD    = 'responsys/general/api_password';

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
}
