<?php
require_once 'lib/simple_html_dom.php';
class Hwg_Ordertracking_Block_View extends Mage_Core_Block_Template
{
	public function getDpdOrderTracking()
	{
		$consignment = $this->getRequest()->getParam('consignment', 15502405041348);

		$link = 'http://www.dpd.co.uk/tracking/quicktrack.do?search.consignmentNumber='.$consignment.'&search.searchType=16&search.javascriptValidated=0&appmode=guest';

		try
		{
			$html = file_get_html($link);
		}
		//catch exception
		catch(Exception $e)
		{
			var_dump($e);
		}
		$Status = "Vide";
		$target_script = "Vide";
	
		foreach($html->find('script') as $html_script) {
			if(strstr($html_script->outertext, "var trackCode =")) {
				$target_script = $html_script->outertext;
			}
		}
		$pattern = "/var trackCode = \'(.*?)\';*/";
		preg_match($pattern, $target_script, $matches);
		
		$data = array();
		$data['Status'] = $html->find('td[class=app-light-row-one app-table-indent] div[id='.$matches[1].'_text]',0)->plaintext;
		$data['Parcel_No'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',0)->plaintext;
		$data['Reference'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',1)->plaintext;
		$data['consignment'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',2)->plaintext;
		$data['Post_Code'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',3)->plaintext;
		$data['Collected_Date'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',4)->plaintext;
		$data['Service'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',5)->plaintext;
		$data['Delivery_Status'] = $html->find('td[class=app-light-row-one app-border-top app-data-row]',6)->plaintext;
		$trackingtable = $html->find('table[id=parceldetail]',0)->outertext;
		$data['trackingtable'] = str_replace("SPICERS LTD", "WAREHOUSE", $trackingtable);
		
		return $data;
	}
	
}
