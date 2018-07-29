<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock_hq_outlet_courier extends Root_Controller
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
            $data['date_end']=System_helper::display_date(time());

            $data['title']="Variety ALL Stock Report Search";
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
        $data['amount_price_unit']= 1;
        $data['stock_hq_pkt']= 1;
        $data['stock_hq_kg']= 1;
        $data['amount_hq']= 1;
        $data['stock_outlet_pkt']= 1;
        $data['stock_outlet_kg']= 1;
        $data['amount_outlet']= 1;
        $data['stock_to_pkt']= 1;
        $data['stock_to_kg']= 1;
        $data['amount_to']= 1;
        $data['stock_tr_pkt']= 1;
        $data['stock_tr_kg']= 1;
        $data['amount_tr']= 1;
        $data['stock_ts_pkt']= 1;
        $data['stock_ts_kg']= 1;
        $data['amount_ts']= 1;
        $data['stock_total_pkt']= 1;
        $data['stock_total_kg']= 1;
        $data['amount_total']= 1;
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $data['options']=$reports;
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Variety ALL StockReport";
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
        $pack_size_id=$this->input->post('pack_size_id');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name,v.price_kg');
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

        $price_units=array();
        $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
        $this->db->select('price.variety_id,price.pack_size_id,price.price_net');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $price_units[$result['variety_id']][$result['pack_size_id']]=$result['price_net'];
        }

        $stocks=array();
        //purpose == in stock,in excess,in delivery_short
        $this->db->from($this->config->item('table_sms_stock_in_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in <='.$date_end.' then details.quantity ELSE 0 END) stock_in',false);

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
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_pkt']+=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']+=(($result['stock_in']*$pack_sizes[$result['pack_size_id']])/1000);
        }
        //lc calculation
        $this->db->from($this->config->item('table_sms_lc_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN lco.date_receive <='.$date_end.' then details.quantity_receive ELSE 0 END) in_opening',false);


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
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_pkt']+=$result['in_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']+=(($result['in_opening']*$pack_sizes[$result['pack_size_id']])/1000);
        }

        //convert bulk to pack in out
        /*$this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN details.date_convert<'.$date_start.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack_opening',false);
        $this->db->select('SUM(CASE WHEN details.date_convert<'.$date_start.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack_opening',false);

        $this->db->select('SUM(CASE WHEN details.date_convert>='.$date_start.' and details.date_convert<='.$date_end.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack',false);
        $this->db->select('SUM(CASE WHEN details.date_convert>='.$date_start.' and details.date_convert<='.$date_end.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack',false);
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('details.variety_id',$variety_ids);*/
        /*if($pack_size_id>-1)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }*/
        /*$this->db->group_by('details.variety_id');
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
        }*/

        $type_total=$this->initialize_row('','','Total Type','');
        $crop_total=$this->initialize_row('','Total Crop','','');
        $grand_total=$this->initialize_row('Grand Total','','','');

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;
        $items=array();
        foreach($varieties as $variety)
        {
            //$info=$this->initialize_row($variety['crop_name'],$variety['crop_type_name'],$variety['variety_name'],$variety['pack_size']);
            //$info=$this->initialize_row($variety['crop_name'],$variety['crop_type_name'],$variety['variety_name'],'10');
            if(isset($stocks[$variety['variety_id']]))
            {
                foreach($stocks[$variety['variety_id']] as $pack_size_id=>$info)
                {
                    if(!$first_row)
                    {
                        if($prev_crop_name!=$variety['crop_name'])
                        {
                            $items[]=$type_total;
                            $items[]=$crop_total;
                            $type_total=$this->reset_row($type_total);
                            $crop_total=$this->reset_row($crop_total);

                            $info['crop_name']=$variety['crop_name'];
                            $info['crop_type_name']=$variety['crop_type_name'];
                            $prev_crop_name=$variety['crop_name'];
                            $prev_type_name=$variety['crop_type_name'];

                        }
                        elseif($prev_type_name!=$variety['crop_type_name'])
                        {
                            $items[]=$type_total;
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
                    $amount_price_unit=0;
                    if($pack_size_id==0)
                    {
                        $info['pack_size']='Bulk';
                        $amount_price_unit=$variety['price_kg'];
                        //for bulk removing packet is 0
                        foreach($info  as $key=>$r)
                        {
                            if(substr($key,-3)=='pkt')
                            {
                                $info[$key]=0;
                            }
                        }
                    }
                    else
                    {
                        if(isset($price_units[$variety['variety_id']][$pack_size_id]))
                        {
                            $amount_price_unit=$price_units[$variety['variety_id']][$pack_size_id];
                        }
                    }
                    $info['amount_price_unit']=$amount_price_unit;
                    foreach($info  as $key=>$r)
                    {
                        if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')||($key=='amount_price_unit')))
                        {
                            $type_total[$key]+=$info[$key];
                            $crop_total[$key]+=$info[$key];
                            $grand_total[$key]+=$info[$key];
                        }
                    }
                    $items[]=$info;
                }
            }
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
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
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers());
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
