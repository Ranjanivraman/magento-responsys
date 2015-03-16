<?php
class Pas_Responsys_Helper_Abstract extends Mage_Core_Helper_Abstract
{
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

    /**
     * Set attribute flags for given collection.
     *
     * @param Varien_Data_Collection $collection
     * @param string $attribute
     * @param bool $value
     * @return $this
     */
    public function setFlags(Varien_Data_Collection $collection, $attribute, $value = true)
    {
        foreach($collection as $object) {
            $this->setFlag($object, $attribute, $value);
        }

        return $this;
    }

    /**
     * Set attribute flag for given object.
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $attribute
     * @param bool $value
     * @return $this
     */
    public function setFlag(Mage_Core_Model_Abstract $object, $attribute, $value = true)
    {
        $object->setData($attribute, $value);
        $object->getResource()->saveAttribute($object, $attribute);

        return $this;
    }
}
