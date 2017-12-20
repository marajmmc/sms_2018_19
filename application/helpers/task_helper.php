<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_helper
{
    public static function get_modules_tasks_table_tree()
    {
        $CI=& get_instance();
        $CI->db->from($CI->config->item('table_system_task'));
        $CI->db->order_by('ordering');
        $results=$CI->db->get()->result_array();

        $children=array();
        foreach($results as $result)
        {
            $children[$result['parent']]['ids'][$result['id']]=$result['id'];
            $children[$result['parent']]['modules'][$result['id']]=$result;
        }

        $level0=$children[0]['modules'];
        $tree=array();
        foreach ($level0 as $module)
        {
            Task_helper::get_sub_modules_tasks_tree($module,'',$tree,$children);
        }
        return $tree;
    }
    public static function get_sub_modules_tasks_tree($module,$prefix,&$tree,$children)
    {
        $tree[]=array('prefix'=>$prefix,'module_task'=>$module);
        $subs=array();
        if(isset($children[$module['id']]))
        {
            $subs=$children[$module['id']]['modules'];
        }
        if(sizeof($subs)>0)
        {
            foreach($subs as $sub)
            {
                Task_helper::get_sub_modules_tasks_tree($sub,$prefix.'- ',$tree,$children);
            }
        }
    }
}
