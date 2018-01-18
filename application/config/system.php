<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['system_site_short_name']='sms';
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=7;

$config['system_status_yes']='Yes';
$config['system_status_no']='No';
$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
$config['system_status_closed']='Closed';
$config['system_status_pending']='Pending';
$config['system_status_complete']='Complete';

$config['system_base_url_profile_picture']='http://50.116.76.180/login/';

//Stock
$config['system_purpose_variety_stock_in']='Stock-In';
$config['system_purpose_variety_excess']='Excess';
$config['system_purpose_variety_rnd']='R&D Purpose';
$config['system_purpose_variety_short_inventory']='Short Inventory';
$config['system_purpose_variety_demonstration']='Demonstration';
$config['system_purpose_variety_sample']='Sample Purpose';

$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;
