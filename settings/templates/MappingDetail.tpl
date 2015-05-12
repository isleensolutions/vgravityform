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
    <input type="hidden" value="{$FORMID}" name="formid">
	<div class="container-fluid">
		<div class="row-fluid settingsHeader padding1per">
			<span class="span8">
				<span class="font-x-x-large">{vtranslate('LBL_GRAVITY_FORM_FIELD_MAPPING', $QUALIFIED_MODULE)}</span>
			</span>
			<span class="span4">
				<span class="pull-right">            
                    <button class="btn btn-info update" data-url="?module=GravityForm&parent=Settings&action=Update" data-formid="{$FORMID}">Update Fields</button> 
                    &nbsp;&nbsp;&nbsp;   
					{foreach item=LINK_MODEL from=$MODULE_MODEL->getDetailViewLinks()}   
						<button type="button" data-formid="{$FORMID}" class="btn" onclick={$URL}><strong>{vtranslate($LINK_MODEL->getLabel(), $QUALIFIED_MODULE)}</strong></button>
					{/foreach}
				</span>
			</span>
		</div><hr>
		<div class="contents" id="detailView">
			<table class="table table-bordered" width="100%">
				<tbody>
					<tr class="blockHeader">
						<th class="blockHeader" width="15%">{vtranslate('LBL_FIELD_LABEL', $QUALIFIED_MODULE)}</th>   
						<th class="blockHeader textAlignCenter" colspan="3" width="70%">{vtranslate('LBL_GRAVITY_FIELD_LABEL', $QUALIFIED_MODULE)}</th>
					</tr>
					<tr>
						{foreach key=key item=LABEL from=$MODULE_MODEL->getHeaders()}
							<td width="15%"><b>{vtranslate($LABEL, $LABEL)}</b></td>
						{/foreach}
					</tr>
					{foreach key=MAPPING_ID item=MAPPING from=$MODULE_MODEL->getMapping({$FORMID})}
						<tr class="listViewEntries" data-cfmid="{$MAPPING_ID}">
							<td width="15%">{vtranslate({$MAPPING['Leads']['label']}, 'Leads')}</td>                
							<td width="13%">{vtranslate({$MAPPING['Gravity']['label']}, 'Gravity')}</td>

						</tr>
						
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
{/strip}