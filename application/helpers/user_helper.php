<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_helper
{
    public static $logged_user = null;
    public static $mobile_verification_code_expires = 300;//60*5--5minutes
    public static $mobile_verification_cookie_expires = 864000;//60*60*24*10--10days
    public static $mobile_verification_cookie_prefix = 'login_mobile_verification_2018_19_';//60*60*24*10--10days
    function __construct($id)
    {
        $CI = & get_instance();
        $this->username_password_same=false;
        //user
        $result=Query_helper::get_info($CI->config->item('table_login_setup_user'),'*',array('id ='.$id),1);
        if($result && (md5($result['user_name'])==$result['password']))
        {
            $this->username_password_same=true;
        }
        //user info
        $result=Query_helper::get_info($CI->config->item('table_login_setup_user_info'),'*',array('user_id ='.$id,'revision =1'),1);
        if ($result)
        {
            foreach ($result as $key => $value)
            {
                $this->$key = $value;
            }
        }
        //user_group
        $this->user_group=0;
        $result=Query_helper::get_info($CI->config->item('table_system_assigned_group'),'*',array('user_id ='.$id,'revision =1'),1);
        if($result)
        {
            $this->user_group=$result['user_group'];
        }
    }
    public static function login($username, $password)
    {
        $CI = & get_instance();
        $time=time();

        $CI->db->from($CI->config->item('table_login_setup_user').' user');
        $CI->db->select('user.*');
        $CI->db->join($CI->config->item('table_login_setup_users_other_sites').' uos','uos.user_id=user.id','inner');
        $CI->db->join($CI->config->item('table_login_system_other_sites').' os','uos.site_id=os.id','inner');
        $CI->db->where('uos.revision',1);
        $CI->db->where('os.short_name',$CI->config->item('system_site_short_name'));

        $CI->db->where('user.user_name',$username);
        //$CI->db->where('user.password',md5($password));
        $CI->db->where('user.status',$CI->config->item('system_status_active'));

        $user=$CI->db->get()->row_array();
        //1st digit  0=>!user   0
        //1st digit  1=>user
        //2nd digit 0=>!password
        //3rd digit 0=> not suspend(100)
        //3rd digit 1 =>suspend account(101)
        //2nd digit 1=>password
        //3rd digit 0=>mobile verification required
        //4th digit 0=>mobile no not set(1100)
        //4th digit 1=>send otp(1101)
        //3rd digit 1=>mobile verification not required direct login(111)

        //4th digit 0=>!mobile 1=>view opt login 1100 1101

        if($user)//first digit 1
        {
            if($user['password']==md5($password))//2nd digit 1
            {
                if($user['password_wrong_consecutive']>0)
                {
                    $data=array();
                    $data['password_wrong_consecutive']=0;
                    Query_helper::update($CI->config->item('table_login_setup_user'),$data,array("id = ".$user['id']),false);
                }
                $mobile_verification_required=true;
                if($user['time_mobile_authentication_off_end']>$time)//for user if inactive
                {
                    $mobile_verification_required=false;
                }
                else
                {
                    $result=Query_helper::get_info($CI->config->item('table_login_setup_system_configures'),array('config_value'),array('purpose ="' .$CI->config->item('system_purpose_login_status_mobile_verification').'"','status ="'.$CI->config->item('system_status_active').'"'),1);
                    if($result && ($result['config_value']!=1))//if global inactive
                    {
                        $mobile_verification_required=false;
                    }
                    else
                    {
                        $cookie_info=get_cookie(User_helper::$mobile_verification_cookie_prefix.$user['id']);//verification_id
                        if($cookie_info)
                        {
                            $max_logged_browser=1;
                            if($user['max_logged_browser']>0)
                            {
                                $max_logged_browser=$user['max_logged_browser'];
                            }
                            $CI->db->from($CI->config->item('table_system_history_login_verification_code').' vc');
                            $CI->db->where('vc.user_id',$user['id']);
                            $CI->db->where('vc.status_used',$CI->config->item('system_status_yes'));
                            $CI->db->order_by('vc.id DESC');
                            $CI->db->limit($max_logged_browser);
                            $verification_infos=$CI->db->get()->result_array();

                            foreach($verification_infos as $verification_info)
                            {
                                if($verification_info['id']==$cookie_info)
                                {
                                    set_cookie(User_helper::$mobile_verification_cookie_prefix.$verification_info['user_id'],$verification_info['id'],User_helper::$mobile_verification_cookie_expires);
                                    $mobile_verification_required=false;
                                    break;
                                }
                            }
                        }
                    }
                }
                if($mobile_verification_required)//3rd digit 0=>mobile verification required
                {
                    $user_info=Query_helper::get_info($CI->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user['id'] ,'revision =1'),1);
                    if($user_info && (strlen($user_info['mobile_no'])>0))
                    {
                        //send verification code
                        $verification_code=mt_rand(1000,999999);
                        $data=array();
                        $data['user_id']=$user['id'];
                        $data['code_verification']=$verification_code;
                        $data['date_created']=$time;
                        $verification_id=Query_helper::add($CI->config->item('table_system_history_login_verification_code'),$data,false);

                        $CI->load->helper('mobile_sms');
                        $CI->lang->load('mobile_sms');
                        $mobile_no=$user_info['mobile_no'];
                        Mobile_sms_helper::send_sms(Mobile_sms_helper::$API_SENDER_ID_MALIK_SEEDS,$mobile_no,sprintf($CI->lang->line('SMS_LOGIN_OTP'),$verification_code),'text');
                        $CI->session->set_userdata("login_mobile_verification_id", $verification_id);
                        return array('status_code'=>'1101','message'=>'','message_warning'=>$CI->lang->line('WARNING_LOGIN_FAIL_1101'));
                    }
                    else
                    {
                        //mobile number not set
                        return array('status_code'=>'1100','message'=>$CI->lang->line('MSG_LOGIN_FAIL_1100'),'message_warning'=>$CI->lang->line('WARNING_LOGIN_FAIL_1100'));
                    }
                }
                else//3rd digit 1=>mobile verification not required direct login(111)
                {
                    $CI->session->set_userdata("user_id", $user['id']);
                    return array('status_code'=>'111','message'=>$CI->lang->line('MSG_LOGIN_SUCCESS'),'message_warning'=>'');
                }
            }
            else//2nd digit 0
            {
                $result=Query_helper::get_info($CI->config->item('table_login_setup_system_configures'),array('config_value'),array('purpose ="' .$CI->config->item('system_purpose_login_max_wrong_password').'"','status ="'.$CI->config->item('system_status_active').'"'),1);

                $data=array();
                $data['password_wrong_consecutive']=$user['password_wrong_consecutive']+1;
                $data['password_wrong_total']=$user['password_wrong_total']+1;

                if($data['password_wrong_consecutive']<=$result['config_value'])//3rd digit 0
                {
                    $message_warning=sprintf($CI->lang->line('WARNING_LOGIN_FAIL_100'),$result['config_value']-$data['password_wrong_consecutive']+1);
                    Query_helper::update($CI->config->item('table_login_setup_user'),$data,array("id = ".$user['id']),false);
                    return array('status_code'=>'100','message'=>$CI->lang->line('MSG_LOGIN_FAIL_100'),'message_warning'=>$message_warning);
                }
                else//3rd digit 1
                {
                    $data['status']=$CI->config->item('system_status_inactive');
                    $data['remarks_status_change']=sprintf($CI->lang->line('REMARKS_USER_SUSPEND_WRONG_PASSWORD'),$data['password_wrong_consecutive']);
                    $data['date_status_changed'] = $time;
                    $data['user_status_changed'] = -1;
                    Query_helper::update($CI->config->item('table_login_setup_user'),$data,array("id = ".$user['id']),false);
                    return array('status_code'=>'101','message'=>$CI->lang->line('MSG_LOGIN_FAIL_101'),'message_warning'=>$CI->lang->line('WARNING_LOGIN_FAIL_101'));
                }
            }
        }
        else//first digit 0
        {
            return array('status_code'=>'0','message'=>$CI->lang->line('MSG_LOGIN_FAIL_0'),'message_warning'=>'');
        }
    }
    public static function login_mobile_verification($code_verification)
    {
        $CI = & get_instance();
        $time=time();
        $verification_id=$CI->session->userdata("login_mobile_verification_id");
        //1st digit  0=>!verification info   0
        //1st digit  1=>verification info
        //2nd digit  0=>code did not matched
        //2nd digit  1=>code ok
        //3rd digit 0=>used
        //3rd digit 1=>unused
        //4th digit 0 expired
        //4th digit 1 make login

        $verification_info=Query_helper::get_info($CI->config->item('table_system_history_login_verification_code'),'*',array('id ="'.$verification_id.'"'),1);
        if($verification_info)//first digit 1
        {
            if(($verification_info['code_verification'])!=$code_verification)//2nd digit 0
            {
                return array('status_code'=>'10','message'=>$CI->lang->line('MSG_LOGIN_VERIFICATION_FAIL_10'));
            }
            else//2nd digit 1
            {
                if(($verification_info['status_used'])==$CI->config->item('system_status_yes'))//3rd digit 0
                {
                    return array('status_code'=>'110','message'=>$CI->lang->line('MSG_LOGIN_VERIFICATION_FAIL_110'));
                }
                else
                {
                    if(($time-$verification_info['date_created'])>User_helper::$mobile_verification_code_expires)//4th digit 0
                    {
                        return array('status_code'=>'1110','message'=>$CI->lang->line('MSG_LOGIN_VERIFICATION_FAIL_1110'));
                    }
                    else//4th digit 1
                    {
                        $data=array();
                        $data['status_used']=$CI->config->item('system_status_yes');
                        $data['date_updated']=$time;
                        Query_helper::update($CI->config->item('table_system_history_login_verification_code'),$data,array("id = ".$verification_id),false);
                        
                        set_cookie(User_helper::$mobile_verification_cookie_prefix.$verification_info['user_id'],$verification_info['id'],User_helper::$mobile_verification_cookie_expires);
                        $CI->session->set_userdata("user_id", $verification_info['user_id']);
                        $CI->session->set_userdata('login_mobile_verification_id','');//delete login_otp_id
                        return array('status_code'=>'1111','message'=>$CI->lang->line('MSG_LOGIN_SUCCESS'));
                    }
                }
            }
        }
        else//first digit 0
        {
            return array('status_code'=>'0','message'=>$CI->lang->line('MSG_LOGIN_VERIFICATION_FAIL_0'));
        }
    }
    public static function get_user()
    {
        $CI = & get_instance();
        if (User_helper::$logged_user)
        {
            return User_helper::$logged_user;
        }
        else
        {
            if($CI->session->userdata("user_id")!="")
            {
                $CI->db->from($CI->config->item('table_login_setup_user').' user');
                $CI->db->select('user.id');

                $CI->db->join($CI->config->item('table_login_setup_users_other_sites').' uos','uos.user_id=user.id','inner');
                $CI->db->join($CI->config->item('table_login_system_other_sites').' os','uos.site_id=os.id','inner');
                $CI->db->where('uos.revision',1);
                $CI->db->where('os.short_name',$CI->config->item('system_site_short_name'));

                $CI->db->where('user.id',$CI->session->userdata('user_id'));
                $CI->db->where('user.status',$CI->config->item('system_status_active'));
                $user=$CI->db->get()->row();

                if($user)
                {
                    User_helper::$logged_user = new User_helper($CI->session->userdata('user_id'));
                    return User_helper::$logged_user;
                }
                return null;
            }
            else
            {
                return null;
            }

        }
    }
    public static function get_html_menu()
    {
        $user=User_helper::get_user();
        $CI = & get_instance();
        $CI->db->order_by('ordering');
        $tasks=$CI->db->get($CI->config->item('table_system_task'))->result_array();

        $roles=Query_helper::get_info($CI->config->item('table_system_user_group_role'),'*',array('revision =1','action0 =1','user_group_id ='.$user->user_group));
        $role_data=array();
        foreach($roles as $role)
        {
            $role_data[]=$role['task_id'];

        }
        $menu_data=array();
        foreach($tasks as $task)
        {
            if($task['type']=='TASK')
            {
                if(in_array($task['id'],$role_data))
                {
                    $menu_data['items'][$task['id']]=$task;
                    $menu_data['children'][$task['parent']][]=$task['id'];
                }
            }
            else
            {
                $menu_data['items'][$task['id']]=$task;
                $menu_data['children'][$task['parent']][]=$task['id'];
            }
        }

        $html='';
        if(isset($menu_data['children'][0]))
        {
            foreach($menu_data['children'][0] as $child)
            {
                $html.=User_helper::get_html_submenu($child,$menu_data,1);
            }
        }
        return $html;
    }
    public static function get_html_submenu($parent,$menu_data,$level)
    {
        if(isset($menu_data['children'][$parent]))
        {
            $sub_html='';
            foreach($menu_data['children'][$parent] as $child)
            {
                $sub_html.=User_helper::get_html_submenu($child,$menu_data,$level+1);

            }
            $html='';
            if($sub_html)
            {
                if($level==1)
                {
                    $html.='<li class="menu-item dropdown">';
                    $html.='<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$menu_data['items'][$parent]['name'].'<b class="caret"></b></a>';
                }
                else
                {
                    $html.='<li class="menu-item dropdown dropdown-submenu">';
                    $html.='<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$menu_data['items'][$parent]['name'].'</a>';
                }

                $html.='<ul class="dropdown-menu">';
                $html.=$sub_html;
                $html.='</ul></li>';
            }

            return $html;

        }
        else
        {
            if($menu_data['items'][$parent]['type']=='TASK')
            {
                return '<li><a href="'.site_url(strtolower($menu_data['items'][$parent]['controller'])).'">'.$menu_data['items'][$parent]['name'].'</a></li>';
            }
            else
            {
                return '';
            }

        }
    }
    public static function get_permission($controller_name)
    {
        $CI = & get_instance();
        $user=User_helper::get_user();
        $CI->db->from($CI->config->item('table_system_user_group_role').' ugr');
        $CI->db->select('ugr.*');

        $CI->db->join($CI->config->item('table_system_task').' task','task.id = ugr.task_id','INNER');
        $CI->db->where("controller",$controller_name,"after");
        $CI->db->where("user_group_id",$user->user_group);
        $CI->db->where("revision",1);
        $result=$CI->db->get()->row_array();
        return $result;
    }
    public static function get_locations()
    {
        $CI = & get_instance();
        $user=User_helper::get_user();
        $CI->db->from($CI->config->item('table_login_setup_user_area').' aa');
        $CI->db->select('aa.*');
        $CI->db->select('union.name union_name');
        $CI->db->select('u.name upazilla_name');
        $CI->db->select('d.name district_name');
        $CI->db->select('t.name territory_name');
        $CI->db->select('zone.name zone_name');
        $CI->db->select('division.name division_name');
        $CI->db->join($CI->config->item('table_login_setup_location_unions').' union','union.id = aa.union_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_upazillas').' u','u.id = aa.upazilla_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_districts').' d','d.id = aa.district_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_territories').' t','t.id = aa.territory_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
        $CI->db->where('aa.revision',1);
        $CI->db->where('aa.user_id',$user->user_id);
        $assigned_area=$CI->db->get()->row_array();
        if($assigned_area)
        {
            $CI->db->from($CI->config->item('table_login_setup_user_area').' aa');
            if($assigned_area['division_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','INNER');
            }
            if($assigned_area['zone_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_zones').' zone','zone.division_id = division.id','INNER');
                $CI->db->where('zone.id',$assigned_area['zone_id']);
            }
            if($assigned_area['territory_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_territories').' t','t.zone_id = zone.id','INNER');
                $CI->db->where('t.id',$assigned_area['territory_id']);
            }
            if($assigned_area['district_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_districts').' d','d.territory_id = t.id','INNER');
                $CI->db->where('d.id',$assigned_area['district_id']);
            }
            if($assigned_area['upazilla_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_upazillas').' u','u.district_id = d.id','INNER');
                $CI->db->where('u.id',$assigned_area['upazilla_id']);
            }
            if($assigned_area['union_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_unions').' union','union.upazilla_id = u.id','INNER');
                $CI->db->where('union.id',$assigned_area['union_id']);
            }
            $CI->db->where('aa.revision',1);
            $CI->db->where('aa.user_id',$user->user_id);
            $info=$CI->db->get()->row_array();
            if(!$info)
            {
                return array();
            }
        }
        return $assigned_area;
    }
}
