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
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['opening_stock_pkt_pcs']= 1;
        $data['in_stock_in_pkt_pcs']= 1;
        $data['in_stock_excess_pkt_pcs']= 1;
        $data['in_purchase_pkt_pcs']= 1;
        $data['out_stock_damage_pkt_pcs']= 1;
        $data['end_stock_pkt_pcs']= 1;
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
            $data['options']=$reports;
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Details Raw Stock Report";
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
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        //Getting varieties
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
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.id','ASC');
        $varieties=$this->db->get()->result_array();

        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }

        $pack_sizes=array();
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]=$result['text'];
        }
        $pack_sizes['0']=1000;
        $stocks=array();

        //Raw stock in calculation
        //purpose == in stock,in excess
        if($packing_item==$this->config->item('system_common_foil'))
        {
            $this->db->from($this->config->item('table_sms_stock_in_raw_foil').' stock_in');
            $this->db->select('SUM(CASE WHEN stock_in.date_stock_in<'.$date_start.' then stock_in.quantity ELSE 0 END) in_opening',false);
            $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_stock_in').'" then stock_in.quantity ELSE 0 END) in_stock_in',false);
            $this->db->select('SUM(CASE WHEN stock_in.date_stock_in>='.$date_start.' and stock_in.date_stock_in<='.$date_end.' and purpose="'.$this->config->item('system_purpose_variety_excess').'" then stock_in.quantity ELSE 0 END) in_stock_excess',false);

            $this->db->where('stock_in.status !=',$this->config->item('system_status_delete'));
            $this->db->group_by('stock_in.id');
            $results=$this->db->get()->result_array();
        }
        else
        {
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
            $this->db->where_in('details.variety_id',$variety_ids);
            if($pack_size_id>0)
            {
                $this->db->where('details.pack_size_id',$pack_size_id);
            }
            $this->db->group_by('details.variety_id');
            $this->db->group_by('details.pack_size_id');
            $results=$this->db->get()->result_array();
        }

        foreach($results as $result)
        {
            if($packing_item==$this->config->item('system_common_foil'))
            {
                $result['variety_id']='';
                $result['pack_size_id']='';
            }
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                if($packing_item==$this->config->item('system_common_foil'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$result['pack_size_id']);
                }
                else
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
                }

            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt_pcs']+=$result['in_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_in_pkt_pcs']+=$result['in_stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_excess_pkt_pcs']+=$result['in_stock_excess'];

            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt_pcs']=$stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt_pcs'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt_pcs']+=$stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_in_pkt_pcs'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt_pcs']+=$stocks[$result['variety_id']][$result['pack_size_id']]['in_stock_excess_pkt_pcs'];
        }

        //Purchase calculation
        if($packing_item==$this->config->item('system_common_foil'))
        {
            $this->db->from($this->config->item('table_sms_purchase_raw_foil').' purchase');
            $this->db->select('SUM(CASE WHEN purchase.date_receive<'.$date_start.' then purchase.quantity_receive ELSE 0 END) in_opening',false);
            $this->db->select('SUM(CASE WHEN purchase.date_receive>='.$date_start.' and purchase.date_receive<='.$date_end.' then purchase.quantity_receive ELSE 0 END) in_purchase',false);
            $this->db->where('purchase.status !=',$this->config->item('system_status_delete'));
            $this->db->group_by('purchase.id');
            $results=$this->db->get()->result_array();
        }
        else
        {
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
            $this->db->where('details.revision',1);
            $this->db->where_in('details.variety_id',$variety_ids);
            $this->db->where('details.quantity_supply>',0);
            if($pack_size_id>0)
            {
                $this->db->where('details.pack_size_id',$pack_size_id);
            }
            $this->db->group_by('details.variety_id');
            $this->db->group_by('details.pack_size_id');
            $results=$this->db->get()->result_array();
        }

        foreach($results as $result)
        {
            if($packing_item==$this->config->item('system_common_foil'))
            {
                $result['variety_id']='';
                $result['pack_size_id']='';
            }

            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                if($packing_item==$this->config->item('system_common_foil'))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$result['pack_size_id']);
                }
                else
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
                }
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt_pcs']+=$result['in_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['in_purchase_pkt_pcs']+=$result['in_purchase'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt_pcs']+=($result['in_opening']+$result['in_purchase']);
        }

        //out stock damage
        if($packing_item==$this->config->item('system_common_foil'))
        {
            $this->db->from($this->config->item('table_sms_stock_out_raw_foil').' stock_out');
            $this->db->select('SUM(CASE WHEN stock_out.date_stock_out<'.$date_start.' then stock_out.quantity ELSE 0 END) out_opening',false);
            $this->db->select('SUM(CASE WHEN stock_out.date_stock_out>='.$date_start.' and stock_out.date_stock_out<='.$date_end.' and stock_out.purpose="'.$this->config->item('system_purpose_raw_stock_damage').'" then stock_out.quantity ELSE 0 END) out_stock_damage',false);
            $this->db->where('stock_out.status !=',$this->config->item('system_status_delete'));
            $this->db->group_by('stock_out.id');
            $results=$this->db->get()->result_array();
        }
        else
        {
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
            $this->db->where_in('details.variety_id',$variety_ids);
            if($pack_size_id>0)
            {
                $this->db->where('details.pack_size_id',$pack_size_id);
            }
            $this->db->group_by('details.variety_id');
            $this->db->group_by('details.pack_size_id');
            $results=$this->db->get()->result_array();
        }

        foreach($results as $result)
        {
            if($packing_item==$this->config->item('system_common_foil'))
            {
                $result['variety_id']='';
                $result['pack_size_id']='';
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['opening_stock_pkt_pcs']-=$result['out_opening'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['out_stock_damage_pkt_pcs']+=$result['out_stock_damage'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['end_stock_pkt_pcs']-=($result['out_opening']+$result['out_stock_damage']);
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

        if($packing_item!=$this->config->item('system_common_foil'))
        {
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
        }
        else
        {
            $item=array();
            foreach($stocks as $stock)
            {
                foreach($stock as $item)
                {
                    foreach($item as $key=>$value)
                    {
                        if(substr($key,-7)=='pkt_pcs')
                        {
                            $item[$key]=number_format($value,3,'.','');
                        }
                    }

                }
            }
            $items[]=$item;
        }
        if($packing_item!=$this->config->item('system_common_foil'))
        {
            $items[]=$this->get_row($type_total);
            $items[]=$this->get_row($crop_total);
            $items[]=$this->get_row($grand_total);
        }
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
            $packing_item=$this->input->post('packing_item');
            $row[$key]=$info[$key];
            if($packing_item==$this->config->item('system_master_foil'))
            {
                if(substr($key,-7)=='pkt_pcs')
                {
                    $row[$key]=number_format($info[$key],3,'.','');
                }
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