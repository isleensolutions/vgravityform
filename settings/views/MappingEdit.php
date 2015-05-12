<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_MappingEdit_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);     
        
        $formid = $request->get('formid');    
        
        $viewer->assign('FORMID', $formid);         
		$viewer->assign('MODULE_MODEL', Settings_GravityForm_Mapping_Model::getInstance($formid, TRUE));
		$viewer->assign('LEADS_MODULE_MODEL', Settings_Leads_Module_Model::getInstance('Leads'));  
        $viewer->assign('GRAVITY_MODULE_MODEL', Settings_GravityForm_Module_Model::getGravityFields($formid));
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('GravityFieldMappingEdit.tpl', $qualifiedModuleName);
	}
    
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.Settings.$moduleName.resources.GravityFieldMapping"
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
	
	
}