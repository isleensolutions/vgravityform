/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

jQuery.Class("Settings_GravityForm_GravityForm_Js",{},{
    
        /**
     * This function will show the model for Add/Edit tax
     */
    editform : function(url, currentTrElement) {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;
        
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
            
        AppConnector.request(url).then(
            function(data) {  
                var callBackFunction = function(data) {
                    //cache should be empty when modal opened 
                    thisInstance.duplicateCheckCache = {};
                    var form = jQuery('#editform'); 
                    var params = app.validationEngineOptions;
                    params.onValidationComplete = function(form, valid){
                        if(valid) {
                            thisInstance.saveFormDetails(form, currentTrElement);
                            return valid;
                        }
                    }
                    form.validationEngine(params);
                    
                    form.submit(function(e) {
                        e.preventDefault();
                    })
                }
                
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                app.showModalWindow(data,function(data){
                    if(typeof callBackFunction == 'function'){
                        callBackFunction(data);
                    }
                }, {'width':'500px'});
            },
            function(error) {       console.info(error);
                //TODO : Handle error
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
    },
         
     editconfig: function(url, currentTrElement) {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;
        
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
            
        AppConnector.request(url).then(
            function(data) {  
                var callBackFunction = function(data) {
                    //cache should be empty when modal opened 
                    thisInstance.duplicateCheckCache = {};
                    var form = jQuery('#editconfig');
                    
                    var params = app.validationEngineOptions;
                    params.onValidationComplete = function(form, valid){
                        if(valid) {
                            thisInstance.saveConfig(form, currentTrElement);
                            return valid;
                        }
                    }
                    form.validationEngine(params);
                    
                    form.submit(function(e) {
                        e.preventDefault();
                    })
                }
                
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                app.showModalWindow(data,function(data){
                    if(typeof callBackFunction == 'function'){
                        callBackFunction(data);
                    }
                }, {'width':'500px'});
            },
            function(error) {       console.info(error);
                //TODO : Handle error
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
    },
    
    saveConfig: function(form){
        var aDeferred = jQuery.Deferred();
        
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        
        var data = form.serializeFormData();
        data['module'] = app.getModuleName();
        data['parent'] = app.getParentModuleName(); 
        data['action'] = 'Save';

        AppConnector.request(data).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    aDeferred.resolve(data);
                    var result = data.result.success; 
                    if(result){
                        var params = {
                        text: app.vtranslate('Credentials Saved Successfully')
                        };
                        Settings_Vtiger_Index_Js.showMessage(params);
                    } 
                },
                function(error) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    aDeferred.reject(error);
                }
            );
            return aDeferred.promise();
    },
    
    /*
     * Function to Save the Tax Details
     */
    saveFormDetails : function(form, currentTrElement) {
        var thisInstance = this;
        var params = form.serializeFormData();
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });   
        if(typeof params == 'undefined' ) {
            params = {};
        }

        params.module = app.getModuleName();
        params.parent = app.getParentModuleName();
        params.action = 'SaveAjax'; 
        AppConnector.request(params).then(
            function(data) {           
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                app.hideModalWindow();
                var result = data.result.success; 
               // var table = jQuery('.listViewEntriesTable').append('<tr><td width="1%" nowrap="" class="medium"></td><td width="33%" nowrap="" class="listViewEntryValue medium">&nbsp;'+result[0]+'</td><td width="33%" nowrap="" class="listViewEntryValue medium">&nbsp;'+result[1]+'</td><td width="33%" nowrap="" class="listViewEntryValue medium">&nbsp;'+result[3]+'</td><td width="33%" nowrap="" class="listViewEntryValue medium">&nbsp;'+result[4]+'</td><td width="33%" nowrap="" class="listViewEntryValue medium">&nbsp;'+result[5]+'</td></tr>');
                //jQuery('.listViewEntriesDiv').html(table);
                
                //show notification after tax details saved
                var params = {
                            text: app.vtranslate('Credentials Saved Successfully')
                };
                Settings_Vtiger_Index_Js.showMessage(params);
                window.location.href = "index.php?module=GravityForm&view=MappingDetail&parent=Settings&id="+result;
                 
            },
            function(data,err) {
            }
           
        ); 
    },
    
    registerActions : function() {
        var thisInstance = this;
        var container = jQuery('div.container-fluid');
        
        //register click event for Add New button
        container.find('.addform').click(function(e) {   
            var addButton = jQuery(e.currentTarget);   
            var createUrl = addButton.data('url')+'&type='+addButton.data('type');
            thisInstance.editform(createUrl);
        });
        
        container.find('.configure').click(function(e) {   
            var syncButton = jQuery(e.currentTarget);   
            var createUrl = syncButton.data('url')+'&type='+syncButton.data('type');
            thisInstance.editconfig(createUrl);
        });  
        container.find('.update').click(function(e) {
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        }); 
            var updateButton = jQuery(e.currentTarget); 
            var formid = updateButton.data('formid');  
            var createUrl = updateButton.data('url')+'&formid='+formid;
            AppConnector.request(createUrl).then(
            function(data) {           
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                var result = data.result.success; 
                if(!data.result.success){
                    var notifyParams={
                        title:app.vtranslate('JS_FAILED'), 
                        type:'error',
                        width:'25%'
                    };
                }
                else{
                    notifyParams={
                        title:app.vtranslate('JS_FIELDS_UPDATED_SUCCESSFULLY'),  
                        type:'info',
                        width:'25%'
                    };
                }     
                Vtiger_Helper_Js.showPnotify(notifyParams);
            },
            function(data,err) {
            }
           
        ); 
        });
        
    },
    
    registerEvents: function() {
        this.registerActions();
    }
    
});

