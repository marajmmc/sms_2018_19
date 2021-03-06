<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_wo_delivery extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Transfer_wo_delivery');
        $this->controller_url='transfer_wo_delivery';
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
        if($action=="list_all")
        {
            $this->system_list_all();
        }
        elseif($action=="get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="challan_print")
        {
            $this->system_challan_print($id);
        }
        elseif($action=="print_courier")
        {
            $this->system_print_courier($id);
        }
        elseif($action=="delivery")
        {
            $this->system_delivery($id);
        }
        elseif($action=="save_delivery")
        {
            $this->system_save_delivery();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="set_preference_all")
        {
            $this->system_set_preference_all();
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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['title']="HQ to Outlet Delivery List";
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
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->select('transfer_wo.id, transfer_wo.date_request,transfer_wo.date_approve, transfer_wo.quantity_total_request_kg quantity_total_request, transfer_wo.quantity_total_approve_kg quantity_total_approve');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('transfer_wo.status_request',$this->config->item('system_status_forwarded'));
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_wo.status_delivery',$this->config->item('system_status_pending'));
        $this->db->where('outlet_info.revision',1);
        //$this->db->order_by('transfer_wo.id','DESC');
        $this->db->order_by('transfer_wo.date_approve','DESC');

        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($result['id']);
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['date_approve']=System_helper::display_date_time($result['date_approve']);
            $item['outlet_code']=$result['outlet_code'];
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_approve']=number_format($result['quantity_total_approve'],3,'.','');
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_all();
            $data['title']="HQ to Outlet Transfer All List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_all');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_all()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=100;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->select(
            '
            transfer_wo.id,
            transfer_wo.date_request,
            transfer_wo.date_approve,
            transfer_wo.quantity_total_request_kg quantity_total_request,
            transfer_wo.quantity_total_approve_kg quantity_total_approve,
            transfer_wo.quantity_total_receive_kg quantity_total_receive,
            transfer_wo.status, transfer_wo.status_request,
            transfer_wo.status_approve,
            transfer_wo.status_delivery,
            transfer_wo.status_receive,
            transfer_wo.status_receive_forward,
            transfer_wo.status_receive_approve,
            transfer_wo.status_system_delivery_receive
            ');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('transfer_wo.status_request',$this->config->item('system_status_forwarded'));
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('outlet_info.revision',1);
        $this->db->order_by('transfer_wo.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($result['id']);
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['date_approve']=System_helper::display_date_time($result['date_approve']);
            $item['outlet_code']=$result['outlet_code'];
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_request']=number_format($result['quantity_total_request'],3,'.','');
            $item['quantity_total_approve']=number_format($result['quantity_total_approve'],3,'.','');
            $item['quantity_total_receive']=number_format($result['quantity_total_receive'],3,'.','');
            $item['status']=$result['status'];
            $item['status_request']=$result['status_request'];
            $item['status_approve']=$result['status_approve'];
            $item['status_delivery']=$result['status_delivery'];
            $item['status_receive']=$result['status_receive'];
            $item['status_receive_forward']=$result['status_receive_forward'];
            $item['status_receive_approve']=$result['status_receive_approve'];
            $item['status_system_delivery_receive']=$result['status_system_delivery_receive'];
            if($result['status_approve']==$this->config->item('system_status_rejected'))
            {
                $item['status_delivery']='';
                $item['status_receive']='';
                $item['status_receive_forward']='';
                $item['status_receive_approve']='';
                $item['status_system_delivery_receive']='';
            }
            if($result['status_system_delivery_receive']==$this->config->item('system_status_yes'))
            {
                $item['status_receive_forward']='';
                $item['status_receive_approve']='';
            }
            $items[]=$item;
        }
        $this->json_return($items);
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

            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO is not forwarded (request). Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO not approve & forwarded. Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected. Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']==$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already delivered. Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $user_ids[$data['item']['user_updated_delivery']]=$data['item']['user_updated_delivery'];
            $user_ids[$data['item']['user_updated_delivery_forward']]=$data['item']['user_updated_delivery_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $variety_ids=array();
            foreach($data['items'] as $row)
            {
                $variety_ids[$row['variety_id']]=$row['variety_id'];
            }
            $data['stocks']=Stock_helper::get_variety_stock($variety_ids);
            $data['stock_available_packing']=$this->get_available_packing_stocks($variety_ids, $item_id);

            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['couriers']=Query_helper::get_info($this->config->item('table_login_basic_setup_couriers'),array('id, name'),array('status="'.$this->config->item('system_status_active').'"'), '','',array('ordering'));
            $data['courier']=Query_helper::get_info($this->config->item('table_sms_transfer_wo_courier_details'),array('*'),array('transfer_wo_id='.$item_id),1);
            if(!$data['courier'])
            {
                $data['courier']['date_delivery']='';
                $data['courier']['date_challan']='';
                $data['courier']['challan_no']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
                $data['courier']['courier_id']='';
                $data['courier']['courier_tracing_no']='';
                $data['courier']['place_booking_source']='';
                $data['courier']['place_destination']='';
                $data['courier']['date_booking']='';
                $data['courier']['remarks']='';
            }

            $data['title']="HQ to Outlet Edit Delivery :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
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
    private function get_available_packing_stocks($variety_ids, $id)
    {
        /*current stock */
        $this->db->from($this->config->item('table_sms_stock_summary_variety'));
        if(sizeof($variety_ids)>0)
        {
            $this->db->where_in('variety_id',$variety_ids);
        }
        $results=$this->db->get()->result_array();
        $stock_available_packing=array();
        foreach($results as $result)
        {
            $stock_available_packing[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]=$result;
            $stock_available_packing[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['stock_available_packing_pkt']=$result['current_stock'];
        }
        /*all TO */
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->join($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details','transfer_wo_details.transfer_wo_id=transfer_wo.id AND transfer_wo_details.status="'.$this->config->item('system_status_active').'"','INNER');
        $this->db->select('SUM(transfer_wo_details.quantity_approve) quantity_approve, transfer_wo_details.variety_id, transfer_wo_details.pack_size_id, transfer_wo_details.pack_size, transfer_wo_details.warehouse_id');
        $this->db->where('transfer_wo.status',$this->config->item('system_status_active'));
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_wo.status_delivery',$this->config->item('system_status_pending'));
        $this->db->group_by('transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $this->db->where_in('transfer_wo_details.variety_id',$variety_ids);
        $this->db->where('transfer_wo_details.warehouse_id IS NOT NULL');
        $this->db->where('transfer_wo_details.transfer_wo_id !='.$id);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stock_available_packing[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['stock_available_packing_pkt']=($stock_available_packing[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['current_stock']-$result['quantity_approve']);
        }
        return $stock_available_packing;
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        $courier=$this->input->post('courier');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('
            transfer_wo.id,
            transfer_wo.date_request,
            transfer_wo.quantity_total_request_kg,
            transfer_wo.quantity_total_approve_kg,
            transfer_wo.status_request,
            transfer_wo.remarks_request,
            transfer_wo.date_approve,
            transfer_wo.status_approve,
            transfer_wo.status_delivery');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO is not request forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO not approved & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']==$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already delivered.';
                $this->json_return($ajax);
            }
            //$two_variety_info=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        /*if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }*/
        /*date validation*/
        /*if(!isset($courier['date_delivery']) || !strtotime($courier['date_delivery']))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('LABEL_DATE_DELIVERY'). ' field is required.';
            $this->json_return($ajax);
        }*/
        if(System_helper::get_time($courier['date_delivery'])>0)
        {
            if(!(System_helper::get_time($courier['date_delivery'])>=System_helper::get_time(System_helper::display_date($data['item']['date_approve']))))
            {
                $ajax['status']=false;
                $ajax['system_message']='Delivery date should be is greater than approval date.';
                $this->json_return($ajax);
            }
        }
        /*if(System_helper::get_time($courier['date_challan'])>0)
        {
            if(!(System_helper::get_time($courier['date_challan'])>=System_helper::get_time($courier['date_delivery'])))
            {
                $ajax['status']=false;
                $ajax['system_message']='Challan date should be is greater than delivery date.';
                $this->json_return($ajax);
            }
        }*/

        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
        $quantity_to_maximum_kg=$result['config_value'];
        $quantity_total_approve_kg=$data['item']['quantity_total_approve_kg'];
        if($quantity_total_approve_kg>$quantity_to_maximum_kg)
        {
            $ajax['status']=false;
            $ajax['system_message']='Transfer order maximum quantity '.$quantity_to_maximum_kg.' kg. you have to already exist quantity ('.($quantity_total_approve_kg-$quantity_to_maximum_kg).' kg).';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
        $this->db->select('transfer_wo_details.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('transfer_wo_details.transfer_wo_id',$id);
        $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
        $data['items']=$this->db->get()->result_array();


        $old_items=array();
        $variety_ids=array();
        foreach($data['items'] as $item)
        {
            $old_items[$item['variety_id']][$item['pack_size_id']]=$item;
            $variety_ids[$item['variety_id']]=$item['variety_id'];
        }

        $current_stocks=Stock_helper::get_variety_stock($variety_ids);
        foreach($items as $item)
        {
            if(!isset($old_items[$item['variety_id']][$item['pack_size_id']]))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                $this->json_return($ajax);
            }
            if(!isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]) || !($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Stock not available :: ( Variety ID: '.$item['variety_id'].' - Pack Size ID: '.$item['pack_size_id'].' - Warehouse ID: '.$item['warehouse_id'].')';
                $this->json_return($ajax);
            }
            if($old_items[$item['variety_id']][$item['pack_size_id']]['quantity_approve']>$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Current Stock Exceed :: ( Variety ID: '.$item['variety_id'].' - Pack Size ID: '.$item['pack_size_id'].' - Warehouse ID: '.$item['warehouse_id'].')';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $result=Query_helper::get_info($this->config->item('table_sms_transfer_wo_courier_details'),array('*'),array('transfer_wo_id='.$id));
        if($result)
        {
            if(strtotime($courier['date_challan']))
            {
                $courier['date_challan']=System_helper::get_time($courier['date_challan']);
            }
            else
            {
                unset($courier['date_challan']);
            }
            if(strtotime($courier['date_booking']))
            {
                $courier['date_booking']=System_helper::get_time($courier['date_booking']);
            }
            else
            {
                unset($courier['date_booking']);
            }
            $courier['date_delivery']=System_helper::get_time($courier['date_delivery']);
            $courier['date_updated']=$time;
            $courier['user_updated']=$user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_transfer_wo_courier_details'),$courier, array('transfer_wo_id='.$id));
        }
        else
        {
            if(strtotime($courier['date_challan']))
            {
                $courier['date_challan']=System_helper::get_time($courier['date_challan']);
            }
            else
            {
                unset($courier['date_challan']);
            }
            if(strtotime($courier['date_booking']))
            {
                $courier['date_booking']=System_helper::get_time($courier['date_booking']);
            }
            else
            {
                unset($courier['date_booking']);
            }
            $courier['transfer_wo_id']=$id;
            $courier['date_delivery']=System_helper::get_time($courier['date_delivery']);
            $courier['revision_count']=1;
            $courier['date_updated']=$time;
            $courier['user_updated']=$user->user_id;
            Query_helper::add($this->config->item('table_sms_transfer_wo_courier_details'),$courier);
        }

        /* variety relational table insert & update*/
        $data=array();
        $data['date_updated'] = $time;
        $data['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_wo_details_histories'),$data, array('transfer_wo_id='.$id,'revision=1'), false);

        $this->db->where('transfer_wo_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_sms_transfer_wo_details_histories'));

        $data=array();
        $data['remarks_challan']=$item_head['remarks_challan'];
        $data['date_updated_delivery']=$time;
        $data['user_updated_delivery']=$user->user_id;
        $this->db->set('revision_count_delivery', 'revision_count_delivery+1', FALSE);
        Query_helper::update($this->config->item('table_sms_transfer_wo'),$data, array('id='.$id), false);

        foreach($items as $item)
        {
            // this isset condition using for insert row information history table
            if(isset($old_items[$item['variety_id']][$item['pack_size_id']]))
            {
                $data=array();
                $data['warehouse_id']=$item['warehouse_id'];
                Query_helper::update($this->config->item('table_sms_transfer_wo_details'),$data, array('transfer_wo_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);

                $data=array();
                $data['transfer_wo_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['pack_size']=$old_items[$item['variety_id']][$item['pack_size_id']]['pack_size'];
                $data['quantity']=$old_items[$item['variety_id']][$item['pack_size_id']]['quantity_approve'];
                $data['warehouse_id']=$item['warehouse_id'];
                $data['revision']=1;
                $data['date_created']=$time;
                $data['user_created']=$user->user_id;
                Query_helper::add($this->config->item('table_sms_transfer_wo_details_histories'),$data, false);
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
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_wo.user_updated_receive_forward','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_receive_forward');
            $this->db->join($this->config->item('table_sms_transfer_wo_courier_details').' wo_courier_details','wo_courier_details.transfer_wo_id=transfer_wo.id','LEFT');
            $this->db->select('
                                wo_courier_details.date_delivery courier_date_delivery,
                                wo_courier_details.date_challan,
                                wo_courier_details.challan_no,
                                wo_courier_details.courier_tracing_no,
                                wo_courier_details.place_booking_source,
                                wo_courier_details.place_destination,
                                wo_courier_details.date_booking,
                                wo_courier_details.remarks remarks_couriers
                                ');
            $this->db->join($this->config->item('table_login_basic_setup_couriers').' courier','courier.id=wo_courier_details.courier_id','LEFT');
            $this->db->select('courier.name courier_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');

            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('details',$item_id,'View Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $user_ids[$data['item']['user_updated_delivery']]=$data['item']['user_updated_delivery'];
            $user_ids[$data['item']['user_updated_delivery_forward']]=$data['item']['user_updated_delivery_forward'];
            $user_ids[$data['item']['user_updated_receive_approve']]=$data['item']['user_updated_receive_approve'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=transfer_wo_details.warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $this->db->order_by('transfer_wo_details.id');
            $data['items']=$this->db->get()->result_array();

            $data['title']="HQ to Outlet Transfer Details :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_challan_print($id)
    {
        if(isset($this->permissions['action4'])&&($this->permissions['action4']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select(
                '
                outlet_info.customer_id outlet_id,
                outlet_info.name outlet_name, outlet_info.customer_code outlet_code,
                outlet_info.address outlet_address,
                outlet_info.phone outlet_phone
                ');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_sms_transfer_wo_courier_details').' wo_courier_details','wo_courier_details.transfer_wo_id=transfer_wo.id','LEFT');
            $this->db->select('
                                wo_courier_details.date_delivery courier_date_delivery,
                                wo_courier_details.date_challan,
                                wo_courier_details.challan_no,
                                wo_courier_details.courier_tracing_no,
                                wo_courier_details.place_booking_source,
                                wo_courier_details.place_destination,
                                wo_courier_details.date_booking,
                                wo_courier_details.remarks remarks_couriers
                                ');
            $this->db->join($this->config->item('table_login_basic_setup_couriers').' courier','courier.id=wo_courier_details.courier_id','LEFT');
            $this->db->select('courier.name courier_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_created','ui_created.user_id = transfer_wo.user_created_request','LEFT');
            $this->db->select('ui_created.name user_created_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated','ui_updated.user_id = transfer_wo.user_updated_request','LEFT');
            $this->db->select('ui_updated.name user_updated_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated_approve','ui_updated_approve.user_id = transfer_wo.user_updated_approve','LEFT');
            $this->db->select('ui_updated_approve.name user_updated_approve_full_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('challan_print',$item_id,'Print View Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            /*if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO request not forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO not approve & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO already rejected.';
                $this->json_return($ajax);
            }*/

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=transfer_wo_details.warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $variety_ids=array();
            foreach($data['items'] as $row)
            {
                $variety_ids[$row['variety_id']]=$row['variety_id'];
            }
            $data['stocks']=Stock_helper::get_variety_stock($variety_ids);

            /*
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];*/
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);

            $data['title']="HQ to Outlet Print View Delivery :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/challan_print",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/challan_print/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_print_courier($id)
    {
        if(isset($this->permissions['action4'])&&($this->permissions['action4']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select(
                '
                outlet_info.customer_id outlet_id,
                outlet_info.name outlet_name, outlet_info.customer_code outlet_code,
                outlet_info.address outlet_address,
                outlet_info.phone outlet_phone
                ');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_sms_transfer_wo_courier_details').' wo_courier_details','wo_courier_details.transfer_wo_id=transfer_wo.id','LEFT');
            $this->db->select('
                                wo_courier_details.date_delivery courier_date_delivery,
                                wo_courier_details.date_challan,
                                wo_courier_details.challan_no,
                                wo_courier_details.courier_tracing_no,
                                wo_courier_details.place_booking_source,
                                wo_courier_details.place_destination,
                                wo_courier_details.date_booking,
                                wo_courier_details.remarks remarks_couriers
                                ');
            $this->db->join($this->config->item('table_login_basic_setup_couriers').' courier','courier.id=wo_courier_details.courier_id','LEFT');
            $this->db->select('courier.name courier_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_created','ui_created.user_id = transfer_wo.user_created_request','LEFT');
            $this->db->select('ui_created.name user_created_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated','ui_updated.user_id = transfer_wo.user_updated_request','LEFT');
            $this->db->select('ui_updated.name user_updated_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated_approve','ui_updated_approve.user_id = transfer_wo.user_updated_approve','LEFT');
            $this->db->select('ui_updated_approve.name user_updated_approve_full_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Courier_print',$item_id,'Print View Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['title']="HQ to Outlet Courier Print :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/print_courier",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/print_courier/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_delivery($id)
    {
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_sms_transfer_wo_courier_details').' wo_courier_details','wo_courier_details.transfer_wo_id=transfer_wo.id','LEFT');
            $this->db->select('
                                wo_courier_details.date_delivery courier_date_delivery,
                                wo_courier_details.date_challan,
                                wo_courier_details.challan_no,
                                wo_courier_details.courier_tracing_no,
                                wo_courier_details.place_booking_source,
                                wo_courier_details.place_destination,
                                wo_courier_details.date_booking,
                                wo_courier_details.remarks remarks_couriers
                                ');
            $this->db->join($this->config->item('table_login_basic_setup_couriers').' courier','courier.id=wo_courier_details.courier_id','LEFT');
            $this->db->select('courier.name courier_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('delivery',$item_id,'Edit Delivery Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO request not forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO not approve & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TO already rejected.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']==$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already delivered.';
                $this->json_return($ajax);
            }

            /*date validation*/
            if(!($data['item']['courier_date_delivery']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='At first edit HQ to outlet delivery & provide required information';
                $this->json_return($ajax);
            }
            if(!($data['item']['courier_date_delivery']>=System_helper::get_time(System_helper::display_date($data['item']['date_approve']))))
            {
                $ajax['status']=false;
                $ajax['system_message']='At first edit HQ to outlet delivery & provide required information';
                $this->json_return($ajax);
            }
            /*if(strtotime(System_helper::display_date($data['item']['date_challan'])))
            {
                if(!(System_helper::get_time($data['item']['date_challan'])>=System_helper::get_time($data['item']['courier_date_delivery'])))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='At first edit HQ to outlet delivery & provide required information ';
                    $this->json_return($ajax);
                }
            }*/

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $user_ids[$data['item']['user_updated_delivery']]=$data['item']['user_updated_delivery'];
            $user_ids[$data['item']['user_updated_delivery_forward']]=$data['item']['user_updated_delivery_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=transfer_wo_details.warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $variety_ids=array();
            $status_empty_warehouse=false;
            foreach($data['items'] as $row)
            {
                $variety_ids[$row['variety_id']]=$row['variety_id'];
                if(!($row['warehouse_id']>0))
                {
                    $status_empty_warehouse=true;
                }
            }
            /*warehouse empty checking*/
            if($status_empty_warehouse)
            {
                $ajax['status']=false;
                $ajax['system_message']='At first edit HQ to outlet delivery & provide required information';
                $this->json_return($ajax);
            }
            $data['stocks']=Stock_helper::get_variety_stock($variety_ids);

            $data['title']="HQ to Outlet Delivery :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/delivery",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/delivery/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_delivery()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if($id>0)
        {
            if(!(isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if($item_head['status_delivery']!=$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Delivery is required.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select(
                '
                transfer_wo.id,
                transfer_wo.date_request,
                transfer_wo.quantity_total_request_kg,
                transfer_wo.status_request,
                transfer_wo.remarks_request,
                transfer_wo.status_approve,
                transfer_wo.status_delivery
                ');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select(
                'outlet_info.customer_id outlet_id,
                outlet_info.name outlet_name,
                outlet_info.customer_code outlet_code
                ');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('save_delivery',$id,'Update Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO is not forwarded (request).';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO is not approved & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']==$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already delivered.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        /*date validation*/
        /*if(!isset($courier['date_delivery']) || !strtotime($courier['date_delivery']))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('LABEL_DATE_DELIVERY'). ' field is required.';
            $this->json_return($ajax);
        }*/
        /*if(!(System_helper::get_time($courier['date_delivery'])>=System_helper::get_time(System_helper::display_date($data['item']['date_approve']))))
        {
            $ajax['status']=false;
            $ajax['system_message']='Delivery date should be is greater than approval date.';
            $this->json_return($ajax);
        }*/
        $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
        $this->db->select('transfer_wo_details.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('transfer_wo_details.transfer_wo_id',$id);
        $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
        $items=$this->db->get()->result_array();

        $quantity_total_approve_kg=0;
        $old_items=array();
        $variety_ids=array();
        $status_empty_warehouse=false;
        foreach($items as $item)
        {
            if(!($item['warehouse_id']>0))
            {
                $status_empty_warehouse=true;
            }
            $old_items[$item['variety_id']][$item['pack_size_id']]=$item;
            $variety_ids[$item['variety_id']]=$item['variety_id'];

            $quantity_total_approve=(($item['pack_size']*$item['quantity_approve'])/1000);
            $quantity_total_approve_kg+=$quantity_total_approve;
        }
        /*warehouse empty checking*/
        if($status_empty_warehouse)
        {
            $ajax['status']=false;
            $ajax['system_message']='Warehouse is empty.';
            $this->json_return($ajax);
        }

        $current_stocks=Stock_helper::get_variety_stock($variety_ids);
        foreach($items as $item)
        {
            if(!isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]) || !($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Stock not available :: ( Variety ID: '.$item['variety_id'].' - Pack Size ID: '.$item['pack_size_id'].' - Warehouse ID: '.$item['warehouse_id'].')';
                $this->json_return($ajax);
            }
            if($item['quantity_approve']>$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Current Stock Exceed :: ( Variety ID: '.$item['variety_id'].' - Pack Size ID: '.$item['pack_size_id'].' - Warehouse ID: '.$item['warehouse_id'].')';
                $this->json_return($ajax);
            }
        }

        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
        $quantity_to_maximum_kg=$result['config_value'];
        if($quantity_total_approve_kg>$quantity_to_maximum_kg)
        {
            $ajax['status']=false;
            $ajax['system_message']='You have to already exceed quantity is ('.($quantity_total_approve_kg-$quantity_to_maximum_kg).' kg). Transfer order maximum total quantity is '.$quantity_to_maximum_kg.' kg.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        /* variety relational table insert & update*/
        $data=array();
        $data['date_delivery']=$time;
        $data['status_delivery']=$item_head['status_delivery'];
        $data['remarks_delivery']=$item_head['remarks_delivery'];
        $data['date_updated_delivery_forward']=$time;
        $data['user_updated_delivery_forward']=$user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_wo'),$data, array('id='.$id), false);

        foreach($items as $item)
        {
            $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'];
            $data=array();
            $data['current_stock']=($current_stock-$item['quantity_approve']);
            $data['out_wo']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_wo']+$item['quantity_approve']);
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['warehouse_id']));
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
    /*private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[status_delivery]',$this->lang->line('LABEL_STATUS_DELIVERY'),'required');
        $this->form_validation->set_rules('id',$this->lang->line('LABEL_ID'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }*/
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference();
            $data['preference_method_name']='list';
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
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
        //$data['id']= 1;
        $data['barcode']= 1;
        $data['outlet_name']= 1;
        $data['date_request']= 1;
        $data['date_approve']= 1;
        $data['outlet_code']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_approve']= 1;
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
    private function system_set_preference_all()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference_all();
            $data['preference_method_name']='list_all';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_all');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function get_preference_all()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list_all"'),1);
        //$data['id']= 1;
        $data['barcode']= 1;
        $data['outlet_name']= 1;
        $data['date_request']= 1;
        $data['date_approve']= 1;
        $data['outlet_code']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_request']= 1;
        $data['quantity_total_approve']= 1;
        $data['quantity_total_receive']= 1;
        $data['status_delivery']= 1;
        $data['status_receive']= 1;
        $data['status_receive_forward']= 1;
        $data['status_receive_approve']= 1;
        $data['status_system_delivery_receive']= 1;
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
