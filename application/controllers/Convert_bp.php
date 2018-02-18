<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Convert_bp extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Convert_bp');
        $this->controller_url='convert_bp';
    }
    public function index($action='list',$id=0)
    {
        if($action=='list')
        {
            $this->system_list();
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=='add')
        {
            $this->system_add();
        }
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']='Convert (Bulk to Packet) List';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
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
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
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
        $this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' convert_bp');
        $this->db->select('convert_bp.*');
        $this->db->where('convert_bp.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('convert_bp.date_convert','DESC');
        $this->db->order_by('convert_bp.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_convert']=System_helper::display_date($item['date_convert']);
            $item['barcode']=Barcode_helper::get_barcode_convert_bulk_to_packet($item['id']);
        }

        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Convert (Bulk to Packet)";
            $data["item"] = Array
            (
                'id' => 0,
                'date_convert' => $time,
                'crop_id'=>0,
                'crop_type_id'=>0,
                'variety_id'=>0,
                'destination_warehouse_id' => '',
                'current_stock' => 0,
                'quantity' => '',
                'actual_master_foil' =>'',
                'actual_foil' =>'',
                'actual_sticker' =>'',
                'number_of_actual_packet' =>'',
                'remarks' => ''
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['destination_warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
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
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' convert_bp');
            $this->db->select('convert_bp.*');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = convert_bp.variety_id','LEFT');


            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','LEFT');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','LEFT');

            $this->db->select('source_ware_house.name source_ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' source_ware_house','source_ware_house.id = convert_bp.source_warehouse_id','LEFT');




            $this->db->select('destination_ware_house.name destination_ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' destination_ware_house','destination_ware_house.id = convert_bp.destination_warehouse_id','LEFT');

            $this->db->select('v_pack_size.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = convert_bp.pack_size_id','LEFT');

            $this->db->where('convert_bp.id',$item_id);
            $this->db->where('convert_bp.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('convert_bp.id','ASC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

//            print_r($data['item']);
//            exit;

            $current_stocks=System_helper::get_variety_stock(array($data['item']['variety_id']));
            $data['item']['current_stock']=$current_stocks[$data['item']['variety_id']][0][$data['item']['source_warehouse_id']]['current_stock'];



            $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config') . ' raw_config');
            $this->db->select('raw_config.*');
            $this->db->where('raw_config.variety_id', $data['item']['variety_id']);
            $this->db->where('raw_config.pack_size_id', $data['item']['pack_size_id']);
            $this->db->where('raw_config.revision', 1);
            $result = $this->db->get()->row_array();

            $data['item']['actual_master_foil']=0;
            $data['item']['actual_foil']=0;
            $data['item']['actual_sticker']=0;
            if ($result['masterfoil'] > 0)
            {
                $data['item']['actual_master_foil']=(($result['masterfoil']*$data['item']['number_of_actual_packet'])/1000);
            }
            elseif ($result['foil'] > 0 && $result['sticker'] > 0)
            {
                $data['item']['actual_foil']=(($result['foil']*$data['item']['number_of_actual_packet'])/1000);
                $data['item']['actual_sticker']=($result['sticker']*$data['item']['number_of_actual_packet']);
            }
            $data['title']="Convert (Bulk to Packet)";
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
        $id=$this->input->post('id');
        $user=User_helper::get_user();
        $time = time();
        $item=$this->input->post('item');

        $old_value=0;
        $old_number_of_actual_packet=0;

        if($id>0)
        {
            $old_item=Query_helper::get_info($this->config->item('table_sms_convert_bulk_to_pack'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
            if(!$old_item)
            {
                System_helper::invalid_try('Save Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $item['variety_id']=$old_item['variety_id'];
            $item['source_warehouse_id']=$old_item['source_warehouse_id'];
            $item['destination_warehouse_id']=$old_item['destination_warehouse_id'];
            $item['pack_size_id']=$old_item['pack_size_id'];
            $old_value=$old_item['quantity'];
            $old_number_of_actual_packet=$old_item['number_of_actual_packet'];

            //$old_number_of_actual_packet=
        }

        $item['source_pack_size_id']=0;
        $current_stocks=System_helper::get_variety_stock(array($item['variety_id']));
        $current_raw_stocks=System_helper::get_raw_stock(array($item['variety_id']));
        $current_foil_stocks=System_helper::get_raw_stock(array(0));

        //Getting Number Of Packet
        $pack_size_value = Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'), 'name value', array('status !="' . $this->config->item('system_status_delete') . '"', 'id =' . $item['pack_size_id']), 1);
        $number_of_packet = (($item['quantity']*1000) / $pack_size_value['value']);

//        print_r($number_of_packet);
//        exit;

        /*--Start-- Permission Checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        /*--End-- Permission Checking */


        /*-- Start-- Validation Checking */

        //Negative Stock Checking For Source Warehouse and destination warehouse
        $stock_source=0;
        $stock_destination=0;

        if(isset($current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['source_warehouse_id']]))
        {
            $stock_source=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['source_warehouse_id']]['current_stock'];
        }
        if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]))
        {
            $stock_destination=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock'];
//            print_r($stock_destination);
//            exit;
        }
        if($id>0)
        {
            if($item['quantity']>$old_value)
            {
                $variance=$item['quantity']-$old_value;
                if($variance>$stock_source)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This convert('.$item['variety_id'].'-'.$item['source_pack_size_id'].'-'.$item['source_warehouse_id'].'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
            else if($item['quantity']<$old_value)
            {
                $variance=$old_value-$item['quantity'];
                $number_of_packet_variance = (($variance*1000) / $pack_size_value['value']);
                if($number_of_packet_variance>$stock_destination)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['destination_warehouse_id'].'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
        }
        else
        {
            if($item['quantity']>$stock_source)
            {
                $ajax['status']=false;
                $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['source_pack_size_id'].'-'.$item['source_warehouse_id'].'-'.$item['quantity'].') will make current stock negative.';
                $this->json_return($ajax);
            }
        }

        //Variety And pack size wise Packing Materials Setup Checking

        $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config') . ' raw_config');
        $this->db->select('raw_config.*');
        $this->db->where('raw_config.variety_id', $item['variety_id']);
        $this->db->where('raw_config.pack_size_id', $item['pack_size_id']);
        $this->db->where('raw_config.revision', 1);
        $result = $this->db->get()->row_array();
        if (!($result['masterfoil'] > 0))
        {
            if (!($result['foil'] > 0 && $result['sticker'] > 0))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Packing Materials is not setup for this variety';
                $this->json_return($ajax);
            }
        }


        //Negative Raw Stock Checking
        $required_mf=0;
        $required_f=0;
        $required_number_of_sticker=0;

        $old_required_mf=0;
        $old_required_f=0;
        $old_required_number_of_sticker=0;

        if ($result['masterfoil'] > 0 && $result['foil']<=0 && $result['sticker']<=0)
        {
            $master_foil=$this->config->item('system_master_foil');
            if(isset($current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]))
            {
                $required_mf=(($result['masterfoil']*$item['number_of_actual_packet'])/1000);
                $current_mf_stock=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['current_stock'];
                if($id>0)
                {
                    $old_required_mf=(($result['masterfoil']*$old_number_of_actual_packet)/1000);
                    if($required_mf>$old_required_mf)
                    {
                        $variance_mf=$required_mf-$old_required_mf;
                        if($variance_mf>$current_mf_stock)
                        {
                            $ajax['status'] = false;
                            $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$master_foil.'-'.$old_value.'-'.$item['quantity'].') will make current raw stock negative.';
                            $this->json_return($ajax);
                        }

                    }
                }
                else
                {
                    if($required_mf>$current_mf_stock)
                    {
                        $ajax['status'] = false;
                        $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$master_foil.'-'.$item['quantity'].') will make current raw stock negative.';
                        $this->json_return($ajax);
                    }
                }
            }
            else
            {
                $ajax['status'] = false;
                $ajax['system_message']='This Raw Materials('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$master_foil.') is absent in stock.';
                $this->json_return($ajax);
            }
        }
        elseif ($result['foil'] > 0 && $result['sticker'] > 0 && $result['masterfoil']<=0)
        {
            $foil=$this->config->item('system_common_foil');
            $sticker=$this->config->item('system_sticker');

            if((isset($current_foil_stocks[0][0][$foil])) && (isset($current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker])))
            {
                $required_f=(($result['foil']*$item['number_of_actual_packet'])/1000);

                $required_number_of_sticker=($result['sticker']*$item['number_of_actual_packet']);
                $current_f_stock=$current_foil_stocks[0][0][$foil]['current_stock'];
                $current_sticker_stock=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['current_stock'];

                if($id>0)
                {
//                    print_r($result);
//                    exit;
                    $old_required_f=(($result['foil']*$old_number_of_actual_packet)/1000);
                    $old_required_number_of_sticker=($result['sticker']*$old_number_of_actual_packet);
                    if(($required_f>$old_required_f) || ($required_number_of_sticker>$old_required_number_of_sticker))
                    {
                        $variance_f=$required_f-$old_required_f;
                        $variance_sticker=$required_number_of_sticker-$old_required_number_of_sticker;
                        if($variance_f>$current_f_stock || $variance_sticker>$current_sticker_stock)
                        {
                            $ajax['status'] = false;
                            $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$foil.'-'.' OR '.'-'.$sticker.'-'.$old_value.'-'.$item['quantity'].') will make current raw stock negative.';
                            $this->json_return($ajax);
                        }

                    }
                }
                else
                {
                    if(($required_f>$current_f_stock) || ($required_number_of_sticker>$current_sticker_stock))
                    {
                        $ajax['status'] = false;
                        $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$foil.'-'.' OR '.'-'.$sticker.'-'.$item['quantity'].') will make current raw stock negative.';
                        $this->json_return($ajax);
                    }
                }
            }
            else
            {
                $ajax['status'] = false;
                $ajax['system_message']='This Raw Materials('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$foil.'-'.' OR '.'-'.$sticker.') is absent in stock.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Packing materials setup is not correct for this pack size';
            $this->json_return($ajax);
        }
//        echo $old_required_mf;
//        echo 'hi';
//        echo $old_required_f;
//        echo 'hi';
//        echo $old_required_number_of_sticker;
//        echo 'new';
//        echo $required_mf;
//        echo 'hi';
//        echo $required_f;
//        echo 'hi';
//        echo $required_number_of_sticker;
//        exit;
        /*-- End-- Validation Checking */


        $this->db->trans_start(); //DB Transaction Handle START
        if($id>0)
        {
            $data=array(); //Main Data
            $data['date_convert']=System_helper::get_time($item['date_convert']);
            $data['quantity']=$item['quantity'];
            $data['number_of_actual_packet']=$item['number_of_actual_packet'];
            $data['remarks']=$item['remarks'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_counter', 'revision_counter+1', FALSE);
            Query_helper::update($this->config->item('table_sms_convert_bulk_to_pack'),$data,array('id='.$id));

            $data=array(); //Summary Data(for source warehouse)
            $data['out_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['out_transfer_warehouse']-$old_value+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['current_stock']+$old_value-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['source_warehouse_id']));

            $data=array(); //Summary Data(for destination warehouse)
            $data['out_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['source_warehouse_id']]['out_convert_bulk_pack']-$old_value+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock']-$old_value+$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['destination_warehouse_id']));

        }
        else
        {
            $data=array(); //Main Data
            $data['date_convert']=System_helper::get_time($item['date_convert']);
            $data['variety_id']=$item['variety_id'];
            $data['source_warehouse_id']=$item['source_warehouse_id'];
            $data['quantity']=$item['quantity'];
            $data['destination_warehouse_id']=$item['destination_warehouse_id'];
            $data['pack_size_id']=$item['pack_size_id'];
            $data['number_of_actual_packet']=$item['number_of_actual_packet'];
            $data['remarks']=$item['remarks'];
            $data['revision_counter']=1;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            Query_helper::add($this->config->item('table_sms_convert_bulk_to_pack'),$data);

            $data=array(); //Stock Summary Data(for Bulk and source warehouse)
            $data['out_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['source_warehouse_id']]['out_convert_bulk_pack']+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['source_warehouse_id']]['current_stock']-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['source_pack_size_id'],'warehouse_id='.$item['source_warehouse_id']));

            $data=array(); //Stock Summary Data(for packet and destination warehouse)
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]))
            {
                $data['in_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['in_convert_bulk_pack']+$number_of_packet;
                $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock']+$number_of_packet;
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['destination_warehouse_id']));
            }
            else
            {
                $data['variety_id'] = $item['variety_id'];
                $data['pack_size_id'] = $item['pack_size_id'];
                $data['warehouse_id'] = $item['destination_warehouse_id'];
                $data['in_convert_bulk_pack']=$number_of_packet;
                $data['current_stock']=$number_of_packet;
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$data);
            }

            $data=array(); //Raw Stock Summary data
