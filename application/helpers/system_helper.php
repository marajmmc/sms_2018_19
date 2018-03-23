<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_helper
{
    public static function display_date($time)
    {
        if(is_numeric($time))
        {
            return date('d-M-Y',$time);
        }
        else
        {
            return '';
        }
    }
    public static function display_date_time($time)
    {
        if(is_numeric($time))
        {
            return date('d-M-Y h:i:s A',$time);
        }
        else
        {
            return '';
        }
    }
    public static function get_time($str)
    {
        $time=strtotime($str);
        if($time===false)
        {
            return 0;
        }
        else
        {
            return $time;
        }
    }
    public static function upload_file($save_dir='images',$allowed_types='gif|jpg|png',$max_size=10240)
    {
        $CI= & get_instance();
        $CI->load->library('upload');
        $config=array();
        $config['upload_path']=FCPATH.$save_dir;
        $config['allowed_types']=$allowed_types;
        $config['max_size']=$max_size;
        $config['overwrite']=false;
        $config['remove_spaces']=true;

        $uploaded_files=array();
        foreach ($_FILES as $key=>$value)
        {
            if(strlen($value['name'])>0)
            {
                $CI->upload->initialize($config);
                if($CI->upload->do_upload($key))
                {
                    $uploaded_files[$key]=array('status'=>true,'info'=>$CI->upload->data());
                }
                else
                {
                    $uploaded_files[$key]=array('status'=>false,'message'=>$value['name'].': '.$CI->upload->display_errors());
                }
            }
        }
        return $uploaded_files;
    }
    public static function invalid_try($action='',$action_id='',$other_info='')
    {
        $CI =& get_instance();
        $user = User_helper::get_user();
        $time=time();
        $data=array();
        $data['user_id']=$user->user_id;
        $data['controller']=$CI->router->class;
        $data['action']=$action;
        $data['action_id']=$action_id;
        $data['other_info']=$other_info;
        $data['date_created']=$time;
        $data['date_created_string']=System_helper::display_date_time($time);
        $CI->db->insert($CI->config->item('table_system_history_hack'), $data);
    }
    //saving preference
    public static function save_preference()
    {
        $CI =& get_instance();
        $preference_method_name=$CI->input->post('preference_method_name');
        $method=isset($preference_method_name)?$preference_method_name:'list';
        $user = User_helper::get_user();
        if(!(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$CI->lang->line("YOU_DONT_HAVE_ACCESS");
            $CI->json_return($ajax);
            die();
        }
        else
        {
            $system_preference_items=$CI->input->post('system_preference_items');
            if(!$system_preference_items)
            {
                $ajax['status']=false;
                $ajax['system_message']=$CI->lang->line("MSG_SELECT_ONE");
                $CI->json_return($ajax);
                die();
            }

            $time=time();
            $CI->db->trans_start();  //DB Transaction Handle START

            $result=Query_helper::get_info($CI->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$CI->controller_url.'"','method ="'.$method.'"'),1);
            if($result)
            {
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;
                $data['preferences']=json_encode($system_preference_items);
                Query_helper::update($CI->config->item('table_system_user_preference'),$data,array('id='.$result['id']),false);
            }
            else
            {
                $data['user_id']=$user->user_id;
                $data['controller']=$CI->controller_url;
                $data['method']="$method";
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['preferences']=json_encode($system_preference_items);
                Query_helper::add($CI->config->item('table_system_user_preference'),$data,false);
            }

            $CI->db->trans_complete();   //DB Transaction Handle END
            $ajax['status']=true;
            if ($CI->db->trans_status() === TRUE)
            {
                $CI->message=$CI->lang->line("MSG_SAVED_SUCCESS");
                $CI->index($method);
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$CI->lang->line("MSG_SAVED_FAIL");
                $CI->json_return($ajax);
            }
        }
    }
    public static function get_users_info($user_ids)
    {
        //can be upgrade select field from user_info
        //but no more join query
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_login_setup_user').' user');
        $CI->db->select('user.id,user.employee_id,user.user_name,user.status');
        $CI->db->join($CI->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $CI->db->select('user_info.name,user_info.ordering,user_info.blood_group,user_info.mobile_no');
        $CI->db->where('user_info.revision',1);
        if(sizeof($user_ids)>0)
        {
            $CI->db->where_in('user.id',$user_ids);
        }
        $results=$CI->db->get()->result_array();
        $users=array();
        foreach($results as $result)
        {
            $users[$result['id']]=$result;
        }
        return $users;
    }
}
