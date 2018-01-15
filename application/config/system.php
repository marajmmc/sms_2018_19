<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['system_site_short_name']='sms';
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=7;

$config['system_status_yes']='Yes';
$config['system_status_no']='No';
$config['system_status_active']='ACTIVE';
$config['system_status_inactive']='IN-ACTIVE';
$config['system_status_delete']='DELETED';
$config['system_status_pending']='PENDING';
$config['system_status_complete']='COMPLETE';

$config['system_base_url_profile_picture']='http://50.116.76.180/login/';

//Stock
$config['system_purpose_variety_stock_in']='STOCK_IN';
$config['system_purpose_variety_excess']='EXCESS';
$config['system_purpose_variety_rnd']='OUT_STOCK_RND';
$config['system_purpose_variety_short_inventory']='OUT_STOCK_SHORT_INVENTORY';
$config['system_purpose_variety_demonstration']='OUT_STOCK_DEMONSTRATION';
$config['system_purpose_variety_sample']='OUT_STOCK_SAMPLE';

$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;
