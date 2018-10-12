<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$configs_path=str_replace('sms_2018_19','login_2018_19',APPPATH).'config/';

require_once($configs_path.'user_group.php');
require_once($configs_path.'table_login.php');
require_once($configs_path.'table_pos.php');
require_once($configs_path.'table_sms.php');

