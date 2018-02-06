<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_variety_stock_details extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Report_variety_stock_details');
        $this->controller_url='report_variety_stock_details';
    }
    public function index($action='search')
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
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']=System_helper::display_date(time());

            $data['title']="Details Stock Report Search";
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/search',$data,true));
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['crop_name']= 1;
            $data['system_preference_items']['crop_type']= 1;
            $data['system_preference_items']['variety']= 1;
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['pack_size']= 1;
            $data['system_preference_items']['starting_stock']= 1;
            $data['system_preference_items']['total_stock_in']= 1;
            $data['system_preference_items']['total_stock_out']= 1;
            $data['system_preference_items']['current_stock']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }

            $reports=$this->input->post('report');

            if($reports)
            {
                $reports['date_end']=System_helper::get_time($reports['date_end']);
                $reports['date_end']=$reports['date_end']+3600*24-1;
                $reports['date_start']=System_helper::get_time($reports['date_start']);
                if($reports['date_start'])
                {
                    $reports['date_start']-=1;
                }
                if($reports['date_start']>=$reports['date_end'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Starting Date should be less than End date';
                    $this->json_return($ajax);
                }

                $data['options']=$reports;
            }

            $data['title']="Stock Report In Details";
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_report_container','html'=>$this->load->view($this->controller_url.'/list',$data,true));

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

    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['crop_name']= 1;
            $data['system_preference_items']['crop_type']= 1;
            $data['system_preference_items']['variety']= 1;
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['pack_size']= 1;
            $data['system_preference_items']['starting_stock']= 1;
            $data['system_preference_items']['total_stock_in']= 1;
            $data['system_preference_items']['total_stock_out']= 1;
            $data['system_preference_items']['current_stock']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }
            $data['preference_method_name']='list';

            $data['title']="Set Preference";
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

    private function system_get_items()
    {
        $warehouse_id=$this->input->post('warehouse_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        $fiscal_year_id=$this->input->post('fiscal_year_id');  // may not be used

        $starting_items=$this->get_stocks($date_end,$warehouse_id,$crop_id,$crop_type_id,$variety_id,$pack_size_id);

//        print_r($starting_items);
//        exit;

        $items=array();

        $this->json_return($items);


    }
    private function get_stocks($time,$warehouse_id,$crop_id,$crop_type_id,$variety_id,$pack_size_id)
    {
        $stocks=array();
        if($time==0)
        {
            return $stocks;
        }
        //Stock In(By LC)
        $this->db->from($this->config->item('table_sms_lc_open').' lco');
        $this->db->select('lco.*');
        $this->db->select('lcd.variety_id, lcd.pack_size_id,lcd.receive_warehouse_id,lcd.quantity_receive');
        $this->db->join($this->config->item('table_sms_lc_details').' lcd','lcd.lc_id = lco.id','INNER');
        $this->db->select('variety.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = lcd.variety_id','INNER');
        $this->db->select('v_pack_size.name pack_size_name, v_pack_size.id pack_id');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = lcd.pack_size_id','LEFT');
        $this->db->select('ware_house.name ware_house_name');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = lcd.receive_warehouse_id','INNER');
        $this->db->select('type.name crop_type_name, type.id type_id');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
        $this->db->select('crop.name crop_name, crop.id crop_id');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('SUM(lcd.quantity_receive)');
        $this->db->select('SUM(quantity_receive) in_lc');
        $this->db->group_by(array('variety_id','pack_size_id'));

        $this->db->where('lco.date_receive_completed <=',$time);
        if($warehouse_id>0)
        {
            $this->db->where('lcd.receive_warehouse_id',$warehouse_id);
        }
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
        }
        if($crop_type_id>0)
        {
            $this->db->where('type.id',$crop_type_id);
        }
        if($variety_id>0)
        {
            $this->db->where('lcd.variety_id',$variety_id);
        }
        if($pack_size_id>0)
        {
            $this->db->where('v_pack_size.id',$pack_size_id);
        }
        $this->db->order_by('crop.ordering');
        $this->db->order_by('type.ordering');
        $this->db->order_by('variety.ordering');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['lc']=$result['in_lc'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['short']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['rnd']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sample']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['demonstration']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['pack_size_name']=$result['pack_size_name'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['variety_name']=$result['variety_name'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['crop_type_name']=$result['crop_type_name'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['crop_name']=$result['crop_name'];
        }

        //Stock In (By in_stock and in_excess purpose)
        $this->db->from($this->config->item('table_sms_stock_in_variety').' stock_in');
        $this->db->select('stock_in.*');
        $this->db->select('stock_in_details.variety_id, stock_in_details.pack_size_id, stock_in_details.warehouse_id, stock_in_details.quantity');
        $this->db->join($this->config->item('table_sms_stock_in_variety_details').' stock_in_details','stock_in_details.stock_in_id = stock_in.id','INNER');
        $this->db->select('variety.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_in_details.variety_id','INNER');
        $this->db->select('v_pack_size.name pack_size_name, v_pack_size.id pack_id');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = stock_in_details.pack_size_id','LEFT');
        $this->db->select('ware_house.name ware_house_name');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = stock_in_details.warehouse_id','INNER');
        $this->db->select('type.name crop_type_name, type.id type_id');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
        $this->db->select('crop.name crop_name, crop.id crop_id');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('SUM(quantity) in_stock_excess');
        $this->db->group_by(array('variety_id','pack_size_id'));

        $this->db->where('stock_in.date_stock_in <=',$time);
        if($warehouse_id>0)
        {
            $this->db->where('stock_in_details.warehouse_id',$warehouse_id);
        }
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
        }
        if($crop_type_id>0)
        {
            $this->db->where('type.id',$crop_type_id);
        }
        if($variety_id>0)
        {
            $this->db->where('stock_in_details.variety_id',$variety_id);
        }
        if($pack_size_id>0)
        {
            $this->db->where('v_pack_size.id',$pack_size_id);
        }
        $this->db->order_by('crop.ordering');
        $this->db->order_by('type.ordering');
        $this->db->order_by('variety.ordering');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(isset($stocks[$result['variety_id']][$result['pack_size_id']]))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=$result['in_stock_excess'];
            }

        }

        //stock out
        $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
        $this->db->select('stock_out.*');
        $this->db->select('stock_out_details.variety_id, stock_out_details.pack_size_id, stock_out_details.warehouse_id, stock_out_details.quantity');
        $this->db->join($this->config->item('table_sms_stock_out_variety_details').' stock_out_details','stock_out_details.stock_out_id = stock_out.id','INNER');
        $this->db->select('variety.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_out_details.variety_id','INNER');
        $this->db->select('v_pack_size.name pack_size_name');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = stock_out_details.pack_size_id','LEFT');
        $this->db->select('ware_house.name ware_house_name');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = stock_out_details.warehouse_id','INNER');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('SUM(quantity) stock_out');
        $this->db->group_by(array('variety_id','pack_size_id'));

        $this->db->where('stock_out.date_stock_out <=',$time);
        if($warehouse_id>0)
        {
            $this->db->where('stock_out_details.warehouse_id',$warehouse_id);
        }
        if($crop_id>0)
        {
            $this->db->where('type.crop_id',$crop_id);
        }
        if($crop_type_id>0)
        {
            $this->db->where('type.id',$crop_type_id);
        }
        if($variety_id>0)
        {
            $this->db->where('stock_out_details.variety_id',$variety_id);
        }
        if($pack_size_id>0)
        {
            $this->db->where('v_pack_size.id',$pack_size_id);
        }
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(isset($stocks[$result['variety_id']][$result['pack_size_id']]))
            {
                if($result['purpose']==$this->config->item('system_purpose_variety_short_inventory'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['short']=$result['stock_out'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_rnd'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['rnd']=$result['stock_out'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_sample'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['sample']=$result['stock_out'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_demonstration'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['demonstration']=$result['stock_out'];
                }
            }

        }


        //Sales Portion have to do.


        return $stocks;

    }
}