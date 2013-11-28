<?php
class Hwg_Attributemanager_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{
	protected $_customerTypeId;
	protected $_type;
	protected $_block;
	
   public function preDispatch()
    {
        parent::preDispatch();
        $this->_customerTypeId = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
		
		$this->_block = 'customer';
		$this->_type =  'customer';
		
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('attributemanager/attributemanager')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Attribute Manager'), Mage::helper('adminhtml')->__('Attribute Manager'));
		return $this;
	}   
	public function customerAction()
	{
		$this->_initAction()->renderLayout();
	}
	public function editAction() 
	{
		$id     = $this->getRequest()->getParam('attribute_id');
		$model  = Mage::getModel('eav/entity_attribute')->load($id);

		if(0!==$id){
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$model->setData("sort_order",$db->fetchOne("select sort_order from eav_entity_attribute where attribute_id=$id"));
		}
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('attributemanager_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('attributemanager/attributemanager');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this
				->_addContent($this->getLayout()->createBlock('attributemanager/adminhtml_'.$this->_block.'_edit'))
				->_addLeft($this->getLayout()->createBlock('attributemanager/adminhtml_'.$this->_block.'_edit_tabs'))
				;

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('attributemanager')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('type');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        
        $this->_entityTypeId=$this->_customerTypeId;
        
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
 
	public function newAction() {
		$this->_forward('edit');
	}
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('attributemanager/attributemanager');
			$model->setData($data);
			if( $this->getRequest()->getParam('attribute_id') > 0 ) {
				
					$model->setId($this->getRequest()->getParam('attribute_id'));
			}
			
			try {
				
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				
				$model->save();
				$id=$model->getId();
				
				if($this->_block == 'customer' || $this->_block == 'address')
				{
					$attribute = Mage::getModel('eav/entity_attribute')->load($id);
					
					Mage::getSingleton('eav/config')
					->getAttribute($this->_type, $attribute->getAttributeCode())
					->setData('used_in_forms', $data['customer_form'])
					->save();				
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('attributemanager')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('type'=>'customer', 'attribute_id' => $id));
					return;
				}
				
				$this->_redirect('*/*/'.$this->_block);
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('type'=>$this->getRequest()->getParam('type'),'attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('attributemanager')->__('Unable to find item to save'));
        $this->_redirect('*/*/'.$this->_block);
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('attribute_id') > 0 ) {
			try {
				$model = Mage::getModel('eav/entity_attribute');
				 
				$model->setId($this->getRequest()->getParam('attribute_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/'.$this->_block);
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('type'=>$this->getRequest()->getParam('type'),'attribute_id' => $this->getRequest()->getParam('attribute_id')));
			}
		}
		$this->_redirect('*/*/'.$this->_block);
	}

    public function massDeleteAction() {
        $categoriesattributesIds = $this->getRequest()->getParam('attributemanager');
        if(!is_array($categoriesattributesIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoriesattributesIds as $categoriesattributesId) {
                    $categoriesattributes = Mage::getModel('eav/entity_attribute')->load($categoriesattributesId);
                    $categoriesattributes->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($categoriesattributesIds)
                    )
                );
            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/'.$this->_type.'/filter//');
    }
  
    public function exportCsvAction()
    {
		$fileName   = $this->_type.'attributes.csv';
        $content    = $this->getLayout()->createBlock('attributemanager/adminhtml_'.$this->_block.'_grid')
            ->getCsv();
			
        $this->_sendUploadResponse($fileName, $content);
    }
	public function exportXmlAction()
    {
        $fileName   = $this->_type.'attributes.xml';
        $content    = $this->getLayout()->createBlock('attributemanager/adminhtml_'.$this->_block.'_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}

?>