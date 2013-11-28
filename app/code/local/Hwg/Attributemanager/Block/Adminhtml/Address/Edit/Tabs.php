<?php
class Hwg_Attributemanager_Block_Adminhtml_Address_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('attributemanager_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('attributemanager_data');
        
        $this->addTab('main', array(
            'label'     => Mage::helper('catalog')->__('Properties'),
            'title'     => Mage::helper('catalog')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('attributemanager/adminhtml_address_edit_tab_main')->toHtml(),
            'active'    => true
        ));


        $this->addTab('labels', array(
            'label'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'title'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('attributemanager/adminhtml_address_edit_tab_options')->toHtml(),
        ));
        
        /*if ('select' == $model->getFrontendInput()) {
            $this->addTab('options_section', array(
                'label'     => Mage::helper('catalog')->__('Options Control'),
                'title'     => Mage::helper('catalog')->__('Options Control'),
                'content'   => $this->getLayout()->createBlock('attributemanager/edit_tab_options')->toHtml(),
            ));
        }*/

        return parent::_beforeToHtml();
    }

}
