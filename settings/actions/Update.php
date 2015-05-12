<?php

/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_Update_Action extends Settings_Vtiger_Index_Action {
    
    public function process(Vtiger_Request $request) {
        
        $id = $request->get('formid');
        $module = $request->get('module');
        $response = new Vtiger_Response(); 
        $adb = PearDatabase::getInstance();
        try{ 
        $sql = "SELECT * FROM vtiger_gravityform WHERE formid=?"; 
        $result = $adb->pquery($sql,array($id));
          
        while ($row = $adb->fetch_array($result)){ 
            $formid = $row['formid'];
            $signature = $row['signature'];
            $expires = $row['expires'];    
        }  
        
        $config_result = $adb->pquery("SELECT * FROM vtiger_gravityform_config", $params);
        
        while ($row = $adb->fetch_array($config_result)){
            $url = $row['url'];
            $api_key = $row['api_key'];              
        }  
        $service_url = $url.'gravityformsapi/forms/'.$formid.'/?api_key='.$api_key.'&signature='.$signature.'&expires='.$expires;
        //var_dump($service_url);exit;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
            $result = curl_exec($ch);

            $res = json_decode ($result, true); 
         
            $status = $res['status'];   
            if($status == 200){
                $del = "DELETE FROM vtiger_gravityformfieldlabel WHERE gravityformid =".$formid;
                $result2 = $adb->pquery($del,array());
                $noofrows = count($res['response']['fields']);
                $list = array();
                $list3 = array();
                for($i=0; $i<=$noofrows; $i++){
                   $list[$res['response']['fields'][$i]['id']] = $res['response']['fields'][$i]['label'];
                   $list3[] = $res['response']['fields'][$i]['id'];
                       
                   if(is_array($res['response']['fields'][$i]['inputs'])){
                        
                       for($j=0; $j<count($res['response']['fields'][$i]['inputs']); $j++){    
                           $list[$res['response']['fields'][$i]['inputs'][$j]['id']] = $res['response']['fields'][$i]['inputs'][$j]['label'];
                           $list3[] = $res['response']['fields'][$i]['inputs'][$j]['id'];
                       }
                        
                   }
                }
               curl_close($ch);  
            
                for($i=0; $i<=count($list); $i++){              
                    $sql = 'INSERT INTO vtiger_gravityformfieldlabel(gravityformid, id, label) VALUES (?,?,?)';
                    $params = array($formid, $list3[$i], $list[$list3[$i]]);
                    $adb->pquery($sql, $params);
                    $response->setResult(array('success'=>$result)); 
               }    
            }else if($status != 200){
                 $response->setResult(array('Failed'=>$result));
            }
            
            
        } catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
         
        
    }
        
    
}