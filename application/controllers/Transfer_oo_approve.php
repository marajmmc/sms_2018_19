<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_oo_approve extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Transfer_oo_approve');
        $this->controller_url='transfer_oo_approve';
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
        elseif($action=="forward")
        {
            $this->system_forward($id);
        }
        elseif($action=="save_forward")
        {
            $this->system_save_forward();
        }
        /*elseif($action=="ajax_transfer_oo_variety_info")
        {
            $this->system_ajax_transfer_oo_variety_info();
        }*/
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
            $data['title']="Showroom to showroom Transfer Approval Pending List";
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
        $this->db->select('transfer_oo.id, transfer_oo.date_request, transfer_oo.quantity_total_request_kg quantity_total_request, transfer_oo.quantity_total_approve_kg quantity_total_approve');

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
        $this->db->where('transfer_oo.status_request',$this->config->item('system_status_forwarded'));
        $this->db->where('transfer_oo.status_approve',$this->config->item('system_status_pending'));
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
            $item['outlet_name_source']=$result['outlet_name_source'].' - '.$result['outlet_code_source'];
            $item['outlet_name_destination']=$result['outlet_name_destination'].' - '.$result['outlet_code_destination'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_request']=System_helper::get_string_kg($result['quantity_total_request']);
            $item['quantity_total_approve']=System_helper::get_string_kg($result['quantity_total_approve']);
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_all();
            $data['title']="Showroom to showroom Transfer Request Forwarded & Approved All List";
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

        $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
        $this->db->select(
            '
            transfer_oo.id,
            transfer_oo.date_request,
            transfer_oo.quantity_total_request_kg quantity_total_request,
            transfer_oo.quantity_total_approve_kg quantity_total_approve,
            transfer_oo.quantity_total_receive_kg quantity_total_receive,
            transfer_oo.status, transfer_oo.status_request,
            transfer_oo.status_approve,
            transfer_oo.status_delivery,
            transfer_oo.status_receive,
            transfer_oo.status_receive_forward,
            transfer_oo.status_receive_approve,
            transfer_oo.status_system_delivery_receive
            ');

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
        $this->db->where('transfer_oo.status_request',$this->config->item('system_status_forwarded'));
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
        $this->db->limit($pagesize,$current_records);
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_outlet($result['id']);

            $item['outlet_name_source']=$result['outlet_name_source'].' - '.$result['outlet_code_source'];
            $item['outlet_name_destination']=$result['outlet_name_destination'].' - '.$result['outlet_code_destination'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_request']=System_helper::get_string_kg($result['quantity_total_request']);
            $item['quantity_total_approve']=System_helper::get_string_kg($result['quantity_total_approve']);
            $item['quantity_total_receive']=System_helper::get_string_kg($result['quantity_total_receive']);
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

            $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
            $this->db->select(
                '
                transfer_oo.id,
                transfer_oo.date_request,
                transfer_oo.quantity_total_request_kg,
                transfer_oo.status_request,
                transfer_oo.status_approve,
                transfer_oo.remarks_request,
                transfer_oo.remarks_approve,
                transfer_oo.date_updated_approve,
                transfer_oo.user_updated_approve,
                transfer_oo.remarks_approve_edit
                ');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' source_outlet_info','source_outlet_info.customer_id=transfer_oo.outlet_id_source AND source_outlet_info.revision=1 AND source_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('source_outlet_info.customer_id outlet_id_source, source_outlet_info.name outlet_name_source, source_outlet_info.customer_code outlet_code_source');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' destination_outlet_info','destination_outlet_info.customer_id=transfer_oo.outlet_id_destination AND destination_outlet_info.revision=1 AND destination_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('destination_outlet_info.name outlet_name_destination, destination_outlet_info.customer_code outlet_code_destination');

            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = source_outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo.id',$item_id);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('edit',$item_id,'Edit Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer request not forwarded. Invalid try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer already approve & forwarded. Invalid try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer already rejected. Invalid try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('edit',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $data['too_variety_info']=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);

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


            $data['title']=$data['item']['outlet_name_source']." to ".$data['item']['outlet_name_destination']." Transfer Approve Edit :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
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
        if(!($id>0))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        /*$this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
        $this->db->select(
            '
            transfer_oo.id,
            transfer_oo.date_request,
            transfer_oo.quantity_total_request_kg,
            transfer_oo.status_request,
            transfer_oo.remarks_request,
            transfer_oo.status_approve
            ');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_oo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select(
            '
            outlet_info.customer_id outlet_id,
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
        $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('transfer_oo.id',$id);
        $this->db->where('outlet_info.revision',1);
        $this->db->order_by('transfer_oo.id','DESC');

        $data['item']=$this->db->get()->row_array();*/

        $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_oo'),array('*'),array('status !="'.$this->config->item('system_status_delete').'"', 'id ='.$id),1);

        if(!$data['item'])
        {
            System_helper::invalid_try('save',$id,'Update Non Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to showroom transfer is not forwarded from request.';
            $this->json_return($ajax);
        }
        if($data['item']['status_approve']==$this->config->item('system_status_approved'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to showroom transfer already approved & forwarded.';
            $this->json_return($ajax);
        }
        if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to showroom transfer already rejected.';
            $this->json_return($ajax);
        }

        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        /* Source & Destination outlet checking assign permission*/
        $outlet_id_source=$data['item']['outlet_id_source'];
        $outlet_id_destination=$data['item']['outlet_id_destination'];

        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('customer.id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->where('customer.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->where('customer.id',$outlet_id_source);
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
        $outlet_source=$this->db->get()->row_array();
        if(!$outlet_source)
        {
            System_helper::invalid_try('save',$id,'User Outlet Not Assign (Source)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Source outlet not assign.';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('customer.id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->where('customer.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->where('customer.id',$outlet_id_destination);
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
        $outlet_destination=$this->db->get()->row_array();
        if(!$outlet_destination)
        {
            System_helper::invalid_try('save',$id,'User Outlet Not Assign (Destination)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Destination outlet not assign.';
            $this->json_return($ajax);
        }

        /*if(!$this->check_my_editable($data['item']))
        {
            System_helper::invalid_try('save',$id,'User location not assign.');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }*/

        $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);



        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
        $pack_sizes=array();
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]['value']=$result['value'];
            $pack_sizes[$result['value']]['text']=$result['text'];
        }

        $results=Query_helper::get_info($this->config->item('table_sms_transfer_oo_details'),array('*'),array('transfer_oo_id ='.$id));
        $old_items=array();
        foreach($results as $result)
        {
            $old_items[$result['variety_id']][$result['pack_size_id']]=$result;
        }

        $quantity_total_approve_kg=0;
        foreach($items as $item)
        {
            if(!isset($too_variety_info[$item['variety_id']][$item['pack_size_id']]))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                $this->json_return($ajax);
            }
            $quantity_approve=$item['quantity_approve'];
            $quantity_approve_kg=(($pack_sizes[$item['pack_size_id']]['text']*$quantity_approve)/1000);
            $quantity_total_approve_kg+=$quantity_approve_kg;
            if($quantity_approve>$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt'])
            {
                $stock_available_exceed=($quantity_approve-$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                $ajax['status']=false;
                $ajax['system_message']='Available quantity already exceed. ( Exceed quantity is: '.$stock_available_exceed.' pkt)';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

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

        $item_head['quantity_total_approve_kg']=$quantity_total_approve_kg;
        $item_head['quantity_total_receive_kg']=$quantity_total_approve_kg;
        $item_head['date_updated_approve']=$time;
        $item_head['user_updated_approve']=$user->user_id;
        $this->db->set('revision_count_approve', 'revision_count_approve+1', FALSE);
        Query_helper::update($this->config->item('table_sms_transfer_oo'),$item_head, array('id='.$id), false);

        foreach($items as $item)
        {
            if(isset($old_items[$item['variety_id']][$item['pack_size_id']]))
            {
                $data=array();
                $data['quantity_approve']=$item['quantity_approve'];
                $data['quantity_receive']=$item['quantity_approve'];
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
                $data['quantity_approve']=$item['quantity_approve'];
                $data['quantity_receive']=$item['quantity_approve'];
                $data['status']=$this->config->item('system_status_active');
                Query_helper::add($this->config->item('table_sms_transfer_oo_details'),$data, false);
            }

            $data=array();
            $data['transfer_oo_id']=$id;
            $data['variety_id']=$item['variety_id'];
            $data['pack_size_id']=$item['pack_size_id'];
            $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
            $data['quantity']=$item['quantity_approve'];
            $data['revision']=1;
            $data['date_created']=$time;
            $data['user_created']=$user->user_id;
            Query_helper::add($this->config->item('table_sms_transfer_oo_details_histories'),$data, false);
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
            $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
            $this->db->select('transfer_oo.*');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' source_outlet_info','source_outlet_info.customer_id=transfer_oo.outlet_id_source AND source_outlet_info.revision=1 AND source_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('source_outlet_info.customer_id outlet_id_source, source_outlet_info.name outlet_name_source, source_outlet_info.customer_code outlet_code_source');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' destination_outlet_info','destination_outlet_info.customer_id=transfer_oo.outlet_id_destination AND destination_outlet_info.revision=1 AND destination_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('source_outlet_info.customer_id outlet_id_destination, destination_outlet_info.name outlet_name_destination, destination_outlet_info.customer_code outlet_code_destination');

            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = source_outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_oo.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_oo.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');
            $this->db->join($this->config->item('table_sms_transfer_oo_courier_details').' wo_courier_details','wo_courier_details.transfer_oo_id=transfer_oo.id','LEFT');
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
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo.id',$item_id);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('details',$item_id,'View Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('details',$item_id,'User location Not Assign.');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $user_ids[$data['item']['user_updated_receive']]=$data['item']['user_updated_receive'];
            $user_ids[$data['item']['user_updated_receive_forward']]=$data['item']['user_updated_receive_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

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
            $this->db->order_by('transfer_oo_details.id');
            $data['items']=$this->db->get()->result_array();

            $data['title']=$data['item']['outlet_name_source']." to ".$data['item']['outlet_name_destination']." Transfer Details :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
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
    private function system_forward($id)
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
            $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
            $this->db->select('transfer_oo.*');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' source_outlet_info','source_outlet_info.customer_id=transfer_oo.outlet_id_source AND source_outlet_info.revision=1 AND source_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('source_outlet_info.customer_id outlet_id_source, source_outlet_info.name outlet_name_source, source_outlet_info.customer_code outlet_code_source');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' destination_outlet_info','destination_outlet_info.customer_id=transfer_oo.outlet_id_destination AND destination_outlet_info.revision=1 AND destination_outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('source_outlet_info.customer_id outlet_id_destination, destination_outlet_info.name outlet_name_destination, destination_outlet_info.customer_code outlet_code_destination');

            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = source_outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo.id',$item_id);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('forward',$item_id,'Forward Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer is not forwarded (request).';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer  already approved & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer  already rejected.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('forward',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

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

            $data['too_variety_info']=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);

            $data['title']=$data['item']['outlet_name_source']." to ".$data['item']['outlet_name_destination']." Transfer Approved or Rejected :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_forward()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if(!($id>0))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($item_head['status_approve']!=$this->config->item('system_status_approved') && $item_head['status_approve']!=$this->config->item('system_status_rejected'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Approved/Rejected is required.';
            $this->json_return($ajax);
        }
        if($item_head['status_approve']==$this->config->item('system_status_rejected'))
        {
            if(!$item_head['remarks_approve'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Rejected remarks is required.';
                $this->json_return($ajax);
            }
        }
        /*$this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
        $this->db->select(
            '
            transfer_oo.id,
            transfer_oo.date_request,
            transfer_oo.quantity_total_request_kg,
            transfer_oo.status_request,
            transfer_oo.remarks_request,
            transfer_oo.status_approve
            ');
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

        $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_oo'),array('*'),array('status !="'.$this->config->item('system_status_delete').'"', 'id ='.$id),1);
        if(!$data['item'])
        {
            System_helper::invalid_try('save_forward',$id,'Update Forward Approved Non Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if($data['item']['status_request']!=$this->config->item('system_status_forwarded'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to showroom transfer is not forwarded from (request).';
            $this->json_return($ajax);
        }
        if($data['item']['status_approve']==$this->config->item('system_status_approved'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to showroom transfer already approved.';
            $this->json_return($ajax);
        }
        if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to showroom transfer already rejected.';
            $this->json_return($ajax);
        }

        /* Source & Destination outlet checking assign permission*/
        $outlet_id_source=$data['item']['outlet_id_source'];
        $outlet_id_destination=$data['item']['outlet_id_destination'];

        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('customer.id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->where('customer.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->where('customer.id',$outlet_id_source);
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
        $outlet_source=$this->db->get()->row_array();
        if(!$outlet_source)
        {
            System_helper::invalid_try('save_forward',$id,'User Outlet Not Assign (Source)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Source outlet not assign.';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('customer.id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->where('customer.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->where('customer.id',$outlet_id_destination);
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
        $outlet_destination=$this->db->get()->row_array();
        if(!$outlet_destination)
        {
            System_helper::invalid_try('save_forward',$id,'User Outlet Not Assign (Destination)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Destination outlet not assign.';
            $this->json_return($ajax);
        }

        if($item_head['status_approve']==$this->config->item('system_status_approved'))
        {
            $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' transfer_oo_details');
            $this->db->select('transfer_oo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_oo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('transfer_oo_details.transfer_oo_id',$id);
            $this->db->where('transfer_oo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $quantity_total_approve_kg=0;
            foreach($data['items'] as $item)
            {
                if(!isset($too_variety_info[$item['variety_id']][$item['pack_size_id']]))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                    $this->json_return($ajax);
                }
                $quantity_approve=$item['quantity_approve'];
                $quantity_total_approve=(($item['pack_size']*$quantity_approve)/1000);
                $quantity_total_approve_kg+=$quantity_total_approve;
                if($quantity_approve>$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt'])
                {
                    $stock_available_exceed=($quantity_total_approve-$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                    $ajax['status']=false;
                    $ajax['system_message']='Available quantity already exceed. ( Exceed approve quantity: '.$stock_available_exceed.' kg.)';
                    $this->json_return($ajax);
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item_head['date_approve']=$time;
        $item_head['date_updated_approve_forward']=$time;
        $item_head['user_updated_approve_forward']=$user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_oo'),$item_head,array('id='.$id));

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
    /*private function system_ajax_transfer_oo_variety_info()
    {
        $outlet_id=$this->input->post('outlet_id');
        $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($outlet_id);
        $this->json_return($too_variety_info);
    }*/
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
                if(!($item['quantity_approve']>0))
                {
                    $this->message="Approve quantity can't be zero.";
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
                $this->message='Variety info duplicate entry. Invalid input.';
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
        $data['outlet_name_source']= 1;
        $data['outlet_name_destination']= 1;
        $data['date_request']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_request']= 1;
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
        $data['outlet_name_source']= 1;
        $data['outlet_name_destination']= 1;
        $data['date_request']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_request']= 1;
        $data['quantity_total_approve']= 1;
        $data['quantity_total_receive']= 1;
        $data['status_approve']= 1;
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
