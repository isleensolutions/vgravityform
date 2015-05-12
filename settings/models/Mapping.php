<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_Mapping_Model extends Settings_Vtiger_Module_Model {

	var $name = 'GravityForm';

	/**
	 * Function to get detail view url of this model
	 * @return <String> url
	 */
	public function getDetailViewUrl() {
		return 'index.php?parent='. $this->getParentName() .'&module=GravityForm&view=MappingDetail';
	}

	/**
	 * Function to get edit view url of this model
	 * @return <String> url
	 */
	public function getEditViewUrl() {
		return 'index.php?parent='. $this->getParentName() .'&module=GravityForm&view=MappingEdit';
	}

	/**
	 * Function to get delete url of this mapping model
	 * @return <String> url
	 */
	public function getMappingDeleteUrl() {
		return 'index.php?parent='. $this->getParentName() .'&module=GravityForm&action=MappingDelete';
	}

	/**
	 * Function to get headers for detail view
	 * @return <Array> headers list
	 */
	public function getHeaders() {
		return array('Leads' => 'Leads', 'GravityForm' => 'GravityForm');
	}

	/**
	 * Function to get list of detail view link models
	 * @return <Array> list of detail view link models <Vtiger_Link_Model>
	 */
	public function getDetailViewLinks() {
		return array(Vtiger_Link_Model::getInstanceFromValues(array(
				'linktype' => 'DETAILVIEW',
				'linklabel' => 'LBL_EDIT',
				'linkurl' => 'javascript:Settings_GravityFieldMapping_Js.triggerEdit("'. $this->getEditViewUrl() .'")',
				'linkicon' => ''
				)));
	}

	/**
	 * Function to get list of mapping link models
	 * @return <Array> list of mapping link models <Vtiger_Link_Model>
	 */
	public function getMappingLinks() {
		return array(Vtiger_Link_Model::getInstanceFromValues(array(
				'linktype' => 'DETAILVIEW',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => 'javascript:Settings_GravityFieldMapping_Js.triggerDelete(event,"'. $this->getMappingDeleteUrl() .'")',
				'linkicon' => ''
				)));
	}

	/**
	 * Function to get mapping details
	 * @return <Array> list of mapping details
	 */
	public function getMapping($formid, $editable = false) { 
		if (!$this->mapping) {
			$db = PearDatabase::getInstance();
			$query = 'SELECT * FROM vtiger_gravityformfieldmapping';
			if ($editable) {
				$query .= ' WHERE editable = 1 AND gravityformid = '.$formid;
			}
            else{
                $query .= ' WHERE gravityformid = '.$formid;
            }
            
			$result = $db->pquery($query, array());   
			$numOfRows = $db->num_rows($result);
            $mapping = array();
			for ($i=0; $i<$numOfRows; $i++) {
				$rowData = $db->query_result_rowdata($result, $i);
				$mapping[$rowData['mid']] = $rowData;
			}

			$finalMapping = $fieldIdsList = $fieldIdsList1 = array(); 
			foreach ($mapping as $mappingDetails) { 
                array_push($fieldIdsList, $mappingDetails['leadfid']);
                array_push($fieldIdsList1, $mappingDetails['gravityfid']);
            }  
            
            $fieldLabelsList1 = $fieldLabelsList = array(); 
            if(!empty($fieldIdsList) && !empty($fieldIdsList1)){
                $fieldLabelsList = $this->getFieldsInfo(array_unique($fieldIdsList));
                $fieldLabelsList1 = $this->getFieldsInfo1(array_unique($fieldIdsList1), $formid);
            }               
            
			foreach ($mapping as $mappingId => $mappingDetails) {   
				$finalMapping[$mappingId] = array(
						'editable'	=> $mappingDetails['editable'],
						'Leads'		=> $fieldLabelsList[$mappingDetails['leadfid']],   
						'Gravity'	=> $fieldLabelsList1[$mappingDetails['gravityfid']]
				);
			}  
			$this->mapping = $finalMapping;
		}                
		return $this->mapping;
	}
    
	public function getFieldsInfo($fieldIdsList) {
		$leadModel = Vtiger_Module_Model::getInstance($this->getName());
		$leadId = $leadModel->getId();

		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT fieldid, fieldlabel, uitype, typeofdata, fieldname, tablename, tabid FROM vtiger_field WHERE fieldid IN ('. generateQuestionMarks($fieldIdsList). ')', $fieldIdsList);
		$numOfRows = $db->num_rows($result);

		$fieldLabelsList = array();
		for ($i=0; $i<$numOfRows; $i++) {
			$rowData = $db->query_result_rowdata($result, $i);

			$fieldInfo = array('id' => $rowData['fieldid'], 'label' => $rowData['fieldlabel']);
			if ($rowData['tabid'] === $leadId) {
				$fieldModel = Settings_Leads_Field_Model::getCleanInstance();
				$fieldModel->set('uitype', $rowData['uitype']);
				$fieldModel->set('typeofdata', $rowData['typeofdata']);
				$fieldModel->set('name', $rowData['fieldname']);
				$fieldModel->set('table', $rowData['tablename']);

				$fieldInfo['fieldDataType'] = $fieldModel->getFieldDataType();
			}

			$fieldLabelsList[$rowData['fieldid']] = $fieldInfo;
		}
		return $fieldLabelsList;
	}
    
	public function save($mapping) {
		$db = PearDatabase::getInstance();
        $formid = $mapping['formid'];
		$deleteMappingsList = $updateMappingsList = $createMappingsList = array();
		foreach ($mapping as $mappingDetails) {
			$mappingId = $mappingDetails['mappingId'];  
            
			if ($mappingDetails['lead']) {
				if ($mappingId) {
                    if(is_array($mappingDetails)){  
					if ((array_key_exists('deletable', $mappingDetails)) || (!$mappingDetails['gravity'])) {
						$deleteMappingsList[] = $mappingId;
					} else {
						if ($mappingDetails['gravity']) {
							$updateMappingsList[] = $mappingDetails;
						}
					}
                }
				} else {
					if ($mappingDetails['gravity']) {
						$createMappingsList[] = $mappingDetails;
					}
				}
			}
		}
        
		if($deleteMappingsList) {
			$db->pquery('DELETE FROM vtiger_gravityformfieldmapping WHERE editable = 1 AND mid IN ('. generateQuestionMarks($deleteMappingsList) .')', $deleteMappingsList);
		}

		if ($createMappingsList) {
			$insertQuery = 'INSERT INTO vtiger_gravityformfieldmapping(leadfid, gravityfid, gravityformid) VALUES ';

			$count = count($createMappingsList);
			for ($i=0; $i<$count; $i++) {
				$mappingDetails = $createMappingsList[$i];
				$insertQuery .= '('. $mappingDetails['lead'] .', '. $mappingDetails['gravity'] .','.$formid.')';
				if ($i !== $count-1) {
					$insertQuery .= ', ';
				}
			} 
            
			$db->pquery($insertQuery, array());
		}
        
		if ($updateMappingsList) {
			$leadQuery		= ' SET leadfid = CASE ';  
			$contactQuery	= ' gravityfid = CASE ';    

			foreach ($updateMappingsList as $mappingDetails) {
				$mappingId		 = $mappingDetails['mappingId'];
				$leadQuery		.= " WHEN mid = $mappingId THEN ". $mappingDetails['lead'];     
				$contactQuery	.= " WHEN mid = $mappingId THEN ". $mappingDetails['gravity'];      
			}
			$leadQuery		.= ' ELSE leadfid END ';     
			$contactQuery	.= ' ELSE gravityfid END ';  
			$res = $db->pquery("UPDATE vtiger_gravityformfieldmapping $leadQuery, $contactQuery WHERE editable = ?", array(1));
     		}
	}
    
	public static function getRestrictedFieldIdsList() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_convertleadmapping WHERE editable = ?', array(0));
		$numOfRows = $db->num_rows($result);

		$restrictedIdsList = array();
		for ($i=0; $i<$numOfRows; $i++) {
			$rowData = $db->query_result_rowdata($result, $i);
			if ($rowData['accountfid']) {
				$restrictedIdsList[] = $rowData['accountfid'];
			}
			if ($rowData['contactfid']) {
				$restrictedIdsList[] = $rowData['contactfid'];
			}
			if ($rowData['potentialfid']) {
				$restrictedIdsList[] = $rowData['potentialfid'];
			}
		}
		return $restrictedIdsList;
	}
     
	public static function getSupportedModulesList() {
		return array('Accounts', 'Contacts', 'Potentials');
	}
 
	public static function getInstance($formid, $editable = false) {
		$instance = new self();
		$instance->getMapping($formid, $editable);
		return $instance;
	}  
	public static function getCleanInstance() {
		return new self();
	}
    
	public static function deleteMapping($mappingIdsList) {
		$db = PearDatabase::getInstance();
		$db->pquery('DELETE FROM vtiger_convertleadmapping WHERE cfmid IN ('. generateQuestionMarks($mappingIdsList). ')', $mappingIdsList);
	}
    
    public function getFieldsInfo1($fieldIdsList1, $formid) {
        $leadModel = Vtiger_Module_Model::getInstance($this->getName());
        $leadId = $leadModel->getId();

        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT id, label FROM vtiger_gravityformfieldlabel WHERE gravityformid = '.$formid.' AND id IN ('. generateQuestionMarks($fieldIdsList1). ')', $fieldIdsList1);
        $numOfRows = $db->num_rows($result);
        
        $fieldLabelsList = array();
        for ($i=0; $i<$numOfRows; $i++) {
            $rowData = $db->query_result_rowdata($result, $i);

            $fieldInfo = array('id' => $rowData['id'], 'label' => $rowData['label']);
           

            $fieldLabelsList[$rowData['id']] = $fieldInfo;
        }                      
        return $fieldLabelsList;
                                   
    }
}
