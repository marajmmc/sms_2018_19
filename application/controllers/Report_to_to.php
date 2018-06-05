<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_to_to extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
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
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
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
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            //$data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            //$data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }

            $data['date_start']='';
            $data['date_end']='';
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

            $data['title']="TO Wise Report Search";
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

            $data['system_preference_items']= $this->get_preference();
            $data['title']="TO (Transfer Order) Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

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

    /* Start Transfer report function */
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_transfer"'),1);

        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['district_name']= 1;
        $data['outlet_name']= 1;
        $data['barcode']= 1;
        $data['date_request']= 1;
        $data['quantity_total_request']= 1;
        $data['status_request']= 1;
        $data['date_approve']= 1;
        $data['quantity_total_approve']= 1;
        $data['status_approve']= 1;
        $data['date_delivery']= 1;
        $data['status_delivery']= 1;
        $data['date_receive']= 1;
        $data['quantity_total_receive']= 1;
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
    private function system_get_items()
    {
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');
        $date_type=$this->input->post('date_type');

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $status_request=$this->input->post('status_request');
        $status_approve=$this->input->post('status_approve');
        $status_delivery=$this->input->post('status_delivery');
        $status_receive=$this->input->post('status_receive');
        //$status_receive_forward=$this->input->post('status_receive_forward');
        $status_receive_approve=$this->input->post('status_receive_approve');
        $status_system_delivery_receive=$this->input->post('status_system_delivery_receive');

        $items=array();

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.id division_id, divisions.name division_name');
        $this->db->order_by('divisions.id, zones.id, territories.id, districts.id, outlet_info.customer_id');
        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));

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
        if($division_id)
        {
            $this->db->where('divisions.id',$division_id);
            if($zone_id)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id)
                {
                    $this->db->where('territories.id',$territory_id);
                    if($district_id)
                    {
                        $this->db->where('districts.id',$district_id);
                        if($outlet_id)
                        {
                            $this->db->where('outlet_info.customer_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $data['location']=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $outlet_ids=array();
        foreach($data['location'] as $result)
        {
            $outlet_ids[]=$result['outlet_id'];
        }

        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->select('transfer_wo.*');
        $this->db->order_by('transfer_wo.id');
        $this->db->group_by('transfer_wo.id');
        $this->db->where('transfer_wo.'.$date_type.'>='.$date_start.' and transfer_wo.'.$date_type.'<='.$date_end);
        if($status_request)
        {
            $this->db->where('transfer_wo.status_request',$status_request);
        }
        if($status_approve)
        {
            $this->db->where('transfer_wo.status_approve',$status_approve);
        }
        if($status_delivery)
        {
            $this->db->where('transfer_wo.status_delivery',$status_delivery);
        }
        if($status_receive)
        {
            if($status_receive==$this->config->item('system_status_forwarded'))
            {
                $this->db->where('transfer_wo.status_receive_forward',$status_receive);
            }
            else
            {
                $this->db->where('transfer_wo.status_receive',$status_receive);
            }
        }
        if($status_receive_approve)
        {
            $this->db->where('transfer_wo.status_receive_approve',$status_receive_approve);
        }
        if($status_system_delivery_receive)
        {
            $this->db->where('transfer_wo.status_system_delivery_receive',$status_system_delivery_receive);
        }

        if(sizeof($outlet_ids)>0)
        {
            $this->db->where_in('transfer_wo.outlet_id',$outlet_ids);
        }
        if($outlet_id)
        {
            $this->db->where('transfer_wo.outlet_id',$outlet_id);
        }
        $data['items']=$this->db->get()->result_array();
        $all_to=array();
        foreach($data['items'] as $item)
        {
            $all_to[$item['outlet_id']][$item['id']]=$item;
        }

        $first_row=true;
        $prev_division_name='';
        $prev_zone_name='';
        $prev_territory_name='';
        $prev_district_name='';
        foreach($data['location'] as $result)
        {
            if(!$first_row)
            {
                if($prev_division_name!=$result['division_name'])
                {
                    $prev_division_name=$result['division_name'];
                    $prev_zone_name=$result['zone_name'];
                    $prev_territory_name=$result['territory_name'];
                    $prev_district_name=$result['district_name'];
                }
                elseif($prev_zone_name!=$result['zone_name'])
                {
                    $result['division_name']='';
                    $prev_zone_name=$result['zone_name'];
                    $prev_territory_name=$result['territory_name'];
                    $prev_district_name=$result['district_name'];
                }
                elseif($prev_territory_name!=$result['territory_name'])
                {
                    $result['division_name']='';
                    $result['zone_name']='';
                    $prev_territory_name=$result['territory_name'];
                    $prev_district_name=$result['district_name'];
                }
                elseif($prev_district_name!=$result['district_name'])
                {
                    $result['division_name']='';
                    $result['zone_name']='';
                    $result['territory_name']='';
                    $prev_district_name=$result['district_name'];
                }
                else
                {
                    $result['division_name']='';
                    $result['zone_name']='';
                    $result['territory_name']='';
                    $result['district_name']='';
                }
            }
            else
            {
                $prev_division_name=$result['division_name'];
                $prev_zone_name=$result['zone_name'];
                $prev_territory_name=$result['territory_name'];
                $prev_district_name=$result['district_name'];
                $first_row=false;
            }
            $items[]=$this->get_row_location($result);
            if(isset($all_to[$result['outlet_id']]))
            {
                if(sizeof($all_to[$result['outlet_id']])>0)
                {
                    foreach($all_to[$result['outlet_id']] as $id=>$two)
                    {
                        $row['transfer_wo_id']=$id;
                        $row['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($id);
                        $row['date_request']=System_helper::display_date($two['date_request']);
                        $row['quantity_total_request']=number_format($two['quantity_total_request_kg'],3,'.','');
                        $row['status_request']=$two['status_request'];
                        $row['date_approve']=System_helper::display_date($two['date_approve']);
                        $row['quantity_total_approve']=number_format($two['quantity_total_approve_kg'],3,'.','');
                        $row['status_approve']=$two['status_approve'];
                        $row['date_delivery']=System_helper::display_date($two['date_delivery']);
                        $row['status_delivery']=$two['status_delivery'];
                        $row['date_receive']=System_helper::display_date($two['date_receive']);
                        $row['quantity_total_receive']=number_format($two['quantity_total_receive_kg'],3,'.','');
                        $row['status_receive']=$two['status_receive'];
                        $row['status_receive_forward']=$two['status_receive_forward'];
                        $row['status_receive_approve']=$two['status_receive_approve'];
                        $row['status_system_delivery_receive']=$two['status_system_delivery_receive'];
                        if($two['status_approve']==$this->config->item('system_status_rejected'))
                        {
                            $row['status_delivery']='';
                            $row['status_receive']='';
                            $row['status_receive_forward']='';
                            $row['status_receive_approve']='';
                            $row['status_system_delivery_receive']='';
                        }
                        if($two['status_system_delivery_receive']==$this->config->item('system_status_yes'))
                        {
                            $row['status_receive_forward']='';
                            $row['status_receive_approve']='';
                        }
                        $row['status']=$two['status'];
                        $items[]=$row;
                    }
                }
            }
        }

        $this->json_return($items);
        die();
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference_transfer();
            $data['preference_method_name']='search_transfer';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_transfer');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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

            /*$result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('*'),array('purpose="'.$this->config->item('system_purpose_sms_quantity_order_max').'"', 'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['quantity_to_maximum_kg']=$result['config_value'];
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['two_variety_info']=Stock_helper::transfer_wo_variety_stock_info($data['item']['outlet_id']);*/

            $data['title']="HQ to Outlet Details Transfer Request :: ". Barcode_helper::get_barcode_transfer_warehouse_to_outlet($data['item']['id']);
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
    /* End Transfer report function */

    /* Start Variety report function */
    private function get_preference_variety()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_variety"'),1);

        $data['crop_name']= 1;
        $data['crop_type']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['date_delivery']= 1;
        $data['outlet_name']= 1;
        $data['quantity_order']= 1;
        $data['quantity_approve']= 1;
        $data['quantity_receive']= 1;
        $data['quantity_deference']= 1;
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
    private function system_list_variety()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $data['options']=$reports;
            if(!System_helper::get_time($reports['date_start']) || !System_helper::get_time($reports['date_end']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting date and end date is required.';
                $this->json_return($ajax);
            }
            $data['system_preference_items']= $this->get_preference();
            $data['title']="TO Variety Wise Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_variety",$data,true));
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
    private function system_get_items_variety()
    {
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_start=System_helper::get_time($this->input->post('date_start'));
        $date_end=System_helper::get_time($this->input->post('date_end'));


        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $items=array();

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.id division_id, divisions.name division_name');
        $this->db->order_by('divisions.id, zones.id, territories.id, districts.id, outlet_info.customer_id');
        $this->db->where('outlet_info.revision',1);

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
        if($division_id)
        {
            $this->db->where('divisions.id',$division_id);
        }
        if($zone_id)
        {
            $this->db->where('zones.id',$zone_id);
        }
        if($territory_id)
        {
            $this->db->where('territories.id',$territory_id);
        }
        if($district_id)
        {
            $this->db->where('districts.id',$district_id);
        }
        if($outlet_id)
        {
            $this->db->where('outlet_info.customer_id',$outlet_id);
        }
        $data['location']=$this->db->get()->result_array();

        $outlet_ids=array();
        foreach($data['location'] as $result)
        {
            $outlet_ids[]=$result['outlet_id'];
        }

        $this->db->from($this->config->item('table_sms_transfer_wo').' transfer_wo');
        $this->db->select('transfer_wo.*');
        $this->db->join($this->config->item('table_sms_transfer_wo_details').' transfer_wo_details','transfer_wo_details.transfer_wo_id=transfer_wo.id','INNER');
        $this->db->select('transfer_wo_details.*');
        $this->db->where('transfer_wo_details.status',$this->config->item('system_status_active'));
        $this->db->where('transfer_wo.date_request>='.$date_start.' and transfer_wo.date_request<='.$date_end);
        $this->db->order_by('transfer_wo.id');
        $this->db->group_by('transfer_wo.id');
        if($crop_id)
        {
            $this->db->where('transfer_wo_details.crop_id',$crop_id);
            if($crop_type_id)
            {
                $this->db->where('transfer_wo_details.crop_type_id',$crop_type_id);
                if($variety_id)
                {
                    $this->db->where('transfer_wo_details.variety_id',$variety_id);
                }
            }
        }
        if($pack_size_id)
        {
            $this->db->where('transfer_wo_details.pack_size_id',$pack_size_id);
        }
        if(sizeof($outlet_ids)>0)
        {
            $this->db->where_in('transfer_wo.outlet_id',$outlet_ids);
        }
        if($outlet_id)
        {
            $this->db->where('transfer_wo.outlet_id',$outlet_id);
        }
        $data['items']=$this->db->get()->result_array();
        $all_to=array();
        foreach($data['items'] as $item)
        {
            $all_to[$item['outlet_id']][$item['transfer_wo_id']]=$item;
        }

        $first_row=true;
        $prev_division_name='';
        $prev_zone_name='';
        $prev_territory_name='';
        $prev_district_name='';
        foreach($data['location'] as $result)
        {
            if(!$first_row)
            {
                if($prev_division_name!=$result['division_name'])
                {
                    $prev_division_name=$result['division_name'];
                    $prev_zone_name=$result['zone_name'];
                    $prev_territory_name=$result['territory_name'];
                    $prev_district_name=$result['district_name'];
                }
                elseif($prev_zone_name!=$result['zone_name'])
                {
                    $result['division_name']='';
                    $prev_zone_name=$result['zone_name'];
                    $prev_territory_name=$result['territory_name'];
                    $prev_district_name=$result['district_name'];
                }
                elseif($prev_territory_name!=$result['territory_name'])
                {
                    $result['division_name']='';
                    $result['zone_name']='';
                    $prev_territory_name=$result['territory_name'];
                    $prev_district_name=$result['district_name'];
                }
                elseif($prev_district_name!=$result['district_name'])
                {
                    $result['division_name']='';
                    $result['zone_name']='';
                    $result['territory_name']='';
                    $prev_district_name=$result['district_name'];
                }
                else
                {
                    $result['division_name']='';
                    $result['zone_name']='';
                    $result['territory_name']='';
                    $result['district_name']='';
                }
            }
            else
            {
                $prev_division_name=$result['division_name'];
                $prev_zone_name=$result['zone_name'];
                $prev_territory_name=$result['territory_name'];
                $prev_district_name=$result['district_name'];
                $first_row=false;
            }
            $items[]=$this->get_row_location($result);
            if(isset($all_to[$result['outlet_id']]))
            {
                if(sizeof($all_to[$result['outlet_id']])>0)
                {
                    foreach($all_to[$result['outlet_id']] as $id=>$two)
                    {
                        $row['transfer_wo_id']=$id;
                        $row['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_outlet($id);
                        $row['date_request']=System_helper::display_date($two['date_request']);
                        $row['quantity_total_request']=number_format($two['quantity_total_request_kg'],3,'.','');
                        $row['status_request']=$two['status_request'];
                        $row['date_approve']=System_helper::display_date($two['date_approve']);
                        $row['quantity_total_approve']=number_format($two['quantity_total_approve_kg'],3,'.','');
                        $row['status_approve']=$two['status_approve'];
                        $row['date_delivery']=System_helper::display_date($two['date_delivery']);
                        $row['status_delivery']=$two['status_delivery'];
                        $row['date_receive']=System_helper::display_date($two['date_receive']);
                        $row['quantity_total_receive']=number_format($two['quantity_total_receive_kg'],3,'.','');
                        $row['status_receive']=$two['status_receive'];
                        $row['status_receive_forward']=$two['status_receive_forward'];
                        $row['status_receive_approve']=$two['status_receive_approve'];
                        $row['status_system_delivery_receive']=$two['status_system_delivery_receive'];
                        $row['status']=$two['status'];
                        $items[]=$row;
                    }
                }
            }
        }

        $this->json_return($items);
        die();
    }
    private function system_set_preference_variety()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference_variety();
            $data['preference_method_name']='search_variety';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_variety');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    /* End Variety report function */




    private function get_row_location($info)
    {
        $row=array();
        $row['division_name']=$info['division_name'];
        $row['zone_name']=$info['zone_name'];
        $row['territory_name']=$info['territory_name'];
        $row['district_name']=$info['district_name'];
        $row['outlet_name']=$info['outlet_name'];
        return $row;
    }
}
