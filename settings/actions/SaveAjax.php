<?php

/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_SaveAjax_Action extends Settings_Vtiger_Index_Action {
    
    public function process(Vtiger_Request $request) {
        $module = $request->get('module');
        $qualifiedModuleName = $request->getModule(false);
         
        $response = new Vtiger_Response();
        try{
            $result = Settings_GravityForm_Module_Model::save($request);
            $response->setResult(array('success'=>$result));
        } catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
}