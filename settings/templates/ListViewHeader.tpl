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
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate($MODULE, $QUALIFIED_MODULE)}</h3>
    </div>
    <hr>
    <div class="row-fluid">
        <span class="span8 btn-toolbar">
            {foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
             <button type="button" class="btn addform addButton" data-url="{$LISTVIEW_BASICACTION->getUrl()}" data-type="0">
                <i class="icon-plus"></i>&nbsp;
                <strong>{vtranslate('LBL_ADD_FORM', $QUALIFIED_MODULE)}</strong>
            </button> &nbsp;&nbsp;
            <button type="button" class="btn configure addButton" data-url="{$CONFIGURE}" data-type="0">
                <i class="icon-plus"></i>&nbsp;
                <strong>{vtranslate('LBL_CONFIGURE', $QUALIFIED_MODULE)}</strong>
            </button>
            {/foreach}
        </span>
        <span class="span4 btn-toolbar">
                    {if $AUTOSYNC['0'] == '0'}
                        <input type="checkbox" name="auot_sync" id="autosync"> Auto Sync &nbsp;&nbsp;&nbsp;
                    {/if}
                    {if $AUTOSYNC['0'] == '1'}
                        <input type="checkbox" name="auot_sync" checked="checked" id="autosync"> Auto Sync &nbsp;&nbsp;&nbsp;
                    {/if}
            {include file='ListViewActions.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
        </span>
    </div>
    <div class="clearfix"></div>      
    <div class="listViewContentDiv" id="listViewContents">
{/strip}