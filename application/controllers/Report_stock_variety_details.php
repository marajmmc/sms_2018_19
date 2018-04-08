<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_stock_variety_details extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Report_stock_variety_details');
        $this->controller_url='report_stock_variety_details';
        $this->lang->load('report_stock_variety_details');
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
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }
            if(!($reports['variety_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Variety Selection Mandatory';
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Variety Details Stock Report";
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
    private function system_get_items()
    {
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $pack_sizes=array();
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]=$result['text'];
        }

        //new pack size can be stock in,lc and convert

        //stock in calculation
        //purpose == in stock,in excess,in delivery_short
        $this->db->from($this->config->item('table_sms_stock_in_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.warehouse_id');
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in<'.$date_start.' then details.quantity ELSE 0 END) in_opening',false);

        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_stock_in').'" then details.quantity ELSE 0 END) in_stock_in',false);
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_excess').'" then details.quantity ELSE 0 END) in_stock_excess',false);
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_in_delivery_short').'" then details.quantity ELSE 0 END) in_stock_delivery_short',false);


        $this->db->join($this->config->item('table_sms_stock_in_variety').' stock_in','stock_in.id=details.stock_in_id','INNER');
        $this->db->where('stock_in.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->group_by('details.warehouse_id');
        $results=$this->db->get()->result_array();

        $packs=array();
        foreach($results as $result)
        {
            if(!(isset($packs[$result['pack_size_id']])))
            {
                $packs[$result['pack_size_id']]=$this->initialize_row($warehouses);
            }
            $packs[$result['pack_size_id']]['stock_opening'][$result['warehouse_id']]=$result['in_opening'];
            $packs[$result['pack_size_id']]['in_stock_in'][$result['warehouse_id']]=$result['in_stock_in'];
            $packs[$result['pack_size_id']]['in_stock_excess'][$result['warehouse_id']]=$result['in_stock_excess'];
            $packs[$result['pack_size_id']]['in_stock_delivery_short'][$result['warehouse_id']]=$result['in_stock_delivery_short'];
            $packs[$result['pack_size_id']]['stock_current'][$result['warehouse_id']]=$result['in_opening']+$result['in_stock_in']+$result['in_stock_excess']+$result['in_stock_delivery_short'];
        }

        //convert bulk to pack in out
        $this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.warehouse_id_source,details.warehouse_id_destination');
        $this->db->select('SUM(CASE WHEN details.date_convert<'.$date_start.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack_opening',false);
        $this->db->select('SUM(CASE WHEN details.date_convert<'.$date_start.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack_opening',false);

        $this->db->select('SUM(CASE WHEN details.date_convert>='.$date_start.' and details.date_convert<='.$date_end.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack',false);
        $this->db->select('SUM(CASE WHEN details.date_convert>='.$date_start.' and details.date_convert<='.$date_end.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack',false);
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->group_by('details.warehouse_id_source');
        $this->db->group_by('details.warehouse_id_destination');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($packs[$result['pack_size_id']])))
            {
                $packs[$result['pack_size_id']]=$this->initialize_row($warehouses);
            }
            $packs[0]['stock_opening'][$result['warehouse_id_source']]-=$result['out_convert_bulk_pack_opening'];
            $packs[$result['pack_size_id']]['stock_opening'][$result['warehouse_id_destination']]+=$result['in_convert_bulk_pack_opening'];

            $packs[0]['out_convert_bulk_pack'][$result['warehouse_id_source']]+=$result['out_convert_bulk_pack'];
            $packs[$result['pack_size_id']]['in_convert_bulk_pack'][$result['warehouse_id_destination']]+=$result['in_convert_bulk_pack'];

            $packs[0]['stock_current'][$result['warehouse_id_source']]-=($result['out_convert_bulk_pack_opening']+$result['out_convert_bulk_pack']);
            $packs[$result['pack_size_id']]['stock_current'][$result['warehouse_id_destination']]+=($result['in_convert_bulk_pack_opening']+$result['in_convert_bulk_pack']);
        }

        //transfer in and out

        $this->db->from($this->config->item('table_sms_transfer_ww').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.warehouse_id_source,details.warehouse_id_destination');
        $this->db->select('SUM(CASE WHEN details.date_transfer<'.$date_start.' then details.quantity_transfer ELSE 0 END) in_out_opening',false);

        $this->db->select('SUM(CASE WHEN details.date_transfer>='.$date_start.' and details.date_transfer<='.$date_end.' then details.quantity_transfer ELSE 0 END) in_out_quantity',false);
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->group_by('details.warehouse_id_source');
        $this->db->group_by('details.warehouse_id_destination');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $packs[$result['pack_size_id']]['stock_opening'][$result['warehouse_id_source']]-=$result['in_out_opening'];
            $packs[$result['pack_size_id']]['stock_opening'][$result['warehouse_id_destination']]+=$result['in_out_opening'];

            $packs[$result['pack_size_id']]['out_ww'][$result['warehouse_id_source']]+=$result['in_out_quantity'];
            $packs[$result['pack_size_id']]['in_ww'][$result['warehouse_id_destination']]+=$result['in_out_quantity'];

            $packs[$result['pack_size_id']]['stock_current'][$result['warehouse_id_source']]-=($result['in_out_opening']+$result['in_out_quantity']);
            $packs[$result['pack_size_id']]['stock_current'][$result['warehouse_id_destination']]+=($result['in_out_opening']+$result['in_out_quantity']);
        }

        //lc calculation
        $this->db->from($this->config->item('table_sms_lc_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.receive_warehouse_id');
        $this->db->select('SUM(CASE WHEN lco.date_receive<'.$date_start.' then details.quantity_receive ELSE 0 END) in_opening',false);

        $this->db->select('SUM(CASE WHEN lco.date_receive>='.$date_start.' and lco.date_receive<='.$date_end.' then details.quantity_receive ELSE 0 END) in_lc',false);



        $this->db->join($this->config->item('table_sms_lc_open').' lco','lco.id=details.lc_id','INNER');
        $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
        $this->db->where('lco.status_receive',$this->config->item('system_status_complete'));
        $this->db->where('details.variety_id',$variety_id);
        $this->db->where('details.quantity_open >',0);
        if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->group_by('details.receive_warehouse_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(!(isset($packs[$result['pack_size_id']])))
            {
                $packs[$result['pack_size_id']]=$this->initialize_row($warehouses);
            }
            $packs[$result['pack_size_id']]['stock_opening'][$result['receive_warehouse_id']]+=$result['in_opening'];
            $packs[$result['pack_size_id']]['in_lc'][$result['receive_warehouse_id']]+=$result['in_lc'];
            $packs[$result['pack_size_id']]['stock_current'][$result['receive_warehouse_id']]+=($result['in_opening']+$result['in_lc']);
        }

        //in transfer from outlet pending
        //out transfer to outlet
        $this->db->from($this->config->item('table_sms_transfer_wo_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.warehouse_id');

        $this->db->select('SUM(CASE WHEN wo.date_updated_delivery_forward<'.$date_start.' then details.quantity_approve ELSE 0 END) out_opening',false);

        $this->db->select('SUM(CASE WHEN wo.date_updated_delivery_forward>='.$date_start.' and wo.date_updated_delivery_forward<='.$date_end.' then details.quantity_approve ELSE 0 END) out_wo',false);



        $this->db->join($this->config->item('table_sms_transfer_wo').' wo','wo.id=details.transfer_wo_id','INNER');
        $this->db->where('wo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('wo.status_delivery',$this->config->item('system_status_delivered'));
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->group_by('details.warehouse_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $packs[$result['pack_size_id']]['stock_opening'][$result['warehouse_id']]-=$result['out_opening'];
            $packs[$result['pack_size_id']]['out_wo'][$result['warehouse_id']]+=$result['out_wo'];
            $packs[$result['pack_size_id']]['stock_current'][$result['warehouse_id']]-=($result['out_opening']+$result['out_wo']);
        }
        //out stock sample,rnd,demonstration, short

        $this->db->from($this->config->item('table_sms_stock_out_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.warehouse_id');
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out<'.$date_start.' then details.quantity ELSE 0 END) out_opening',false);

        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_sample').'" then details.quantity ELSE 0 END) out_stock_sample',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_rnd').'" then details.quantity ELSE 0 END) out_stock_rnd',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_demonstration').'" then details.quantity ELSE 0 END) out_stock_demonstration',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_short_inventory').'" then details.quantity ELSE 0 END) out_stock_short_inventory',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_delivery_excess').'" then details.quantity ELSE 0 END) out_stock_delivery_excess',false);


        $this->db->join($this->config->item('table_sms_stock_out_variety').' stock_out','stock_out.id=details.stock_out_id','INNER');
        $this->db->where('stock_out.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $this->db->group_by('details.warehouse_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $packs[$result['pack_size_id']]['stock_opening'][$result['warehouse_id']]-=$result['out_opening'];
            $packs[$result['pack_size_id']]['out_stock_sample'][$result['warehouse_id']]+=$result['out_stock_sample'];
            $packs[$result['pack_size_id']]['out_stock_rnd'][$result['warehouse_id']]+=$result['out_stock_rnd'];
            $packs[$result['pack_size_id']]['out_stock_demonstration'][$result['warehouse_id']]+=$result['out_stock_demonstration'];
            $packs[$result['pack_size_id']]['out_stock_short_inventory'][$result['warehouse_id']]+=$result['out_stock_short_inventory'];
            $packs[$result['pack_size_id']]['out_stock_delivery_excess'][$result['warehouse_id']]+=$result['out_stock_delivery_excess'];

            $packs[$result['pack_size_id']]['stock_current'][$result['warehouse_id']]-=($result['out_opening']+$result['out_stock_sample']+$result['out_stock_rnd']+$result['out_stock_demonstration']+$result['out_stock_short_inventory']+$result['out_stock_delivery_excess']);
        }


        $grand_total=array();
        $grand_total['pack_size']='Total End Stock';
        $grand_total['type']='';
        foreach($warehouses as $warehouse)
        {
            $grand_total['warehouse_'.$warehouse['value'].'_pkt']=0;
            $grand_total['warehouse_'.$warehouse['value'].'_kg']=0;
        }
        $grand_total['total_pkt']=0;
        $grand_total['total_kg']=0;
        $items=array();

        foreach($packs as $pack_size_id=>$pack)
        {
            $item=array();

            $count=0;
            foreach($pack as $type=>$warehouse_quantity)
            {
                if($count==0)
                {
                    if($pack_size_id==0)
                    {
                        $item['pack_size']='Bulk';
                    }
                    else
                    {
                        $item['pack_size']=$pack_sizes[$pack_size_id];
                    }

                }
                else
                {
                    $item['pack_size']='';
                }
                $count++;
                $item['type']=$this->lang->line('LABEL_'.strtoupper($type));
                $item['total_pkt']=0;
                $item['total_kg']=0;
                foreach($warehouse_quantity as $warehouse_id=>$quantity)
                {
                    if($pack_size_id==0)
                    {
                        $item['warehouse_'.$warehouse_id.'_pkt']='';
                        if($quantity>0)
                        {
                            $item['warehouse_'.$warehouse_id.'_kg']=number_format($quantity,3,'.','');
                        }
                        else
                        {
                            $item['warehouse_'.$warehouse_id.'_kg']='';
                        }

                        $item['total_kg']+=$quantity;
                        if($type=='stock_current')
                        {
                            $grand_total['warehouse_'.$warehouse_id.'_kg']+=$quantity;
                            $grand_total['total_kg']+=$quantity;
                        }
                    }
                    else
                    {

                        if($quantity>0)
                        {
                            $item['warehouse_'.$warehouse_id.'_pkt']=$quantity;
                            $item['warehouse_'.$warehouse_id.'_kg']=number_format($quantity*$pack_sizes[$pack_size_id]/1000,3,'.','');
                        }
                        else
                        {
                            $item['warehouse_'.$warehouse_id.'_pkt']='';
                            $item['warehouse_'.$warehouse_id.'_kg']='';
                        }
                        $item['total_pkt']+=$quantity;
                        $item['total_kg']+=$quantity*$pack_sizes[$pack_size_id]/1000;

                        if($type=='stock_current')
                        {
                            $grand_total['warehouse_'.$warehouse_id.'_pkt']+=$quantity;
                            $grand_total['total_pkt']+=$quantity;
                            $grand_total['warehouse_'.$warehouse_id.'_kg']+=$quantity*$pack_sizes[$pack_size_id]/1000;
                            $grand_total['total_kg']+=$quantity*$pack_sizes[$pack_size_id]/1000;

                        }
                    }
                    if($item['total_kg']>0)
                    {
                        $item['total_kg']=number_format($item['total_kg'],3,'.','');
                    }
                    else
                    {
                        $item['total_kg']='';
                    }
                    if(!($item['total_pkt']>0))
                    {
                        $item['total_pkt']='';
                    }

                }
                $items[]=$item;
            }

        }

        foreach($warehouses as $warehouse)
        {

            if($grand_total['warehouse_'.$warehouse['value'].'_kg']>0)
            {
                $grand_total['warehouse_'.$warehouse['value'].'_kg']=number_format($grand_total['warehouse_'.$warehouse['value'].'_kg'],3,'.','');
            }
            else
            {
                $grand_total['warehouse_'.$warehouse['value'].'_kg']='';
            }
            if(!($grand_total['warehouse_'.$warehouse['value'].'_pkt']>0))
            {
                $grand_total['warehouse_'.$warehouse['value'].'_pkt']='';
            }
        }
        if($grand_total['total_kg']>0)
        {
            $grand_total['total_kg']=number_format($grand_total['total_kg'],3,'.','');
        }
        else
        {
            $grand_total['total_kg']='';
        }
        if(!($grand_total['total_pkt']>0))
        {
            $grand_total['total_pkt']='';
        }
        $items[]=$grand_total;
        $this->json_return($items);


    }
    private function initialize_row($warehouses)
    {
        $data=array();
        foreach($warehouses as $warehouse)
        {
            $data['stock_opening'][$warehouse['value']]=0;
            $data['in_stock_in'][$warehouse['value']]=0;
            $data['in_stock_excess'][$warehouse['value']]=0;
            $data['in_stock_delivery_short'][$warehouse['value']]=0;
            $data['in_ww'][$warehouse['value']]=0;
            $data['out_ww'][$warehouse['value']]=0;
            $data['out_convert_bulk_pack'][$warehouse['value']]=0;
            $data['in_convert_bulk_pack'][$warehouse['value']]=0;
            $data['in_lc'][$warehouse['value']]=0;
            $data['out_wo'][$warehouse['value']]=0;
            $data['out_stock_sample'][$warehouse['value']]=0;
            $data['out_stock_rnd'][$warehouse['value']]=0;
            $data['out_stock_demonstration'][$warehouse['value']]=0;
            $data['out_stock_short_inventory'][$warehouse['value']]=0;
            $data['out_stock_delivery_excess'][$warehouse['value']]=0;


            $data['stock_current'][$warehouse['value']]=0;

        }
        return $data;

    }
    private function get_row($info)
    {

    }

    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='search';
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
    private function get_preference()
    {
        $user = User_helper::get_user();
        $warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));

        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data['pack_size']= 1;
        $data['type']= 1;
        foreach($warehouses as $warehouse)
        {
            $data['warehouse_'.$warehouse['value'].'_pkt']= 1;
            $data['warehouse_'.$warehouse['value'].'_kg']= 1;
        }
        //$data['system_preference_items']['current_stock']= 1;
        $data['total_pkt']= 1;
        $data['total_kg']= 1;
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