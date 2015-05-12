{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
{strip}
    <div class="taxModalContainer">
        <div class="modal-header contentsBackground">
            <button class="close vtButton" data-dismiss="modal">×</button>
            <h3>Gravity Form</h3>
        </div>
        <form id="editform" class="form-horizontal" method="POST"> 
            <div class="modal-body">
                <div class="row-fluid">
                    <div class="control-group">
                        <label class="control-label">Form ID</label> 
                        <div class="controls">
                            <input class="span3" type="text" name="formid" placeholder="Enter Form ID" value="" data-validation-engine='validate[required]' />
                        </div>   
                    </div>      
                </div>
            </div>
            {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
        </form>
    </div>
{/strip}