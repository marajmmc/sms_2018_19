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
        $this->permissions=User_helper::get_permission('Report_stock_raw_summary');
        $this->controller_url='report_stock_raw_summary';
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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            if(!$reports['packing_item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='This packing item field is required';
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
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $packing_item=$this->input->post('packing_item');
        $items=array();
        $this->db->from($this->config->item('table_sms_stock_summary_raw').' stock_summary_raw');
        $this->db->select('stock_summary_raw.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_raw.variety_id','LEFT');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' croptype','croptype.id=v.crop_type_id','LEFT');
        $this->db->select('croptype.id crop_type_id, croptype.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=croptype.crop_id','LEFT');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_raw.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');
        $this->db->order_by('crop.id, croptype.id, v.id, pack.id');
        $this->db->where('stock_summary_raw.packing_item',$packing_item);
        if($variety_id>0 && is_numeric($variety_id))
        {
            $this->db->where('stock_summary_raw.variety_id',$variety_id);
        }
        if($crop_type_id>0 && is_numeric($crop_type_id))
        {
            $this->db->where('v.crop_type_id',$crop_type_id);
        }

        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('croptype.crop_id',$crop_id);
        }
        if($pack_size_id>0 && is_numeric($pack_size_id))
        {
            $this->db->where('stock_summary_raw.pack_size_id',$pack_size_id);
        }
        $results=$this->db->get()->result_array();
        if($packing_item!=$this->config->item('system_common_foil'))
        {
            $varieties=array();
            foreach($results as $result)
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]['packing_item']=$result['packing_item'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['crop_name']=$result['crop_name'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['crop_type_name']=$result['crop_type_name'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['variety_name']=$result['variety_name'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
                $varieties[$result['variety_id']][$result['pack_size_id']]['current_stock_pcs_kg']=$result['current_stock'];
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
            $grand_total['packing_item']=$crop_total['packing_item']=$type_total['packing_item']=$packing_item;
            $grand_total['current_stock_pcs_kg']=$crop_total['current_stock_pcs_kg']=$type_total['current_stock_pcs_kg']=0;
            $prev_crop_name='';
            $prev_type_name='';
            $first_row=true;

            foreach($varieties as $variety)
            {
                foreach($variety as $pack)
                {
                    if(!$first_row)
                    {
                        if($prev_crop_name!=$pack['crop_name'])
                        {
                            $items[]=$this->get_row($type_total);
                            $items[]=$this->get_row($crop_total);

                            $prev_crop_name=$pack['crop_name'];
                            $prev_type_name=$pack['crop_type_name'];
                            $type_total['current_stock_pcs_kg']=0;
                            $crop_total['current_stock_pcs_kg']=0;
                        }
                        elseif($prev_type_name!=$pack['crop_type_name'])
                        {
                            $items[]=$this->get_row($type_total);
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
                    $type_total['current_stock_pcs_kg']+=$pack['current_stock_pcs_kg'];
                    $crop_total['current_stock_pcs_kg']+=$pack['current_stock_pcs_kg'];
                    $grand_total['current_stock_pcs_kg']+=$pack['current_stock_pcs_kg'];
                    $items[]=$this->get_row($pack);
                }
            }
            if($results)
            {
                $items[]=$this->get_row($type_total);
                $items[]=$this->get_row($crop_total);
                $items[]=$this->get_row($grand_total);
            }
        }
        else
        {
            $grand_total['crop_name']='Grand Total';
            $grand_total['crop_type_name']='';
            $grand_total['variety_name']='';
            $grand_total['pack_size']='';
            $grand_total['current_stock_pcs_kg']=0;
            foreach($results as $result)
            {
                $result['current_stock_pcs_kg']=number_format($result['current_stock'],3,'.','');
                $items[]=$result;
                $grand_total['current_stock_pcs_kg']=number_format($result['current_stock'],3,'.','');
                $items[]=$grand_total;
            }
        }
        $this->json_return($items);
        die();
    }
    private function get_row($info)
    {
        $row=array();
        $row['crop_name']=$info['crop_name'];
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['pack_size']=$info['pack_size'];
        if($info['packing_item']==$this->config->item('system_master_foil'))
        {
            $row['current_stock_pcs_kg']=number_format($info['current_stock_pcs_kg'],3,'.','');
        }
        else
        {
            $row['current_stock_pcs_kg']=$info['current_stock_pcs_kg'];
        }
        return $row;
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
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['current_stock_pcs_kg']= 1;
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
}
