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
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type="hidden" value="{$ORDER_BY}" id="orderBy">
<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">

<div class="listViewEntriesDiv" style='overflow-x:auto;'>
    <span class="listViewLoadingImageBlock hide modal" id="loadingListViewModal">
        <img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
        <p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
    </span>
    {assign var="NAME_FIELDS" value=$MODULE_MODEL->getNameFields()}
    {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
    <table class="table table-bordered table-condensed listViewEntriesTable">
        <thead>
            <tr class="listViewHeaders">
                <th width="1%" class="{$WIDTHTYPE}"></th>
                {assign var=WIDTH value={99/(count($LISTVIEW_HEADERS))}}
                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                <th width="{$WIDTH}%" nowrap {if $LISTVIEW_HEADER@last}colspan="2" {/if} class="{$WIDTHTYPE}">
                    <a  {if !($LISTVIEW_HEADER->has('sort'))} class="listViewHeaderValues cursorPointer" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('name')}" {/if}>{vtranslate($LISTVIEW_HEADER->get('label'), $QUALIFIED_MODULE)}
                        {if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
                </th>
                {/foreach}
            </tr>
        </thead>
        <tbody>
        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
            <tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY->getId()}"
                    {if method_exists($LISTVIEW_ENTRY,'getDetailViewUrl')}data-recordurl="{$LISTVIEW_ENTRY->getDetailViewUrl()}"{/if}
             >
            <td width="1%" nowrap class="{$WIDTHTYPE}">
                {if $MODULE eq 'CronTasks'}
                    <img src="{vimage_path('drag.png')}" class="alignTop" title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}" />
                {/if}
            </td>
                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                    {assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
                    {assign var=LAST_COLUMN value=$LISTVIEW_HEADER@last}
                    <td class="listViewEntryValue {$WIDTHTYPE}"  width="{$WIDTH}%" nowrap>
                        &nbsp;{$LISTVIEW_ENTRY->getDisplayValue($LISTVIEW_HEADERNAME)}
                        {if $LAST_COLUMN && $LISTVIEW_ENTRY->getEditViewUrl()}
                            </td>
                            <td><button class="btn btn-info update" data-url="?module=GravityForm&parent=Settings&action=Update" data-formid="{$LISTVIEW_ENTRY->getDisplayValue('formid')}">{vtranslate('LBL_UPDATE_FIELDS', $QUALIFIED_MODULE)}</button>       </td>
                            <td><button class="btn btn-primary map" data-url="?module=GravityForm&view=MappingDetail&parent=Settings&id=" data-type="0" data_formid="{$LISTVIEW_ENTRY->getDisplayValue('gravityformid')}">Map</button> </td>
                            <td>  
                                {foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}      
                                    <button class="btn btn-success sync" data-url="?module=GravityForm&parent=Settings&view=MappingEdit" data-type="0" data_formid="{$LISTVIEW_ENTRY->getDisplayValue('gravityformid')}">Sync</button> 
                                {/foreach}
                            </td>    
                            
                            <td nowrap class="{$WIDTHTYPE}">  
                                <div class="pull-right actions">
                                    <span class="actionImages"> 
                                         <a class="deleteRecordButton" data="{$LISTVIEW_ENTRY->getDisplayValue('gravityformid')}"><i class="icon-trash alignMiddle" title="Delete"></i></a>
                                    </span>
                                </div>
                            </td> 
                        {/if}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>

    <!--added this div for Temporarily -->
    {if $LISTVIEW_ENTRIES_COUNT eq '0'}
    <table class="emptyRecordsDiv">
        <tbody>
            <tr>
                <td>
                    {vtranslate('LBL_NO')} {vtranslate($MODULE, $QUALIFIED_MODULE)} {vtranslate('LBL_FOUND')}
                </td>
            </tr>
        </tbody>
    </table>
    {/if}
</div>
{/strip}