<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_tr_variety extends Root_Controller
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
        $this->lang->load('report_to');
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items_tr")
        {
            $this->system_get_items_tr();
        }
        elseif($action=="get_items_quantity")
        {
            $this->system_get_items_quantity();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
    private function get_preference_headers($method)
    {
        $data['id']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        if($method=='list_quantity_wise')
        {
            $data['quantity_total_request_pkt']= 1;
            $data['quantity_total_request_kg']= 1;
            $data['quantity_total_approve_pkt']= 1;
            $data['quantity_total_approve_kg']= 1;
            $data['quantity_total_receive_pkt']= 1;
            $data['quantity_total_receive_kg']= 1;
        }
        elseif($method=='list_tr_wise')
        {
            $data['barcode']= 1;
            $data['quantity_total_request_pkt']= 1;
            $data['quantity_total_request_kg']= 1;
            $data['quantity_total_approve_pkt']= 1;
            $data['quantity_total_approve_kg']= 1;
            $data['quantity_total_receive_pkt']= 1;
            $data['quantity_total_receive_kg']= 1;
            $data['date_request']= 1;
            $data['date_approve']= 1;
            $data['date_delivery']= 1;
            $data['date_receive']= 1;
        }
        else
        {

        }
        return $data;
    }
    private function system_set_preference($method)
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
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id'],'status ="'.$this->config->item('system_status_active').'"'));
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

            $data['title']="TR Variety Wise Report Search";
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
    private function system_list()
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            if(!($reports['date_start'] || $reports['date_end']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Minimum provide the start or end date.';
                $this->json_return($ajax);
            }
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }

            $data['options']=$reports;

            if($reports['report_name']=='quantity_wise')
            {
                $method='list_quantity_wise';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $data['title']="Quantity Wise Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_quantity_wise",$data,true));
            }
            elseif($reports['report_name']=='tr_wise')
            {
                $method='list_tr_wise';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $data['title']="TR Wise Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_tr_wise",$data,true));
            }
            else
            {

            }


            $ajax['status']=true;
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
    /* Start Transfer TO Wise report function */
    private function system_get_items_tr()
    {
        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');
        $date_type=$this->input->post('date_type');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $status_request=$this->input->post('status_request');
        $status_approve=$this->input->post('status_approve');
        $status_delivery=$this->input->post('status_delivery');
        $status_receive=$this->input->post('status_receive');
        $status_receive_approve=$this->input->post('status_receive_approve');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');

        $varieties=$this->db->get()->result_array();
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }


        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');
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
        foreach($results as $result)
        {
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }


        $this->db->from($this->config->item('table_sms_transfer_ow_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.pack_size, details.transfer_ow_id');

        $this->db->join($this->config->item('table_sms_transfer_ow').' transfer_ow','transfer_ow.id = details.transfer_ow_id','INNER');
        $this->db->select('transfer_ow.date_request');
        $this->db->select('transfer_ow.date_approve');
        $this->db->select('transfer_ow.date_delivery');
        $this->db->select('transfer_ow.date_receive');
        $this->db->select('transfer_ow.status_approve');
        $this->db->select('transfer_ow.status_delivery');
        $this->db->select('transfer_ow.status_receive');
        /*$this->db->select('SUM(CASE WHEN transfer_ow.'.$date_type.'>='.$date_start.' and transfer_ow.'.$date_type.'<='.$date_end.' then details.quantity_request ELSE 0 END) quantity_request',false);
        $this->db->select('SUM(CASE WHEN transfer_ow.'.$date_type.'>='.$date_start.' and transfer_ow.'.$date_type.'<='.$date_end.' then details.quantity_approve ELSE 0 END) quantity_approve',false);
        $this->db->select('SUM(CASE WHEN transfer_ow.'.$date_type.'>='.$date_start.' and transfer_ow.'.$date_type.'<='.$date_end.' then details.quantity_receive ELSE 0 END) quantity_receive',false);*/
        $this->db->select('details.quantity_request');
        $this->db->select('details.quantity_approve');
        $this->db->select('details.quantity_receive');

        $this->db->where('transfer_ow.status',$this->config->item('system_status_active'));
        $this->db->where('details.status',$this->config->item('system_status_active'));
        $this->db->where_in('transfer_ow.outlet_id',$outlet_ids);
        $this->db->where('transfer_ow.'.$date_type.'>= ',$date_start);
        $this->db->where('transfer_ow.'.$date_type.'<= ',$date_end);

        if($status_request)
        {
            $this->db->where('transfer_ow.status_request',$status_request);
        }
        if($status_approve)
        {
            $this->db->where('transfer_ow.status_approve',$status_approve);
        }
        if($status_delivery)
        {
            $this->db->where('transfer_ow.status_delivery',$status_delivery);
        }
        if($status_receive)
        {
            if($status_receive==$this->config->item('system_status_forwarded'))
            {
                $this->db->where('transfer_ow.status_receive_forward',$status_receive);
            }
            else
            {
                $this->db->where('transfer_ow.status_receive',$status_receive);
            }
        }
        if($status_receive_approve)
        {
            $this->db->where('transfer_ow.status_receive_approve',$status_receive_approve);
        }
        $this->db->where_in('details.variety_id',$variety_ids);
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->order_by('transfer_ow.id','DESC');
        $results=$this->db->get()->result_array();

        $varieties_to=array();
        foreach($results as $result)
        {
            $varieties_to[$result['variety_id']][$result['pack_size_id']][]=$result;
        }

        //final items
        $method='list_tr_wise';
        $type_total=$this->initialize_row('','','Total Type','',$method);
        $crop_total=$this->initialize_row('','Total Crop','','',$method);
        $grand_total=$this->initialize_row('Grand Total','','','',$method);
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;
        $items=array();
        foreach($varieties as $variety)
        {
            if(isset($varieties_to[$variety['variety_id']]))
            {
                foreach($varieties_to[$variety['variety_id']] as $invoice_details)
                {
                    $i=0;
                    foreach($invoice_details as $details)
                    {
                        $info=$this->initialize_row($variety['crop_name'],$variety['crop_type_name'],$variety['variety_name'],$details['pack_size'],$method);
                        if(!$first_row)
                        {
                            if($prev_crop_name!=$variety['crop_name'])
                            {
                                $items[]=$this->get_row($type_total);
                                $items[]=$this->get_row($crop_total);
                                $type_total=$this->reset_row($type_total);
                                $crop_total=$this->reset_row($crop_total);

                                $prev_crop_name=$variety['crop_name'];
                                $prev_type_name=$variety['crop_type_name'];


                            }
                            elseif($prev_type_name!=$variety['crop_type_name'])
                            {
                                $items[]=$this->get_row($type_total);
                                $type_total=$this->reset_row($type_total);

                                $info['crop_name']='';
                                $prev_type_name=$variety['crop_type_name'];
                            }
                            else
                            {
                                $info['crop_name']='';
                                $info['crop_type_name']='';
                            }
                        }
                        else
                        {
                            $prev_crop_name=$variety['crop_name'];
                            $prev_type_name=$variety['crop_type_name'];
                            $first_row=false;
                        }
                        if($i>0)
                        {
                            $info['variety_name']='';
                            $info['pack_size']='';
                        }
                        $i++;
                        $info['id']=$details['transfer_ow_id'];
                        $info['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_warehouse($info['id']);

                        $info['quantity_total_request_pkt']=$details['quantity_request'];
                        $info['quantity_total_request_kg']=(($details['pack_size']*$details['quantity_request'])/1000);
                        $info['quantity_total_approve_pkt']='';
                        $info['quantity_total_approve_kg']='';
                        $info['quantity_total_receive_pkt']='';
                        $info['quantity_total_receive_kg']='';
                        $info['date_request']=System_helper::display_date($details['date_request']);
                        if($details['status_approve']==$this->config->item('system_status_approved'))
                        {
                            $info['quantity_total_approve_pkt']=$details['quantity_approve'];
                            $info['quantity_total_approve_kg']=(($details['pack_size']*$details['quantity_approve'])/1000);
                            $info['date_approve']=System_helper::display_date($details['date_approve']);
                        }
                        if($details['status_delivery']==$this->config->item('system_status_delivered'))
                        {
                            $info['date_delivery']=System_helper::display_date($details['date_delivery']);
                        }
                        if($details['status_receive']==$this->config->item('system_status_received'))
                        {
                            $info['quantity_total_receive_pkt']=$details['quantity_receive'];
                            $info['quantity_total_receive_kg']=(($details['pack_size']*$details['quantity_receive'])/1000);
                            $info['date_receive']=System_helper::display_date($details['date_receive']);
                        }

                        $type_total['quantity_total_request_pkt']+=$info['quantity_total_request_pkt'];
                        $type_total['quantity_total_request_kg']+=$info['quantity_total_request_kg'];
                        $type_total['quantity_total_approve_pkt']+=$info['quantity_total_approve_pkt'];
                        $type_total['quantity_total_approve_kg']+=$info['quantity_total_approve_kg'];
                        $type_total['quantity_total_receive_pkt']+=$info['quantity_total_receive_pkt'];
                        $type_total['quantity_total_receive_kg']+=$info['quantity_total_receive_kg'];

                        $crop_total['quantity_total_request_pkt']+=$info['quantity_total_request_pkt'];
                        $crop_total['quantity_total_request_kg']+=$info['quantity_total_request_kg'];
                        $crop_total['quantity_total_approve_pkt']+=$info['quantity_total_approve_pkt'];
                        $crop_total['quantity_total_approve_kg']+=$info['quantity_total_approve_kg'];
                        $crop_total['quantity_total_receive_pkt']+=$info['quantity_total_receive_pkt'];
                        $crop_total['quantity_total_receive_kg']+=$info['quantity_total_receive_kg'];

                        $grand_total['quantity_total_request_pkt']+=$info['quantity_total_request_pkt'];
                        $grand_total['quantity_total_request_kg']+=$info['quantity_total_request_kg'];
                        $grand_total['quantity_total_approve_pkt']+=$info['quantity_total_approve_pkt'];
                        $grand_total['quantity_total_approve_kg']+=$info['quantity_total_approve_kg'];
                        $grand_total['quantity_total_receive_pkt']+=$info['quantity_total_receive_pkt'];
                        $grand_total['quantity_total_receive_kg']+=$info['quantity_total_receive_kg'];

                        $items[]=$info;
                    }
                }
            }
        }
        $items[]=$this->get_row($type_total);
        $items[]=$this->get_row($crop_total);
        $items[]=$this->get_row($grand_total);
        $this->json_return($items);
    }

    private function system_get_items_quantity()
    {
        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');
        $date_type=$this->input->post('date_type');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $status_request=$this->input->post('status_request');
        $status_approve=$this->input->post('status_approve');
        $status_delivery=$this->input->post('status_delivery');
        $status_receive=$this->input->post('status_receive');
        $status_receive_approve=$this->input->post('status_receive_approve');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');

        $varieties=$this->db->get()->result_array();
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');
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
        foreach($results as $result)
        {
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }


        $this->db->from($this->config->item('table_sms_transfer_ow_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.pack_size, details.transfer_ow_id');

        $this->db->join($this->config->item('table_sms_transfer_ow').' transfer_ow','transfer_ow.id = details.transfer_ow_id','INNER');

        $this->db->select('SUM(CASE WHEN transfer_ow.status_request="'.$this->config->item('system_status_forwarded').'" then details.quantity_request ELSE 0 END) quantity_request',false);
        $this->db->select('SUM(CASE WHEN transfer_ow.status_approve="'.$this->config->item('system_status_approved').'" then details.quantity_approve ELSE 0 END) quantity_approve',false);
        $this->db->select('SUM(CASE WHEN transfer_ow.status_receive="'.$this->config->item('system_status_received').'" then details.quantity_receive ELSE 0 END) quantity_receive',false);

        $this->db->where('transfer_ow.status',$this->config->item('system_status_active'));
        $this->db->where('details.status',$this->config->item('system_status_active'));

        $this->db->where_in('transfer_ow.outlet_id',$outlet_ids);
        $this->db->where('transfer_ow.'.$date_type.'>= ',$date_start);
        $this->db->where('transfer_ow.'.$date_type.'<= ',$date_end);

        if($status_request)
        {
            $this->db->where('transfer_ow.status_request',$status_request);
        }
        if($status_approve)
        {
            $this->db->where('transfer_ow.status_approve',$status_approve);
        }
        if($status_delivery)
        {
            $this->db->where('transfer_ow.status_delivery',$status_delivery);
        }
        if($status_receive)
        {
            if($status_receive==$this->config->item('system_status_forwarded'))
            {
                $this->db->where('transfer_ow.status_receive_forward',$status_receive);
            }
            else
            {
                $this->db->where('transfer_ow.status_receive',$status_receive);
            }
        }
        if($status_receive_approve)
        {
            $this->db->where('transfer_ow.status_receive_approve',$status_receive_approve);
        }
        $this->db->where_in('details.variety_id',$variety_ids);
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->order_by('transfer_ow.id');
        $results=$this->db->get()->result_array();

        $varieties_to=array();
        foreach($results as $result)
        {
            $varieties_to[$result['variety_id']][$result['pack_size_id']]=$result;
        }

        //final items
        $method='list_tr_wise';
        $type_total=$this->initialize_row('','','Total Type','',$method);
        $crop_total=$this->initialize_row('','Total Crop','','',$method);
        $grand_total=$this->initialize_row('Grand Total','','','',$method);
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;
        $items=array();
        foreach($varieties as $variety)
        {
            if(isset($varieties_to[$variety['variety_id']]))
            {
                foreach($varieties_to[$variety['variety_id']] as $details)
                {
                    $info=$this->initialize_row($variety['crop_name'],$variety['crop_type_name'],$variety['variety_name'],$details['pack_size'],$method);
                    if(!$first_row)
                    {
                        if($prev_crop_name!=$variety['crop_name'])
                        {
                            $items[]=$this->get_row($type_total);
                            $items[]=$this->get_row($crop_total);
                            $type_total=$this->reset_row($type_total);
                            $crop_total=$this->reset_row($crop_total);

                            $prev_crop_name=$variety['crop_name'];
                            $prev_type_name=$variety['crop_type_name'];


                        }
                        elseif($prev_type_name!=$variety['crop_type_name'])
                        {
                            $items[]=$this->get_row($type_total);
                            $type_total=$this->reset_row($type_total);

                            $info['crop_name']='';
                            $prev_type_name=$variety['crop_type_name'];
                        }
                        else
                        {
                            $info['crop_name']='';
                            $info['crop_type_name']='';
                        }
                    }
                    else
                    {
                        $prev_crop_name=$variety['crop_name'];
                        $prev_type_name=$variety['crop_type_name'];
                        $first_row=false;
                    }

                    $info['id']=$details['transfer_ow_id'];
                    $info['barcode']=Barcode_helper::get_barcode_transfer_outlet_to_warehouse($info['id']);

                    $info['quantity_total_request_pkt']=$details['quantity_request'];
                    $info['quantity_total_request_kg']=(($details['pack_size']*$details['quantity_request'])/1000);



                    $info['quantity_total_approve_pkt']=$details['quantity_approve'];
                    $info['quantity_total_approve_kg']=(($details['pack_size']*$details['quantity_approve'])/1000);


                    $info['quantity_total_receive_pkt']=$details['quantity_receive'];
                    $info['quantity_total_receive_kg']=(($details['pack_size']*$details['quantity_receive'])/1000);

                    $type_total['quantity_total_request_pkt']+=$info['quantity_total_request_pkt'];
                    $type_total['quantity_total_request_kg']+=$info['quantity_total_request_kg'];
                    $type_total['quantity_total_approve_pkt']+=$info['quantity_total_approve_pkt'];
                    $type_total['quantity_total_approve_kg']+=$info['quantity_total_approve_kg'];
                    $type_total['quantity_total_receive_pkt']+=$info['quantity_total_receive_pkt'];
                    $type_total['quantity_total_receive_kg']+=$info['quantity_total_receive_kg'];

                    $crop_total['quantity_total_request_pkt']+=$info['quantity_total_request_pkt'];
                    $crop_total['quantity_total_request_kg']+=$info['quantity_total_request_kg'];
                    $crop_total['quantity_total_approve_pkt']+=$info['quantity_total_approve_pkt'];
                    $crop_total['quantity_total_approve_kg']+=$info['quantity_total_approve_kg'];
                    $crop_total['quantity_total_receive_pkt']+=$info['quantity_total_receive_pkt'];
                    $crop_total['quantity_total_receive_kg']+=$info['quantity_total_receive_kg'];

                    $grand_total['quantity_total_request_pkt']+=$info['quantity_total_request_pkt'];
                    $grand_total['quantity_total_request_kg']+=$info['quantity_total_request_kg'];
                    $grand_total['quantity_total_approve_pkt']+=$info['quantity_total_approve_pkt'];
                    $grand_total['quantity_total_approve_kg']+=$info['quantity_total_approve_kg'];
                    $grand_total['quantity_total_receive_pkt']+=$info['quantity_total_receive_pkt'];
                    $grand_total['quantity_total_receive_kg']+=$info['quantity_total_receive_kg'];

                    $items[]=$info;
                }
            }
        }
        $items[]=$this->get_row($type_total);
        $items[]=$this->get_row($crop_total);
        $items[]=$this->get_row($grand_total);
        $this->json_return($items);
    }

    private function initialize_row($crop_name,$crop_type_name,$variety_name,$pack_size,$method)
    {
        $row=$this->get_preference_headers($method);
        foreach($row  as $key=>$r)
        {
            $row[$key]='';
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
                $info[$key]='';
            }
        }
        return $info;
    }
    private function get_row($info)
    {
        $row=array();
        foreach($info  as $key=>$r)
        {
            if(substr($key,-3)=='pkt')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=$info[$key];
                }
            }
            elseif(substr($key,-2)=='kg')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],3,'.','');
                }
            }
            elseif(substr($key,0,6)=='amount')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],2);
                }
            }
            else
            {
                $row[$key]=$info[$key];
            }

        }
        return $row;
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
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_ow.user_updated_receive_forward','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_receive_forward');
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

            $data['title']="Outlet to HQ Details Transfer Request :: ". Barcode_helper::get_barcode_transfer_outlet_to_warehouse($data['item']['id']);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
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

}
