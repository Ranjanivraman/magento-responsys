<?php

class Pas_Responsys_Model_Resource_Product_Setup extends Mage_Catalog_Model_Resource_Setup
{

    /**
     * Retreive default entities: customer, customer_address
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'                  => 'catalog/product',
                'attribute_model'               => 'catalog/resource_eav_attribute',
                'table'                         => 'catalog/product',
                'additional_attribute_table'    => 'catalog/eav_attribute',
                'entity_attribute_collection'   => 'catalog/product_attribute_collection',
                'attributes'                    => array(
                    'responsys_sync'    => array(
                        'type'      => 'int',
                        'label'     => 'Requires Sync to Responsys',
                        'input'     => 'boolean',
                        'source'    => 'eav/entity_attribute_source_boolean',
                        'position'  => 1,
                        'required'  => false,
                        'default'   => 1
                    )
                )
            )
        );
    }
}