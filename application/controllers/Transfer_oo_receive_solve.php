<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_oo_receive_solve extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $outlets;
    public $outlet_ids;
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

        $this->outlets=System_helper::get_outlets_by_location();
        $this->outlet_ids[0]=0;
        foreach($this->outlets as $result)
        {
            $this->outlet_ids[$result['id']]=$result['id'];
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
    private function get_preference_headers($method)
    {
        $data['id']= 1;
        $data['barcode']= 1;
        $data['outlet_name_source']= 1;
        $data['outlet_name_destination']= 1;
        $data['date_request']= 1;
        $data['outlet_code']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['quantity_total_approve']= 1;
        $data['quantity_total_receive']= 1;
        $data['quantity_total_difference']= 1;
        if($method=='list_all')
        {
            $data['status_solve']= 1;
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
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Showroom to Showroom Transfer  Receive Approve Solve List";
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
        $this->db->from($this->config->item('table_sms_transfer_oo_receive_solves').' transfer_oo_receive_solves');
        $this->db->select('transfer_oo_receive_solves.*, transfer_oo_receive_solves.id id');
        $this->db->join($this->config->item('table_sms_transfer_oo').' transfer_oo','transfer_oo.id=transfer_oo_receive_solves.transfer_oo_id','INNER');
        $this->db->select(
            'transfer_oo.id transfer_oo_id,
            transfer_oo.date_request,
            transfer_oo.outlet_id_source,
            transfer_oo.outlet_id_destination,
            transfer_oo.quantity_total_request_kg quantity_total_request,
            transfer_oo.quantity_total_approve_kg quantity_total_approve,
            transfer_oo.quantity_total_receive_kg quantity_total_receive
            ');
        $this->db->where('transfer_oo.status_receive',$this->config->item('system_status_received'));
        $this->db->where('transfer_oo.status_receive_forward',$this->config->item('system_status_forwarded'));
        $this->db->where('transfer_oo.status_receive_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_oo.status_system_delivery_receive',$this->config->item('system_status_no'));
        $this->db->where('transfer_oo_receive_solves.status_solve',$this->config->item('system_status_no'));
        $this->db->where_in('transfer_oo.outlet_id_source',$this->outlet_ids);
        $this->db->where_in('transfer_oo.outlet_id_destination',$this->outlet_ids);

        $this->db->order_by('transfer_oo.id','DESC');

        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_warehouse($result['transfer_oo_id']);
            $item['outlet_name_source']=$this->outlets[$result['outlet_id_source']]['name'].' ('.$this->outlets[$result['outlet_id_source']]['customer_code'].')';
            $item['outlet_name_destination']=$this->outlets[$result['outlet_id_destination']]['name'].' ('.$this->outlets[$result['outlet_id_destination']]['customer_code'].')';
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['quantity_total_approve']=number_format($result['quantity_total_approve'],3,'.','');
            $item['quantity_total_receive']=number_format($result['quantity_total_receive'],3,'.','');
            $item['quantity_total_difference']=number_format(($result['quantity_total_receive']-$result['quantity_total_approve']),3,'.','');
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method='list_all';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Showroom to Showroom Transfer Receive Approve Solve All List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
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
    private function system_get_items_all()
    {
        $this->db->from($this->config->item('table_sms_transfer_oo_receive_solves').' transfer_oo_receive_solves');
        $this->db->select('transfer_oo_receive_solves.*, transfer_oo_receive_solves.id id');
        $this->db->join($this->config->item('table_sms_transfer_oo').' transfer_oo','transfer_oo.id=transfer_oo_receive_solves.transfer_oo_id','INNER');
        $this->db->select(
            'transfer_oo.id transfer_oo_id,
            transfer_oo.date_request,
            transfer_oo.outlet_id_source,
            transfer_oo.outlet_id_destination,
            transfer_oo.quantity_total_request_kg quantity_total_request,
            transfer_oo.quantity_total_approve_kg quantity_total_approve,
            transfer_oo.quantity_total_receive_kg quantity_total_receive
            ');
        $this->db->where('transfer_oo.status_receive',$this->config->item('system_status_received'));
        $this->db->where('transfer_oo.status_receive_forward',$this->config->item('system_status_forwarded'));
        $this->db->where('transfer_oo.status_receive_approve',$this->config->item('system_status_approved'));
        $this->db->where('transfer_oo.status_system_delivery_receive',$this->config->item('system_status_no'));
        //$this->db->where('transfer_oo_receive_solves.status_solve',$this->config->item('system_status_no'));
        $this->db->where_in('transfer_oo.outlet_id_source',$this->outlet_ids);
        $this->db->where_in('transfer_oo.outlet_id_destination',$this->outlet_ids);
        $this->db->order_by('transfer_oo.id','DESC');

        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_warehouse($result['transfer_oo_id']);
            $item['outlet_name_source']=$this->outlets[$result['outlet_id_source']]['name'].' ('.$this->outlets[$result['outlet_id_source']]['customer_code'].')';
            $item['outlet_name_destination']=$this->outlets[$result['outlet_id_destination']]['name'].' ('.$this->outlets[$result['outlet_id_destination']]['customer_code'].')';
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['quantity_total_approve']=number_format($result['quantity_total_approve'],3,'.','');
            $item['quantity_total_receive']=number_format($result['quantity_total_receive'],3,'.','');
            $item['quantity_total_difference']=number_format(($result['quantity_total_receive']-$result['quantity_total_approve']),3,'.','');
            $item['status_solve']=$result['status_solve'];
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
            $this->db->from($this->config->item('table_sms_transfer_oo_receive_solves').' transfer_oo_receive_solves');
            $this->db->select('transfer_oo_receive_solves.*,transfer_oo_receive_solves.id id');
            $this->db->join($this->config->item('table_sms_transfer_oo').' transfer_oo','transfer_oo.id=transfer_oo_receive_solves.transfer_oo_id','INNER');
            $this->db->select(
                '
                transfer_oo.id transfer_oo_id,
                transfer_oo.outlet_id_source,
                transfer_oo.outlet_id_destination,
                transfer_oo.date_request,
                transfer_oo.date_approve,
                transfer_oo.date_delivery,
                transfer_oo.quantity_total_request_kg,
                transfer_oo.status_request,
                transfer_oo.remarks_request,
                transfer_oo.status_approve,
                transfer_oo.remarks_receive_approve
                ');
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
            $this->db->where('transfer_oo_receive_solves.id',$item_id);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('edit',$item_id,'Edit Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_source'], $this->outlet_ids))
            {
                System_helper::invalid_try('save',$id,'User Outlet Not Assign (Source)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Source outlet not assign.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_destination'], $this->outlet_ids))
            {
                System_helper::invalid_try('save',$id,'User Outlet Not Assign (Destination)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Destination outlet not assign.';
                $this->json_return($ajax);
            }
            if($data['item']['status_solve']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to showroom transfer problem already solved.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' details');
            $this->db->select('details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('details.transfer_oo_id',$data['item']['transfer_oo_id']);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $data['items']=$this->db->get()->result_array();

            $data['title']=$this->outlets[$data['item']['outlet_id_source']]['name']." to ".$this->outlets[$data['item']['outlet_id_destination']]['name']." Transfer Receive Approve Solve :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$data['item']['id']);
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
        $item=$this->input->post('item');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $result=Query_helper::get_info($this->config->item('table_sms_transfer_oo_receive_solves'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try('save',$id,'Update Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Courier.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($item['status_solve']!=$this->config->item('system_status_yes'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Solve option field is required.';
            $this->json_return($ajax);
        }

        $path='images/transfer/oo_receive_solve/'.$id;
        $dir=(FCPATH).$path;
        if(!is_dir($dir))
        {
            mkdir($dir, 0777);
        }
        $uploaded_images = System_helper::upload_file($path);
        if(array_key_exists('image_name',$uploaded_images))
        {
            if($uploaded_images['image_name']['status'])
            {
                $item['image_name']=$uploaded_images['image_name']['info']['file_name'];
                $item['image_location']=$path.'/'.$uploaded_images['image_name']['info']['file_name'];
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$uploaded_images['image_name']['message'];
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item['date_updated']=$time;
        $item['user_updated']=$user->user_id;
        Query_helper::update($this->config->item('table_sms_transfer_oo_receive_solves'),$item,array('id='.$id));

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

            $this->db->from($this->config->item('table_sms_transfer_oo_receive_solves').' transfer_oo_receive_solves');
            $this->db->select('transfer_oo_receive_solves.*,transfer_oo_receive_solves.id id');
            $this->db->join($this->config->item('table_sms_transfer_oo').' transfer_oo','transfer_oo.id=transfer_oo_receive_solves.transfer_oo_id','INNER');
            $this->db->select('transfer_oo.*, transfer_oo.id transfer_oo_id');
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
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_oo.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_oo.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo_receive_solves.id',$item_id);
            $this->db->order_by('transfer_oo.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('details',$item_id,'View Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_source'], $this->outlet_ids))
            {
                System_helper::invalid_try('save',$id,'User Outlet Not Assign (Source)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Source outlet not assign.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_destination'], $this->outlet_ids))
            {
                System_helper::invalid_try('save',$id,'User Outlet Not Assign (Destination)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Destination outlet not assign.';
                $this->json_return($ajax);
            }
            $user_ids=array();
            $user_ids[$data['item']['user_updated']]=$data['item']['user_updated'];
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $user_ids[$data['item']['user_updated_receive']]=$data['item']['user_updated_receive'];
            $user_ids[$data['item']['user_updated_receive_forward']]=$data['item']['user_updated_receive_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' details');
            $this->db->select('details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('details.transfer_oo_id',$data['item']['transfer_oo_id']);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $this->db->order_by('details.id');
            $data['items']=$this->db->get()->result_array();

            $data['title']=$this->outlets[$data['item']['outlet_id_source']]['name']." to ".$this->outlets[$data['item']['outlet_id_destination']]['name']." Transfer Details :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
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

}
