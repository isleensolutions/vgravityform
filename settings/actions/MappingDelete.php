<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_MappingDelete_Action extends Settings_Vtiger_Index_Action {

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('mappingId');
		$qualifiedModuleName = $request->getModule(false);

		$response = new Vtiger_Response();
		if ($recordId) {
			Settings_GravityForm_Mapping_Model::deleteMapping(array($recordId));
			$response->setResult(array(vtranslate('LBL_DELETED_SUCCESSFULLY', $qualifiedModuleName)));
		} else {
			$response->setError(vtranslate('LBL_INVALID_MAPPING', $qualifiedModuleName));
		}
		$response->emit();
	}
}