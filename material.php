<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page_includes.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthentication()->applyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class materialPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Материалы');
            $this->SetMenuLabel('Материалы');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $this->dataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`material`');
            $this->dataset->addFields(
                array(
                    new IntegerField('idmaterial', true, true, true),
                    new StringField('namematerial'),
                    new StringField('invnumber'),
                    new IntegerField('category_idcategory'),
                    new StringField('category'),
                    new BlobField('qrcode')
                )
            );
            $this->dataset->AddLookupField('category_idcategory', 'category', new IntegerField('idcategory'), new StringField('category_name', false, false, false, false, 'category_idcategory_category_name', 'category_idcategory_category_name_category'), 'category_idcategory_category_name_category');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'idmaterial', 'idmaterial', 'Idmaterial'),
                new FilterColumn($this->dataset, 'namematerial', 'namematerial', 'Наименование материала'),
                new FilterColumn($this->dataset, 'category', 'category', 'Категория'),
                new FilterColumn($this->dataset, 'category_idcategory', 'category_idcategory_category_name', 'Категория'),
                new FilterColumn($this->dataset, 'invnumber', 'invnumber', 'Инвентарный номер'),
                new FilterColumn($this->dataset, 'qrcode', 'qrcode', 'QR-код')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['namematerial'])
                ->addColumn($columns['category_idcategory'])
                ->addColumn($columns['invnumber'])
                ->addColumn($columns['qrcode']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
            
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_namematerial_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_category_idcategory_category_name_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_invnumber_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for qrcode field
            //
            $column = new BlobImageViewColumn('qrcode', 'qrcode', 'QR-код', $this->dataset, false, 'materialGrid_qrcode_handler_list');
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_namematerial_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_category_idcategory_category_name_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_invnumber_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for qrcode field
            //
            $column = new BlobImageViewColumn('qrcode', 'qrcode', 'QR-код', $this->dataset, false, 'materialGrid_qrcode_handler_view');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for namematerial field
            //
            $editor = new TextAreaEdit('namematerial_edit', 50, 8);
            $editColumn = new CustomEditColumn('Наименование материала', 'namematerial', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for category_idcategory field
            //
            $editor = new ComboBox('category_idcategory_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`category`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcategory', true, true, true),
                    new StringField('category_name'),
                    new StringField('description')
                )
            );
            $lookupDataset->setOrderByField('category_name', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Категория', 
                'category_idcategory', 
                $editor, 
                $this->dataset, 'idcategory', 'category_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for invnumber field
            //
            $editor = new TextAreaEdit('invnumber_edit', 50, 8);
            $editColumn = new CustomEditColumn('Инвентарный номер', 'invnumber', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for qrcode field
            //
            $editor = new ImageUploader('qrcode_edit');
            $editor->SetShowImage(false);
            $editColumn = new FileUploadingColumn('QR-код', 'qrcode', $editor, $this->dataset, false, false, 'materialGrid_qrcode_handler_edit');
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for namematerial field
            //
            $editor = new TextAreaEdit('namematerial_edit', 50, 8);
            $editColumn = new CustomEditColumn('Наименование материала', 'namematerial', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for category_idcategory field
            //
            $editor = new ComboBox('category_idcategory_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`category`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcategory', true, true, true),
                    new StringField('category_name'),
                    new StringField('description')
                )
            );
            $lookupDataset->setOrderByField('category_name', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Категория', 
                'category_idcategory', 
                $editor, 
                $this->dataset, 'idcategory', 'category_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for invnumber field
            //
            $editor = new TextAreaEdit('invnumber_edit', 50, 8);
            $editColumn = new CustomEditColumn('Инвентарный номер', 'invnumber', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for qrcode field
            //
            $editor = new ImageUploader('qrcode_edit');
            $editor->SetShowImage(false);
            $editColumn = new FileUploadingColumn('QR-код', 'qrcode', $editor, $this->dataset, false, false, 'materialGrid_qrcode_handler_multi_edit');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for namematerial field
            //
            $editor = new TextAreaEdit('namematerial_edit', 50, 8);
            $editColumn = new CustomEditColumn('Наименование материала', 'namematerial', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for category_idcategory field
            //
            $editor = new ComboBox('category_idcategory_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`category`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcategory', true, true, true),
                    new StringField('category_name'),
                    new StringField('description')
                )
            );
            $lookupDataset->setOrderByField('category_name', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Категория', 
                'category_idcategory', 
                $editor, 
                $this->dataset, 'idcategory', 'category_name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for invnumber field
            //
            $editor = new TextAreaEdit('invnumber_edit', 50, 8);
            $editColumn = new CustomEditColumn('Инвентарный номер', 'invnumber', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for qrcode field
            //
            $editor = new ImageUploader('qrcode_edit');
            $editor->SetShowImage(false);
            $editColumn = new FileUploadingColumn('QR-код', 'qrcode', $editor, $this->dataset, false, false, 'materialGrid_qrcode_handler_insert');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_namematerial_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_category_idcategory_category_name_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_invnumber_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for qrcode field
            //
            $column = new BlobImageViewColumn('qrcode', 'qrcode', 'QR-код', $this->dataset, false, 'materialGrid_qrcode_handler_print');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_namematerial_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_category_idcategory_category_name_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_invnumber_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for qrcode field
            //
            $column = new BlobImageViewColumn('qrcode', 'qrcode', 'QR-код', $this->dataset, false, 'materialGrid_qrcode_handler_export');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_namematerial_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_category_idcategory_category_name_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('materialGrid_invnumber_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for qrcode field
            //
            $column = new BlobImageViewColumn('qrcode', 'qrcode', 'QR-код', $this->dataset, false, 'materialGrid_qrcode_handler_compare');
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->setAllowSortingByDialog(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(false);
            $result->setAllowAddMultipleRecords(false);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && false);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(false);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(false);
            $this->setAllowPrintSelectedRecords(false);
            $this->setExportListAvailable(array());
            $this->setExportSelectedRecordsAvailable(array());
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array());
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_namematerial_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_category_idcategory_category_name_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_invnumber_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_list', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_namematerial_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_category_idcategory_category_name_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_invnumber_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_print', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_namematerial_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_category_idcategory_category_name_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_invnumber_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_compare', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_insert', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for namematerial field
            //
            $column = new TextViewColumn('namematerial', 'namematerial', 'Наименование материала', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_namematerial_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for category_name field
            //
            $column = new TextViewColumn('category_idcategory', 'category_idcategory_category_name', 'Категория', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_category_idcategory_category_name_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('invnumber', 'invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'materialGrid_invnumber_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_view', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_edit', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'qrcode', 'materialGrid_qrcode_handler_multi_edit', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
    
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }

    SetUpUserAuthorization();

    try
    {
        $Page = new materialPage("material", "material.php", GetCurrentUserPermissionSetForDataSource("material"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("material"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
