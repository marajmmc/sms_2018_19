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
        elseif($action=="delivery")
        {
            $this->system_delivery($id);
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
        $this->db->select('transfer_wo.id, transfer_wo.date_request, transfer_wo.quantity_total_request_kg quantity_total_request, transfer_wo.quantity_total_approve_kg quantity_total_approve');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.revision=1 AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
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
        $this->db->order_by('transfer_wo.id','DESC');

        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($result['id']);
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
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
                System_helper::invalid_try('Edit Non Exists',$item_id);
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

            $data['couriers']=Query_helper::get_info($this->config->item('table_login_basic_setup_couriers'),array('id, name'),array('status="'.$this->config->item('system_status_active').'"'), '','',array('ordering'));
            $data['courier']=Query_helper::get_info($this->config->item('table_sms_transfer_wo_courier_details'),array('*'),array('transfer_wo_id='.$item_id),1);
            if(!$data['courier'])
            {
                $data['courier']['date_delivery']='';
                $data['courier']['date_challan']='';
                $data['courier']['challan_no']='';
                $data['courier']['courier_id']='';
                $data['courier']['courier_tracing_no']='';
                $data['courier']['date_booking']='';
                $data['courier']['remarks']='';
            }

            $system_purpose_config=$this->config->item('system_purpose_config');
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);

            $data['title']="HQ to Outlet Edit Delivery :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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
        $courier=$this->input->post('courier');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            //$data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_wo'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.id, transfer_wo.date_request, transfer_wo.quantity_total_request_kg, transfer_wo.status_request, transfer_wo.remarks_request, transfer_wo.status_approve, transfer_wo.status_delivery');
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
            $two_variety_info=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
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
        $quantity_total_approve_kg=0;
        $old_items=array();
        foreach($data['items'] as $item)
        {
            if(!isset($two_variety_info[$item['variety_id']][$item['pack_size_id']]))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                $this->json_return($ajax);
            }

            $quantity_total_approve=(($item['pack_size']*$item['quantity_approve'])/1000);
            $quantity_total_approve_kg+=$quantity_total_approve;
            if($quantity_total_approve>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable'])
            {
                $quantity_max_transferable_excess=($quantity_total_approve-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable']);
                $ajax['status']=false;
                $ajax['system_message']='Outlet maximum transferable quantity already exist. ( Excess order quantity: '.$quantity_max_transferable_excess.' kg.)';
                $this->json_return($ajax);
            }
            if($quantity_total_approve>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available'])
            {
                $stock_available_excess=($quantity_total_approve-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available']);
                $ajax['status']=false;
                $ajax['system_message']='Available quantity already exist. ( Excess approve quantity: '.$stock_available_excess.' kg.)';
                $this->json_return($ajax);
            }

            $old_items[$item['variety_id']][$item['pack_size_id']]=$item;
        }

        $system_purpose_config=$this->config->item('system_purpose_config');
        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
        $quantity_to_maximum_kg=$result['config_value'];
        if($quantity_total_approve_kg>$quantity_to_maximum_kg)
        {
            $ajax['status']=false;
            $ajax['system_message']='Transfer order maximum quantity '.$quantity_to_maximum_kg.' kg. you have to already exist quantity ('.($quantity_total_approve_kg-$quantity_to_maximum_kg).' kg).';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['date_updated'] = $time;
        $data['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_wo_courier_histories'),$data, array('transfer_wo_id='.$id,'revision=1'), false);

        $this->db->where('transfer_wo_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_sms_transfer_wo_courier_histories'));

        $result=Query_helper::get_info($this->config->item('table_sms_transfer_wo_courier_details'),array('*'),array('transfer_wo_id='.$id));
        if($result)
        {
            $courier['date_delivery']=System_helper::get_time($courier['date_delivery']);
            $courier['date_challan']=System_helper::get_time($courier['date_challan']);
            $courier['date_booking']=System_helper::get_time($courier['date_booking']);
            $courier['date_updated']=$time;
            $courier['user_updated']=$user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_transfer_wo_courier_details'),$courier, array('transfer_wo_id='.$id), false);
        }
        else
        {
            $courier['transfer_wo_id']=$id;
            $courier['date_delivery']=System_helper::get_time($courier['date_delivery']);
            $courier['date_challan']=System_helper::get_time($courier['date_challan']);
            $courier['date_booking']=System_helper::get_time($courier['date_booking']);
            $courier['revision_count']=1;
            $courier['date_updated']=$time;
            $courier['user_updated']=$user->user_id;
            Query_helper::add($this->config->item('table_sms_transfer_wo_courier_details'),$courier, false);
        }


        $data=array();
        $data['transfer_wo_id']=$id;
        $data['date_delivery']=System_helper::get_time($courier['date_delivery']);
        $data['date_challan']=System_helper::get_time($courier['date_challan']);
        $data['challan_no']=$courier['challan_no'];
        $data['courier_id']=$courier['courier_id'];
        $data['courier_tracing_no']=$courier['courier_tracing_no'];
        $data['date_booking']=System_helper::get_time($courier['date_booking']);
        $data['remarks']=$courier['remarks'];
        $data['revision']=1;
        $data['date_created']=$time;
        $data['user_created']=$user->user_id;
        Query_helper::add($this->config->item('table_sms_transfer_wo_courier_histories'),$data, false);

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
        $ajax['status']=false;
        $ajax['system_message']='not start task';
        $this->json_return($ajax);
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

            //$data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_wo'),array('*'),array('id ='.$item_id,'status !="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select('transfer_wo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.revision=1 AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_created','ui_created.user_id = transfer_wo.user_created_request','LEFT');
            $this->db->select('ui_created.name user_created_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated','ui_updated.user_id = transfer_wo.user_updated_request','LEFT');
            $this->db->select('ui_updated.name user_updated_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated_approve','ui_updated_approve.user_id = transfer_wo.user_updated_approve','LEFT');
            $this->db->select('ui_updated_approve.name user_updated_approve_full_name');
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
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
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $system_purpose_config=$this->config->item('system_purpose_config');
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);

            $data['title']="HQ to Outlet Details Transfer Approve :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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
    private function system_delivery($id)
    {
        $ajax['status']=false;
        $ajax['system_message']='not start task';
        $this->json_return($ajax);
    }
    private function check_validation()
    {
        $courier = $this->input->post("courier");
        if(!isset($courier['date_delivery']) || !strtotime($courier['date_delivery']))
        {
            $this->message=$this->lang->line('LABEL_DATE_DELIVERY'). ' field is required.';
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
        $data['outlet_name']= 1;
        $data['date_request']= 1;
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
}
