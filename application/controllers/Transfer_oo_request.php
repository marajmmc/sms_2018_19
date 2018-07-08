<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_oo_request extends Root_Controller
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
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="ajax_transfer_oo_variety_info")
        {
            $this->system_ajax_transfer_oo_variety_info();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method)
    {
        $data['barcode']= 1;
        $data['outlet_name_source']= 1;
        $data['outlet_name_destination']= 1;
        $data['date_request']= 1;
        $data['remarks_request']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_request']= 1;
        if($method=='list_all')
        {

        }
        return $data;
    }
    private function system_set_preference($method='list')
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {

            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
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
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            //$data['system_preference_items']= $this->get_preference();
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Outlet to Outlet Transfer Request List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
        $this->db->select('transfer_oo.id, transfer_oo.date_request, transfer_oo.quantity_total_request_kg quantity_total_request, transfer_oo.remarks_request');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' source_outlet_info','source_outlet_info.customer_id=transfer_oo.outlet_id_source AND source_outlet_info.revision=1 AND source_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('source_outlet_info.name outlet_name_source, source_outlet_info.customer_code outlet_code_source');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' destination_outlet_info','destination_outlet_info.customer_id=transfer_oo.outlet_id_destination AND destination_outlet_info.revision=1 AND destination_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('destination_outlet_info.name outlet_name_destination, destination_outlet_info.customer_code outlet_code_destination');

        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = source_outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('transfer_oo.status_request',$this->config->item('system_status_pending'));
        $this->db->order_by('transfer_oo.id','DESC');
        if($this->locations['division_id']>0)
        {
            $this->db->where('divisions.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zones.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_outlet($result['id']);
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['outlet_code']=$result['outlet_code'];
            $item['remarks_request']=$result['remarks_request'];
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_request']=$result['quantity_total_request'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Outlet to Outlet New Transfer Request";
            $data['item']['id']=0;
            $data['item']['outlet_id_source']='';
            $data['item']['outlet_id_destination']='';
            $data['item']['zone_id']='';
            $data['item']['territory_id']='';
            $data['item']['district_id']='';
            $data['item']['outlet_id']='';
            $data['item']['date_request']=time();
            $data['item']['remarks_request']='';
            $data['items']=[];
            $data['too_variety_info']=[];//$this->get_transfer_oo_variety_info(258);

            /*Source Outlet */
            $this->db->from($this->config->item('table_login_csetup_customer').' customer');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('customer.id');
            $this->db->select('cus_info.type, cus_info.district_id, cus_info.customer_id value, cus_info.name text');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->where('customer.status',$this->config->item('system_status_active'));
            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            if($this->locations['division_id']>0)
            {
                $this->db->where('divisions.id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('zones.id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('territories.id',$this->locations['territory_id']);
                        if($this->locations['district_id']>0)
                        {
                            $this->db->where('districts.id',$this->locations['district_id']);
                        }
                    }
                }
            }
            $data['outlet_sources']=$this->db->get()->result_array();

            /*Destination Outlet */
            $this->db->from($this->config->item('table_login_csetup_customer').' customer');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('customer.id');
            $this->db->select('cus_info.type, cus_info.district_id, cus_info.customer_id value, cus_info.name text');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->where('customer.status',$this->config->item('system_status_active'));
            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            if($this->locations['division_id']>0)
            {
                $this->db->where('divisions.id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('zones.id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('territories.id',$this->locations['territory_id']);
                        if($this->locations['district_id']>0)
                        {
                            $this->db->where('districts.id',$this->locations['district_id']);
                        }
                    }
                }
            }
            $data['outlet_destinations']=$this->db->get()->result_array();

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
            $this->db->select('transfer_oo.id, transfer_oo.date_request, transfer_oo.quantity_total_request_kg, transfer_oo.status_request, transfer_oo.remarks_request');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_oo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('edit',$item_id,'Edit Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_pending'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO already forwarded.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('edit',$item_id,'User Location Not Assign.');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $data['zones']=true;
            $data['territories']=true;
            $data['districts']=true;
            $data['outlets']=true;

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' transfer_oo_details');
            $this->db->select('transfer_oo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_oo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('transfer_oo_details.transfer_oo_id',$item_id);
            $this->db->where('transfer_oo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['too_variety_info']=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id']);

            $data['title']="Outlet to HQ Edit Transfer Request :: ". Barcode_helper::get_barcode_transfer_outlet_to_warehouse($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_oo'),array('*'),array('status !="'.$this->config->item('system_status_delete').'"', 'id ='.$id));

            /*$this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
            $this->db->select('transfer_oo.id, transfer_oo.date_request, transfer_oo.quantity_total_request_kg, transfer_oo.status_request, transfer_oo.remarks_request');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_oo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo.id',$id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();*/
            if(!$data['item'])
            {
                System_helper::invalid_try('save',$id,'Update Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet to outlet transfer request already forwarded.';
                $this->json_return($ajax);
            }

            /* Source & Destination outlet checking assign permission*/
            //$outlet_ids[0]=0;
            $outlet_ids[$data['item']['outlet_id_source']]=$data['item']['outlet_id_source'];
            $outlet_ids[$data['item']['outlet_id_destination']]=$data['item']['outlet_id_destination'];

            $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            /* Source & Destination outlet checking assign permission*/
            //$outlet_ids[0]=0;
            $outlet_ids[$item_head['outlet_id_source']]=$item_head['outlet_id_source'];
            $outlet_ids[$item_head['outlet_id_destination']]=$item_head['outlet_id_destination'];

            $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($item_head['outlet_id_source']);
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('customer.id');
        $this->db->select('cus_info.type, cus_info.district_id, cus_info.customer_id value, cus_info.name text');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->where('customer.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->where_in('customer.id',$outlet_ids);
        if($this->locations['division_id']>0)
        {
            $this->db->where('divisions.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zones.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $outlets=$this->db->get()->result_array();
        echo $this->db->last_query();
        die();
        if(!$this->check_my_editable($data['item']))
        {
            System_helper::invalid_try('save',$id,'User location not assign.');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
        $pack_sizes=array();
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]['value']=$result['value'];
            $pack_sizes[$result['value']]['text']=$result['text'];
        }

        $quantity_total_request_kg=0;
        if($items)
        {
            foreach($items as $item)
            {
                if(!isset($too_variety_info[$item['variety_id']][$item['pack_size_id']]))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                    $this->json_return($ajax);
                }

                $quantity_request=$item['quantity_request'];
                $quantity_request_kg=(($pack_sizes[$item['pack_size_id']]['text']*$item['quantity_request'])/1000);
                $quantity_total_request_kg+=$quantity_request_kg;
                if($quantity_request>$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt'])
                {
                    $stock_available_excess=($quantity_request-$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                    $ajax['status']=false;
                    $ajax['system_message']='Return quantity already exceed. ( Exceed return quantity: '.$stock_available_excess.' pkt.)';
                    $this->json_return($ajax);
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            $results=Query_helper::get_info($this->config->item('table_sms_transfer_oo_details'),array('*'),array('transfer_oo_id ='.$id));
            $old_items=array();
            foreach($results as $result)
            {
                $old_items[$result['variety_id']][$result['pack_size_id']]=$result;
            }
            $data=array();
            $data['status'] = $this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_transfer_oo_details'),$data, array('transfer_oo_id='.$id), false);

            $data=array();
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_transfer_oo_details_histories'),$data, array('transfer_oo_id='.$id,'revision=1'), false);

            $this->db->where('transfer_oo_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_transfer_oo_details_histories'));

            $item_head['date_request']=$time;
            $item_head['quantity_total_request_kg']=$quantity_total_request_kg;
            $item_head['quantity_total_approve_kg']=$quantity_total_request_kg;
            $item_head['quantity_total_receive_kg']=$quantity_total_request_kg;
            $item_head['date_updated_request']=$time;
            $item_head['user_updated_request']=$user->user_id;
            $this->db->set('revision_count_request', 'revision_count_request+1', FALSE);
            Query_helper::update($this->config->item('table_sms_transfer_oo'),$item_head, array('id='.$id), false);

            foreach($items as $item)
            {
                if(isset($old_items[$item['variety_id']][$item['pack_size_id']]))
                {
                    $data=array();
                    $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                    $data['quantity_request']=$item['quantity_request'];
                    $data['quantity_approve']=$data['quantity_request'];
                    $data['quantity_receive']=$data['quantity_request'];
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::update($this->config->item('table_sms_transfer_oo_details'),$data, array('transfer_oo_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);
                }
                else
                {
                    $data=array();
                    $data['transfer_oo_id']=$id;
                    $data['variety_id']=$item['variety_id'];
                    $data['pack_size_id']=$item['pack_size_id'];
                    $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                    $data['quantity_request']=$item['quantity_request'];
                    $data['quantity_approve']=$data['quantity_request'];
                    $data['quantity_receive']=$data['quantity_request'];
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::add($this->config->item('table_sms_transfer_oo_details'),$data, false);
                }

                $data=array();
                $data['transfer_oo_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                $data['quantity']=$item['quantity_request'];
                $data['revision']=1;
                $data['date_created']=$time;
                $data['user_created']=$user->user_id;
                Query_helper::add($this->config->item('table_sms_transfer_oo_details_histories'),$data, false);
            }
        }
        else
        {
            $item_head['date_request']=$time;
            $item_head['revision_count_request']=1;
            $item_head['status']=$this->config->item('system_status_active');
            $item_head['status_request']=$this->config->item('system_status_pending');
            $item_head['quantity_total_request_kg']=$quantity_total_request_kg;
            $item_head['quantity_total_approve_kg']=$quantity_total_request_kg;
            $item_head['quantity_total_receive_kg']=$quantity_total_request_kg;
            $item_head['date_created_request']=$time;
            $item_head['user_created_request']=$user->user_id;
            $transfer_oo_id=Query_helper::add($this->config->item('table_sms_transfer_oo'),$item_head, false);
            foreach($items as $item)
            {
                $data=array();
                $data['transfer_oo_id']=$transfer_oo_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                $data['quantity_request']=$item['quantity_request'];
                $data['quantity_approve']=$data['quantity_request'];
                $data['quantity_receive']=$data['quantity_request'];
                $data['status']=$this->config->item('system_status_active');
                Query_helper::add($this->config->item('table_sms_transfer_oo_details'),$data, false);

                $data=array();
                $data['transfer_oo_id']=$transfer_oo_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                $data['quantity']=$item['quantity_request'];
                $data['revision']=1;
                $data['date_created']=$time;
                $data['user_created']=$user->user_id;
                Query_helper::add($this->config->item('table_sms_transfer_oo_details_histories'),$data, false);
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_ajax_transfer_oo_variety_info()
    {
        $outlet_id=$this->input->post('outlet_id_source');
        $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($outlet_id);
        $this->json_return($too_variety_info);
    }
    private function check_my_editable($outlet)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$outlet['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$outlet['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$outlet['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$outlet['district_id']))
        {
            return false;
        }
        return true;
    }
    private function check_validation()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        if($id==0)
        {
            $this->form_validation->set_rules('item[outlet_id_source]',$this->lang->line('LABEL_OUTLET_NAME_SOURCE'),'required');
            $this->form_validation->set_rules('item[outlet_id_destination]',$this->lang->line('LABEL_OUTLET_NAME_DESTINATION'),'required');
        }
        $this->form_validation->set_rules('id',$this->lang->line('LABEL_ID'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        $item = $this->input->post("item");
        if($item['outlet_id_source'] == $item['outlet_id_destination'])
        {
            $this->message="You can't select same outlet.";
            return false;
        }
        $items = $this->input->post("items");
        if((sizeof($items)>0))
        {
            $duplicate_item=array();
            $status_duplicate_item=false;
            foreach($items as $item)
            {

                /// empty checking
                if(!(($item['variety_id']>0) && ($item['pack_size_id']>=0)))
                {
                    $this->message='Un-finish input (variety info :: '.$item['variety_id'].').';
                    return false;
                }
                // quantity zero.
                if(!($item['quantity_request']>0))
                {
                    $this->message="Request quantity can't be zero.";
                    return false;
                }
                // duplicate variety checking
                if(isset($duplicate_item[$item['variety_id']][$item['pack_size_id']]))
                {
                    $duplicate_item[$item['variety_id']][$item['pack_size_id']]+=1;
                    $status_duplicate_item=true;
                }
                else
                {
                    $duplicate_item[$item['variety_id']][$item['pack_size_id']]=1;
                }
            }
            if($status_duplicate_item==true)
            {
                $this->message='Invalid input, variety duplicate entry.';
                return false;
            }
        }
        else
        {
            $this->message='Variety information is empty.';
            return false;
        }
        return true;
    }
}
