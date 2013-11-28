<?php
class Hwg_Attributemanager_Block_Adminhtml_Category_Grid  extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	parent::__construct();	
	$this->setId('attributemanagergrid');
	$this->setDefaultSort('attribute_code');
	$this->setDefaultDir('ASC');
	$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $type='catalog_category';
      $this->type=$type;
      $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType($type)->getTypeId() )
			->addFieldToFilter("is_user_defined", 1);
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
      
  }

  protected function _prepareColumns()
  {
      $this->addColumn('attribute_code', array(
            'header'=>Mage::helper('catalog')->__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=>Mage::helper('catalog')->__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label'
        ));

        $this->addColumn('is_visible', array(
            'header'=>Mage::helper('catalog')->__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_global', array(
            'header'=>Mage::helper('catalog')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_required', array(
            'header'=>Mage::helper('catalog')->__('Required'),
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_user_defined', array(
            'header'=>Mage::helper('catalog')->__('System'),
            'sortable'=>true,
            'index'=>'is_user_defined',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                '0' => Mage::helper('catalog')->__('Yes'),   // intended reverted use
                '1' => Mage::helper('catalog')->__('No'),    // intended reverted use
            ),
        ));

        $this->addExportType('*/*/exportCsv'.$this->_block, Mage::helper('attributemanager')->__('CSV'));
		$this->addExportType('*/*/exportXml'.$this->_block, Mage::helper('attributemanager')->__('XML'));
	  
      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('type' => $this->type,'attribute_id' => $row->getAttributeId()));
  }

}

?>