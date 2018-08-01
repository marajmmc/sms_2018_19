<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfer_oo_request extends Root_Controller
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

        $this->outlets=System_helper::get_outlets_by_location($this->locations['division_id'],$this->locations['zone_id'],$this->locations['territory_id'],$this->locations['district_id']);
        $this->outlet_ids[0]=0;
        foreach($this->outlets as $result)
        {
            $this->outlet_ids[$result['customer_id']]=$result['customer_id'];
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
        elseif($action=="forward")
        {
            $this->system_forward($id);
        }
        elseif($action=="save_forward")
        {
            $this->system_save_forward();
        }
        elseif($action=="ajax_transfer_oo_variety_info")
        {
            $this->system_ajax_transfer_oo_variety_info();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="set_preference_all")
        {
            $this->system_set_preference('list_all');
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
        $data['remarks_request']= 1;
        $data['quantity_total_request']= 1;
        if($method=='list_all')
        {
            $data['quantity_total_approve']= 1;
            $data['quantity_total_receive']= 1;
            $data['status_request']= 1;
            $data['status_approve']= 1;
            $data['status_delivery']= 1;
            $data['status_receive']= 1;
            $data['status_receive_forward']= 1;
            $data['status_receive_approve']= 1;
            $data['status_system_delivery_receive']= 1;
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
            $data['title']="Showroom to Showroom Transfer Request List";
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
        $this->db->select('transfer_oo.id, transfer_oo.outlet_id_source, transfer_oo.outlet_id_destination, transfer_oo.date_request, transfer_oo.quantity_total_request_kg quantity_total_request, transfer_oo.remarks_request');
        $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('transfer_oo.status_request',$this->config->item('system_status_pending'));
        $this->db->where_in('transfer_oo.outlet_id_source',$this->outlet_ids);
        $this->db->where_in('transfer_oo.outlet_id_destination',$this->outlet_ids);
        $this->db->order_by('transfer_oo.id','DESC');
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_outlet($result['id']);
            $item['outlet_name_source']=$this->outlets[$result['outlet_id_source']]['name'].' ('.$this->outlets[$result['outlet_id_source']]['customer_code'].')';
            $item['outlet_name_destination']=$this->outlets[$result['outlet_id_destination']]['name'].' ('.$this->outlets[$result['outlet_id_destination']]['customer_code'].')';
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['remarks_request']=$result['remarks_request'];
            $item['quantity_total_request']=System_helper::get_string_kg($result['quantity_total_request']);
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
            $data['title']="Showroom to Showroom Transfer All List";
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
            transfer_oo.outlet_id_source,
            transfer_oo.outlet_id_destination,
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
        $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('transfer_oo.outlet_id_source',$this->outlet_ids);
        $this->db->where_in('transfer_oo.outlet_id_destination',$this->outlet_ids);
        $this->db->order_by('transfer_oo.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_outlet($result['id']);
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['outlet_name_source']=$this->outlets[$result['outlet_id_source']]['name'].' ('.$this->outlets[$result['outlet_id_source']]['customer_code'].')';
            $item['outlet_name_destination']=$this->outlets[$result['outlet_id_destination']]['name'].' ('.$this->outlets[$result['outlet_id_destination']]['customer_code'].')';
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
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Showroom to Showroom New Transfer Request";
            $data['item']['id']=0;
            $data['item']['outlet_id_source']='';
            $data['item']['outlet_id_destination']='';
            $data['item']['date_request']=time();
            $data['item']['remarks_request']='';
            $data['items']=[];
            $data['too_variety_info']=[];

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
            $this->db->select('transfer_oo.*');
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
            if(!in_array($data['item']['outlet_id_source'], $this->outlet_ids))
            {
                System_helper::invalid_try('edit',$id,'User Outlet Not Assign (Source)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Source outlet not assign.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_destination'], $this->outlet_ids))
            {
                System_helper::invalid_try('edit',$id,'User Outlet Not Assign (Destination)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Destination outlet not assign.';
                $this->json_return($ajax);
            }
            if($data['item']['status_request']!=$this->config->item('system_status_pending'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Showroom to Showroom transfer already forwarded.';
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
            $this->db->where('details.transfer_oo_id',$item_id);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $this->db->order_by('details.id');
            $data['items']=$this->db->get()->result_array();

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['too_variety_info']=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);

            $data['title']=$this->outlets[$data['item']['outlet_id_source']]['name']." to ".$this->outlets[$data['item']['outlet_id_destination']]['name']." Transfer Request Edit :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
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

            $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_oo'),array('*'),array('status !="'.$this->config->item('system_status_delete').'"', 'id ='.$id),1);

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
                $ajax['system_message']='Showroom to Showroom transfer request already forwarded.';
                $this->json_return($ajax);
            }
            /* Source & Destination outlet checking assign permission*/
            $outlet_id_source=$data['item']['outlet_id_source'];
            $outlet_id_destination=$data['item']['outlet_id_destination'];

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
            $outlet_id_source=$item_head['outlet_id_source'];
            $outlet_id_destination=$item_head['outlet_id_destination'];

            $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($item_head['outlet_id_source']);
        }
        if(!in_array($outlet_id_source, $this->outlet_ids))
        {
            System_helper::invalid_try('save',$id,'User Outlet Not Assign (Source)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Source outlet not assign.';
            $this->json_return($ajax);
        }
        if(!in_array($outlet_id_destination, $this->outlet_ids))
        {
            System_helper::invalid_try('save',$id,'User Outlet Not Assign (Destination)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Destination outlet not assign.';
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
                    $stock_available_exceed=($quantity_request-$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                    $ajax['status']=false;
                    $ajax['system_message']='Request quantity already exceed. ( Exceed request quantity: '.$stock_available_exceed.' pkt.)';
                    $this->json_return($ajax);
                }
            }
        }

        $results=Query_helper::get_info($this->config->item('table_sms_transfer_oo_details'),'*',array('transfer_oo_id ='.$id));
        $old_items=array();
        $old_items_rows=array();
        foreach($results as $result)
        {
            $old_items[$result['variety_id']][$result['pack_size_id']]=$result;
            $old_items_rows[$result['id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
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
                    if(!(
                        ($item['variety_id']==$old_items[$item['variety_id']][$item['pack_size_id']]['variety_id'])&&
                        ($item['pack_size_id']==$old_items[$item['variety_id']][$item['pack_size_id']]['pack_size_id'])&&
                        ($item['quantity_request']==$old_items[$item['variety_id']][$item['pack_size_id']]['quantity_request'])&&
                        ($old_items[$item['variety_id']][$item['pack_size_id']]['status']==$this->config->item('system_status_active'))
                    ))
                    {
                        $data=array();
                        $data['pack_size']=$pack_sizes[$item['pack_size_id']]['text'];
                        $data['quantity_request']=$item['quantity_request'];
                        $data['quantity_approve']=$data['quantity_request'];
                        $data['quantity_receive']=$data['quantity_request'];
                        $data['status']=$this->config->item('system_status_active');
                        Query_helper::update($this->config->item('table_sms_transfer_oo_details'),$data, array('transfer_oo_id='.$id, 'variety_id ='.$item['variety_id'], 'pack_size_id ='.$item['pack_size_id']), false);
                    }
                    unset($old_items_rows[$old_items[$item['variety_id']][$item['pack_size_id']]['id']]);
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

            foreach($old_items_rows as $result)
            {
                $data=array();
                $data['status']=$this->config->item('system_status_delete');
                Query_helper::update($this->config->item('table_sms_transfer_oo_details'),$data, array('id='.$result['id']), false);
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

            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_oo.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_oo.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');

            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_user_receive','pos_user_receive.user_id=transfer_oo.user_updated_receive','LEFT');
            $this->db->select('pos_user_receive.name full_name_receive');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_user_receive_forward','pos_user_receive_forward.user_id=transfer_oo.user_updated_receive_forward','LEFT');
            $this->db->select('pos_user_receive_forward.name full_name_receive_forward');

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
            if(!in_array($data['item']['outlet_id_source'], $this->outlet_ids))
            {
                System_helper::invalid_try('details',$id,'User Outlet Not Assign (Source)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Source outlet not assign.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_destination'], $this->outlet_ids))
            {
                System_helper::invalid_try('details',$id,'User Outlet Not Assign (Destination)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Destination outlet not assign.';
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

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' details');
            $this->db->select('details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('details.transfer_oo_id',$item_id);
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
            if($data['item']['status_request']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Showroom to Showroom transfer already forwarded.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_source'], $this->outlet_ids))
            {
                System_helper::invalid_try('forward',$id,'User Outlet Not Assign (Source)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Source outlet not assign.';
                $this->json_return($ajax);
            }
            if(!in_array($data['item']['outlet_id_destination'], $this->outlet_ids))
            {
                System_helper::invalid_try('forward',$id,'User Outlet Not Assign (Destination)');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try. Destination outlet not assign.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' details');
            $this->db->select('details.*');
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $this->db->where('details.transfer_oo_id',$item_id);
            $this->db->order_by('details.id');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $data['items']=$this->db->get()->result_array();

            $data['tow_variety_info']=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);

            $data['title']=$this->outlets[$data['item']['outlet_id_source']]['name']." to ".$this->outlets[$data['item']['outlet_id_destination']]['name']." Transfer (Request Forward) :: ". Barcode_helper::get_barcode_transfer_outlet_to_outlet($data['item']['id']);
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
        if($item_head['status_request']!=$this->config->item('system_status_forwarded'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Forward field is required.';
            $this->json_return($ajax);
        }

        $data['item']=Query_helper::get_info($this->config->item('table_sms_transfer_oo'),array('*'),array('status !="'.$this->config->item('system_status_delete').'"', 'id ='.$id),1);
        if(!$data['item'])
        {
            System_helper::invalid_try('save_forward',$id,'Update Forwarded Non Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!in_array($data['item']['outlet_id_source'], $this->outlet_ids))
        {
            System_helper::invalid_try('save_forward',$id,'User Outlet Not Assign (Source)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Source outlet not assign.';
            $this->json_return($ajax);
        }
        if(!in_array($data['item']['outlet_id_destination'], $this->outlet_ids))
        {
            System_helper::invalid_try('save_forward',$id,'User Outlet Not Assign (Destination)');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try. Destination outlet not assign.';
            $this->json_return($ajax);
        }
        if($data['item']['status_request']==$this->config->item('system_status_forwarded'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Showroom to Showroom transfer already forwarded.';
            $this->json_return($ajax);
        }

        $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($data['item']['outlet_id_source']);

        $this->db->from($this->config->item('table_sms_transfer_oo_details').' details');
        $this->db->select('details.*');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('details.transfer_oo_id',$id);
        $this->db->where('details.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();

        if($results)
        {
            foreach($results as $item)
            {
                if(!isset($too_variety_info[$item['variety_id']][$item['pack_size_id']]))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Invalid variety information :: ( Variety ID: '.$item['variety_id'].' )';
                    $this->json_return($ajax);
                }
                $quantity_request=$item['quantity_request'];
                if($quantity_request>$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt'])
                {
                    $stock_available_exceed=($quantity_request-$too_variety_info[$item['variety_id']][$item['pack_size_id']]['stock_available_pkt']);
                    $ajax['status']=false;
                    $ajax['system_message']='Request quantity already exceed. ( Exceed request quantity: '.$stock_available_exceed.' pkt.)';
                    $this->json_return($ajax);
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item_head['date_updated_forward']=$time;
        $item_head['user_updated_forward']=$user->user_id;
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
    private function system_ajax_transfer_oo_variety_info()
    {
        $outlet_id=$this->input->post('outlet_id_source');
        $too_variety_info=Stock_helper::transfer_oo_variety_stock_info($outlet_id);
        $this->json_return($too_variety_info);
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

        if($id==0)
        {
            $item = $this->input->post("item");
            if($item['outlet_id_source'] == $item['outlet_id_destination'])
            {
                $this->message="You can't select same outlet.";
                return false;
            }
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
