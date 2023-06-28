<?php

date_default_timezone_set("America/Chicago");
//dates in database are saved in "central time" and then converted according the jobsite timezone

//affects databse connection and notification certificates
define("DEBUG","0"); 
//quantity of cases obtained by WS
define("CASES_LIMIT","200");
//WS current version
define("CURRENT_VERSION",1);

////////////////////////////////////////////////////////////////

define("APP_CASE_VIOLATION","1");
define("APP_CASE_RECOGNITION","2");
define("APP_CASE_INCIDENT","3");
define("APP_CASE_OBSERVATION","4");

define("APP_CASE_STATUS_OPEN","1");
define("APP_CASE_STATUS_CLOSE","2");
define("APP_CASE_STATUS_OVERDUE","3");

define("APP_CASE_INCIDENT_PRELIMINARY","1");
define("APP_CASE_INCIDENT_INTERIM","2");
define("APP_CASE_INCIDENT_FINAL","3");

//system
define("ROLE_ADMIN","1");
define("ROLE_WT_PERSONNEL","2");
define("ROLE_WT_SAFETY_PERSONNEL","3");
define("ROLE_WT_EXECUTIVE_MANAGER","4");
define("ROLE_WT_PROJECT_MANAGER","5");
define("ROLE_SYSTEM_ADMIN","6");
define("ROLE_SAFETY_CONTRACTOR","7");
define("ROLE_CLIENT_MANAGER","8");
define("ROLE_TRADE_PARTNER","16");

//notifications
define("ROLE_CONTRACTOR_OWNER","10");
define("ROLE_CONTRACTOR_FOREMAN","11");
define("ROLE_CONTRACTOR_SAFETY_MANAGER","12");
define("ROLE_CONTRACTOR_PROJECT_MANAGER","13");
define("ROLE_CONTRACTOR_EMPLOYEE","14");
define("ROLE_CLIENT_SAFETY_PERSONNEL","15");
define("ROLE_WT_CRAFTSMEN","19");

//timezones
define("SERVER_TIMEZONE","America/Chicago");

define("IS_PRODUCTION",FALSE);

//used to limit select/dropdowns by section
$GLOBALS['jobsite_admin'] = array(ROLE_ADMIN,ROLE_WT_PERSONNEL, ROLE_WT_SAFETY_PERSONNEL, ROLE_WT_EXECUTIVE_MANAGER, ROLE_WT_PROJECT_MANAGER, ROLE_SAFETY_CONTRACTOR, ROLE_CLIENT_MANAGER, ROLE_CONTRACTOR_FOREMAN, ROLE_CONTRACTOR_SAFETY_MANAGER, ROLE_CONTRACTOR_PROJECT_MANAGER, ROLE_CONTRACTOR_EMPLOYEE, ROLE_CLIENT_SAFETY_PERSONNEL, ROLE_TRADE_PARTNER,ROLE_WT_CRAFTSMEN);
$GLOBALS['contractor_roles'] = array(ROLE_CONTRACTOR_OWNER,ROLE_CONTRACTOR_FOREMAN, ROLE_CONTRACTOR_PROJECT_MANAGER, ROLE_CONTRACTOR_SAFETY_MANAGER, ROLE_CONTRACTOR_EMPLOYEE);
$GLOBALS['wt_roles'] = array(ROLE_SYSTEM_ADMIN, ROLE_WT_PERSONNEL, ROLE_WT_SAFETY_PERSONNEL, ROLE_WT_EXECUTIVE_MANAGER, ROLE_WT_PROJECT_MANAGER );
$GLOBALS['wt_roles_access'] = array(ROLE_ADMIN,ROLE_SYSTEM_ADMIN, ROLE_WT_PERSONNEL, ROLE_WT_SAFETY_PERSONNEL, ROLE_WT_EXECUTIVE_MANAGER, ROLE_WT_PROJECT_MANAGER );
$GLOBALS['client'] = array(ROLE_CLIENT_SAFETY_PERSONNEL, ROLE_CLIENT_MANAGER);

return [
    'adminEmail' => 'no-reply@whiting-turner.com',
    'title' => 'Whiting Turner'
];
