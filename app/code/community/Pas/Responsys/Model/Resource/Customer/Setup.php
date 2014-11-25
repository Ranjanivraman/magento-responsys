<?php

class Pas_Responsys_Model_Resource_Customer_Setup extends Mage_Customer_Model_Resource_Setup
{

    /**
     * Retreive default entities: customer, customer_address
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'customer'                      => array(
                'entity_model'                  => 'customer/customer',
                'attribute_model'               => 'customer/attribute',
                'table'                         => 'customer/entity',
                'increment_model'               => 'eav/entity_increment_numeric',
                'additional_attribute_table'    => 'customer/eav_attribute',
                'entity_attribute_collection'   => 'customer/attribute_collection',
                'attributes'                    => array(
                    'responsys_sync'        => array(
                        'type'      => 'int',
                        'label'     => 'Requires Member Sync',
                        'input'     => 'boolean',
                        'backend'   => 'customer/attribute_backend_data_boolean',
                        'position'  => 1,
                        'required'  => false,
                        'default'   => 0
                    ),
                    'responsys_welcome'     => array(
                        'type'      => 'int',
                        'label'     => 'Requires Welcome Campaign',
                        'input'     => 'boolean',
                        'backend'   => 'customer/attribute_backend_data_boolean',
                        'position'  => 1,
                        'required'  => false,
                        'default'   => 0
                    )
                )
            )
        );
    }
}