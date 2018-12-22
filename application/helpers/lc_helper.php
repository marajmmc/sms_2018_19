<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lc_helper
{
    public static function get_view_info_basic($lc_info)
    {
        $CI =& get_instance();
        $info_basic=array();
        $user_ids=array();
        $user_ids[$lc_info['user_open_created']]=$lc_info['user_open_created'];
        if($lc_info['user_open_forward']>0)
        {
            $user_ids[$lc_info['user_open_forward']]=$lc_info['user_open_forward'];
        }
        if($lc_info['user_release_completed']>0)
        {
            $user_ids[$lc_info['user_release_completed']]=$lc_info['user_release_completed'];
        }
        if($lc_info['user_receive_completed']>0)
        {
            $user_ids[$lc_info['user_receive_completed']]=$lc_info['user_receive_completed'];
        }
        if($lc_info['user_expense_completed']>0)
        {
            $user_ids[$lc_info['user_expense_completed']]=$lc_info['user_expense_completed'];
        }

        $users=System_helper::get_users_info($user_ids);

        $result=array();
        $result['label_1']='Lc Created By';
        $result['value_1']=$users[$lc_info['user_open_created']]['name'];
        $result['label_2']='Lc Created Time';
        $result['value_2']=System_helper::display_date_time($lc_info['date_open_created']);
        $info_basic[]=$result;
        $result=array();
        $result['label_1']='Lc Created Remarks';
        $result['value_1']=nl2br($lc_info['remarks_open']);
        $info_basic[]=$result;
        if($lc_info['status_open_forward']==$CI->config->item('system_status_yes'))
        {
            $result=array();
            $result['label_1']='LC Forwarded By';
            $result['value_1']=$users[$lc_info['user_open_forward']]['name'];
            $result['label_2']='LC Forwarded Time';
            $result['value_2']=System_helper::display_date_time($lc_info['date_open_forward']);
            $info_basic[]=$result;
        }
        if($lc_info['status_release']==$CI->config->item('system_status_complete'))
        {
            $result=array();
            $result['label_1']='LC Released By';
            $result['value_1']=$users[$lc_info['user_release_completed']]['name'];
            $result['label_2']='LC Released Time';
            $result['value_2']=System_helper::display_date_time($lc_info['date_release_completed']);
            $info_basic[]=$result;
            $result=array();
            $result['label_1']='Lc Released Remarks';
            $result['value_1']=nl2br($lc_info['remarks_release']);
            $info_basic[]=$result;
        }
        if($lc_info['status_receive']==$CI->config->item('system_status_complete'))
        {
            $result=array();
            $result['label_1']='LC Received By';
            $result['value_1']=$users[$lc_info['user_receive_completed']]['name'];
            $result['label_2']='LC Received Time';
            $result['value_2']=System_helper::display_date_time($lc_info['date_receive_completed']);
            $info_basic[]=$result;
            $result=array();
            $result['label_1']='Lc Received Remarks';
            $result['value_1']=nl2br($lc_info['remarks_receive']);
            $info_basic[]=$result;
        }
        if($lc_info['status_open']==$CI->config->item('system_status_complete'))
        {
            $result=array();
            $result['label_1']='LC Closed/Completed By';
            $result['value_1']=$users[$lc_info['user_expense_completed']]['name'];
            $result['label_2']='LC Closed/Completed Time';
            $result['value_2']=System_helper::display_date_time($lc_info['date_expense_completed']);
            $info_basic[]=$result;
            $result=array();
            $result['label_1']='Lc Closed/Completed Remarks';
            $result['value_1']=nl2br($lc_info['remarks_expense']);
            $info_basic[]=$result;
        }
        return $info_basic;
    }
}
