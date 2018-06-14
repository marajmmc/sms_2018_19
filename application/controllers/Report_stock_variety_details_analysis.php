<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock_variety_details_analysis extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_stock_variety_details');
    }
    public function index($action="search")
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
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']=System_helper::display_date(time());

            $data['title']="Variety Details Stock analysis Report Search";
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
    private function get_preference_headers()
    {
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['opening_stock_pkt']= 1;
        $data['opening_stock_kg']= 1;
        $data['in_stock_in_pkt']= 1;
        $data['in_stock_in_kg']= 1;
        $data['in_stock_excess_pkt']= 1;
        $data['in_stock_excess_kg']= 1;
        $data['in_stock_delivery_short_pkt']= 1;
        $data['in_stock_delivery_short_kg']= 1;
        $data['in_ow_pkt']= 1;
        $data['in_ow_kg']= 1;
        $data['in_convert_bulk_pack_pkt']= 1;
        $data['in_convert_bulk_pack_kg']= 1;
        $data['out_convert_bulk_pack_kg']= 1;
        $data['in_lc_pkt']= 1;
        $data['in_lc_kg']= 1;
        $data['out_stock_sample_pkt']= 1;
        $data['out_stock_sample_kg']= 1;
        $data['out_stock_rnd_pkt']= 1;
        $data['out_stock_rnd_kg']= 1;
        $data['out_stock_demonstration_pkt']= 1;
        $data['out_stock_demonstration_kg']= 1;
        $data['out_stock_short_inventory_pkt']= 1;
        $data['out_stock_short_inventory_kg']= 1;
        $data['out_stock_delivery_excess_pkt']= 1;
        $data['out_stock_delivery_excess_kg']= 1;

        $data['out_wo_pkt']= 1;
        $data['out_wo_kg']= 1;
        $data['end_stock_pkt']= 1;
        $data['end_stock_kg']= 1;

        return $data;
    }
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data=$this->get_preference_headers();
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
            $data['options']=$reports;
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Variety Details Stock Analysis Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');


        $pack_size_id=$this->input->post('pack_size_id');
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

        $pack_sizes=array();
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array());
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]=$result['text'];
        }
        $pack_sizes['0']=1000;
        $stocks=array();
        //purpose == in stock,in excess,in delivery_short
        $this->db->from($this->config->item('table_sms_stock_in_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in<'.$date_start.' then details.quantity ELSE 0 END) in_opening',false);

        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_stock_in').'" then details.quantity ELSE 0 END) in_stock_in',false);
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_excess').'" then details.quantity ELSE 0 END) in_stock_excess',false);
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_in_delivery_short').'" then details.quantity ELSE 0 END) in_stock_delivery_short',false);


        $this->db->join($this->config->item('table_sms_stock_in_variety').' stock_in','stock_in.id=details.stock_in_id','INNER');
        $this->db->where('stock_in.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where_in('details.variety_id',$variety_ids);
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        elseif($pack_size_id ==-2)
        {
            $this->db->where('details.pack_size_id >',0);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']+=$result['in_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']+=(($result['in_opening']*$pack_sizes[$result['pack_size_id']])/1000);
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_in_pkt']+=$result['in_stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_in_kg']+=(($result['in_stock_in']*$pack_sizes[$result['pack_size_id']])/1000);
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_excess_pkt']+=$result['in_stock_excess'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_excess_kg']+=(($result['in_stock_excess']*$pack_sizes[$result['pack_size_id']])/1000);
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_delivery_short_pkt']+=$result['in_stock_delivery_short'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_delivery_short_kg']+=(($result['in_stock_delivery_short']*$pack_sizes[$result['pack_size_id']])/1000);
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']=$result['in_opening']+$result['in_stock_in']+$result['in_stock_excess']+$result['in_stock_delivery_short'];

            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']=$stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=$stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_in_kg'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=$stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_excess_kg'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=$stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_delivery_short_kg'];

        }
        //convert bulk to pack in out
        $this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN details.date_convert<'.$date_start.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack_opening',false);
        $this->db->select('SUM(CASE WHEN details.date_convert<'.$date_start.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack_opening',false);

        $this->db->select('SUM(CASE WHEN details.date_convert>='.$date_start.' and details.date_convert<='.$date_end.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack',false);
        $this->db->select('SUM(CASE WHEN details.date_convert>='.$date_start.' and details.date_convert<='.$date_end.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack',false);
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('details.variety_id',$variety_ids);
        /*if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }*/
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }

            if(($pack_size_id>0)||($pack_size_id==-2))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']+=$result['in_convert_bulk_pack_opening'];
                $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']+=(($result['in_convert_bulk_pack_opening']*$pack_sizes[$result['pack_size_id']])/1000);

                $stocks[$result['variety_id']][$result['pack_size_id']]['in_convert_bulk_pack_pkt']+=$result['in_convert_bulk_pack'];
                $stocks[$result['variety_id']][$result['pack_size_id']]['in_convert_bulk_pack_kg']+=(($result['in_convert_bulk_pack']*$pack_sizes[$result['pack_size_id']])/1000);

                $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']+=($result['in_convert_bulk_pack_opening']+$result['in_convert_bulk_pack']);
                $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=((($result['in_convert_bulk_pack_opening']+$result['in_convert_bulk_pack'])*$pack_sizes[$result['pack_size_id']])/1000);

            }
            else if($pack_size_id==0)
            {
                $stocks[$result['variety_id']][0]['opening_stock_kg']-=$result['out_convert_bulk_pack_opening'];
                $stocks[$result['variety_id']][0]['out_convert_bulk_pack_kg']+=$result['out_convert_bulk_pack'];
                $stocks[$result['variety_id']][0]['end_stock_kg']-=($result['out_convert_bulk_pack_opening']+$result['out_convert_bulk_pack']);
            }
            else
            {
                $stocks[$result['variety_id']][0]['opening_stock_kg']-=$result['out_convert_bulk_pack_opening'];
                $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']+=$result['in_convert_bulk_pack_opening'];
                $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']+=(($result['in_convert_bulk_pack_opening']*$pack_sizes[$result['pack_size_id']])/1000);

                $stocks[$result['variety_id']][0]['out_convert_bulk_pack_kg']+=$result['out_convert_bulk_pack'];
                $stocks[$result['variety_id']][$result['pack_size_id']]['in_convert_bulk_pack_pkt']+=$result['in_convert_bulk_pack'];
                $stocks[$result['variety_id']][$result['pack_size_id']]['in_convert_bulk_pack_kg']+=(($result['in_convert_bulk_pack']*$pack_sizes[$result['pack_size_id']])/1000);

                $stocks[$result['variety_id']][0]['end_stock_kg']-=($result['out_convert_bulk_pack_opening']+$result['out_convert_bulk_pack']);
                $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']+=($result['in_convert_bulk_pack_opening']+$result['in_convert_bulk_pack']);
                $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=((($result['in_convert_bulk_pack_opening']+$result['in_convert_bulk_pack'])*$pack_sizes[$result['pack_size_id']])/1000);

            }
        }
        //transfer in and out no need to calculate
        //lc calculation
        $this->db->from($this->config->item('table_sms_lc_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN lco.date_receive<'.$date_start.' then details.quantity_receive ELSE 0 END) in_opening',false);

        $this->db->select('SUM(CASE WHEN lco.date_receive>='.$date_start.' and lco.date_receive<='.$date_end.' then details.quantity_receive ELSE 0 END) in_lc',false);



        $this->db->join($this->config->item('table_sms_lc_open').' lco','lco.id=details.lc_id','INNER');
        $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
        $this->db->where('lco.status_receive',$this->config->item('system_status_complete'));
        $this->db->where_in('details.variety_id',$variety_ids);
        $this->db->where('details.quantity_open >',0);
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        elseif($pack_size_id ==-2)
        {
            $this->db->where('details.pack_size_id >',0);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']+=$result['in_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']+=(($result['in_opening']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['in_lc_pkt']+=$result['in_lc'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_lc_kg']+=(($result['in_lc']*$pack_sizes[$result['pack_size_id']])/1000);


            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']+=($result['in_opening']+$result['in_lc']);
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=((($result['in_opening']+$result['in_lc'])*$pack_sizes[$result['pack_size_id']])/1000);
        }

        //in transfer from outlet
        $this->db->from($this->config->item('table_sms_transfer_ow_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');

        $this->db->select('SUM(CASE WHEN ow.date_receive<'.$date_start.' then details.quantity_receive ELSE 0 END) in_opening',false);

        $this->db->select('SUM(CASE WHEN ow.date_receive>='.$date_start.' and ow.date_receive<='.$date_end.' then details.quantity_receive ELSE 0 END) in_ow',false);
        $this->db->join($this->config->item('table_sms_transfer_ow').' ow','ow.id=details.transfer_ow_id','INNER');
        $this->db->where('ow.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('ow.status_receive',$this->config->item('system_status_received'));
        $this->db->where_in('details.variety_id',$variety_ids);
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        elseif($pack_size_id ==-2)
        {
            $this->db->where('details.pack_size_id >',0);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']+=$result['in_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']+=(($result['in_opening']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['in_ow_pkt']+=$result['in_ow'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_ow_kg']+=(($result['in_ow']*$pack_sizes[$result['pack_size_id']])/1000);


            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']+=($result['in_opening']+$result['in_ow']);
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']+=((($result['in_opening']+$result['in_ow'])*$pack_sizes[$result['pack_size_id']])/1000);
        }

        //out transfer to outlet
        $this->db->from($this->config->item('table_sms_transfer_wo_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');

        $this->db->select('SUM(CASE WHEN wo.date_updated_delivery_forward<'.$date_start.' then details.quantity_approve ELSE 0 END) out_opening',false);

        $this->db->select('SUM(CASE WHEN wo.date_updated_delivery_forward>='.$date_start.' and wo.date_updated_delivery_forward<='.$date_end.' then details.quantity_approve ELSE 0 END) out_wo',false);



        $this->db->join($this->config->item('table_sms_transfer_wo').' wo','wo.id=details.transfer_wo_id','INNER');
        $this->db->where('wo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('wo.status_delivery',$this->config->item('system_status_delivered'));
        $this->db->where_in('details.variety_id',$variety_ids);
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        elseif($pack_size_id ==-2)
        {
            $this->db->where('details.pack_size_id >',0);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']-=$result['out_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']-=(($result['out_opening']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['out_wo_pkt']+=$result['out_wo'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_wo_kg']+=(($result['out_wo']*$pack_sizes[$result['pack_size_id']])/1000);


            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']-=($result['out_opening']+$result['out_wo']);
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']-=((($result['out_opening']+$result['out_wo'])*$pack_sizes[$result['pack_size_id']])/1000);
        }
        //out stock sample,rnd,demonstration, short

        $this->db->from($this->config->item('table_sms_stock_out_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out<'.$date_start.' then details.quantity ELSE 0 END) out_opening',false);

        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_sample').'" then details.quantity ELSE 0 END) out_stock_sample',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_rnd').'" then details.quantity ELSE 0 END) out_stock_rnd',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_demonstration').'" then details.quantity ELSE 0 END) out_stock_demonstration',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_short_inventory').'" then details.quantity ELSE 0 END) out_stock_short_inventory',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_variety_delivery_excess').'" then details.quantity ELSE 0 END) out_stock_delivery_excess',false);


        $this->db->join($this->config->item('table_sms_stock_out_variety').' stock_out','stock_out.id=details.stock_out_id','INNER');
        $this->db->where('stock_out.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where_in('details.variety_id',$variety_ids);
        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        elseif($pack_size_id ==-2)
        {
            $this->db->where('details.pack_size_id >',0);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt']-=$result['out_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_kg']-=(($result['out_opening']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_sample_pkt']+=$result['out_stock_sample'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_sample_kg']+=(($result['out_stock_sample']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_rnd_pkt']+=$result['out_stock_rnd'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_rnd_kg']+=(($result['out_stock_rnd']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_demonstration_pkt']+=$result['out_stock_demonstration'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_demonstration_kg']+=(($result['out_stock_demonstration']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_short_inventory_pkt']+=$result['out_stock_short_inventory'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_short_inventory_kg']+=(($result['out_stock_short_inventory']*$pack_sizes[$result['pack_size_id']])/1000);


            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_delivery_excess_pkt']+=$result['out_stock_delivery_excess'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_delivery_excess_kg']+=(($result['out_stock_delivery_excess']*$pack_sizes[$result['pack_size_id']])/1000);


            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt']-=($result['out_opening']+$result['out_stock_sample']+$result['out_stock_rnd']+$result['out_stock_demonstration']+$result['out_stock_short_inventory']+$result['out_stock_delivery_excess']);
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_kg']-=((($result['out_opening']+$result['out_stock_sample']+$result['out_stock_rnd']+$result['out_stock_demonstration']+$result['out_stock_short_inventory']+$result['out_stock_delivery_excess'])*$pack_sizes[$result['pack_size_id']])/1000);
        }

        $type_total=$this->initialize_row('','','Total Type','');
        $crop_total=$this->initialize_row('','Total Crop','','');
        $grand_total=$this->initialize_row('Grand Total','','','');

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;
        $headers=$this->get_preference_headers();
        unset($headers['crop_name']);
        unset($headers['crop_type_name']);
        unset($headers['variety_name']);
        unset($headers['pack_size']);
        $items=array();
        foreach($varieties as $variety)
        {
            if(isset($stocks[$variety['variety_id']]))
            {
                foreach($stocks[$variety['variety_id']] as $pack_size_id=>$info)
                {
                    if(!$first_row)
                    {
                        if($prev_crop_name!=$variety['crop_name'])
                        {
                            $items[]=$this->get_row($type_total);
                            $items[]=$this->get_row($crop_total);
                            $type_total=$this->reset_row($type_total);
                            $crop_total=$this->reset_row($crop_total);

                            $info['crop_name']=$variety['crop_name'];
                            $info['crop_type_name']=$variety['crop_type_name'];

                            $prev_crop_name=$variety['crop_name'];
                            $prev_type_name=$variety['crop_type_name'];


                        }
                        elseif($prev_type_name!=$variety['crop_type_name'])
                        {
                            $items[]=$this->get_row($type_total);
                            $type_total=$this->reset_row($type_total);
                            $info['crop_type_name']=$variety['crop_type_name'];
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
                        $info['crop_name']=$variety['crop_name'];
                        $info['crop_type_name']=$variety['crop_type_name'];

                        $prev_crop_name=$variety['crop_name'];
                        $prev_type_name=$variety['crop_type_name'];
                        $first_row=false;
                    }
                    $info['variety_name']=$variety['variety_name'];
                    if($pack_size_id==0)
                    {
                        $info['pack_size']='Bulk';
                        //packet is 0
                        foreach($headers  as $key=>$r)
                        {
                            if(substr($key,-3)=='pkt')
                            {
                                $info[$key]=0;
                            }
                        }
                    }
                    foreach($headers  as $key=>$r)
                    {
                        if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                        {
                            $type_total[$key]+=$info[$key];
                            $crop_total[$key]+=$info[$key];
                            $grand_total[$key]+=$info[$key];
                        }
                    }
                    $items[]=$this->get_row($info);
                }
            }

        }
        $items[]=$this->get_row($type_total);
        $items[]=$this->get_row($crop_total);
        $items[]=$this->get_row($grand_total);
        $this->json_return($items);
        die();

    }
    private function initialize_row($crop_name,$crop_type_name,$variety_name,$pack_size)
    {
        $row=$this->get_preference_headers();
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        return $row;
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
            else
            {
                $row[$key]=$info[$key];
            }

        }
        return $row;


    }
    private function reset_row($info)
    {
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
            {
                $info[$key]=0;
            }
        }
        return $info;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='search';
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

}
