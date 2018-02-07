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

        //$items=array();

        if(sizeof($starting_items)>0)
        {
            //have to complete current price task
            //$prices=$this->get_current_price($warehouse_id,$crop_id,$crop_type_id,$variety_id,$pack_size_id);
//            echo $date_start;
//            exit;
            echo $date_start.'<br>';

            echo $date_end.'<br>';
            exit;
            $initial_items=$this->get_stocks($date_start,$warehouse_id,$crop_id,$crop_type_id,$variety_id,$pack_size_id);

            print_r($initial_items);
            exit;
            $prev_crop_name='';
            $prev_crop_type_name='';
            $count=0;

            $type_starting_stock=0;
            $crop_starting_stock=0;
            $grand_starting_stock=0;

            $type_stock_in=0;
            $crop_stock_in=0;
            $grand_stock_in=0;

            $type_excess=0;
            $crop_excess=0;
            $grand_excess=0;

            $type_sales=0;
            $crop_sales=0;
            $grand_sales=0;

            $type_sales_return=0;
            $crop_sales_return=0;
            $grand_sales_return=0;

            $type_sales_bonus=0;
            $crop_sales_bonus=0;
            $grand_sales_bonus=0;

            $type_sales_return_bonus=0;
            $crop_sales_return_bonus=0;
            $grand_sales_return_bonus=0;

            $type_short=0;
            $crop_short=0;
            $grand_short=0;

            $type_rnd=0;
            $crop_rnd=0;
            $grand_rnd=0;

            $type_sample=0;
            $crop_sample=0;
            $grand_sample=0;

            $type_demonstration=0;
            $crop_demonstration=0;
            $grand_demonstration=0;

            $type_current=0;
            $crop_current=0;
            $grand_current=0;

            $type_total_price=0;
            $crop_total_price=0;
            $grand_total_price=0;

            foreach($starting_items as $vid=>$variety)
            {
                foreach($variety as $pack_id=>$pack)
                {
                    $count++;
                    $initial=array();
                    $initial['stock_in']=0;
                    $initial['excess']=0;
                    $initial['sales']=0;
                    $initial['sales_return']=0;
                    $initial['sales_bonus']=0;
                    $initial['sales_return_bonus']=0;
                    $initial['short']=0;
                    $initial['rnd']=0;
                    $initial['sample']=0;
                    $initial['demonstration']=0;
                    if(isset($initial_items[$vid][$pack_id]))
                    {
                        $initial=$initial_items[$vid][$pack_id];
                    }
                    $info=array();
                    if($count>1)
                    {
                        if($prev_crop_name!=$pack['crop_name'])
                        {

                            $items[]=$this->get_type_total_row($report_type,$type_starting_stock,$type_stock_in,$type_excess,$type_sales,$type_sales_return,$type_sales_bonus,$type_sales_return_bonus,$type_short,$type_rnd,$type_sample,$type_demonstration,$type_current,$type_total_price);
                            $items[]=$this->get_crop_total_row($report_type,$crop_starting_stock,$crop_stock_in,$crop_excess,$crop_sales,$crop_sales_return,$crop_sales_bonus,$crop_sales_return_bonus,$crop_short,$crop_rnd,$crop_sample,$crop_demonstration,$crop_current,$crop_total_price);

                            $type_starting_stock=0;
                            $type_stock_in=0;
                            $type_excess=0;
                            $type_sales=0;
                            $type_sales_return=0;
                            $type_sales_bonus=0;
                            $type_sales_return_bonus=0;
                            $type_short=0;
                            $type_rnd=0;
                            $type_sample=0;
                            $type_demonstration=0;
                            $type_current=0;
                            $type_total_price=0;

                            $crop_starting_stock=0;
                            $crop_stock_in=0;
                            $crop_excess=0;
                            $crop_sales=0;
                            $crop_sales_return=0;
                            $crop_sales_bonus=0;
                            $crop_sales_return_bonus=0;
                            $crop_short=0;
                            $crop_rnd=0;
                            $crop_sample=0;
                            $crop_demonstration=0;
                            $crop_current=0;
                            $crop_total_price=0;
                            $info['crop_name']=$pack['crop_name'];
                            $prev_crop_name=$pack['crop_name'];

                            $info['crop_type_name']=$pack['crop_type_name'];
                            $prev_crop_type_name=$pack['crop_type_name'];
                        }
                        elseif($prev_crop_type_name!=$pack['crop_type_name'])
                        {
                            $items[]=$this->get_type_total_row($report_type,$type_starting_stock,$type_stock_in,$type_excess,$type_sales,$type_sales_return,$type_sales_bonus,$type_sales_return_bonus,$type_short,$type_rnd,$type_sample,$type_demonstration,$type_current,$type_total_price);
                            $type_starting_stock=0;
                            $type_stock_in=0;
                            $type_excess=0;
                            $type_sales=0;
                            $type_sales_return=0;
                            $type_short=0;
                            $type_rnd=0;
                            $type_sample=0;
                            $type_demonstration=0;
                            $type_current=0;
                            $type_total_price=0;
                            $info['crop_name']='';
                            $info['crop_type_name']=$pack['crop_type_name'];
                            $prev_crop_type_name=$pack['crop_type_name'];
                        }
                        else
                        {
                            $info['crop_name']='';
                            $info['crop_type_name']='';
                        }
                    }
                    else
                    {
                        $info['crop_name']=$pack['crop_name'];
                        $prev_crop_name=$pack['crop_name'];
                        $info['crop_type_name']=$pack['crop_type_name'];
                        $prev_crop_type_name=$pack['crop_type_name'];
                    }



                    $info['variety_name']=$pack['variety_name'];
                    $info['stock_id']=$pack['stock_id'];
                    $info['pack_size_name']=$pack['pack_size_name'];
                    $info['starting_stock']=$initial['stock_in']+$initial['excess']-$initial['sales']+$initial['sales_return']-$initial['sales_bonus']+$initial['sales_return_bonus']-$initial['short']-$initial['rnd']-$initial['sample']-$initial['demonstration'];


                    $info['current']=$pack['stock_in']+$pack['excess']-$pack['sales']+$pack['sales_return']-$pack['sales_bonus']+$pack['sales_return_bonus']-$pack['short']-$pack['rnd']-$pack['sample']-$pack['demonstration'];

                    $info['stock_in']=$pack['stock_in']-$initial['stock_in'];
                    $info['excess']=$pack['excess']-$initial['excess'];
                    $info['sales']=$pack['sales']-$initial['sales'];
                    $info['sales_return']=$pack['sales_return']-$initial['sales_return'];
                    $info['sales_bonus']=$pack['sales_bonus']-$initial['sales_bonus'];
                    $info['sales_return_bonus']=$pack['sales_return_bonus']-$initial['sales_return_bonus'];
                    $info['short']=$pack['short']-$initial['short'];
                    $info['rnd']=$pack['rnd']-$initial['rnd'];
                    $info['sample']=$pack['sample']-$initial['sample'];
                    $info['demonstration']=$pack['demonstration']-$initial['demonstration'];

                    $info['current_price']='Not Set';
                    $info['current_total_price']='N/A';
                    if(isset($prices[$vid][$pack_id]))
                    {
                        $unit_price=$prices[$vid][$pack_id]['price'];
                        $total_price=$info['current']*$unit_price;
                        $type_total_price+=$total_price;
                        $crop_total_price+=$total_price;
                        $grand_total_price+=$total_price;
                        $info['current_price']=number_format($unit_price,2);
                        if($report_type=='weight')
                        {
                            $info['current_price']=number_format($unit_price*1000/$info['pack_size_name'],2);
                        }
                        $info['current_total_price']=number_format($total_price,2);
                    }

                    if($report_type=='weight')
                    {
                        $type_starting_stock+=$info['starting_stock']*$info['pack_size_name'];
                        $crop_starting_stock+=$info['starting_stock']*$info['pack_size_name'];
                        $grand_starting_stock+=$info['starting_stock']*$info['pack_size_name'];
                        $type_stock_in+=$info['stock_in']*$info['pack_size_name'];
                        $crop_stock_in+=$info['stock_in']*$info['pack_size_name'];
                        $grand_stock_in+=$info['stock_in']*$info['pack_size_name'];
                        $type_excess+=$info['excess']*$info['pack_size_name'];
                        $crop_excess+=$info['excess']*$info['pack_size_name'];
                        $grand_excess+=$info['excess']*$info['pack_size_name'];
                        $type_sales+=$info['sales']*$info['pack_size_name'];
                        $crop_sales+=$info['sales']*$info['pack_size_name'];
                        $grand_sales+=$info['sales']*$info['pack_size_name'];
                        $type_sales_return+=$info['sales_return']*$info['pack_size_name'];
                        $crop_sales_return+=$info['sales_return']*$info['pack_size_name'];
                        $grand_sales_return+=$info['sales_return']*$info['pack_size_name'];
                        $type_sales_bonus+=$info['sales_bonus']*$info['pack_size_name'];
                        $crop_sales_bonus+=$info['sales_bonus']*$info['pack_size_name'];
                        $grand_sales_bonus+=$info['sales_bonus']*$info['pack_size_name'];
                        $type_sales_return_bonus+=$info['sales_return_bonus']*$info['pack_size_name'];
                        $crop_sales_return_bonus+=$info['sales_return_bonus']*$info['pack_size_name'];
                        $grand_sales_return_bonus+=$info['sales_return_bonus']*$info['pack_size_name'];
                        $type_short+=$info['short']*$info['pack_size_name'];
                        $crop_short+=$info['short']*$info['pack_size_name'];
                        $grand_short+=$info['short']*$info['pack_size_name'];
                        $type_rnd+=$info['rnd']*$info['pack_size_name'];
                        $crop_rnd+=$info['rnd']*$info['pack_size_name'];
                        $grand_rnd+=$info['rnd']*$info['pack_size_name'];
                        $type_sample+=$info['sample']*$info['pack_size_name'];
                        $crop_sample+=$info['sample']*$info['pack_size_name'];
                        $grand_sample+=$info['sample']*$info['pack_size_name'];

                        $type_demonstration+=$info['demonstration']*$info['pack_size_name'];
                        $crop_demonstration+=$info['demonstration']*$info['pack_size_name'];
                        $grand_demonstration+=$info['demonstration']*$info['pack_size_name'];

                        $type_current+=$info['current']*$info['pack_size_name'];
                        $crop_current+=$info['current']*$info['pack_size_name'];
                        $grand_current+=$info['current']*$info['pack_size_name'];

                        $info['starting_stock']=number_format($info['starting_stock']*$info['pack_size_name']/1000,3,'.','');
                        $info['stock_in']=number_format($info['stock_in']*$info['pack_size_name']/1000,3,'.','');
                        $info['excess']=number_format($info['excess']*$info['pack_size_name']/1000,3,'.','');
                        $info['sales']=number_format($info['sales']*$info['pack_size_name']/1000,3,'.','');
                        $info['sales_return']=number_format($info['sales_return']*$info['pack_size_name']/1000,3,'.','');
                        $info['sales_bonus']=number_format($info['sales_bonus']*$info['pack_size_name']/1000,3,'.','');
                        $info['sales_return_bonus']=number_format($info['sales_return_bonus']*$info['pack_size_name']/1000,3,'.','');
                        $info['short']=number_format($info['short']*$info['pack_size_name']/1000,3,'.','');
                        $info['rnd']=number_format($info['rnd']*$info['pack_size_name']/1000,3,'.','');
                        $info['sample']=number_format($info['sample']*$info['pack_size_name']/1000,3,'.','');
                        $info['demonstration']=number_format($info['demonstration']*$info['pack_size_name']/1000,3,'.','');
                        $info['current']=number_format($info['current']*$info['pack_size_name']/1000,3,'.','');
                    }
                    else
                    {
                        $type_starting_stock+=$info['starting_stock'];
                        $crop_starting_stock+=$info['starting_stock'];
                        $grand_starting_stock+=$info['starting_stock'];

                        $type_stock_in+=$info['stock_in'];
                        $crop_stock_in+=$info['stock_in'];
                        $grand_stock_in+=$info['stock_in'];
                        $type_excess+=$info['excess'];
                        $crop_excess+=$info['excess'];
                        $grand_excess+=$info['excess'];
                        $type_sales+=$info['sales'];
                        $crop_sales+=$info['sales'];
                        $grand_sales+=$info['sales'];
                        $type_sales_return+=$info['sales_return'];
                        $crop_sales_return+=$info['sales_return'];
                        $grand_sales_return+=$info['sales_return'];
                        $type_sales_bonus+=$info['sales_bonus'];
                        $crop_sales_bonus+=$info['sales_bonus'];
                        $grand_sales_bonus+=$info['sales_bonus'];
                        $type_sales_return_bonus+=$info['sales_return_bonus'];
                        $crop_sales_return_bonus+=$info['sales_return_bonus'];
                        $grand_sales_return_bonus+=$info['sales_return_bonus'];
                        $type_short+=$info['short'];
                        $crop_short+=$info['short'];
                        $grand_short+=$info['short'];
                        $type_rnd+=$info['rnd'];
                        $crop_rnd+=$info['rnd'];
                        $grand_rnd+=$info['rnd'];
                        $type_sample+=$info['sample'];
                        $crop_sample+=$info['sample'];
                        $grand_sample+=$info['sample'];
                        $type_demonstration+=$info['demonstration'];
                        $crop_demonstration+=$info['demonstration'];
                        $grand_demonstration+=$info['demonstration'];
                        $type_current+=$info['current'];
                        $crop_current+=$info['current'];
                        $grand_current+=$info['current'];

                    }

                    $items[]=$info;
                }
            }
            $items[]=$this->get_type_total_row($report_type,$type_starting_stock,$type_stock_in,$type_excess,$type_sales,$type_sales_return,$type_sales_bonus,$type_sales_return_bonus,$type_short,$type_rnd,$type_sample,$type_demonstration,$type_current,$type_total_price);
            $items[]=$this->get_crop_total_row($report_type,$crop_starting_stock,$crop_stock_in,$crop_excess,$crop_sales,$crop_sales_return,$crop_sales_bonus,$crop_sales_return_bonus,$crop_short,$crop_rnd,$crop_sample,$crop_demonstration,$crop_current,$crop_total_price);
            $items[]=$this->get_grand_total_row($report_type,$grand_starting_stock,$grand_stock_in,$grand_excess,$grand_sales,$grand_sales_return,$grand_sales_bonus,$grand_sales_return_bonus,$grand_short,$grand_rnd,$grand_sample,$grand_demonstration,$grand_current,$grand_total_price);
        }

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
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_lc']=$result['in_lc'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_excess']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_transfer_warehouse']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_convert_bulk_pack']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_sales_return']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_sales']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_sample']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_rnd']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_demonstration']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_short_inventory']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_transfer_warehouse']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_convert_pack_bulk']=0;
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
        $this->db->group_by(array('variety_id','pack_size_id','stock_in.purpose'));

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
                if($result['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock']=$result['in_stock_excess'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_excess'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['in_excess']=$result['in_stock_excess'];
                }
            }
        }


        //Stock In (By Transfer warehouse to warehouse)

        $this->db->from($this->config->item('table_sms_transfer_warehouse_variety').' transfer_warehouse');
        $this->db->select('transfer_warehouse.*');
        $this->db->select('variety.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = transfer_warehouse.variety_id','LEFT');
        $this->db->select('v_pack_size.name pack_size_name');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = transfer_warehouse.pack_size_id','LEFT');
        $this->db->select('source_ware_house.name source_ware_house_name');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' source_ware_house','source_ware_house.id = transfer_warehouse.source_warehouse_id','LEFT');
        $this->db->select('destination_ware_house.name destination_ware_house_name');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' destination_ware_house','destination_ware_house.id = transfer_warehouse.destination_warehouse_id','LEFT');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','LEFT');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','LEFT');
        $this->db->select('SUM(quantity) in_transfer_warehouse');
        $this->db->group_by(array('variety_id','pack_size_id'));
        $this->db->where('transfer_warehouse.date_transfer <=',$time);
        if($warehouse_id>0)
        {
            $this->db->where('transfer_warehouse.destination_warehouse_id',$warehouse_id);
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
            $this->db->where('transfer_warehouse.variety_id',$variety_id);
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
                $stocks[$result['variety_id']][$result['pack_size_id']]['in_transfer_warehouse']=$result['in_transfer_warehouse'];
            }
        }

        /* Stock In (By convert bulk to packet) have to do when convert task will complete. */


        /* Stock In (By sales return) have to do when transfer outlet to warehouse task will complete. */



        //stock out (By short, rnd, demonstration and sample purposes)

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
        $this->db->group_by(array('variety_id','pack_size_id','stock_out.purpose'));

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
                    $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_short_inventory']=$result['stock_out'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_rnd'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_rnd']=$result['stock_out'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_sample'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_sample']=$result['stock_out'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_demonstration'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_demonstration']=$result['stock_out'];
                }
            }

        }


        /* stock out (By Sales) have to do when POS task will complete */


        /* stock out (By Transfer) have to do when Transfer Warehouse to Outlet will complete */


        /* stock out (By Convert) have to do when Convert Pack to Bulk will complete */

        return $stocks;

    }
}