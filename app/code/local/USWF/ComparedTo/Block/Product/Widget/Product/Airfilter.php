<?php
/**
 * Simple.php
 *
 * @category    USWF
 * @package     USWF_ComparedTo
 * @copyright
 * @author
 */
class USWF_ComparedTo_Block_Product_Widget_Product_Airfilter extends Mage_Core_Block_Template
{
    protected function _prepareLayout() {
        $this->setTemplate('catalog/product/widget/compare/product/airfilter.phtml');
        return parent::_prepareLayout();
    }
}