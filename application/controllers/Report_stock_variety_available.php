<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock_variety_available extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
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
            $data['title']="Variety Available Stock Report Search";
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                        if($this->locations['district_id']>0)
                        {
                            $this->db->from($this->config->item('table_login_csetup_customer').' customer');
                            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id=customer.id','INNER');
                            $this->db->select('customer.id value, cus_info.name text');
                            $this->db->where('customer.status',$this->config->item('system_status_active'));
                            $this->db->where('cus_info.district_id',$this->locations['district_id']);
                            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
                            $this->db->where('cus_info.revision',1);
                            $data['outlets']=$this->db->get()->result_array();
                        }
                    }

                }
            }

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
        $data['current_stock_pkt']= 1;
        $data['current_stock_kg']= 1;
        $data['to_all_pkt']= 1;
        $data['to_all_kg']= 1;
        $data['to_search_pkt']= 1;
        $data['to_search_kg']= 1;
        $data['available_stock_pkt']= 1;
        $data['available_stock_kg']= 1;
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {

            $reports=$this->input->post('report');
            $data['options']=$reports;

            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));

            $data['title']="Variety Available Stock Report";
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

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->order_by('outlet_info.ordering');
        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));

        if($division_id>0)
        {
            $this->db->where('zones.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('territories.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('districts.id',$district_id);
                        if($outlet_id>0)
                        {
                            $this->db->where('outlet_info.customer_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $results=$this->db->get()->result_array();

        $outlet_ids=array();
        $outlet_ids[0]=0;
        foreach($results as $result)        {

            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }
        /* calculate search to*/
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->join($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details','transfer_wo_details.transfer_wo_id=transfer_wo.id AND transfer_wo_details.status="'.$this->config->item('system_status_active').'"','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('SUM(transfer_wo_details.quantity_approve) quantity_approve, transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $this->db->where('transfer_wo.status',$this->config->item('system_status_active'));
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_wo.status_delivery',$this->config->item('system_status_pending'));
        $this->db->where_in('transfer_wo.outlet_id',$outlet_ids);
        $this->db->group_by('transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0 && is_numeric($crop_type_id))
            {
                $this->db->where('v.crop_type_id',$crop_type_id);
                if($variety_id>0 && is_numeric($variety_id))
                {
                    $this->db->where('transfer_wo_details.variety_id',$variety_id);
                }
            }
        }
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $to_search=array();
        foreach($results as $result)
        {
            $to_search[$result['variety_id']][$result['pack_size_id']]=$result['quantity_approve'];
        }


        /* calculate total to*/
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->join($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details','transfer_wo_details.transfer_wo_id=transfer_wo.id AND transfer_wo_details.status="'.$this->config->item('system_status_active').'"','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('SUM(transfer_wo_details.quantity_approve) quantity_approve, transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $this->db->where('transfer_wo.status',$this->config->item('system_status_active'));
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_wo.status_delivery',$this->config->item('system_status_pending'));
        $this->db->group_by('transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0 && is_numeric($crop_type_id))
            {
                $this->db->where('v.crop_type_id',$crop_type_id);
                if($variety_id>0 && is_numeric($variety_id))
                {
                    $this->db->where('transfer_wo_details.variety_id',$variety_id);
                }
            }
        }
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $to_all=array();
        foreach($results as $result)
        {
            $to_all[$result['variety_id']][$result['pack_size_id']]=$result['quantity_approve'];
        }


        $items=array();
        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('SUM(stock_summary_variety.current_stock) current_stock',false);

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_variety.variety_id','INNER');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size,pack.id pack_size_id');

        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $this->db->order_by('pack.id');
        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0 && is_numeric($crop_type_id))
            {
                $this->db->where('v.crop_type_id',$crop_type_id);
                if($variety_id>0 && is_numeric($variety_id))
                {
                    $this->db->where('stock_summary_variety.variety_id',$variety_id);
                }
            }
        }
        $this->db->where('v.status',$this->config->item('system_status_active'));
        if($pack_size_id>0 && is_numeric($pack_size_id))
        {
            $this->db->where('stock_summary_variety.pack_size_id',$pack_size_id);
        }
        else
        {
            $this->db->where('stock_summary_variety.pack_size_id >',0);
        }
        $this->db->group_by('v.id');
        $this->db->group_by('pack.id');
        $results=$this->db->get()->result_array();
        $type_total=$this->initialize_row('','','Total Type','');
        $crop_total=$this->initialize_row('','Total Crop','','');
        $grand_total=$this->initialize_row('Grand Total','','','');
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        foreach($results as $result)
        {
            $info=$this->initialize_row($result['crop_name'],$result['crop_type_name'],$result['variety_name'],$result['pack_size']);
            if(!$first_row)
            {
                if($prev_crop_name!=$result['crop_name'])
                {
                    $items[]=$type_total;
                    $items[]=$crop_total;
                    $type_total=$this->reset_row($type_total);
                    $crop_total=$this->reset_row($crop_total);
                    $prev_crop_name=$result['crop_name'];
                    $prev_type_name=$result['crop_type_name'];
                }
                elseif($prev_type_name!=$result['crop_type_name'])
                {
                    $items[]=$type_total;
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
            $info['current_stock_pkt']=$result['current_stock'];
            $info['current_stock_kg']=$result['current_stock']*$result['pack_size']/1000;
            if(isset($to_all[$result['variety_id']][$result['pack_size_id']]))
            {
                $info['to_all_pkt']=$to_all[$result['variety_id']][$result['pack_size_id']];
                $info['to_all_kg']=$info['to_all_pkt']*$result['pack_size']/1000;
            }
            $info['available_stock_pkt']=$info['current_stock_pkt']-$info['to_all_pkt'];
            $info['available_stock_kg']=$info['current_stock_kg']-$info['to_all_kg'];
            if(isset($to_search[$result['variety_id']][$result['pack_size_id']]))
            {
                $info['to_search_pkt']=$to_search[$result['variety_id']][$result['pack_size_id']];
                $info['to_search_kg']=$info['to_search_pkt']*$result['pack_size']/1000;
            }
            foreach($info  as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }
            $items[]=$info;
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
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
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers());
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
