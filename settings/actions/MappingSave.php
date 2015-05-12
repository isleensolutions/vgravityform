<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_MappingSave_Action extends Settings_Vtiger_Index_Action {

	public function process(Vtiger_Request $request) {
        
		$qualifiedModuleName = $request->getModule(false);
		$mapping = $request->get('mapping');    
		$csrfKey = $GLOBALS['csrf']['input-name'];
		if(array_key_exists($csrfKey,$mapping)){
			unset($mapping[$csrfKey]);
		}
  
		$mappingModel = Settings_GravityForm_Mapping_Model::getCleanInstance();

		$response = new Vtiger_Response();
		if ($mapping) {
			$mappingModel->save($mapping);
            $result = array('status' => true);
		} else {
            $result['status'] = false;
		}
        $response->setResult($result);
		return $response->emit();
	}

	public function validateRequest(Vtiger_Request $request){
		$request->validateWriteAccess();
	}
}
