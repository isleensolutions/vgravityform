<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_AutoSync_View extends Settings_Vtiger_Index_View
{
    function __construct(){
        $this->exposeMethod("checked"); 
        $this->exposeMethod("unchecked"); 
    }
    
    public function process(Vtiger_Request $request){
        $mode = $request->get('mode');   
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
    
    public function checked(Vtiger_Request $request){
        global $db;
        $db = PearDatabase::getInstance();  
        $result = $db->pquery('update vtiger_cron_task set status = 1 where module = "GravityForm"');   
    }
    
    public function unchecked(Vtiger_Request $request){
        global $db;
        $db = PearDatabase::getInstance();  
        $result = $db->pquery('update vtiger_cron_task set status = 0 where module = "GravityForm"');  
    }
}