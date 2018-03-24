<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['system_site_short_name']='sms';
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=8;

$config['system_status_yes']='Yes';
$config['system_status_no']='No';
$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
$config['system_status_closed']='Closed';
$config['system_status_pending']='Pending';
$config['system_status_forwarded']='Forwarded';
$config['system_status_complete']='Complete';
$config['system_status_approved']='Approved';
$config['system_status_delivered']='Delivered';
$config['system_status_received']='Received';
$config['system_status_rejected']='Rejected';

$config['system_base_url_profile_picture']='http://50.116.76.180/login/';
$config['system_base_url_picture_setup_print']='http://localhost/sms_2018_19/';

//Stock
$config['system_purpose_variety_stock_in']='Stock-In';
$config['system_purpose_variety_excess']='Excess';
$config['system_purpose_variety_rnd']='R&D Purpose';
$config['system_purpose_variety_short_inventory']='Short Inventory';
$config['system_purpose_variety_demonstration']='Demonstration';
$config['system_purpose_variety_sample']='Sample Purpose';
$config['system_purpose_variety_in_delivery_short']='Delivery Short';
$config['system_purpose_variety_delivery_excess']='Deliver Excess';





$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;

/*Bank & Account Config*/
// purpose
$config['system_bank_account_purpose']['lc']='Lc';
$config['system_bank_account_purpose']['sale_receive']='Sale Receive';

/// Added by saiful. Need to review.
$config['system_master_foil']='Master';
$config['system_common_foil']='Foil';
$config['system_sticker']='Sticker';

//Stock out purpose
$config['system_purpose_raw_stock_damage']='Damage';
//System Configuration
$config['system_purpose_config']['sms_date_expire']='sms_date_expire';
$config['system_purpose_config']['sms_quantity_order_max']='sms_quantity_order_max';

