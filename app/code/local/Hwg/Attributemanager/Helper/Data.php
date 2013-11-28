<?php

class Hwg_Attributemanager_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Selected products for massupdate
     *
     * @var Mage_Catalog_Model_Entity_Product_Collection
     */
    protected $_categories;

    /**
     * Array of categories that not available in selected store
     *
     * @var array
     */
    protected $_categoriesNotInStore;

    /**
     * Same attribtes for selected categories
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected $_attributes;


    /**
     * Excluded from batch update attribute codes
     *
     * @var array
     */
    protected $_excludedAttributes = array('url_key');

    /**
     * Retrive product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getCategories()
    {
        if (is_null($this->_categories)) {
            $categoriesIds = $this->getCategoryIds();

            if(!is_array($categoriesIds)) {
                $categoriesIds = array(0);
            }

            $this->_categories = Mage::getResourceModel('catalog/category_collection')
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($categoriesIds);
               
                /*$this->load();
                $this->addStoreNamesToResult();*/
        }

        return $this->_categories;
    }

    /**
     * Retrive selected categories ids from post or session
     *
     * @return array|null
     */
    public function getCategoryIds()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if ($this->_getRequest()->isPost() && $this->_getRequest()->getActionName()=='edit') {
            $session->setCategoryIds($this->_getRequest()->getParam('category', null));
        }

        return $session->getCategoryIds();
    }

    /**
     * Retrive selected store id
     *
     * @return integer
     */
    public function getSelectedStoreId()
    {
        return (int) $this->_getRequest()->getParam('store', 0);
    }

    /**
     * Retrive selected categories' attribute sets
     *
     * @return array
     */
    public function getCategoriesSetIds()
    {
        return $this->getCategories()->getSetIds();
    }

    /**
     * Retrive same attributes for selected categories without unique
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getCategories()->getEntity()->getEntityType()->getAttributeCollection()
                ->addIsNotUniqueFilter()
                ->setInAllAttributeSetsFilter($this->getCategoriesSetIds());

            foreach ($this->_excludedAttributes as $attributeCode) {
                $this->_attributes->addFieldToFilter('attribute_code', array('neq'=>$attributeCode));
            }

            $this->_attributes->load();
            foreach($this->_attributes as $attribute) {
                $attribute->setEntity($this->getCategories()->getEntity());
            }
        }

        return $this->_attributes;
    }

    /**
     * Retrive categories ids that not available for selected store
     *
     * @return array
     */
    public function getCategoriesNotInStoreIds()
    {
        if (is_null($this->_categoriesNotInStore)) {
            $this->_categoriesNotInStoreIds = array();
            /*foreach ($this->getCategories() as $category) {
                $stores = $category->getStores();
                if (!isset($stores[$this->getSelectedStoreId()]) && $this->getSelectedStoreId() != 0) {
                    $this->_categoriesNotInStoreIds[] = $category->getId();
                }
            }*/
        }

        return $this->_categoriesNotInStoreIds;
    }
}

?>