jQuery(document).ready(function(e){
    var instance = new Settings_GravityForm_GravityForm_Js();
    instance.registerEvents();
    jQuery('.sync').click(function() {
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });  
        var gravityFormid = $(this).attr('data_formid');
        
        var params = {};  
            params['parent'] = 'Settings';
            params['module'] = 'GravityForm';   
            params['action'] = 'sync';
            params['id'] = gravityFormid;
            params['type'] = 'POST';  
    
         AppConnector.request(params).then(
          function(data) {  
          progressIndicatorElement.progressIndicator({'mode':'hide'});           
               if(!data.result.success){
                    var notifyParams={
                        title:app.vtranslate('JS_FAILED'), 
                        type:'error',
                        width:'25%'
                    };
                }
                else{
                    notifyParams={
                        title:app.vtranslate('JS_LEADS_CREATED_SUCCESSFULLY'),  
                        type:'info',
                        width:'25%'
                    };
                }

                Vtiger_Helper_Js.showPnotify(notifyParams);
                location.reload(true);   
          })   
    })
    
    jQuery('.map').click(function() {
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });  
        var gravityFormid = $(this).attr('data_formid');
        var url = $(this).attr('data-url');
        window.location.href = url+gravityFormid;           
    })
    
    jQuery('.deleteRecordButton').click(function() {
        var gravityFormid =  $(this).attr('data');
         var params = {};  
            params['parent'] = 'Settings';
            params['module'] = 'GravityForm';   
            params['action'] = 'delete';
            params['id'] = gravityFormid;
            params['type'] = 'POST';  
    
         AppConnector.request(params).then(
          function(data) {
               location.reload(true);
          })
         
    });
    
    jQuery('#autosync').click(function(){ 
            if(jQuery(this).prop("checked") == true){ 
                jQuery.ajax({
                url: 'index.php?module=GravityForm&view=AutoSync&parent=Settings&mode=checked',
                type: 'POST',
                data: {'hi':'hi'},
                datatype: 'JSON',
                complete: function(response, status) {  
                }
            });  
            }
            else if(jQuery(this).prop("checked") == false){
                jQuery.ajax({
                url: 'index.php?module=GravityForm&view=AutoSync&parent=Settings&mode=unchecked',
                type: 'POST',
                data: {'hi':'hi'},
                datatype: 'JSON',
                complete: function(response, status) {  
                }
            });  
            }
        });   
})