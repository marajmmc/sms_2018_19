<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_wo_approve extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Transfer_wo_approve');
        $this->controller_url='transfer_wo_approve';
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
        elseif($action=="ajax_transfer_wo_variety_info")
        {
            $this->system_ajax_transfer_wo_variety_info();
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
            $data['title']="HQ to Outlet Transfer Approve List";
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
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_pending'));
        $this->db->where('outlet_info.revision',1);
        $this->db->order_by('transfer_wo.id','DESC');
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
            $item['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($result['id']);
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['outlet_code']=$result['outlet_code'];
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_request']=number_format($result['quantity_total_request'],3,'.','');
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
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->select(
            '
            transfer_wo.id,
            transfer_wo.date_request,
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
        $this->db->where('outlet_info.revision',1);
        //$this->db->where('transfer_wo.status_request',$this->config->item('system_status_forwarded'));
        $this->db->order_by('transfer_wo.id','DESC');
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
            $item['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($result['id']);
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

            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select(
                '
                transfer_wo.id,
                transfer_wo.date_request,
                transfer_wo.quantity_total_request_kg,
                transfer_wo.status_request,
                transfer_wo.status_approve,
                transfer_wo.remarks_request,
                transfer_wo.remarks_approve,
                transfer_wo.date_updated_approve,
                transfer_wo.user_updated_approve,
                transfer_wo.remarks_approve_edit
                ');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
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
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
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
                $ajax['system_message']='TO request not forwarded. Invalid try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already approve & forwarded. Invalid try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected. Invalid try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('edit',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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

            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);

            $data['title']="HQ to Outlet Transfer Approve Edit :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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

            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select(
                '
                transfer_wo.id,
                transfer_wo.date_request,
                transfer_wo.quantity_total_request_kg,
                transfer_wo.status_request,
                transfer_wo.remarks_request,
                transfer_wo.status_approve
                ');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
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
            $this->db->where('transfer_wo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_wo.id',$id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_wo.id','DESC');
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
            $data['item']=$this->db->get()->row_array();
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
                $ajax['system_message']='TO is not forwarded from request.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already approved & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('save',$id,'User location not assign.');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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

        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
        $pack_sizes=array();
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]['value']=$result['value'];
            $pack_sizes[$result['value']]['text']=$result['text'];
        }

        $results=Query_helper::get_info($this->config->item('table_sms_transfer_wo_details'),array('*'),array('transfer_wo_id ='.$id));
        $old_items=array();
        foreach($results as $result)
        {
            $old_items[$result['variety_id']][$result['pack_size_id']]=$result;
        }

        $quantity_total_approve_kg=0;
        foreach($items as $item)
        {
            if(!isset($two_variety_info[$item['variety_id']][$item['pack_size_id']]))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                $this->json_return($ajax);
            }
            $quantity_approve=$item['quantity_approve'];
            $quantity_approve_kg=(($pack_sizes[$item['pack_size_id']]['text']*$quantity_approve)/1000);
            $quantity_total_approve_kg+=$quantity_approve_kg;
            if($quantity_approve_kg>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable'])
            {
                $quantity_max_transferable_excess=($quantity_approve_kg-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable']);
                $ajax['status']=false;
                $ajax['system_message']='Outlet maximum transferable quantity exceed. ( Exceed quantity is: '.$quantity_max_transferable_excess.' kg.)';
                $this->json_return($ajax);
            }
            if($quantity_approve>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt'])
            {
                $stock_available_excess=($quantity_approve-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                $ajax['status']=false;
                $ajax['system_message']='Available quantity already exceed. ( Exceed quantity is: '.$stock_available_excess.' pkt.)';
                $this->json_return($ajax);
            }
        }

        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
        $quantity_to_maximum_kg=$result['config_value'];
        if($quantity_total_approve_kg>$quantity_to_maximum_kg)
        {
            $ajax['status']=false;
            $ajax['system_message']='Transfer order maximum quantity '.$quantity_to_maximum_kg.' kg. you have to already exceed quantity ('.($quantity_total_approve_kg-$quantity_to_maximum_kg).' kg).';
            $this->json_return($ajax);
        }



        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['status'] = $this->config->item('system_status_delete');
        Query_helper::update($this->config->item('table_sms_transfer_wo_details'),$data, array('transfer_wo_id='.$id), false);

        $data=array();
        $data['date_updated'] = $time;
        $data['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_wo_details_histories'),$data, array('transfer_wo_id='.$id,'revision=1'), false);

        $this->db->where('transfer_wo_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_sms_transfer_wo_details_histories'));

        $item_head['quantity_total_approve_kg']=$quantity_total_approve_kg;
        $item_head['quantity_total_receive_kg']=$quantity_total_approve_kg;
        $item_head['date_updated_approve']=$time;
        $item_head['user_updated_approve']=$user->user_id;
        $this->db->set('revision_count_approve', 'revision_count_approve+1', FALSE);
        Query_helper::update($this->config->item('table_sms_transfer_wo'),$item_head, array('id='.$id), false);

        foreach($items as $item)
        {
            if(isset($old_items[$item['variety_id']][$item['pack_size_id']]))
            {
                $data=array();
                $data['quantity_approve']=$item['quantity_approve'];
                $data['quantity_receive']=$item['quantity_approve'];
                $data['status']=$this->config->item('system_status_active');
                Query_helper::update($this->config->item('table_sms_transfer_wo_details'),$data, array('transfer_wo_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);
            }
            else
            {
                $data=array();
                $data['transfer_wo_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                $data['quantity_approve']=$item['quantity_approve'];
                $data['quantity_receive']=$item['quantity_approve'];
                $data['status']=$this->config->item('system_status_active');
                Query_helper::add($this->config->item('table_sms_transfer_wo_details'),$data, false);
            }

            $data=array();
            $data['transfer_wo_id']=$id;
            $data['variety_id']=$item['variety_id'];
            $data['pack_size_id']=$item['pack_size_id'];
            $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
            $data['quantity']=$item['quantity_approve'];
            $data['revision']=1;
            $data['date_created']=$time;
            $data['user_created']=$user->user_id;
            Query_helper::add($this->config->item('table_sms_transfer_wo_details_histories'),$data, false);
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
            /*if($this->locations['division_id']>0)
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
            }*/
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
                $ajax['system_message']='TO is not forwarded (request).';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already approved & forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('forward',$item_id,'Forward user location not assign');
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

            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);

            $data['title']="HQ to Outlet Forward Transfer Approve :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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
        if($id>0)
        {
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

            $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
            $this->db->select(
                '
                transfer_wo.id,
                transfer_wo.date_request,
                transfer_wo.quantity_total_request_kg,
                transfer_wo.status_request,
                transfer_wo.remarks_request,
                transfer_wo.status_approve
                ');
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
            $data['item']=$this->db->get()->row_array();
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
                $ajax['system_message']='TO is not forwarded (request).';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already approved.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already rejected.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('save_forward',$id,'Update forward user location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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
        foreach($data['items'] as $item)
        {
            if(!isset($two_variety_info[$item['variety_id']][$item['pack_size_id']]))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                $this->json_return($ajax);
            }

            $quantity_approve=$item['quantity_approve'];
            $quantity_total_approve=(($item['pack_size']*$quantity_approve)/1000);
            $quantity_total_approve_kg+=$quantity_total_approve;
            if($quantity_total_approve>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable'])
            {
                $quantity_max_transferable_excess=($quantity_total_approve-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable']);
                $ajax['status']=false;
                $ajax['system_message']='Outlet maximum transferable quantity already exist. ( Excess order quantity: '.$quantity_max_transferable_excess.' kg.)';
                $this->json_return($ajax);
            }
            if($quantity_approve>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt'])
            {
                $stock_available_excess=($quantity_approve-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                $ajax['status']=false;
                $ajax['system_message']='Available quantity already exist. ( Excess approve quantity: '.$stock_available_excess.' pkt.)';
                $this->json_return($ajax);
            }
        }

        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
        $quantity_to_maximum_kg=$result['config_value'];
        if($quantity_total_approve_kg>$quantity_to_maximum_kg)
        {
            $ajax['status']=false;
            $ajax['system_message']='Transfer order maximum quantity '.$quantity_to_maximum_kg.' kg. you have to already exist quantity ('.($quantity_total_approve_kg-$quantity_to_maximum_kg).' kg).';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item_head['date_approve']=$time;
        $item_head['date_updated_approve_forward']=$time;
        $item_head['user_updated_approve_forward']=$user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_wo'),$item_head,array('id='.$id));

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
    private function system_ajax_transfer_wo_variety_info()
    {
        $outlet_id=$this->input->post('outlet_id');
        $two_variety_info=Stock_helper::transfer_wo_variety_stock_info($outlet_id);
        $this->json_return($two_variety_info);
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
        $data['outlet_name']= 1;
        $data['date_request']= 1;
        $data['outlet_code']= 1;
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
        $data['status']= 1;
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
