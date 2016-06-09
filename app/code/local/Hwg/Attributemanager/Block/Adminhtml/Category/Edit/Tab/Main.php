<?php

class Hwg_Attributemanager_Block_Adminhtml_Category_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('attributemanager_data');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('catalog')->__('Attribute Properties'))
        );
        if ($model->getId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));

        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => Mage::helper('catalog')->__('Attribute Code'),
            'title' => Mage::helper('catalog')->__('Attribute Code'),
            'note'  => Mage::helper('catalog')->__('For internal use. Must be unique with no spaces'),
            'class' => 'validate-code',
            'required' => true,
        ));

        $categoryType = Mage::getModel('eav/config')->getEntityType('catalog_category')->getEntityTypeId();
        $categoryAttributeSet = Mage::getModel('eav/entity_attribute_set')->load($categoryType, 'entity_type_id');

        $groupCollection = Mage::getModel('eav/entity_attribute_group')
            ->getCollection()
            ->addFieldToFilter('attribute_set_id', $categoryAttributeSet->getId());
        $groups = array();
        foreach($groupCollection as $group) {
            $groups[$group->getId()] = $group->getAttributeGroupName();
        }

        $fieldset->addField('attribute_group_id', 'select', array(
            'name'  => 'attribute_group_id',
            'label' => Mage::helper('catalog')->__('Group'),
            'title' => Mage::helper('catalog')->__('Group'),
            'required' => true,
            'values' => $groups
        ));

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );

        if ($model->getAttributeCode() == 'status' || $model->getAttributeCode() == 'tax_class_id') {
            unset($scopes[Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('catalog')->__('Scope'),
            'title' => Mage::helper('catalog')->__('Scope'),
            'note'  => Mage::helper('catalog')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ));

        $inputTypes = array(
            array(
                'value' => 'text',
                'label' => Mage::helper('catalog')->__('Text Field')
            ),
            array(
                'value' => 'textarea',
                'label' => Mage::helper('catalog')->__('Text Area')
            ),
            array(
                'value' => 'date',
                'label' => Mage::helper('catalog')->__('Date')
            ),
            array(
                'value' => 'boolean',
                'label' => Mage::helper('catalog')->__('Yes/No')
            ),
            array(
                'value' => 'multiselect',
                'label' => Mage::helper('catalog')->__('Multiple Select')
            ),
            array(
                'value' => 'select',
                'label' => Mage::helper('catalog')->__('Dropdown')
            ),
        );
        if($this->getRequest ()->getParam ( 'type' )==="catalog_category"){
         $inputTypes[]=   array(
                'value' => 'image',
                'label' => Mage::helper('catalog')->__('Image')

        );
        }

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_category_attribute_types', array('response'=>$response));

        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);


        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'title' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'value' => 'text',
            'values'=> $inputTypes
        ));
    /****** champs cach�s dans le formulaire **********/
        $fieldset->addField('entity_type_id', 'hidden', array(
            'name' => 'entity_type_id',
            'value' => Mage::getModel('eav/entity')->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));
        
        
        
        $fieldset->addField('is_user_defined', 'hidden', array(
            'name' => 'is_user_defined',
            'value' => 1
        ));
        
        $fieldset->addField('attribute_set_id', 'hidden', array(
            'name' => 'attribute_set_id',
            'value' => Mage::getModel('eav/entity')->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));
        
 /*******************************************************/
        $fieldset->addField('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => Mage::helper('catalog')->__('Unique Value'),
            'title' => Mage::helper('catalog')->__('Unique Value (not shared with other products)'),
            'note'  => Mage::helper('catalog')->__('Not shared with other products'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => Mage::helper('catalog')->__('Values Required'),
            'title' => Mage::helper('catalog')->__('Values Required'),
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'title' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'values'=>  array(
                array(
                    'value' => '',
                    'label' => Mage::helper('catalog')->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => Mage::helper('catalog')->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => Mage::helper('catalog')->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => Mage::helper('catalog')->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => Mage::helper('catalog')->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => Mage::helper('catalog')->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => Mage::helper('catalog')->__('Letters(a-zA-Z) or Numbers(0-9)')
                ),
            )
        ));

        // -----


        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset',
        	array('legend'=>Mage::helper('catalog')->__('Frontend Properties')));

     

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('catalog')->__('Position'),
            'title' => Mage::helper('catalog')->__('Position In Layered Navigation'),
            'note' => Mage::helper('catalog')->__('Position of attribute in layered navigation block'),
            'class' => 'validate-digits',
        ));

        if ($model->getIsUserDefined() || !$model->getId()) {
            $fieldset->addField('is_visible_on_front', 'select', array(
                'name' => 'is_visible_on_front',
                'label' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
                'title' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
                'values' => $yesno,
            ));
        }
        
        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('catalog')->__('Order'),
            'title' => Mage::helper('catalog')->__('Order in form'),
            'note' => Mage::helper('catalog')->__('order of attribute in form edit/create. Leave blank for form bottom.'),
            'class' => 'validate-digits',
        	'value' => $model->getAttributeSetInfo()
        ));
        

        if ($model->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);

            if (isset($disableAttributeFields[$model->getAttributeCode()])) {
                foreach ($disableAttributeFields[$model->getAttributeCode()] as $field) {
                    $form->getElement($field)->setDisabled(1);
                }
            }
        }
       /* if (!$model->getIsUserDefined() && $model->getId()) {
            $form->getElement('is_unique')->setDisabled(1);
        }
		*/
        //var_dump($model->getData());exit;
        $form->addValues($model->getData());
        

        // load group ID 
        // should only lead to one match as we have no attribute sets for categories

        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select  = $adapter->select()
            ->from('eav_entity_attribute') // FIXME: Fetch table name the right way
            ->where('attribute_id = ?', $model->getAttributeId());
        $row = $adapter->fetchRow($select);
        $groupId = $row ? $row['attribute_group_id'] : 0;

        $form->addValues(array('attribute_group_id' => $groupId));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply')
        );
    }

}
