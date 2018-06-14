<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock_variety_summary extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Report_stock_variety_summary');
        $this->controller_url='report_stock_variety_summary';
        $this->lang->load('report_stock_variety_details');
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
            $data['title']="Variety Current Stock Report Search";
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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $data['options']=$reports;
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Variety Current Stock Report";
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
    private function get_preference_headers($warehouses)
    {
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['amount_price_unit']= 1;
        foreach($warehouses as $warehouse)
        {
            $data['warehouse_'.$warehouse['value'].'_pkt']= 1;
            $data['warehouse_'.$warehouse['value'].'_kg']= 1;
        }
        //$data['system_preference_items']['current_stock']= 1;
        $data['current_stock_pkt']= 1;
        $data['current_stock_kg']= 1;
        $data['amount_price_total']= 1;
        return $data;
    }
    private function get_preference()
    {
        $user = User_helper::get_user();
        $warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data=$this->get_preference_headers($warehouses);
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
    private function system_get_items()
    {
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $items=array();

        $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
        $this->db->select('price.variety_id,price.pack_size_id,price.price_net');
        $results=$this->db->get()->result_array();
        $price_units=array();
        foreach($results as $result)
        {
            $price_units[$result['variety_id']][$result['pack_size_id']]=$result['price_net'];
        }
        $warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));

        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('stock_summary_variety.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_variety.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $this->db->order_by('pack.id');

        if($variety_id>0 && is_numeric($variety_id))
        {
            $this->db->where('stock_summary_variety.variety_id',$variety_id);
        }
        if($crop_type_id>0 && is_numeric($crop_type_id))
        {
            $this->db->where('v.crop_type_id',$crop_type_id);
        }

        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('crop_type.crop_id',$crop_id);
        }
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('stock_summary_variety.pack_size_id',$pack_size_id);
        }
        elseif($pack_size_id ==-2)
        {
            $this->db->where('stock_summary_variety.pack_size_id >',0);
        }
        $results=$this->db->get()->result_array();
        $varieties=array();
        foreach($results as $result)
        {
            if(!(isset($varieties[$result['variety_id']][$result['pack_size_id']])))
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row($result['crop_name'],$result['crop_type_name'],$result['variety_name'],$result['pack_size'],$warehouses);
                if($result['pack_size_id']==0)
                {
                    $varieties[$result['variety_id']][$result['pack_size_id']]['pack_size']='Bulk';
                }
                if(isset($price_units[$result['variety_id']][$result['pack_size_id']]))
                {
                    $varieties[$result['variety_id']][$result['pack_size_id']]['amount_price_unit']=$price_units[$result['variety_id']][$result['pack_size_id']];
                }
                else
                {
                    $varieties[$result['variety_id']][$result['pack_size_id']]['amount_price_unit']=0;
                }
            }
            if($result['pack_size_id']==0)
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_pkt']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_kg']=$result['current_stock'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['current_stock_kg']+=$result['current_stock'];
            }
            else
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_pkt']=$result['current_stock'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['current_stock_pkt']+=$result['current_stock'];

                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_kg']=$result['current_stock']*$result['pack_size']/1000;
                $varieties[$result['variety_id']][$result['pack_size_id']]['current_stock_kg']+=$varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_kg'];
            }
        }

        $type_total=$this->initialize_row('','','Total Type','',$warehouses);
        $crop_total=$this->initialize_row('','Total Crop','','',$warehouses);
        $grand_total=$this->initialize_row('Grand Total','','','',$warehouses);

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;
        foreach($varieties as $variety_id=>$variety)
        {
            foreach($variety as $pack_size_id=>$pack)
            {
                if(!$first_row)
                {
                    if($prev_crop_name!=$pack['crop_name'])
                    {
                        $items[]=$this->get_row($type_total);
                        $type_total=$this->reset_row($type_total);
                        $items[]=$this->get_row($crop_total);
                        $crop_total=$this->reset_row($crop_total);
                        $prev_crop_name=$pack['crop_name'];
                        $prev_type_name=$pack['crop_type_name'];
                    }
                    elseif($prev_type_name!=$pack['crop_type_name'])
                    {
                        $items[]=$this->get_row($type_total);
                        $type_total=$this->reset_row($type_total);
                        $pack['crop_name']='';
                        $prev_type_name=$pack['crop_type_name'];
                    }
                    else
                    {
                        $pack['crop_name']='';
                        $pack['crop_type_name']='';
                    }
                }
                else
                {
                    $prev_crop_name=$pack['crop_name'];
                    $prev_type_name=$pack['crop_type_name'];
                    $first_row=false;
                }
                $pack['amount_price_total']=$pack['current_stock_pkt']*$pack['amount_price_unit'];
                foreach($pack as $key=>$r)
                {
                    if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')||($key=='amount_price_unit')))
                    {
                        $type_total[$key]+=$pack[$key];
                        $crop_total[$key]+=$pack[$key];
                        $grand_total[$key]+=$pack[$key];
                    }
                }
                $items[]=$this->get_row($pack);
            }
        }
        $items[]=$this->get_row($type_total);
        $items[]=$this->get_row($crop_total);
        $items[]=$this->get_row($grand_total);
        $this->json_return($items);
        die();


    }
    private function initialize_row($crop_name,$crop_type_name,$variety_name,$pack_size,$warehouses)
    {
        $row=$this->get_preference_headers($warehouses);
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
        foreach($info  as $key=>$r)
        {
            if(substr($key,-3)=='pkt')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=$info[$key];
                }
            }
            elseif(substr($key,-2)=='kg')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],3,'.','');
                }
            }
            elseif(substr($key,0,6)=='amount')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],2);
                }
            }
            else
            {
                $row[$key]=$info[$key];
            }

        }
        return $row;


    }
    private function reset_row($info)
    {
        foreach($info as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')||($key=='amount_price_unit')))
            {
                $info[$key]=0;
            }
        }
        return $info;
    }

}
