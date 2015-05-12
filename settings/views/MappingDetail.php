<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_MappingDetail_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);     

        $id = $request->get('id');
        $module = $request->get('module');
        
        $adb = PearDatabase::getInstance();
        
        $sql = "SELECT * FROM vtiger_gravityform WHERE gravityformid=?"; 
        $result = $adb->pquery($sql,array($id));  
        while ($row = $adb->fetch_array($result)){ 
            $api_key = $row['api_key'];
            $formid = $row['formid'];
            $signature = $row['signature'];
            $expires = $row['expires'];   
        }
        
        if($formid == ''){
           
            $service_url = 'http://demo.gravityforms.com/gravityformsapi/forms/'.$formid.'/?api_key='.$api_key.'&signature='.$signature.'&expires='.$expires;
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
            $result = curl_exec($ch);  
            $res = json_decode ($result, true);  
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
           }
         
        } 
        $url = 'javascript:Settings_GravityFieldMapping_Js.triggerEdit("index.php?parent=Settings&module=GravityForm&view=MappingEdit&formid='.$formid.'")';
        
        $viewer->assign('URL', $url);                      
        $viewer->assign('FORMID', $formid);
		$viewer->assign('MODULE_MODEL', Settings_GravityForm_Mapping_Model::getInstance($formid));
		$viewer->assign('ERROR_MESSAGE', $request->get('errorMessage'));
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->view('MappingDetail.tpl', $qualifiedModuleName);
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