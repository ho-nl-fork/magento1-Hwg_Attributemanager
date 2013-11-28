<?php
class Hwg_Attributemanager_Block_Adminhtml_Category_Edit_Tab_Front extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('attributemanager_data');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('Frontend Properties')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));


        $fieldset->addField('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => Mage::helper('catalog')->__('Use in quick search'),
            'title' => Mage::helper('catalog')->__('Use in quick search'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('catalog')->__('Use in advanced search'),
            'title' => Mage::helper('catalog')->__('Use in advanced search'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'title' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'values' => $yesno,
        ));


        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('catalog')->__("Use In Layered Navigation<br/>(Can be used only with catalog input type 'Dropdown')"),
            'title' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('catalog')->__('No')),
                array('value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')),
                array('value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')),
            ),
        ));

//        if ($model->getIsUserDefined() || !$model->getId()) {
            $fieldset->addField('is_visible_on_front', 'select', array(
                'name' => 'is_visible_on_front',
                'label' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
                'title' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
                'values' => $yesno,
            ));
//        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
