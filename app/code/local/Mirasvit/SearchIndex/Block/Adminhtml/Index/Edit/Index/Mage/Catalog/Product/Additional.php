<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchIndex_Block_Adminhtml_Index_Edit_Index_Mage_Catalog_Product_Additional extends Varien_Data_Form_Element_Fieldset
{
    public function toHtml()
    {
        $model = $this->getModel();

        parent::__construct(array('legend' => Mage::helper('searchindex')->__('Additional Search Index Configuration')));

        $this->addField('include_category', 'select', array(
            'name'     => 'properties[include_category]',
            'label'    => Mage::helper('searchindex')->__('Search by parent categories names'),
            'required' => true,
            'value'    => $model->getProperty('include_category'),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $this->addField('include_bundled', 'select', array(
            'name'     => 'properties[include_bundled]',
            'label'    => Mage::helper('searchindex')->__('Search by child products attributes (for bundle and configurable products)'),
            'required' => true,
            'value'    => $model->getProperty('include_bundled'),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $this->addField('include_tag', 'select', array(
            'name'     => 'properties[include_tag]',
            'label'    => Mage::helper('searchindex')->__('Search by product tags'),
            'required' => true,
            'value'    => $model->getProperty('include_tag'),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $this->addField('include_id', 'select', array(
            'name'     => 'properties[include_id]',
            'label'    => Mage::helper('searchindex')->__('Search by product id'),
            'required' => true,
            'value'    => $model->getProperty('include_id'),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        return parent::toHtml();
    }
}