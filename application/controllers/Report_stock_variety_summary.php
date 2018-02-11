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
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
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
            $user = User_helper::get_user();
            $data['options']=$reports;
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));

            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
            $data['system_preference_items']['crop_name']= 1;
            $data['system_preference_items']['crop_type_name']= 1;
            $data['system_preference_items']['variety_name']= 1;
            $data['system_preference_items']['pack_size']= 1;
            foreach($data['warehouses'] as $warehouse)
            {
                $data['system_preference_items']['warehouse_'.$warehouse['value'].'_pkt']= 1;
                $data['system_preference_items']['warehouse_'.$warehouse['value'].'_kg']= 1;
            }
            //$data['system_preference_items']['current_stock']= 1;
            $data['system_preference_items']['current_stock_pkt']= 1;
            $data['system_preference_items']['current_stock_kg']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }
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
    private function system_get_items()
    {
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $items=array();

        $warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));

        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('stock_summary_variety.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_variety.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' croptype','croptype.id=v.crop_type_id','INNER');
        $this->db->select('croptype.id crop_type_id, croptype.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=croptype.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');
        $this->db->order_by('crop.id, croptype.id, v.id, pack.id');

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
            $this->db->where('croptype.crop_id',$crop_id);
        }
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('stock_summary_variety.pack_size_id',$pack_size_id);
        }
        $results=$this->db->get()->result_array();
        $varieties=array();
        foreach($results as $result)
        {
            $varieties[$result['variety_id']][$result['pack_size_id']]['crop_name']=$result['crop_name'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['crop_type_name']=$result['crop_type_name'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['variety_name']=$result['variety_name'];
            if($result['pack_size_id']==0)
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]['pack_size']='Bulk';
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_pkt']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_kg']=$result['current_stock'];
            }
            else
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_pkt']=$result['current_stock'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['warehouse_'.$result['warehouse_id'].'_kg']=$result['current_stock']*$result['pack_size']/1000;
            }


        }


        $type_total=array();
        $crop_total=array();
        $grand_total=array();
        $type_total['crop_name']='';
        $type_total['crop_type_name']='';
        $type_total['variety_name']='Total Type';

        $crop_total['crop_name']='';
        $crop_total['crop_type_name']='Total Crop';
        $crop_total['variety_name']='';

        $grand_total['crop_name']='Grand Total';
        $grand_total['crop_type_name']='';
        $grand_total['variety_name']='';

        $grand_total['pack_size']=$crop_total['pack_size']=$type_total['pack_size']='';
        foreach($warehouses as $warehouse)
        {
            $grand_total['warehouse_'.$warehouse['value'].'_pkt']=$crop_total['warehouse_'.$warehouse['value'].'_pkt']=$type_total['warehouse_'.$warehouse['value'].'_pkt']=0;
            $grand_total['warehouse_'.$warehouse['value'].'_kg']=$crop_total['warehouse_'.$warehouse['value'].'_kg']=$type_total['warehouse_'.$warehouse['value'].'_kg']=0;
        }
        $grand_total['current_stock_pkt']=$crop_total['current_stock_pkt']=$type_total['current_stock_pkt']=0;
        $grand_total['current_stock_kg']=$crop_total['current_stock_kg']=$type_total['current_stock_kg']=0;


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
                        $items[]=$this->get_row($type_total,$warehouses);
                        $type_total=$this->reset_row($type_total,$warehouses);
                        $items[]=$this->get_row($crop_total,$warehouses);
                        $crop_total=$this->reset_row($crop_total,$warehouses);
                        $prev_crop_name=$pack['crop_name'];
                        $prev_type_name=$pack['crop_type_name'];
                    }
                    elseif($prev_type_name!=$pack['crop_type_name'])
                    {
                        $items[]=$this->get_row($type_total,$warehouses);
                        $type_total=$this->reset_row($type_total,$warehouses);
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
                foreach($warehouses as $warehouse)
                {
                    if(isset($pack['warehouse_'.$warehouse['value'].'_pkt'])&&($pack['warehouse_'.$warehouse['value'].'_pkt']>0))
                    {
                        $type_total['warehouse_'.$warehouse['value'].'_pkt']+=$pack['warehouse_'.$warehouse['value'].'_pkt'];
                        $crop_total['warehouse_'.$warehouse['value'].'_pkt']+=$pack['warehouse_'.$warehouse['value'].'_pkt'];
                        $grand_total['warehouse_'.$warehouse['value'].'_pkt']+=$pack['warehouse_'.$warehouse['value'].'_pkt'];
                    }
                    if(isset($pack['warehouse_'.$warehouse['value'].'_kg'])&&($pack['warehouse_'.$warehouse['value'].'_kg']>0))
                    {
                        $type_total['warehouse_'.$warehouse['value'].'_kg']+=$pack['warehouse_'.$warehouse['value'].'_kg'];
                        $crop_total['warehouse_'.$warehouse['value'].'_kg']+=$pack['warehouse_'.$warehouse['value'].'_kg'];
                        $grand_total['warehouse_'.$warehouse['value'].'_kg']+=$pack['warehouse_'.$warehouse['value'].'_kg'];
                    }
                }
                $items[]=$this->get_row($pack,$warehouses);
            }
        }
        $items[]=$this->get_row($type_total,$warehouses);
        $items[]=$this->get_row($crop_total,$warehouses);
        $items[]=$this->get_row($grand_total,$warehouses);
        $this->json_return($items);
        die();


    }
    private function get_row($info,$warehouses)
    {
        $row=array();
        $row['crop_name']=$info['crop_name'];
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['pack_size']=$info['pack_size'];
        $row['current_stock_pkt']=0;
        $row['current_stock_kg']=0;
        foreach($warehouses as $warehouse)
        {
            if(isset($info['warehouse_'.$warehouse['value'].'_pkt'])&&($info['warehouse_'.$warehouse['value'].'_pkt']>0))
            {
                $row['current_stock_pkt']+=$info['warehouse_'.$warehouse['value'].'_pkt'];
                $row['warehouse_'.$warehouse['value'].'_pkt']=$info['warehouse_'.$warehouse['value'].'_pkt'];

            }
            else
            {
                $row['warehouse_'.$warehouse['value'].'_pkt']='';
            }

            if(isset($info['warehouse_'.$warehouse['value'].'_kg'])&&($info['warehouse_'.$warehouse['value'].'_kg']>0))
            {
                $row['current_stock_kg']+=$info['warehouse_'.$warehouse['value'].'_kg'];
                $row['warehouse_'.$warehouse['value'].'_kg']=number_format($info['warehouse_'.$warehouse['value'].'_kg'],3,'.','');
            }
            else
            {
                $row['warehouse_'.$warehouse['value'].'_kg']='';
            }
        }
        if($row['current_stock_pkt']==0)
        {
            $row['current_stock_pkt']='';
        }
        if($row['current_stock_kg']==0)
        {
            $row['current_stock_kg']='';
        }
        else
        {
            $row['current_stock_kg']=number_format($row['current_stock_kg'],3,'.','');
        }
        return $row;


    }
    private function reset_row($info, $warehouses)
    {
        foreach($warehouses as $warehouse)
        {
            $info['warehouse_'.$warehouse['value'].'_pkt']=0;
            $info['warehouse_'.$warehouse['value'].'_kg']=0;
        }
        return $info;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));

            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
            $data['system_preference_items']['crop_name']= 1;
            $data['system_preference_items']['crop_type_name']= 1;
            $data['system_preference_items']['variety_name']= 1;
            $data['system_preference_items']['pack_size']= 1;
            foreach($data['warehouses'] as $warehouse)
            {
                $data['system_preference_items']['warehouse_'.$warehouse['value'].'_pkt']= 1;
                $data['system_preference_items']['warehouse_'.$warehouse['value'].'_kg']= 1;
            }
            //$data['system_preference_items']['current_stock']= 1;
            $data['system_preference_items']['current_stock_pkt']= 1;
            $data['system_preference_items']['current_stock_kg']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }
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
