<?php
class Hwg_Attributemanager_Block_Adminhtml_Address_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected function _prepareLayout()
    {        
        $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_mode . '_form'));
        return parent::_prepareLayout();
    }
    
    public function __construct()
    {	
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_address';
        $this->_blockGroup = 'attributemanager';

        parent::__construct();

        if($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label'     => Mage::helper('catalog')->__('Close Window'),
                    'class'     => 'cancel',
                    'onclick'   => 'window.close()',
                    'level'     => -1
                )
            );
        }
		
		$this->_updateButton('back', 'onclick', "setLocation('".$this->getUrl('*/*/address')."')");
		
        $this->_updateButton('save', 'label', Mage::helper('catalog')->__('Save Attribute'));
        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('catalog')->__('Save And Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );
		 $this->_formScripts[] = "
		function toggleEditor() {
                if (tinyMCE.getInstanceById('company_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'company_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'company_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
       if (! Mage::registry('attributemanager_data')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('catalog')->__('Delete Attribute'));
            $this->_updateButton('delete', 'onclick', "deleteConfirm(
            		'".Mage::helper('adminhtml')->__('Are you sure you want to do this?')."',
            		'".$this->getUrl('*/*/delete/type/'.$this->getRequest()->getParam('type').'/attribute_id/'.$this->getRequest()->getParam('attribute_id')
            		)."')");
        }
    }

    public function getHeaderText()
    {
    	switch ($this->getRequest()->getParam('type')){
    		case 'catalog_category':
    			$type="Category";
    			break;
   			case 'customer':
    			$type="Customer";
    			break;
   			case 'customer_address':
    			$type="Customer address";
    			break;
    	}
    	
        if (Mage::registry('attributemanager_data')->getId()) {
            return Mage::helper('attributemanager')->__('Edit %s Attribute "%s"', $type, $this->htmlEscape(Mage::registry('attributemanager_data')->getFrontendLabel()));
        }
        else {
            return Mage::helper('attributemanager')->__('New %s Attribute', $type);
        }
       
    }
}
