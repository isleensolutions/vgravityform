<?php
/*+**********************************************************************************
 * The contents of this file are subject to the Isleen Solutions Pvt Ltd Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Isleen Solutions Pvt Ltd open source	
 * The Initial Developer of the Original Code is isleen.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_GravityForm_List_View extends Settings_Vtiger_List_View {
	protected $listViewEntries = false;
	protected $listViewHeaders = false;

    
	function __construct() {
		parent::__construct();
	}
    
    function preProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign('AUTOSYNC', Settings_GravityForm_Module_Model::getAutosync()); 
        parent::preProcess($request, true);
    }
        
        
             
	public function process(Vtiger_Request $request) {
        
		$viewer = $this->getViewer($request);
		$this->initializeListViewContents($request, $viewer);
        
		$viewer->view('ListViewContents.tpl', $request->getModule(false));
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$pageNumber = $request->get('page');
		$orderBy = $request->get('orderby');
		$sortOrder = $request->get('sortorder');
		$sourceModule = $request->get('sourceModule');
		$forModule = $request->get('formodule');
		
		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		
		if($sortOrder == "ASC"){
			$nextSortOrder = "DESC";
			$sortImage = "icon-chevron-down";
		}else{
			$nextSortOrder = "ASC";
			$sortImage = "icon-chevron-up";
		}
		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$listViewModel = Settings_Vtiger_ListView_Model::getInstance($qualifiedModuleName);

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);

		if(!empty($searchKey) && !empty($searchValue)) {
			$listViewModel->set('search_key', $searchKey);
			$listViewModel->set('search_value', $searchValue);
		}
		
		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder',$sortOrder);
		}
		if(!empty($sourceModule)) {
			$listViewModel->set('sourceModule', $sourceModule);
		}
		if(!empty($forModule)) {
			$listViewModel->set('formodule', $forModule);
		}
		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		$noOfEntries = count($this->listViewEntries);
		if(!$this->listViewLinks){
			$this->listViewLinks = $listViewModel->getListViewLinks();
		}
		$viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);
        $configure = Settings_GravityForm_Module_Model::getConfigueUrl();
        $viewer->assign('CONFIGURE', $configure); 
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('MODULE_MODEL', $listViewModel->getModule());

		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('PAGE_NUMBER',$pageNumber);

		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('COLUMN_NAME',$orderBy);

		$viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());

		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
			if(!$this->listViewCount){
				$this->listViewCount = $listViewModel->getListViewCount();
			}
			$totalCount = $this->listViewCount;
			$pageLimit = $pagingModel->getPageLimit();
			$pageCount = ceil((int) $totalCount / (int) $pageLimit);

			if($pageCount == 0){
				$pageCount = 1;
			}
			$viewer->assign('PAGE_COUNT', $pageCount);
			$viewer->assign('LISTVIEW_COUNT', $totalCount);
		}
	}     
	
}
