<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_ow_receive extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Transfer_ow_receive');
        $this->controller_url='transfer_ow_receive';
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
        elseif($action=="receive")
        {
            $this->system_receive($id);
        }
        elseif($action=="save_receive")
        {
            $this->system_save_receive();
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
            $data['title']="Outlet to HQ Transfer Receive List";
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
        $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
        $this->db->select('transfer_ow.id, transfer_ow.date_request, transfer_ow.quantity_total_request_kg quantity_total_request, transfer_ow.quantity_total_approve_kg quantity_total_approve');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
        $this->db->where('transfer_ow.status_delivery',$this->config->item('system_status_delivered'));
        $this->db->where('transfer_ow.status_receive',$this->config->item('system_status_pending'));
        $this->db->where('transfer_ow.status_receive_forward',$this->config->item('system_status_pending'));
        $this->db->where('outlet_info.revision',1);
        $this->db->order_by('transfer_ow.id','DESC');

        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_warehouse($result['id']);
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
    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_all();
            $data['title']="Outlet to HQ Transfer All List";
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
        $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
        $this->db->select(
            '
            transfer_ow.id,
            transfer_ow.date_request,
            transfer_ow.quantity_total_request_kg quantity_total_request,
            transfer_ow.quantity_total_approve_kg quantity_total_approve,
            transfer_ow.quantity_total_receive_kg quantity_total_receive,
            transfer_ow.status, transfer_ow.status_request,
            transfer_ow.status_approve,
            transfer_ow.status_delivery,
            transfer_ow.status_receive,
            transfer_ow.status_receive_forward,
            transfer_ow.status_receive_approve,
            transfer_ow.status_system_delivery_receive
            ');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
        $this->db->where('outlet_info.revision',1);
        $this->db->order_by('transfer_ow.id','DESC');

        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_warehouse($result['id']);
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
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

            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('transfer_ow.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_ow.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_ow.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');
            $this->db->join($this->config->item('table_sms_transfer_ow_courier_details').' wo_courier_details','wo_courier_details.transfer_ow_id=transfer_ow.id','LEFT');
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
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('edit',$item_id, 'Edit Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']!=$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR is not delivered. Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR already received. Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
            $this->db->select('transfer_ow_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('transfer_ow_details.transfer_ow_id',$item_id);
            $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $variety_ids=array();
            foreach($data['items'] as $row)
            {
                $variety_ids[$row['variety_id']]=$row['variety_id'];
            }
            $data['stocks']=Stock_helper::get_variety_stock($variety_ids);

            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Outlet to HQ Transfer Receive Edit :: ". Barcode_helper::get_barcode_transfer_outlet_to_warehouse($data['item']['id']);
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
            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('
            transfer_ow.id,
            transfer_ow.date_request,
            transfer_ow.quantity_total_request_kg,
            transfer_ow.status_request,
            transfer_ow.remarks_request,
            transfer_ow.date_approve,
            transfer_ow.status_approve,
            transfer_ow.status_receive,
            transfer_ow.status_delivery');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('save',$id,'Update Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']!=$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR is not delivered.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR already received.';
                $this->json_return($ajax);
            }

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
        $this->db->select('transfer_ow_details.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('transfer_ow_details.transfer_ow_id',$id);
        $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
        $data['items']=$this->db->get()->result_array();

        $old_items=array();
        foreach($data['items'] as $item)
        {
            $old_items[$item['variety_id']][$item['pack_size_id']]=$item;
        }

        $quantity_total_receive_kg=0;
        foreach($items as $item)
        {
            if(!($item['warehouse_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Warehouse is empty.';
                $this->json_return($ajax);
            }
            if(!($item['quantity_receive']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not empty or zero receive quantity';
                $this->json_return($ajax);
            }
            if(!isset($old_items[$item['variety_id']][$item['pack_size_id']]))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                $this->json_return($ajax);
            }
            $quantity_total_receive_kg+=(($item['quantity_receive']*$old_items[$item['variety_id']][$item['pack_size_id']]['pack_size'])/1000);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        /* variety relational table insert & update */
        $data=array();
        $data['date_updated'] = $time;
        $data['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_ow_details_histories'),$data, array('transfer_ow_id='.$id,'revision=1'), false);

        $this->db->where('transfer_ow_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_sms_transfer_ow_details_histories'));

        $data=array();
        $data['quantity_total_receive_kg']=$quantity_total_receive_kg;
        $data['remarks_receive_forward']=$item_head['remarks_receive_forward'];
        $data['date_updated_receive']=$time;
        $data['user_updated_receive']=$user->user_id;
        $this->db->set('revision_count_receive', 'revision_count_receive+1', FALSE);
        Query_helper::update($this->config->item('table_sms_transfer_ow'),$data, array('id='.$id), false);

        foreach($items as $item)
        {
            $data=array();
            $data['warehouse_id']=$item['warehouse_id'];
            $data['quantity_receive']=$item['quantity_receive'];
            Query_helper::update($this->config->item('table_sms_transfer_ow_details'),$data, array('transfer_ow_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);

            $data=array();
            $data['transfer_ow_id']=$id;
            $data['variety_id']=$item['variety_id'];
            $data['pack_size_id']=$item['pack_size_id'];
            $data['pack_size']=$old_items[$item['variety_id']][$item['pack_size_id']]['pack_size'];
            $data['quantity']=$item['quantity_receive'];
            $data['revision']=1;
            $data['date_created']=$time;
            $data['user_created']=$user->user_id;
            Query_helper::add($this->config->item('table_sms_transfer_ow_details_histories'),$data, false);
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
            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('transfer_ow.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_ow.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_ow.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');
            $this->db->join($this->config->item('table_sms_transfer_ow_courier_details').' wo_courier_details','wo_courier_details.transfer_ow_id=transfer_ow.id','LEFT');
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
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
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
            $user_ids[$data['item']['user_updated_receive']]=$data['item']['user_updated_receive'];
            $user_ids[$data['item']['user_updated_receive_forward']]=$data['item']['user_updated_receive_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
            $this->db->select('transfer_ow_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=transfer_ow_details.warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('transfer_ow_details.transfer_ow_id',$item_id);
            $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
            $this->db->order_by('transfer_ow_details.id');
            $data['items']=$this->db->get()->result_array();

            $data['title']="Outlet to HQ Transfer Details :: ". Barcode_helper::get_barcode_transfer_outlet_to_warehouse($data['item']['id']);
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
            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('transfer_ow.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
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
            $this->db->join($this->config->item('table_sms_transfer_ow_courier_details').' wo_courier_details','wo_courier_details.transfer_ow_id=transfer_ow.id','LEFT');
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
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_created','ui_created.user_id = transfer_ow.user_created_request','LEFT');
            $this->db->select('ui_created.name user_created_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated','ui_updated.user_id = transfer_ow.user_updated_request','LEFT');
            $this->db->select('ui_updated.name user_updated_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated_approve','ui_updated_approve.user_id = transfer_ow.user_updated_approve','LEFT');
            $this->db->select('ui_updated_approve.name user_updated_approve_full_name');
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
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
                $ajax['system_message']='Invalid Try. TR request not forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TR not approve & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. TR already rejected.';
                $this->json_return($ajax);
            }*/

            $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
            $this->db->select('transfer_ow_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=transfer_ow_details.warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('transfer_ow_details.transfer_ow_id',$item_id);
            $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
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
            $data['two_variety_info']=Stock_helper::transfer_ow_variety_stock_info($data['item']['outlet_id']);

            $data['title']="Outlet to HQ Transfer Receive Print View :: ". Barcode_helper::get_barcode_transfer_outlet_to_warehouse($data['item']['id']);
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
    private function system_receive($id)
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

            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('transfer_ow.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_ow.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_ow.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_forward');
            $this->db->join($this->config->item('table_sms_transfer_ow_courier_details').' wo_courier_details','wo_courier_details.transfer_ow_id=transfer_ow.id','LEFT');
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
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('edit',$item_id, 'Edit Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']!=$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR is not delivered. Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR already received. Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
            $this->db->select('transfer_ow_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=transfer_ow_details.warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('transfer_ow_details.transfer_ow_id',$item_id);
            $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
            $this->db->order_by('transfer_ow_details.id');
            $data['items']=$this->db->get()->result_array();

            $variety_ids=array();
            foreach($data['items'] as $row)
            {
                $variety_ids[$row['variety_id']]=$row['variety_id'];
            }
            $data['stocks']=Stock_helper::get_variety_stock($variety_ids);

            $data['title']="Outlet to HQ Transfer Receive :: ". Barcode_helper::get_barcode_transfer_outlet_to_warehouse($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/receive",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/receive/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_receive()
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
            if($item_head['status_receive']!=$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Receive is required.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select(
                '
                transfer_ow.id,
                transfer_ow.date_request,
                transfer_ow.quantity_total_request_kg,
                transfer_ow.status_request,
                transfer_ow.remarks_request,
                transfer_ow.status_approve,
                transfer_ow.status_delivery,
                transfer_ow.status_receive
                ');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
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
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('save_receive',$id,'Update Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_delivery']!=$this->config->item('system_status_delivered'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR is not delivered.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TR already received.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
        $this->db->select('transfer_ow_details.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('transfer_ow_details.transfer_ow_id',$id);
        $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
        $items=$this->db->get()->result_array();

        $status_quantity_deference=false;
        $old_items=array();
        $variety_ids=array();
        $quantity_total_receive_kg=0;
        foreach($items as $item)
        {
            if(!($item['warehouse_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Warehouse is empty.';
                $this->json_return($ajax);
            }
            if(!($item['quantity_receive']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not empty or zero receive quantity';
                $this->json_return($ajax);
            }
            $old_items[$item['variety_id']][$item['pack_size_id']]=$item;
            $variety_ids[$item['variety_id']]=$item['variety_id'];

            if(!($item['quantity_approve']==$item['quantity_receive']))
            {
                $status_quantity_deference=true;
            }
            $quantity_total_receive_kg+=(($item['quantity_receive']*$old_items[$item['variety_id']][$item['pack_size_id']]['pack_size'])/1000);
        }
        $current_stocks=Stock_helper::get_variety_stock($variety_ids);

        $this->db->trans_start();  //DB Transaction Handle START

        if($status_quantity_deference)
        {
            $data=array();
            $data['date_receive']=$time;
            $data['quantity_total_receive_kg']=$quantity_total_receive_kg;
            $data['status_receive']=$this->config->item('system_status_pending');
            $data['status_receive_forward']=$this->config->item('system_status_forwarded');
            $data['status_receive_approve']=$this->config->item('system_status_pending');
            $data['status_system_delivery_receive']=$this->config->item('system_status_no');
            $data['date_updated_receive']=$time;
            $data['user_updated_receive']=$user->user_id;
            $data['date_updated_receive_forward']=$time;
            $data['user_updated_receive_forward']=$user->user_id;
            //$this->db->set('revision_count_receive', 'revision_count_receive+1', FALSE);
            Query_helper::update($this->config->item('table_sms_transfer_ow'),$data, array('id='.$id), false);

            $this->message='System automatically forwarded. Approve quantity is not equal to receive quantity.';
        }
        else
        {
            $data=array();
            $data['date_receive']=$time;
            $data['quantity_total_receive_kg']=$quantity_total_receive_kg;
            $data['status_receive']=$item_head['status_receive'];
            $data['status_receive_forward']=$this->config->item('system_status_forwarded');
            $data['status_receive_approve']=$this->config->item('system_status_approved');
            $data['status_system_delivery_receive']=$this->config->item('system_status_yes');
            $data['date_updated_receive']=$time;
            $data['user_updated_receive']=$user->user_id;
            $data['date_updated_receive_forward']=$time;
            $data['user_updated_receive_forward']=$user->user_id;
            $data['date_updated_receive_approve']=$time;
            $data['user_updated_receive_approve']=$user->user_id;
            Query_helper::update($this->config->item('table_sms_transfer_ow'),$data, array('id='.$id));

            foreach($items as $item)
            {
                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                {
                    $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'];
                    $data=array();
                    $data['current_stock']=($current_stock+$item['quantity_receive']);
                    $data['in_ow']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['in_ow']+$item['quantity_receive']);
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['warehouse_id']));
                }
                else
                {
                    $data=array();
                    $data['variety_id']=$item['variety_id'];
                    $data['pack_size_id']=$item['pack_size_id'];
                    $data['warehouse_id']=$item['warehouse_id'];
                    $data['in_ow']=$item['quantity_receive'];
                    $data['current_stock']=$item['quantity_receive'];
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$data);
                }
            }
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            //$this->message=$this->lang->line("MSG_SAVED_SUCCESS");
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
        $data['outlet_code']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_request']= 1;
        $data['quantity_total_approve']= 1;
        $data['quantity_total_receive']= 1;
        $data['status_request']= 1;
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
