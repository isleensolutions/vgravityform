<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_Configure_View extends Settings_Vtiger_Index_View {        
    
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $configDetail = Settings_GravityForm_Module_Model::fetchConfig(); 
        $viewer->assign('CONFIG',$configDetail);  
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('MODULE', $moduleName);

        echo $viewer->view('Configure.tpl', $qualifiedModuleName, true);
    }
    
     
    
}