//            print_r($current_raw_stocks);
//            exit;
            if($required_mf>0)
            {
                $master_foil=$this->config->item('system_master_foil');
                $data['out_convert']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['out_convert']+$required_mf;
                $data['current_stock']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['current_stock']-$required_mf;
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
//                print_r($data);
//                echo $item['variety_id'];
//                echo $item['pack_size_id'];
//                echo $master_foil;
//                exit;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$master_foil.'"'));
            }
            if($required_f>0)
            {
                $foil=$this->config->item('system_common_foil');
//                echo $foil;
//                exit;
                $data['out_convert']=$current_foil_stocks[0][0][$foil]['out_convert']+$required_f;
                $data['current_stock']=$current_foil_stocks[0][0][$foil]['current_stock']-$required_f;
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
//                print_r($data);
//                exit;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.'0','pack_size_id='.$item['source_pack_size_id'],'packing_item= "'.$foil.'"'));
            }
            if($required_number_of_sticker>0)
            {
                $sticker=$this->config->item('system_sticker');
                $data['out_convert']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['out_convert']+$required_number_of_sticker;
                $data['current_stock']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['current_stock']-$required_number_of_sticker;
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$sticker.'"'));
            }

        }
        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status()===true)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line('MSG_SAVED_SUCCESS');
            if($save_and_new==1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_SAVED_FAIL');
            $this->json_return($ajax);
        }




    }

    public function get_source_warehouse()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = 0;
        $html_container_id='#source_warehouse_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary');
        $this->db->select('stock_summary.warehouse_id value');
        $this->db->select('ware_house.name text');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = stock_summary.warehouse_id','LEFT');
        $this->db->where('stock_summary.variety_id',$variety_id);
        $this->db->where('stock_summary.pack_size_id',$pack_size_id);
        $data['items']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }

    public function check_variety_raw_config()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $quantity = $this->input->post('quantity');

        $pack_size_value = Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'), 'name value', array('status !="' . $this->config->item('system_status_delete') . '"', 'id =' . $pack_size_id), 1);

        $html_container_id = '#number_of_packet_id';
        if ($this->input->post('html_container_id'))
        {
            $html_container_id = $this->input->post('html_container_id');
        }
        if ($this->input->post('html_container_id'))
        {
            $html_container_id = $this->input->post('html_container_id');
        }

        $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config') . ' raw_config');
        $this->db->select('raw_config.*');
        $this->db->where('raw_config.variety_id', $variety_id);
        $this->db->where('raw_config.pack_size_id', $pack_size_id);
        $this->db->where('raw_config.revision', 1);
        $result = $this->db->get()->row_array();
        if (!($result['masterfoil'] > 0))
        {
            if (!($result['foil'] > 0 && $result['sticker'] > 0))
            {
                $ajax['status'] = false;
                $ajax['system_content'][] = array("id" => $html_container_id, "html" => '');
                $ajax['system_content'][] = array("id" => '#number_of_actual_packet_id', "html" => '');
                $ajax['system_message'] = 'Packing Materials is not setup for this variety';
                $this->json_return($ajax);
            }
        }
        $number_of_packet = (($quantity*1000) / $pack_size_value['value']);

        $ajax['status'] = true;
        $ajax['system_content'][] = array("id" => $html_container_id, "html" => $number_of_packet);
        $ajax['system_content'][] = array("id" => '#number_of_actual_packet_id_input_container', "html" => '<input type="text" name="item[number_of_actual_packet]" id="number_of_actual_packet_id" class="form-control float_type_positive" value="' . $number_of_packet . '"/>');

        if ($result['masterfoil'] > 0)
        {
            $number_of_mf=(($result['masterfoil']*$number_of_packet)/1000);

            $ajax['quantity_master_foil']=$result['masterfoil'];
            $ajax['quantity_foil']=0;
            $ajax['quantity_sticker']=0;

            $ajax['system_content'][] = array("id" => '#expected_mf_id', "html" => $number_of_mf);
            $ajax['system_content'][] = array("id" => '#actual_mf_id_input_container', "html" => '<input type="text" name="item[actual_mf]" id="actual_mf_id" class="form-control float_type_positive" value="' . $number_of_mf . '"/>');
            $ajax['system_content'][] = array("id" => '#expected_mf_id_in_pack_size_container', "html" => '<input type="hidden" id="expected_mf_id_in_pack_size_change" class="form-control float_type_positive" value="' . $number_of_mf . '"/>');

        }
        elseif ($result['foil'] > 0 && $result['sticker'] > 0)
        {
            $number_of_f=(($result['foil']*$number_of_packet)/1000);
            $number_of_sticker=($result['sticker']*$number_of_packet);

            $ajax['quantity_master_foil']=0;
            $ajax['quantity_foil']=$result['foil'];
            $ajax['quantity_sticker']=$result['sticker'];

            $ajax['system_content'][] = array("id" => '#expected_f_id', "html" => $number_of_f);
            $ajax['system_content'][] = array("id" => '#actual_f_id_input_container', "html" => '<input type="text" name="item[actual_f]" id="actual_f_id" class="form-control float_type_positive" value="' . $number_of_f . '"/>');
            $ajax['system_content'][] = array("id" => '#expected_f_id_in_pack_size_change_container', "html" => '<input type="hidden" id="expected_f_id_in_pack_size_change" class="form-control float_type_positive" value="' . $number_of_f . '"/>');

            $ajax['system_content'][] = array("id" => '#expected_sticker_id', "html" => $number_of_sticker);
            $ajax['system_content'][] = array("id" => '#actual_sticker_id_input_container', "html" => '<input type="text" name="item[actual_sticker]" id="actual_sticker_id" class="form-control float_type_positive" value="' . $number_of_sticker . '"/>');
            $ajax['system_content'][] = array("id" => '#expected_sticker_id_in_pack_size_change_container', "html" => '<input type="hidden" id="expected_sticker_id_in_pack_size_change" class="form-control float_type_positive" value="' . $number_of_sticker . '"/>');

        }
        else
        {
            $ajax['quantity_master_foil']=0;
            $ajax['quantity_foil']=0;
            $ajax['quantity_sticker']=0;
        }
        $this->json_return($ajax);

    }

    private function check_validation()
    {
        $id = $this->input->post("id");
        if(!($id>0))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('item[date_convert]','Date Convert','required');
            $this->form_validation->set_rules('item[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
            $this->form_validation->set_rules('item[source_warehouse_id]','Source Warehouse','required');
            $this->form_validation->set_rules('item[quantity]',$this->lang->line('LABEL_QUANTITY') .' (In KG)','required');
            $this->form_validation->set_rules('item[destination_warehouse_id]','Destination Warehouse','required');
            $this->form_validation->set_rules('item[pack_size_id]',$this->lang->line('LABEL_PACK_SIZE'),'required');

            $this->form_validation->set_rules('item[number_of_actual_packet]','Number Of Actual Packet','required');
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }

}