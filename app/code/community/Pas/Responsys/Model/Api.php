<?php
class Pas_Responsys_Model_Api extends Mage_Core_Model_Abstract
{
    const MERGE_LIMIT   = 200;
    const EVENT_LIMIT   = 2000;

    /**
     * @var Responsys_API
     */
    protected $_client;


    protected function _construct()
    {
        $username = Mage::helper('responsys')->getUsername();
        $password = Mage::helper('responsys')->getPassword();

        $this->_client = new Responsys_API($username, $password);
    }

    public function mergeList($folder, $list, array $records)
    {
        // Don't execute API call if nothing to send.
        if(!count($records)) return $this;

        try {
            // Todo: Turn this into a less memory hungry loop.
            foreach(array_chunk($records, self::MERGE_LIMIT) as $chunk) {
                $this->_client->mergeListMembers($folder, $list, $chunk);
                Mage::helper('responsys')->log('Merged member list data for ' . count($chunk) . ' records.');
            }
        }
        catch (Exception $e) {
            Mage::helper('responsys')->log($e->getMessage());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    public function mergeExtension($folder, $extension, array $records)
    {
        // Don't execute API call if nothing to send.
        if(!count($records)) return $this;

        try {
            // Todo: Turn this into a less memory hungry loop.
            foreach(array_chunk($records, self::MERGE_LIMIT) as $chunk) {
                $this->_client->mergeIntoProfileExtension($folder, $extension, $chunk);
                Mage::helper('responsys')->log('Merged profile extension data for ' . count($chunk) . ' records.');
            }
        }
        catch (Exception $e) {
            Mage::helper('responsys')->log($e->getMessage());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    public function mergeTable($folder, $table, array $records)
    {
        // Don't execute API call if nothing to send.
        if(!count($records)) return $this;

        try {
            // Todo: Turn this into a less memory hungry loop.
            foreach(array_chunk($records, self::MERGE_LIMIT) as $chunk) {
                $this->_client->mergeTableRecords($folder, $table, $chunk);
                Mage::helper('responsys')->log('Merged table data for ' . count($chunk) . ' records.');
            }
        }
        catch (Exception $e) {
            Mage::helper('responsys')->log($e->getMessage());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    public function triggerEvent($folder, $list, $event, array $records)
    {
        // Don't execute API call if nothing to send.
        if(!count($records)) return $this;

        try {
            // Todo: Turn this into a less memory hungry loop.
            foreach(array_chunk($records, self::EVENT_LIMIT) as $chunk) {
                $this->_client->triggerCustomEvent($folder, $list, $event, $chunk);
                Mage::helper('responsys')->log('Triggered event `' . $event . '` for ' . count($chunk) . ' records.');
            }
        }
        catch (Exception $e) {
            Mage::helper('responsys')->log($e->getMessage());
            Mage::throwException($e->getMessage());
        }

        return $this;
    }
}