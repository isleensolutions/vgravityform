<?php

/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_Delete_Action extends Settings_Vtiger_Index_Action {
    
    public function process(Vtiger_Request $request) {
        
        $id = $request->get('id');
        $module = $request->get('module');
        
        $adb = PearDatabase::getInstance();
        
        $sql = "DELETE FROM vtiger_gravityform WHERE gravityformid=?"; 
        
        
        $response = new Vtiger_Response();
        try{
            $result = $adb->pquery($sql,array($id));
            $response->setResult(array('success'=>$result));
        } catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
        
    }
    
}