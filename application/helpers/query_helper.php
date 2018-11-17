<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Query_helper
{
    public static function add($table_name,$data,$save_history=true)
    {
        $CI =& get_instance();
        $CI->db->insert($table_name, $data);
        if($CI->db->affected_rows() >0)
        {
            $id = $CI->db->insert_id();
            if($save_history)
            {
                $user = User_helper::get_user();
                $time=time();
                $historyData = Array(
                    'controller'=>$CI->router->class,
                    'table_id'=>$id,
                    'table_name'=>$table_name,
                    'data'=>json_encode($data),
                    'user_id'=>$user->user_id,
                    'action'=>'INSERT',
                    'date'=>$time
                );
                $CI->db->insert($CI->config->item('table_system_history'), $historyData);
            }
            return $id;
        }
        else
        {
            return false;
        }
    }

    public static  function update($table_name,$data,$conditions,$save_history=true)
    {
        $CI =& get_instance();
        foreach($conditions as $condition)
        {
            $CI->db->where($condition);

        }
        $rows=$CI->db->get($table_name)->result_array();


        foreach($conditions as $condition)
        {
            $CI->db->where($condition);

        }
        $CI->db->update($table_name, $data);

        if($CI->db->affected_rows() >0)
        {
            if($save_history)
            {
                $user = User_helper::get_user();
                $time=time();
                foreach($rows as $row)
                {

                    $historyData = Array(
                        'controller'=>$CI->router->class,
                        'table_id'=>$row['id'],
                        'table_name'=>$table_name,
                        'data'=>json_encode($data),
                        'user_id'=>$user->user_id,
                        'action'=>'UPDATE',
                        'date'=>$time
                    );

                    $CI->db->insert($CI->config->item('table_system_history'), $historyData);
                }
            }
            return true;

        }
        else
        {
            return false;
        }
    }
    public static function get_info($table_name,$field_names,$conditions,$limit=0,$start=0,$order_by=null)
    {
        $CI =& get_instance();

        if(is_array($field_names))
        {
            foreach($field_names as $field_name)
            {
                $CI->db->select($field_name);

            }
        }
        else
        {
            $CI->db->select($field_names);

        }

        foreach($conditions as $condition)
        {
            $CI->db->where($condition);
        }
        if(is_array($order_by))
        {
            foreach($order_by as $order)
            {
                $CI->db->order_by($order);
            }

        }
        if($limit==0)
        {
            return $CI->db->get($table_name)->result_array();
        }
        if($limit==1)
        {
            return $CI->db->get($table_name)->row_array();
        }
        else
        {
            $CI->db->limit($limit,$start);
            return $CI->db->get($table_name)->result_array();
        }

    }

}
