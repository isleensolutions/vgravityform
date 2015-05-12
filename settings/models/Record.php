<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Profiles Record Model Class
 */
class Settings_GravityForm_Record_Model extends Settings_Vtiger_Record_Model {
    
    /**
     * Function to get the Id
     * @return <Number> Profile Id
     */
    public function getId() {
        return $this->get('gravityformid');
    }
    /**
     * Function to get the Id
     * @return <Number> Profile Id
     */
    protected function setId($id) {
        $this->set('gravityformid', $id);
        return $this;
    }
    
    /**
     * Function to get Name of this record instance
     * @return <String> Name
     */
    public function getName() {
        return '';
    }
    
    public function getEditViewUrl() {
        return '?module=Profiles&parent=Settings&view=EditAjax&record='.$this->getId();
    }
    
    
 
}