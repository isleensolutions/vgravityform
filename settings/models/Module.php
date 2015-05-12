<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

/*
 * Settings Module Model Class
 */
class Settings_GravityForm_Module_Model extends Settings_Vtiger_Module_Model {

    var $baseTable = 'vtiger_gravityform';
    var $baseIndex = 'gravityformid';
    
    var $listFields = array('gravityformid' => 'ID', 'formid' => 'Form Id', 'status' => 'Status');
    
    const GLOBAL_ACTION_VIEW = 1;
    const GLOBAL_ACTION_EDIT = 2;
    const GLOBAL_ACTION_DEFAULT_VALUE = 1;

    const IS_PERMITTED_VALUE = 0;
    const NOT_PERMITTED_VALUE = 1;

    const FIELD_ACTIVE = 0;
    const FIELD_INACTIVE = 1;
    
    const FIELD_READWRITE = 0;
    const FIELD_READONLY = 1;
    
    var $name = 'GravityForm';
    
        /**
     * Function to get the url for default view of the module
     * @return <string> - url
     */
    public function getDefaultUrl() {
        return 'index.php?module=GravityForm&parent=Settings&view=List&block=4&fieldid=33';
    }

    /**
     * Function to get the url for create view of the module
     * @return <string> - url
     */
    public function getCreateRecordUrl() {
        return '?module=GravityForm&parent=Settings&view=EditAjax';
    }
    
    public function fetchConfig(){
        $db = PearDatabase::getInstance();
        $data = array();
        $result = $db->pquery('SELECT * FROM vtiger_gravityform_config',array());
        if($db->num_rows($result)){
            $data = $db->fetch_array($result);
        }
        
        return $data;
    }
    
    public function getConfigueUrl() {
        return '?module=GravityForm&parent=Settings&view=Configure';
    }
    
    public function save($request) { 
        $adb = PearDatabase::getInstance();                    
        $gravityformid = $adb->getUniqueId('vtiger_gravityform');
        
        $config_result = $adb->pquery("SELECT * FROM vtiger_gravityform_config", $params);
        
        while ($row = $adb->fetch_array($config_result)){
            $url = $row['url'];
            $api_key = $row['api_key'];
            $private_key = $row['private_key'];
        }  
                         
        $method  = "GET";
        $route    = "forms/".$request->get('formid'); 
        $route2 = "forms/".$request->get('formid')."/entries";
        $expires = strtotime("+525600 mins");
        $expires2 = strtotime("+525600 mins");  
        $string_to_sign = sprintf("%s:%s:%s:%s", $api_key, $method, $route, $expires);
        $string_to_sign2 = sprintf("%s:%s:%s:%s", $api_key, $method, $route2, $expires2); 
        $signature = self::calculate_signature($string_to_sign, $private_key);
        $signature2 = self::calculate_signature($string_to_sign2, $private_key);  
        
        $sql = 'INSERT INTO vtiger_gravityform(gravityformid, formid, signature,expires,signature2,expires2) VALUES (?,?,?,?,?,?)';
            $params = array($gravityformid, $request->get('formid'), $signature,$expires,$signature2,$expires2);
        $adb->pquery($sql, $params);
        
        return array($gravityformid);
    }
    
    static public function calculate_signature($string, $private_key) {
        $hash = hash_hmac("sha1", $string, $private_key, true);
        $sig = rawurlencode(base64_encode($hash));
        return $sig;
    } 
    
    /*Gravity Field mapping code..shivu*/
    public function getFields() {
        if (!$this->fields) {
            $fieldModelsList = array();
            $fieldIds = $this->getMappingSupportedFieldIdsList();

            foreach ($fieldIds as $fieldId) {
                $fieldModel = Settings_Leads_Field_Model::getInstance($fieldId, $this);
                $fieldModelsList[$fieldModel->getFieldDataType()][$fieldId] = $fieldModel;
            }
            $this->fields = $fieldModelsList;
        }
        return $this->fields;
    }

    /**
     * Function to get mapping supported field ids list
     * @return <Array> list of field ids
     */
    public function getMappingSupportedFieldIdsList() {
        if (!$this->supportedFieldIdsList) {
            $selectedTabidsList[] = getTabid($this->getName());
            $presense = array(0, 2);
            $restrictedFieldNames = array('campaignrelstatus');
            $restrictedUitypes = array(4, 10, 51, 52, 53, 57, 58, 69, 70);
            $selectedGeneratedTypes = array(1, 2);

            $db = PearDatabase::getInstance();
            $query = 'SELECT fieldid FROM vtiger_field
                        WHERE presence IN ('. generateQuestionMarks($presense) .')
                        AND tabid IN ('. generateQuestionMarks($selectedTabidsList) .')
                        AND uitype NOT IN ('. generateQuestionMarks($restrictedUitypes) .')
                        AND fieldname NOT IN ('. generateQuestionMarks($restrictedFieldNames) .')
                        AND generatedtype IN ('.generateQuestionMarks($selectedGeneratedTypes).')';

            $params = array_merge($presense, $selectedTabidsList, $restrictedUitypes,$restrictedFieldNames, $selectedGeneratedTypes);

            $result = $db->pquery($query, $params);
            $numOfRows = $db->num_rows($result);

            $fieldIdsList = array();
            for ($i=0; $i<$numOfRows; $i++) {
                $fieldIdsList[] = $db->query_result($result, $i, 'fieldid');
            }
            $this->supportedFieldIdsList = $fieldIdsList;
        }
        return $this->supportedFieldIdsList;
    }

    /**
     * Function to get instance of module
     * @param <String> $moduleName
     * @return <Settings_GravityForm_Module_Model>
     */
    public static function getInstance($moduleName) {
        $moduleModel = parent::getInstance($moduleName);
        
        $objectProperties = get_object_vars($moduleModel);

        $moduleModel = new self();
        foreach    ($objectProperties as $properName => $propertyValue) {
            $moduleModel->$properName = $propertyValue;
        }
        return $moduleModel;
    }
    
    public function getGravityFields($id){  
        $db = PearDatabase::getInstance();  
        $result = $db->pquery('SELECT id, label FROM vtiger_gravityformfieldlabel WHERE vtiger_gravityformfieldlabel.gravityformid ='.$id);    
        $status = array();
        
        while ($row = $db->fetch_array($result)){
            $status[$row['id']] = array($row['label']);
        }                     
        return $status;
    }
      
    public function save_config($request){
        $db = PearDatabase::getInstance();
        $api_key = $request->get('api_key');
        $private_key = $request->get('private_key');
        $url = $request->get('url'); 
        $result = $db->pquery('SELECT * FROM vtiger_gravityform_config',array());
        if($db->num_rows($result) != 0){
            $db->pquery('UPDATE vtiger_gravityform_config set url=?,api_key=?, private_key=?',array($url,$api_key,$private_key));
        }else {
            $db->pquery('INSERT INTO vtiger_gravityform_config(url,api_key,private_key) values(?,?,?)',array($url,$api_key,$private_key));
        }
    }
    
    public function getAutosync(){
        $db = PearDatabase::getInstance();  
        $result = $db->pquery('SELECT status from vtiger_cron_task where module = "GravityForm"');    
        while ($row = $db->fetch_array($result)){
            $status = $row;
        }               
        return $status;
    }
}