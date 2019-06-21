<?php

require_once 'components/application.php';
require_once 'components/page/page.php';
require_once 'components/security/permission_set.php';
require_once 'components/security/user_authentication/hard_coded_user_authentication.php';
require_once 'components/security/grant_manager/hard_coded_user_grant_manager.php';

include_once 'components/security/user_identity_storage/user_identity_session_storage.php';

$users = array('Admin' => 'admin');

$grants = array('guest' => 
        array()
    ,
    'defaultUser' => 
        array('category' => new PermissionSet(false, false, false, false),
        'employee' => new PermissionSet(false, false, false, false),
        'material' => new PermissionSet(false, false, false, false),
        'position' => new PermissionSet(false, false, false, false),
        'revision' => new PermissionSet(false, false, false, false),
        'storage' => new PermissionSet(false, false, false, false))
    ,
    'guest' => 
        array('category' => new PermissionSet(false, false, false, false),
        'employee' => new PermissionSet(false, false, false, false),
        'material' => new PermissionSet(false, false, false, false),
        'position' => new PermissionSet(false, false, false, false),
        'revision' => new PermissionSet(false, false, false, false),
        'storage' => new PermissionSet(false, false, false, false))
    ,
    'Admin' => 
        array('category' => new PermissionSet(false, false, false, false),
        'employee' => new PermissionSet(false, false, false, false),
        'material' => new PermissionSet(false, false, false, false),
        'position' => new PermissionSet(false, false, false, false),
        'revision' => new PermissionSet(false, false, false, false),
        'storage' => new PermissionSet(false, false, false, false))
    );

$appGrants = array('guest' => new PermissionSet(false, false, false, false),
    'defaultUser' => new PermissionSet(true, false, false, false),
    'guest' => new PermissionSet(false, false, false, false),
    'Admin' => new AdminPermissionSet());

$dataSourceRecordPermissions = array();

$tableCaptions = array('category' => 'Категории',
'employee' => 'Сотрудники',
'material' => 'Материалы',
'position' => 'Должности',
'revision' => 'Инвентаризация',
'storage' => 'Место хранения');

function SetUpUserAuthorization()
{
    global $users;
    global $grants;
    global $appGrants;
    global $dataSourceRecordPermissions;

    $hasher = GetHasher('');
    $userAuthentication = new HardCodedUserAuthentication(new UserIdentitySessionStorage(), false, $hasher, $users);
    $grantManager = new HardCodedUserGrantManager($grants, $appGrants);

    GetApplication()->SetUserAuthentication($userAuthentication);
    GetApplication()->SetUserGrantManager($grantManager);
    GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}
