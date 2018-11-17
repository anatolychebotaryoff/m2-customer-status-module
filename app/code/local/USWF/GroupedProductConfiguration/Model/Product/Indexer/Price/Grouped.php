<?php
/**
 * Grouped.php
 *
 * @category    USWF
 * @package     USWF_GroupedProductConfiguration
 * @copyright
 * @author
 */

class USWF_GroupedProductConfiguration_Model_Product_Indexer_Price_Grouped 
    extends Itoris_GroupedProductConfiguration_Model_Product_Indexer_Price_Grouped {

    protected function _prepareGroupedProductPriceData($entityIds = null) {
        if ($this->getDataHelper()->getSettings()->getEnabled() && $this->getDataHelper()->isAdminRegistered()) {
            $write = $this->_getWriteAdapter();
            $table = $this->getIdxTable();
            $sortOrderAttrId = 2;
            $select = $write->select()
                ->from(array('e' => $this->getTable('catalog/product')), 'entity_id')
                ->joinLeft(
                    array('l' => $this->getTable('catalog/product_link')),
                    'e.entity_id = l.product_id AND l.link_type_id=' . Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
                    array())
                ->joinLeft(
                    array('la' => $this->getTable('catalog/product_link_attribute_int')),
                    'l.link_id = la.link_id AND la.product_link_attribute_id=' . $sortOrderAttrId,
                    array())
                ->join(
                    array('cg' => $this->getTable('customer/customer_group')),
                    '',
                    array('customer_group_id'));
            $this->_addWebsiteJoinToSelect($select, true);
            $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
            $minCheckSql = $write->getCheckSql('le.required_options = 0', 'i.min_price', 'i.min_price');
            $maxCheckSql = $write->getCheckSql('le.required_options = 0', 'i.max_price', 0);

            $select->columns('website_id', 'cw')
                ->joinLeft(
                    array('le' => $this->getTable('catalog/product')),
                    'le.entity_id = l.linked_product_id',
                    array())
                ->joinLeft(
                    array('i' => $table),
                    'i.entity_id = l.linked_product_id AND i.website_id = cw.website_id'
                    . ' AND i.customer_group_id = cg.customer_group_id',
                    array(
                        'tax_class_id' => $this->_getReadAdapter()
                            ->getCheckSql('MIN(i.tax_class_id) IS NULL', '0', 'MIN(i.tax_class_id)'),
                        'price'        => 'i.price',
                        'final_price'  => 'i.final_price',
                        'min_price'    => new Zend_Db_Expr('MIN(' . $minCheckSql . ')'),
                        'max_price'    => new Zend_Db_Expr('MAX(' . $maxCheckSql . ')'),
                        'tier_price'   => new Zend_Db_Expr('NULL'),
                        'group_price'  => new Zend_Db_Expr('NULL'),
                        'min_unit_price' => $this->_getReadAdapter()
                            ->getCheckSql('MIN(i.min_unit_price) IS NULL', '0', 'MIN(i.min_unit_price)')
                    ))
                ->group(array('e.entity_id', 'cg.customer_group_id', 'cw.website_id'))
                ->order('la.value ASC')
                ->where('e.type_id=?', $this->getTypeId());

            if (!is_null($entityIds)) {
                $select->where('l.product_id IN(?)', $entityIds);
            }

            /**
             * Add additional external limitation
             */
            Mage::dispatchEvent('catalog_product_prepare_index_select', array(
                'select'        => $select,
                'entity_field'  => new Zend_Db_Expr('e.entity_id'),
                'website_field' => new Zend_Db_Expr('cw.website_id'),
                'store_field'   => new Zend_Db_Expr('cs.store_id')
            ));


            $query = $select->insertFromSelect($table);
            $write->query($query);

            return $this;
        } else {
            return Mage_Catalog_Model_Resource_Product_Indexer_Price_Grouped::_prepareGroupedProductPriceData($entityIds = null);
        }
    }

}
?>