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
$config['system_purpose_variety_stock_in']='stock_in';
$config['system_purpose_variety_excess']='excess';
$config['system_purpose_variety_rnd']='out_stock_rnd';
$config['system_purpose_variety_short_inventory']='out_stock_short_inventory';
$config['system_purpose_variety_demonstration']='out_stock_demonstration';
$config['system_purpose_variety_sample']='out_stock_sample';

$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;

// ID Generation Prefix Config
$config['system_id_prefix_stock_in']='SI';
$config['system_id_prefix_stock_out']='SO';
$config['system_id_prefix_transfer_warehouse_to_warehouse']='TW';
$config['system_id_prefix_transfer_warehouse_to_outlet']='TS'; // Sales
$config['system_id_prefix_transfer_outlet_to_warehouse']='TR'; // Sales Return
$config['system_id_prefix_transfer_outlet_to_outlet']='TO';

//Convert Quantity
$config['system_convert_to_kg']='KG';
