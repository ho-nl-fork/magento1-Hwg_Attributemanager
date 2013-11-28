<?php
require_once 'lib/simple_html_dom.php';

class Hwg_Ordertracking_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
		
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        /*if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }*/
    }
	
	public function dpdAction()
    {
		date_default_timezone_set('Europe/Berlin');
		
		
		
		//Mage::getSingleton('customer/session')->setCarrierTracking('dpd');
		$this->loadLayout();
		$this->renderLayout();
    }  
	
}
