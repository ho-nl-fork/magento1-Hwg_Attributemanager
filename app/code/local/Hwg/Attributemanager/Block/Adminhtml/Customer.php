<?php
class Hwg_Attributemanager_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_customer';
		$this->_blockGroup = 'attributemanager';
		$this->_headerText = Mage::helper('attributemanager')->__('Manage Customer Attribute');
		$this->_addButtonLabel = Mage::helper('attributemanager')->__('Add New Attribute');
		parent::__construct();
		
		$this->_addButton('add', array(
            'label'     => $this->getAddButtonLabel(),
			'onclick'   => 'setLocation(\''.$this->getUrl('*/*/new', array('type' => 'customer','attribute_id'=>0)).'\')',
            'class'     => 'add',
        ));
	}
}

?>