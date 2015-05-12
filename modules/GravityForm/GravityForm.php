<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/


class GravityForm {
	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
			// TODO Handle actions after this module is installed.
            $fieldid = $adb->getUniqueID('vtiger_settings_field');
            $adb->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence,active) VALUES (?,?,?,?,?,?,?,?)',
                array($fieldid,4,'Gravity Form Settings','','','index.php?module='.$moduleName.'&view=Index&parent=Settings','20' ,0));
		} else if($eventType == 'module.disabled') {
			// TODO Handle actions before this module is being uninstalled.
            $adb->pquery("UPDATE vtiger_settings_field SET active=? WHERE name=?", array(1, 'Gravity Form Settings'));
		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
            $adb->pquery("UPDATE vtiger_settings_field SET active=? WHERE name=?", array(0, 'Gravity Form Settings')); 
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
 	}
}