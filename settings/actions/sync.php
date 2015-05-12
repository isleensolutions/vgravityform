<?php

/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_Sync_Action extends Settings_Vtiger_Index_Action {
    
    public function process(Vtiger_Request $request) {
        $id = $request->get('id');
        $module = $request->get('module');
        try{ 
            $adb = PearDatabase::getInstance();
            $response = new Vtiger_Response();  
            $sql = "SELECT * FROM vtiger_gravityform WHERE gravityformid=?"; 
            $result = $adb->pquery($sql,array($id));  
            while ($row = $adb->fetch_array($result)){ 
                $formid = $row['formid'];
                $signature = $row['signature'];
                $expires = $row['expires'];
                $signature2 = $row['signature2'];
                $expires2 = $row['expires2'];
            }
             
            $result1 = $adb->pquery('SELECT * FROM vtiger_gravityformfieldmapping WHERE gravityformid='.$formid);    
            $leadid = $gravityid = array();                                
            while ($row = $adb->fetch_array($result1)){
               $gravityid[] = (float)$row['gravityfid'];
               $leadid[] =  $row['leadfid'];
            }
            
            $config_result = $adb->pquery("SELECT * FROM vtiger_gravityform_config", $params);
        
            while ($row = $adb->fetch_array($config_result)){
                $api_key = $row['api_key'];
                $url = $row['url'];              
            }  
            $service_url1 = $url.'gravityformsapi/forms/'.$formid.'/entries/?api_key='.$api_key.'&signature='.$signature2.'&expires='.$expires2;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $service_url1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
            $result2 = curl_exec($ch); 
            
            $res2 = json_decode ($result2, true); 
            $status = $res2['status']; 
            if($status == 200){  
                $noofrows = $res2['response']['total_count']; 
                curl_close($ch);              
                $list2 = $list3 = $list4 = array();  
                for($i=0; $i<=$noofrows; $i++){ 
                    for($j=0; $j<=count($gravityid); $j++){
                        if($res2['response']['entries'][$i] != null){  
                            $list2 = $res2['response']['entries'][$i] ;
                            $list3[$i][$leadid[$j]] = $list2[(string)$gravityid[$j]];
                        }                                          
                    }                                                         
                }
                
                $focus = CRMEntity::getInstance('Leads');  
                for($i=0; $i<count($list3); $i++){
                    for($j=0; $j<count($leadid); $j++){   
                        $list4[$i][$this->getLeadColumn($leadid[$j])] = $list3[$i][$leadid[$j]];  
                        if($list3[$i][$leadid[$j]] != null){                 
                           $focus->column_fields[$this->getLeadColumn($leadid[$j])] = $list3[$i][$leadid[$j]];  
                        }
                    }
                    
                    if($res2['response']['entries'][$i] != null){  
                        $ids = $res2['response']['entries'][$i]['id']; 
                    }
                    
                    $select = "SELECT vtiger_gravityformstore.leadid AS leadid FROM vtiger_gravityformstore
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_gravityformstore.leadid 
                                WHERE vtiger_crmentity.deleted = 0 AND entryid = ? AND formid = ?"; 
                    $selectResult = $adb->pquery($select,array($ids, $formid));
                    $num_rows = $adb->num_rows($selectResult);
                    if ($num_rows > 0) { 
                        while ($row = $adb->fetch_array($selectResult)){ 
                            $lead = $row['leadid'];
                        }                      
                        $focus->mode = "edit";
                        $focus->id = $lead;
                        $focus->save('Leads');
                    }else{
                        $focus->mode = "";
                        $focus->id = "";
                        $focus->save('Leads');
                        $focusid = $focus->id;
                        $insert = "insert into vtiger_gravityformstore(entryid, leadid, formid) values(?,?,?)";
                        $insertResult = $adb->pquery($insert,array($ids, $focusid, $formid));
                    } 
                }           
                
                if($status == 200){
                   $status = 'Success'; 
                } 
                $update = "UPDATE vtiger_gravityform SET STATUS = ? WHERE vtiger_gravityform.gravityformid = ?";
                $updateResult = $adb->pquery($update,array($status, $id));
                $response->setResult(array('success'=>$updateResult)); 
            }else if($status != 200){
                 $response->setResult(array('Failed'=>$result)); 
            }
             
        }catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit(); 
    }
    public function autoSync(){
        $adb = PearDatabase::getInstance();
        $response = new Vtiger_Response();  
        $sql = "SELECT gravityformid FROM vtiger_gravityform"; 
        $result = $adb->pquery($sql);
        $count = $adb->num_rows($result);
        while ($row = $adb->fetch_array($result)){ 
                $id[] = $row['gravityformid'];
        }
        
        $module = 'GravityForm';
        for($k=0; $k<$count; $k++){
            $adb = PearDatabase::getInstance();
            $response = new Vtiger_Response();  
            $sql = "SELECT * FROM vtiger_gravityform WHERE gravityformid=?"; 
            $result = $adb->pquery($sql,array($id[$k]));  
            while ($row = $adb->fetch_array($result)){ 
                $formid = $row['formid'];
                $signature = $row['signature'];
                $expires = $row['expires'];
                $signature2 = $row['signature2'];
                $expires2 = $row['expires2'];
            }
             
            $result1 = $adb->pquery('SELECT * FROM vtiger_gravityformfieldmapping WHERE gravityformid='.$formid);    
            $leadid = $gravityid = array();                                
            while ($row = $adb->fetch_array($result1)){
               $gravityid[] = (float)$row['gravityfid'];
               $leadid[] =  $row['leadfid'];
            }
            
            $config_result = $adb->pquery("SELECT * FROM vtiger_gravityform_config", $params);
        
            while ($row = $adb->fetch_array($config_result)){
                $api_key = $row['api_key'];
                $url = $row['url'];              
            }  
            $service_url1 = $url.'gravityformsapi/forms/'.$formid.'/entries/?api_key='.$api_key.'&signature='.$signature2.'&expires='.$expires2;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $service_url1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
            $result2 = curl_exec($ch); 
            
            $res2 = json_decode ($result2, true); 
            $status = $res2['status']; 
            
            if($status == 200){  
                $noofrows = $res2['response']['total_count']; 
                curl_close($ch);              
                $list2 = $list3 = $list4 = array();  
                for($i=0; $i<=$noofrows; $i++){ 
                    for($j=0; $j<=count($gravityid); $j++){
                        if($res2['response']['entries'][$i] != null){  
                            $list2 = $res2['response']['entries'][$i] ;
                            $list3[$i][$leadid[$j]] = $list2[(string)$gravityid[$j]];
                        }                                          
                    }                                                         
                }
                
                $focus = CRMEntity::getInstance('Leads');  
                for($i=0; $i<count($list3); $i++){
                    for($j=0; $j<count($leadid); $j++){   
                        $list4[$i][Settings_GravityForm_Sync_Action::getLeadColumn($leadid[$j])] = $list3[$i][$leadid[$j]];  
                        if($list3[$i][$leadid[$j]] != null){                 
                           $focus->column_fields[Settings_GravityForm_Sync_Action::getLeadColumn($leadid[$j])] = $list3[$i][$leadid[$j]];  
                        }
                    }
                    
                    if($res2['response']['entries'][$i] != null){  
                        $ids = $res2['response']['entries'][$i]['id']; 
                    }
                    
                    $select = "SELECT vtiger_gravityformstore.leadid AS leadid FROM vtiger_gravityformstore
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_gravityformstore.leadid 
                                WHERE vtiger_crmentity.deleted = 0 AND entryid = ? AND formid = ?"; 
                    $selectResult = $adb->pquery($select,array($ids, $formid));
                    $num_rows = $adb->num_rows($selectResult);
                    
                    if ($num_rows > 0) { 
                        while ($row = $adb->fetch_array($selectResult)){ 
                            $lead = $row['leadid'];
                        }                      
                        $focus->mode = "edit";
                        $focus->id = $lead;
                        $focus->save('Leads');
                    }else{
                        $focus->mode = "";
                        $focus->id = "";
                        $focus->save('Leads');
                        $focusid = $focus->id;
                        $insert = "insert into vtiger_gravityformstore(entryid, leadid, formid) values(?,?,?)";
                        $insertResult = $adb->pquery($insert,array($ids, $focusid, $formid));
                    } 
                }           
                
                if($status == 200){
                   $status = 'Success'; 
                } 
                $update = "UPDATE vtiger_gravityform SET STATUS = ? WHERE vtiger_gravityform.gravityformid = ?";
                $updateResult = $adb->pquery($update,array($status, $id[$k]));
            }
        
        }
    
    }
    
    public function getLeadColumn($fieldid){
        $adb = PearDatabase::getInstance();  
        $sql = "SELECT columnname FROM vtiger_field WHERE vtiger_field.fieldid =?"; 
        $result = $adb->pquery($sql,array($fieldid));  
        while ($row = $adb->fetch_array($result)){ 
            $columnname = $row['columnname'];
        }
        return $columnname;
    }
       
}