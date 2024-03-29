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
    
    
    
    class revisionPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Инвентаризация');
            $this->SetMenuLabel('Инвентаризация');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $this->dataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`revision`');
            $this->dataset->addFields(
                array(
                    new IntegerField('idrevision', true, true),
                    new DateTimeField('date'),
                    new StringField('info'),
                    new IntegerField('material_idmaterial', true),
                    new IntegerField('employee_idemployee'),
                    new StringField('count'),
                    new IntegerField('storage', true)
                )
            );
            $this->dataset->AddLookupField('material_idmaterial', 'material', new IntegerField('idmaterial'), new StringField('invnumber', false, false, false, false, 'material_idmaterial_invnumber', 'material_idmaterial_invnumber_material'), 'material_idmaterial_invnumber_material');
            $this->dataset->AddLookupField('employee_idemployee', 'employee', new IntegerField('idemployee'), new StringField('name', false, false, false, false, 'employee_idemployee_name', 'employee_idemployee_name_employee'), 'employee_idemployee_name_employee');
            $this->dataset->AddLookupField('storage', '`storage`', new IntegerField('idstorage'), new StringField('storagename', false, false, false, false, 'storage_storagename', 'storage_storagename_storage'), 'storage_storagename_storage');
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
                new FilterColumn($this->dataset, 'idrevision', 'idrevision', 'Idrevision'),
                new FilterColumn($this->dataset, 'date', 'date', 'Дата'),
                new FilterColumn($this->dataset, 'info', 'info', 'Информация'),
                new FilterColumn($this->dataset, 'material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер'),
                new FilterColumn($this->dataset, 'employee_idemployee', 'employee_idemployee_name', 'Сотрудник'),
                new FilterColumn($this->dataset, 'count', 'count', 'Количество'),
                new FilterColumn($this->dataset, 'storage', 'storage_storagename', 'Место хранения')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['date'])
                ->addColumn($columns['info'])
                ->addColumn($columns['material_idmaterial'])
                ->addColumn($columns['employee_idemployee'])
                ->addColumn($columns['count'])
                ->addColumn($columns['storage']);
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
            // View column for date field
            //
            $column = new DateTimeViewColumn('date', 'date', 'Дата', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_info_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_material_idmaterial_invnumber_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('employee_idemployee', 'employee_idemployee_name', 'Сотрудник', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for count field
            //
            $column = new TextViewColumn('count', 'count', 'Количество', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for storagename field
            //
            $column = new TextViewColumn('storage', 'storage_storagename', 'Место хранения', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for date field
            //
            $column = new DateTimeViewColumn('date', 'date', 'Дата', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_info_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_material_idmaterial_invnumber_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('employee_idemployee', 'employee_idemployee_name', 'Сотрудник', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for count field
            //
            $column = new TextViewColumn('count', 'count', 'Количество', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for storagename field
            //
            $column = new TextViewColumn('storage', 'storage_storagename', 'Место хранения', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for date field
            //
            $editor = new DateTimeEdit('date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Дата', 'date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for info field
            //
            $editor = new TextAreaEdit('info_edit', 50, 8);
            $editColumn = new CustomEditColumn('Информация', 'info', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for material_idmaterial field
            //
            $editor = new ComboBox('material_idmaterial_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`material`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idmaterial', true, true, true),
                    new StringField('namematerial'),
                    new StringField('invnumber'),
                    new IntegerField('category_idcategory'),
                    new StringField('category'),
                    new BlobField('qrcode')
                )
            );
            $lookupDataset->setOrderByField('invnumber', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Инвентарный номер', 
                'material_idmaterial', 
                $editor, 
                $this->dataset, 'idmaterial', 'invnumber', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for employee_idemployee field
            //
            $editor = new ComboBox('employee_idemployee_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`employee`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idemployee', true, true, true),
                    new StringField('name'),
                    new StringField('phone'),
                    new StringField('info'),
                    new IntegerField('position_idposition')
                )
            );
            $lookupDataset->setOrderByField('name', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Сотрудник', 
                'employee_idemployee', 
                $editor, 
                $this->dataset, 'idemployee', 'name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for count field
            //
            $editor = new TextEdit('count_edit');
            $editor->SetMaxLength(10);
            $editColumn = new CustomEditColumn('Количество', 'count', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for storage field
            //
            $editor = new ComboBox('storage_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`storage`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idstorage', true, true),
                    new StringField('storagename'),
                    new StringField('storageadress'),
                    new StringField('storagedesc')
                )
            );
            $lookupDataset->setOrderByField('storagename', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Место хранения', 
                'storage', 
                $editor, 
                $this->dataset, 'idstorage', 'storagename', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for date field
            //
            $editor = new DateTimeEdit('date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Дата', 'date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for info field
            //
            $editor = new TextAreaEdit('info_edit', 50, 8);
            $editColumn = new CustomEditColumn('Информация', 'info', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for material_idmaterial field
            //
            $editor = new ComboBox('material_idmaterial_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`material`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idmaterial', true, true, true),
                    new StringField('namematerial'),
                    new StringField('invnumber'),
                    new IntegerField('category_idcategory'),
                    new StringField('category'),
                    new BlobField('qrcode')
                )
            );
            $lookupDataset->setOrderByField('invnumber', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Инвентарный номер', 
                'material_idmaterial', 
                $editor, 
                $this->dataset, 'idmaterial', 'invnumber', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for employee_idemployee field
            //
            $editor = new ComboBox('employee_idemployee_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`employee`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idemployee', true, true, true),
                    new StringField('name'),
                    new StringField('phone'),
                    new StringField('info'),
                    new IntegerField('position_idposition')
                )
            );
            $lookupDataset->setOrderByField('name', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Сотрудник', 
                'employee_idemployee', 
                $editor, 
                $this->dataset, 'idemployee', 'name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for count field
            //
            $editor = new TextEdit('count_edit');
            $editor->SetMaxLength(10);
            $editColumn = new CustomEditColumn('Количество', 'count', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for storage field
            //
            $editor = new ComboBox('storage_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`storage`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idstorage', true, true),
                    new StringField('storagename'),
                    new StringField('storageadress'),
                    new StringField('storagedesc')
                )
            );
            $lookupDataset->setOrderByField('storagename', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Место хранения', 
                'storage', 
                $editor, 
                $this->dataset, 'idstorage', 'storagename', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for date field
            //
            $editor = new DateTimeEdit('date_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Дата', 'date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for info field
            //
            $editor = new TextAreaEdit('info_edit', 50, 8);
            $editColumn = new CustomEditColumn('Информация', 'info', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for material_idmaterial field
            //
            $editor = new ComboBox('material_idmaterial_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`material`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idmaterial', true, true, true),
                    new StringField('namematerial'),
                    new StringField('invnumber'),
                    new IntegerField('category_idcategory'),
                    new StringField('category'),
                    new BlobField('qrcode')
                )
            );
            $lookupDataset->setOrderByField('invnumber', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Инвентарный номер', 
                'material_idmaterial', 
                $editor, 
                $this->dataset, 'idmaterial', 'invnumber', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for employee_idemployee field
            //
            $editor = new ComboBox('employee_idemployee_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`employee`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idemployee', true, true, true),
                    new StringField('name'),
                    new StringField('phone'),
                    new StringField('info'),
                    new IntegerField('position_idposition')
                )
            );
            $lookupDataset->setOrderByField('name', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Сотрудник', 
                'employee_idemployee', 
                $editor, 
                $this->dataset, 'idemployee', 'name', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for count field
            //
            $editor = new TextEdit('count_edit');
            $editor->SetMaxLength(10);
            $editColumn = new CustomEditColumn('Количество', 'count', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for storage field
            //
            $editor = new ComboBox('storage_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`storage`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idstorage', true, true),
                    new StringField('storagename'),
                    new StringField('storageadress'),
                    new StringField('storagedesc')
                )
            );
            $lookupDataset->setOrderByField('storagename', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Место хранения', 
                'storage', 
                $editor, 
                $this->dataset, 'idstorage', 'storagename', $lookupDataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
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
            // View column for date field
            //
            $column = new DateTimeViewColumn('date', 'date', 'Дата', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddPrintColumn($column);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_info_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_material_idmaterial_invnumber_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('employee_idemployee', 'employee_idemployee_name', 'Сотрудник', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for count field
            //
            $column = new TextViewColumn('count', 'count', 'Количество', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for storagename field
            //
            $column = new TextViewColumn('storage', 'storage_storagename', 'Место хранения', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for date field
            //
            $column = new DateTimeViewColumn('date', 'date', 'Дата', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddExportColumn($column);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_info_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_material_idmaterial_invnumber_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('employee_idemployee', 'employee_idemployee_name', 'Сотрудник', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for count field
            //
            $column = new TextViewColumn('count', 'count', 'Количество', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for storagename field
            //
            $column = new TextViewColumn('storage', 'storage_storagename', 'Место хранения', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for date field
            //
            $column = new DateTimeViewColumn('date', 'date', 'Дата', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddCompareColumn($column);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_info_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('revisionGrid_material_idmaterial_invnumber_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for name field
            //
            $column = new TextViewColumn('employee_idemployee', 'employee_idemployee_name', 'Сотрудник', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for count field
            //
            $column = new TextViewColumn('count', 'count', 'Количество', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for storagename field
            //
            $column = new TextViewColumn('storage', 'storage_storagename', 'Место хранения', $this->dataset);
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
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_info_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_material_idmaterial_invnumber_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_info_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_material_idmaterial_invnumber_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_info_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_material_idmaterial_invnumber_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for info field
            //
            $column = new TextViewColumn('info', 'info', 'Информация', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_info_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for invnumber field
            //
            $column = new TextViewColumn('material_idmaterial', 'material_idmaterial_invnumber', 'Инвентарный номер', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'revisionGrid_material_idmaterial_invnumber_handler_view', $column);
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
        $Page = new revisionPage("revision", "revision.php", GetCurrentUserPermissionSetForDataSource("revision"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("revision"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
