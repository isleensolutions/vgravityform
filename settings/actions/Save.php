<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/


class Settings_GravityForm_Save_Action extends Settings_Vtiger_Index_Action
{
    public function process(Vtiger_Request $request){

        $db = PearDatabase::getInstance();
        //result array to send as json response
        $result = array();
        
        $moduleName = $request->getModule();
        
        $moduleModel = Settings_GravityForm_Module_Model::getInstance($moduleName);
        $moduleModel->set('url', $request->get('url'));
        $moduleModel->set('api_key', $request->get('api_key'));
        $moduleModel->set('private_key', $request->get('private_key'));
        $moduleModel->save_config($request);
        
        $result['success'] = true;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }
    
}