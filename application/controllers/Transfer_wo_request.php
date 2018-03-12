<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_wo_request extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Transfer_wo_request');
        $this->controller_url='transfer_wo_request';
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
        elseif($action=="list_all")
        {
            $this->system_list_all();
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
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save_forward")
        {
            $this->system_save_forward();
        }
        elseif($action=="forward")
        {
            $this->system_forward($id);
        }
        elseif($action=="ajax_transfer_wo_variety_info")
        {
            $this->system_ajax_transfer_wo_variety_info();
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
            $data['title']="Transfer (HQ to Outlet) List";
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
        $this->db->select('transfer_wo.id, transfer_wo.date_request date_request, transfer_wo.quantity_total_request_kg quantity_total_request');
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
        $this->db->where('transfer_wo.status_request',$this->config->item('system_status_pending'));
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
            $item['quantity_total_request']=number_format($result['quantity_total_request'],3,'.','');
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        $ajax['status']=false;
        $ajax['system_message']='Pending task. Still working.';
        $this->json_return($ajax);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="HQ TO Create";
            $data['item']['id']=0;
            $data['item']['outlet_id']='';
            $data['item']['date_request']=time();
            $data['item']['remarks_request']='';
            $data['items']=[];
            $data['two_variety_info']=[];//$this->get_transfer_wo_variety_info(258);

            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            $data['upazillas']=array();

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $system_purpose_config=$this->config->item('system_purpose_config');
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];

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
            $data['user_location']=$this->locations;
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
            $this->db->select('transfer_wo.id, transfer_wo.date_request, transfer_wo.quantity_total_request_kg, transfer_wo.status_request, transfer_wo.remarks_request');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.revision=1 AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_pending'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->locations['division_id']=$data['item']['division_id'];
            $this->locations['division_name']=$data['item']['division_name'];
            $this->locations['zone_id']=$data['item']['zone_id'];
            $this->locations['zone_name']=$data['item']['zone_name'];
            $this->locations['territory_id']=$data['item']['territory_id'];
            $this->locations['territory_name']=$data['item']['territory_name'];
            $this->locations['district_id']=$data['item']['district_id'];
            $this->locations['district_name']=$data['item']['district_name'];

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=transfer_wo_details.pack_size_id','LEFT');
            $this->db->select('pack.id pack_size_id, pack.name pack_size');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $system_purpose_config=$this->config->item('system_purpose_config');
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=$this->system_transfer_wo_variety_info($data['item']['outlet_id']);

            $data['title']="Edit HQ Transfer Order (TO) :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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

            $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_wo'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already forwarded.';
                $this->json_return($ajax);
            }
            $two_variety_info=$this->system_transfer_wo_variety_info($data['item']['outlet_id']);
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $two_variety_info=$this->system_transfer_wo_variety_info($item_head['outlet_id']);
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

        $quantity_total_request_kg=0;
        if($items)
        {
            foreach($items as $item)
            {
                if(!isset($two_variety_info[$item['variety_id']][$item['pack_size_id']]))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                    $this->json_return($ajax);
                }

                $quantity_total_request=(($pack_sizes[$item['pack_size_id']]['text']*$item['quantity_request'])/1000);
                $quantity_total_request_kg+=$quantity_total_request;
                if($quantity_total_request>$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable'])
                {
                    $quantity_max_transferable_excess=($quantity_total_request-$two_variety_info[$item['variety_id']][$item['pack_size_id']]['quantity_max_transferable']);
                    $ajax['status']=false;
                    $ajax['system_message']='Outlet maximum transferable quantity already exist. ( Excess order quantity: '.$quantity_max_transferable_excess.' kg.)';
                    $this->json_return($ajax);
                }
            }
        }

        $system_purpose_config=$this->config->item('system_purpose_config');
        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
        $quantity_to_maximum_kg=$result['config_value'];
        if($quantity_total_request_kg>$quantity_to_maximum_kg)
        {
            $ajax['status']=false;
            $ajax['system_message']='Transfer order maximum quantity '.$quantity_to_maximum_kg.' kg. you have to already exist quantity ('.($quantity_total_request_kg-$quantity_to_maximum_kg).' kg).';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            $results=Query_helper::get_info($this->config->item('table_sms_transfer_wo_details'),array('*'),array('transfer_wo_id ='.$id));
            $old_items=array();
            foreach($results as $result)
            {
                $old_items[$result['variety_id']][$result['pack_size_id']]=$result;
            }
            $data=array();
            $data['status'] = $this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_transfer_wo_details'),$data, array('transfer_wo_id='.$id), false);

            $data=array();
            $data['status'] = $this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_transfer_wo_details_histories'),$data, array('transfer_wo_id='.$id), false);

            $data=array();
            $data['date_updated_request'] = $time;
            $data['user_updated_request'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_transfer_wo_details_histories'),$data, array('transfer_wo_id='.$id,'revision=1'), false);

            $this->db->where('transfer_wo_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_transfer_wo_details_histories'));

            $item_head['date_request']=System_helper::get_time($item_head['date_request']);
            $item_head['quantity_total_request_kg']=$quantity_total_request_kg;
            $item_head['quantity_total_approve_kg']=$item_head['quantity_total_request_kg'];
            $item_head['date_updated_request']=$time;
            $item_head['user_updated_request']=$user->user_id;
            $this->db->set('revision_count_request', 'revision_count_request+1', FALSE);
            Query_helper::update($this->config->item('table_sms_transfer_wo'),$item_head, array('id='.$id), false);

            foreach($items as $item)
            {
                if(isset($old_items[$item['variety_id']][$item['pack_size_id']]))
                {
                    $data=array();
                    $data['quantity_request']=$item['quantity_request'];
                    $data['quantity_approve']=$data['quantity_request'];
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::update($this->config->item('table_sms_transfer_wo_details'),$data, array('transfer_wo_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);

                    $data=array();
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::update($this->config->item('table_sms_transfer_wo_details_histories'),$data, array('transfer_wo_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);
                }
                else
                {
                    $data=array();
                    $data['transfer_wo_id']=$id;
                    $data['variety_id']=$item['variety_id'];
                    $data['pack_size_id']=$item['pack_size_id'];
                    $data['quantity_request']=$item['quantity_request'];
                    $data['quantity_approve']=$data['quantity_request'];
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::add($this->config->item('table_sms_transfer_wo_details'),$data, false);
                }

                $data=array();
                $data['transfer_wo_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity_request'];
                $data['revision']=1;
                $data['status']=$this->config->item('system_status_active');
                $data['date_created_request']=$time;
                $data['user_created_request']=$user->user_id;
                Query_helper::add($this->config->item('table_sms_transfer_wo_details_histories'),$data, false);
            }
        }
        else
        {

            $item_head['date_request']=System_helper::get_time($item_head['date_request']);
            $item_head['revision_count_request']=1;
            $item_head['status']=$this->config->item('system_status_active');
            $item_head['status_request']=$this->config->item('system_status_pending');
            $item_head['quantity_total_request_kg']=$quantity_total_request_kg;
            $item_head['quantity_total_approve_kg']=$item_head['quantity_total_request_kg'];
            $item_head['date_created_request']=$time;
            $item_head['user_created_request']=$user->user_id;
            $transfer_wo_id=Query_helper::add($this->config->item('table_sms_transfer_wo'),$item_head, false);
            foreach($items as $item)
            {
                $data=array();
                $data['transfer_wo_id']=$transfer_wo_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity_request']=$item['quantity_request'];
                $data['quantity_approve']=$data['quantity_request'];
                $data['status']=$this->config->item('system_status_active');
                Query_helper::add($this->config->item('table_sms_transfer_wo_details'),$data, false);

                $data=array();
                $data['transfer_wo_id']=$transfer_wo_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity_request'];
                $data['revision']=1;
                $data['status']=$this->config->item('system_status_active');
                $data['date_created_request']=$time;
                $data['user_created_request']=$user->user_id;
                Query_helper::add($this->config->item('table_sms_transfer_wo_details_histories'),$data, false);
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
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
            $data['user_location']=$this->locations;
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
            $this->db->select('*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.revision=1 AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
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

            $this->locations['division_id']=$data['item']['division_id'];
            $this->locations['division_name']=$data['item']['division_name'];
            $this->locations['zone_id']=$data['item']['zone_id'];
            $this->locations['zone_name']=$data['item']['zone_name'];
            $this->locations['territory_id']=$data['item']['territory_id'];
            $this->locations['territory_name']=$data['item']['territory_name'];
            $this->locations['district_id']=$data['item']['district_id'];
            $this->locations['district_name']=$data['item']['district_name'];

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=transfer_wo_details.pack_size_id','LEFT');
            $this->db->select('pack.id pack_size_id, pack.name pack_size');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $system_purpose_config=$this->config->item('system_purpose_config');
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=$this->system_transfer_wo_variety_info($data['item']['outlet_id']);

            $data['title']="Details HQ Transfer Order (TO) :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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
            $data['user_location']=$this->locations;
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
            $this->db->select('*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_wo.outlet_id AND outlet_info.revision=1 AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
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
            $this->db->where('transfer_wo.id',$item_id);
            $this->db->order_by('transfer_wo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Forward Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->locations['division_id']=$data['item']['division_id'];
            $this->locations['division_name']=$data['item']['division_name'];
            $this->locations['zone_id']=$data['item']['zone_id'];
            $this->locations['zone_name']=$data['item']['zone_name'];
            $this->locations['territory_id']=$data['item']['territory_id'];
            $this->locations['territory_name']=$data['item']['territory_name'];
            $this->locations['district_id']=$data['item']['district_id'];
            $this->locations['district_name']=$data['item']['district_name'];

            $this->db->from($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details');
            $this->db->select('transfer_wo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_wo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=transfer_wo_details.pack_size_id','LEFT');
            $this->db->select('pack.id pack_size_id, pack.name pack_size');
            $this->db->where('transfer_wo_details.transfer_wo_id',$item_id);
            $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $system_purpose_config=$this->config->item('system_purpose_config');
            $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$system_purpose_config['sms_quantity_order_max'].'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=$this->system_transfer_wo_variety_info($data['item']['outlet_id']);

            $data['title']="Forward HQ Transfer Order (TO) :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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
            if($item_head['status_request']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Forward TO is required.';
                $this->json_return($ajax);
            }

            $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_wo'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Update Forwarded Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='TO already forwarded.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item_head['date_updated_forward']=$time;
        $item_head['user_updated_forward']=$user->user_id;
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
    private function system_transfer_wo_variety_info($id=0)
    {
        /* HQ stock */
        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('SUM(stock_summary_variety.current_stock) current_stock, stock_summary_variety.variety_id, stock_summary_variety.pack_size_id');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');
        $this->db->where('stock_summary_variety.current_stock > 0');
        $this->db->where('stock_summary_variety.pack_size_id > 0');
        $this->db->group_by('stock_summary_variety.variety_id, stock_summary_variety.pack_size_id');
        $results=$this->db->get()->result_array();

        /*Initiate variable */
        $two_variety_info=array();
        foreach($results as $result)
        {
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']=number_format($result['current_stock'],3,'.','');
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_min']=number_format(0,3,'.','');
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max']=number_format(0,3,'.','');
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']=number_format(0,3,'.','');
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_outlet']=number_format(0,3,'.','');
        }

        /* calculate available stock */
        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->join($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details','transfer_wo_details.transfer_wo_id=transfer_wo.id AND transfer_wo_details.status="'.$this->config->item('system_status_active').'"','INNER');
        $this->db->select('SUM(transfer_wo_details.quantity_approve) quantity_approve, transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $this->db->where('transfer_wo.status',$this->config->item('system_status_active'));
        $this->db->where('transfer_wo.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_wo.status_delivery',$this->config->item('system_status_pending'));
        $this->db->group_by('transfer_wo_details.variety_id, transfer_wo_details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']=number_format(($two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_available']-$result['quantity_approve']),3,'.','');
        }

        /* min max stock */
        $results=Query_helper::get_info($this->config->item('table_pos_setup_stock_min_max'), array('*'),array('customer_id='.$id));
        foreach($results as $result)
        {
            if(isset($two_variety_info[$result['variety_id']][$result['pack_size_id']]))
            {
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_min']=number_format($result['quantity_min'],3,'.','');
                $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max']=number_format($result['quantity_max'],3,'.','');
            }
        }

        /* outlet stock */
        $this->db->from($this->config->item('table_pos_stock_summary_variety').' pos_stock_summary_variety');
        $this->db->select('SUM(pos_stock_summary_variety.current_stock) current_stock, pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $this->db->where('pos_stock_summary_variety.outlet_id',$id);
        $this->db->group_by('pos_stock_summary_variety.variety_id, pos_stock_summary_variety.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['stock_outlet']=number_format($result['current_stock'],3,'.','');
            $quantity_max_transferable=($two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max']-$result['current_stock']);
            $two_variety_info[$result['variety_id']][$result['pack_size_id']]['quantity_max_transferable']=number_format($quantity_max_transferable,3,'.','');
        }
        return $two_variety_info;
    }
    private function system_ajax_transfer_wo_variety_info()
    {
        $outlet_id=$this->input->post('outlet_id');
        $two_variety_info=$this->system_transfer_wo_variety_info($outlet_id);
        $this->json_return($two_variety_info);
    }
    private function check_validation()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date_request]',$this->lang->line('LABEL_DATE_TO_REQUEST'),'required');
        if($id==0)
        {
            $this->form_validation->set_rules('division_id',$this->lang->line('LABEL_DIVISION_NAME'),'required');
            $this->form_validation->set_rules('zone_id',$this->lang->line('LABEL_ZONE_NAME'),'required');
            $this->form_validation->set_rules('territory_id',$this->lang->line('LABEL_TERRITORY_NAME'),'required');
            $this->form_validation->set_rules('district_id',$this->lang->line('LABEL_DISTRICT_NAME'),'required');
            $this->form_validation->set_rules('item[outlet_id]',$this->lang->line('LABEL_OUTLET_NAME'),'required');
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }


        $item_head = $this->input->post("item");
        $items = $this->input->post("items");
        if(!isset($item_head['date_request']) || !strtotime($item_head['date_request']))
        {
            $this->message=$this->lang->line('LABEL_DATE_TO_REQUEST'). ' field is required.';
            return false;
        }


        if((sizeof($items)>0))
        {
            $duplicate_item=array();
            $status_duplicate_item=false;
            foreach($items as $item)
            {
                /// empty checking
                if(!(($item['variety_id']>0) && ($item['pack_size_id']>=0) && ($item['quantity_request']>0)))
                {
                    $this->message='Un-finish input (variety info :: '.$item['variety_id'].').';
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
            $this->message='Order item information is empty.';
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
