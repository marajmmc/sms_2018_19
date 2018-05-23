<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_stock_raw_details extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_stock_variety_details');
        $this->lang->load('report_stock_raw');
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
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['date_start']='';
            $data['date_end']=System_helper::display_date(time());
            $data['title']="Details Raw Stock Report Search";
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
    private function get_preference_headers()
    {
        $data['pack_size']= 1;
        $data['type']= 1;
        $data['quantity_total_pcs_kg']= 1;
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
            $data['system_preference_items']= $this->get_preference();
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if(!$reports['packing_item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Packing item Selection Mandatory';
                $this->json_return($ajax);
            }
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
        $packing_item=$this->input->post('packing_item');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $pack_sizes=array();
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]=$result['text'];
        }

        //Raw stock in calculation
        //purpose == in stock,in excess
        if($packing_item==$this->config->item('system_master_foil'))
        {
            $this->db->from($this->config->item('table_sms_stock_in_raw_master_details').' details');
        }
        elseif($packing_item==$this->config->item('system_sticker'))
        {
            $this->db->from($this->config->item('table_sms_stock_in_raw_sticker_details').' details');
        }
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in<'.$date_start.' then details.quantity ELSE 0 END) in_opening',false);
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_stock_in').'" then details.quantity ELSE 0 END) in_stock_in',false);
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_excess').'" then details.quantity ELSE 0 END) in_stock_excess',false);
        if($packing_item==$this->config->item('system_master_foil'))
        {
            $this->db->join($this->config->item('table_sms_stock_in_raw_master').' stock_in','stock_in.id=details.stock_in_id','INNER');
        }
        elseif($packing_item==$this->config->item('system_sticker'))
        {
            $this->db->join($this->config->item('table_sms_stock_in_raw_sticker').' stock_in','stock_in.id=details.stock_in_id','INNER');
        }
        $this->db->where('stock_in.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();

        $packs=array();
        foreach($results as $result)
        {
            if(!(isset($packs[$result['pack_size_id']])))
            {
                $packs[$result['pack_size_id']]=$this->initialize_row();
            }
            $packs[$result['pack_size_id']]['stock_opening']=$result['in_opening'];
            $packs[$result['pack_size_id']]['in_stock_in']=$result['in_stock_in'];
            $packs[$result['pack_size_id']]['in_stock_excess']=$result['in_stock_excess'];
            $packs[$result['pack_size_id']]['stock_current']=$result['in_opening']+$result['in_stock_in']+$result['in_stock_excess'];
        }

        //Purchase calculation
        if($packing_item==$this->config->item('system_master_foil'))
        {
            $this->db->from($this->config->item('table_sms_purchase_raw_master_details').' details');
        }
        elseif($packing_item==$this->config->item('system_sticker'))
        {
            $this->db->from($this->config->item('table_sms_purchase_raw_sticker_details').' details');
        }

        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN purchase.date_receive<'.$date_start.' then details.quantity_receive ELSE 0 END) in_opening',false);
        $this->db->select('SUM(CASE WHEN purchase.date_receive>='.$date_start.' and purchase.date_receive<='.$date_end.' then details.quantity_receive ELSE 0 END) in_purchase',false);
        if($packing_item==$this->config->item('system_master_foil'))
        {
            $this->db->join($this->config->item('table_sms_purchase_raw_master').' purchase','purchase.id=details.purchase_id','INNER');
        }
        elseif($packing_item==$this->config->item('system_sticker'))
        {
            $this->db->join($this->config->item('table_sms_purchase_raw_sticker').' purchase','purchase.id=details.purchase_id','INNER');
        }
        $this->db->where('purchase.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.variety_id',$variety_id);
        $this->db->where('details.quantity_supply>',0);
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($packs[$result['pack_size_id']])))
            {
                $packs[$result['pack_size_id']]=$this->initialize_row();
            }
            $packs[$result['pack_size_id']]['stock_opening']+=$result['in_opening'];
            $packs[$result['pack_size_id']]['in_purchase']+=$result['in_purchase'];
            $packs[$result['pack_size_id']]['stock_current']+=($result['in_opening']+$result['in_purchase']);
        }

        //out stock damage

        if($packing_item==$this->config->item('system_master_foil'))
        {
            $this->db->from($this->config->item('table_sms_stock_out_raw_master_details').' details');
        }
        elseif($packing_item==$this->config->item('system_sticker'))
        {
            $this->db->from($this->config->item('table_sms_stock_out_raw_sticker_details').' details');
        }
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out<'.$date_start.' then details.quantity ELSE 0 END) out_opening',false);
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_raw_stock_damage').'" then details.quantity ELSE 0 END) out_stock_damage',false);
        if($packing_item==$this->config->item('system_master_foil'))
        {
            $this->db->join($this->config->item('table_sms_stock_out_raw_master').' stock_out','stock_out.id=details.stock_out_id','INNER');
        }
        elseif($packing_item==$this->config->item('system_sticker'))
        {
            $this->db->join($this->config->item('table_sms_stock_out_raw_sticker').' stock_out','stock_out.id=details.stock_out_id','INNER');
        }
        $this->db->where('stock_out.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where('details.variety_id',$variety_id);
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            if(!(isset($packs[$result['pack_size_id']])))
            {
                $packs[$result['pack_size_id']]=$this->initialize_row();
            }
            $packs[$result['pack_size_id']]['stock_opening']-=$result['out_opening'];
            $packs[$result['pack_size_id']]['out_stock_damage']+=$result['out_stock_damage'];


            $packs[$result['pack_size_id']]['stock_current']-=($result['out_opening']+$result['out_stock_damage']);
        }

        $grand_total=array();
        $grand_total['pack_size']='Total End Stock';
        $grand_total['type']='';
        $items=array();
        foreach($packs as $pack_size_id=>$pack)
        {
            $item=array();
            $count=0;
            foreach($pack as $type=>$quantity)
            {
                if($count==0)
                {
                    $item['pack_size']=$pack_sizes[$pack_size_id];
                }
                else
                {
                    $item['pack_size']='';
                }
                $count++;
                $item['type']=$this->lang->line('LABEL_'.strtoupper($type));
                if($quantity>0)
                {
                    $item['quantity_total_pcs_kg']=$quantity;
                }
                else
                {
                    $item['quantity_total_pcs_kg']='';
                }
                $items[]=$item;
            }
        }
        $this->json_return($items);
    }

    private function initialize_row()
    {
        $data=array();
        $data['stock_opening']=0;
        $data['in_stock_in']=0;
        $data['in_stock_excess']=0;
        $data['in_purchase']=0;
        $data['out_stock_damage']=0;
        $data['stock_current']=0;
        return $data;
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