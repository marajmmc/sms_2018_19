<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock_raw_summary extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_stock_variety_details');
        $this->lang->load('report_stock_raw');
    }
    public function index($action="search")
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $data['title']="Raw Current Stock Report Search";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function get_preference_headers()
    {
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['in_stock_in_kg_pcs']= 1;
        $data['in_stock_excess_kg_pcs']= 1;
        $data['in_purchase_kg_pcs']= 1;
        $data['out_stock_damage_kg_pcs']= 1;
        $data['out_convert_kg_pcs']= 1;
        $data['current_stock_kg_pcs']= 1;
        $data['current_stock_cal_kg_pcs']= 1;
        return $data;
    }
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data=$this->get_preference_headers();
        if($result)
        {
            if($result['preferences']!=null)
            {
                $preferences=json_decode($result['preferences'],true);
                foreach($data as $key=>$value)
                {
                    if(isset($preferences[$key]))
                    {
                        $data[$key]=$value;
                    }
                    else
                    {
                        $data[$key]=0;
                    }
                }
            }
        }
        return $data;
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            if(!$reports['packing_item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Packing item selection mandatory';
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Raw Current Stock Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $packing_item=$this->input->post('packing_item');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $items=array();

        $this->db->from($this->config->item('table_sms_stock_summary_raw').' stock_summary_raw');
        $this->db->select('SUM(stock_summary_raw.in_stock) in_stock_in',false);
        $this->db->select('SUM(stock_summary_raw.in_excess) in_stock_excess',false);
        $this->db->select('SUM(stock_summary_raw.in_purchase) in_purchase',false);
        $this->db->select('SUM(stock_summary_raw.out_stock_damage) out_stock_damage',false);
        $this->db->select('SUM(stock_summary_raw.out_convert) out_convert',false);
        $this->db->select('SUM(stock_summary_raw.current_stock) current_stock',false);
        if($packing_item!=$this->config->item('system_common_foil'))
        {
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_raw.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_raw.pack_size_id','LEFT');
            $this->db->select('pack.name pack_size,pack.id pack_size_id');

            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('crop_type.ordering','ASC');
            $this->db->order_by('crop_type.id','ASC');
            $this->db->order_by('v.ordering','ASC');
            $this->db->order_by('v.id','ASC');
            $this->db->order_by('pack.id');
        }
        $this->db->where('stock_summary_raw.packing_item',$packing_item);
        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0 && is_numeric($crop_type_id))
            {
                $this->db->where('v.crop_type_id',$crop_type_id);
                if($variety_id>0 && is_numeric($variety_id))
                {
                    $this->db->where('stock_summary_raw.variety_id',$variety_id);
                }
            }
        }
        if($pack_size_id>0 && is_numeric($pack_size_id))
        {
            $this->db->where('stock_summary_raw.pack_size_id',$pack_size_id);
        }
        if($packing_item!=$this->config->item('system_common_foil'))
        {
            $this->db->group_by('v.id');
            $this->db->group_by('pack.id');
        }
        $results=$this->db->get()->result_array();

        $type_total=$this->initialize_row('','','Total Type','');
        $crop_total=$this->initialize_row('','Total Crop','','');
        $grand_total=$this->initialize_row('Grand Total','','','');
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $headers=$this->get_preference_headers();
        unset($headers['crop_name']);
        unset($headers['crop_type_name']);
        unset($headers['variety_name']);
        unset($headers['pack_size']);

        foreach($results as $result)
        {
            if($packing_item==$this->config->item('system_common_foil'))
            {
                $result['crop_name']='';
                $result['crop_type_name']='';
                $result['variety_name']='';
                $result['pack_size']='';
            }
            $info=$this->initialize_row($result['crop_name'],$result['crop_type_name'],$result['variety_name'],$result['pack_size']);
            if(!$first_row)
            {
                if($prev_crop_name!=$result['crop_name'])
                {
                    $items[]=$this->get_row($type_total);
                    $items[]=$this->get_row($crop_total);
                    $type_total=$this->reset_row($type_total);
                    $crop_total=$this->reset_row($crop_total);
                    $prev_crop_name=$result['crop_name'];
                    $prev_type_name=$result['crop_type_name'];
                }
                elseif($prev_type_name!=$result['crop_type_name'])
                {
                    $items[]=$this->get_row($type_total);
                    $type_total=$this->reset_row($type_total);
                    $info['crop_name']='';
                    $prev_type_name=$result['crop_type_name'];
                }
                else
                {
                    $info['crop_name']='';
                    $info['crop_type_name']='';
                }
            }
            else
            {
                $prev_crop_name=$result['crop_name'];
                $prev_type_name=$result['crop_type_name'];
                $first_row=false;
            }

            unset($headers['current_stock_cal_kg_pcs']);

            foreach($headers  as $key=>$r)
            {
                $info[$key]=$result[substr($key, 0, -7)];
            }
            $info['current_stock_cal_kg_pcs']=0;
            foreach($headers  as $key=>$r)
            {
                if(substr($key,0,2)=='in')
                {
                    $info['current_stock_cal_kg_pcs']+=$info[$key];
                }
                elseif(substr($key,0,3)=='out')
                {
                    $info['current_stock_cal_kg_pcs']-=$info[$key];
                }
            }
            $headers['current_stock_cal_kg_pcs']=1;
            foreach($headers  as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }
            $items[]=$this->get_row($info);
        }
        if($packing_item!=$this->config->item('system_common_foil'))
        {
            $items[]=$this->get_row($type_total);
            $items[]=$this->get_row($crop_total);
        }
        $items[]=$this->get_row($grand_total);
        $this->json_return($items);
        die();
    }
    private function initialize_row($crop_name,$crop_type_name,$variety_name,$pack_size)
    {
        $row=$this->get_preference_headers();
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        return $row;
    }
    private function get_row($info)
    {
        $row=array();
        $packing_item=$this->input->post('packing_item');
        foreach($info  as $key=>$r)
        {
            $row[$key]=$info[$key];
            if(substr($key,-6)=='kg_pcs')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    if($packing_item==$this->config->item('system_master_foil') || $packing_item==$this->config->item('system_common_foil'))
                    {
                        $row[$key]=number_format($info[$key],3,'.','');
                    }
                }
            }
        }
        return $row;
    }
    private function reset_row($info)
    {
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
            {
                $info[$key]=0;
            }
        }
        return $info;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='search';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
}
