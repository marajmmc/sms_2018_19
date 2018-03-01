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
        elseif($action=="delete")
        {
            $this->system_delete($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
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
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
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
//        $current_records = $this->input->post('total_records');
//        if(!$current_records)
//        {
//            $current_records=0;
//        }
//        $pagesize = $this->input->post('pagesize');
//        if(!$pagesize)
//        {
//            $pagesize=100;
//        }
//        else
//        {
//            $pagesize=$pagesize*2;
//        }
//        $this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' convert_bp');
//        $this->db->select('convert_bp.*');
//        $this->db->where('convert_bp.status !=',$this->config->item('system_status_delete'));
//        $this->db->order_by('convert_bp.date_convert','DESC');
//        $this->db->order_by('convert_bp.id','DESC');
//        $this->db->limit($pagesize,$current_records);
//        $items=$this->db->get()->result_array();
//        foreach($items as &$item)
//        {
//            $item['date_convert']=System_helper::display_date($item['date_convert']);
//            $item['barcode']=Barcode_helper::get_barcode_convert_bulk_to_packet($item['id']);
//            $item['quantity_total_kg']=$item['quantity_convert'];
//        }
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

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = convert_bp.variety_id','INNER');
        $this->db->select('variety.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = convert_bp.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = variety.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse_source','warehouse_source.id = convert_bp.warehouse_id_source','INNER');
        $this->db->select('warehouse_source.name warehouse_name_source');

        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse_destination','warehouse_destination.id = convert_bp.warehouse_id_destination','INNER');
        $this->db->select('warehouse_destination.name warehouse_name_destination');

        $this->db->where('convert_bp.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('convert_bp.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_convert']=System_helper::display_date($item['date_convert']);
            $item['barcode']=Barcode_helper::get_barcode_convert_bulk_to_packet($item['id']);
            $item['quantity_convert_total_kg']=$item['quantity_convert'];
        }
//
//        print_r($item);
//        exit;
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
                'warehouse_id_destination' => '',
                'current_stock' => 0,
                'convert_quantity' => '',
                'remarks' => ''
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['destination_warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add",$data,true));
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
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = convert_bp.variety_id','INNER');

            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');

            $this->db->select('warehouse_source.name warehouse_name_source');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse_source','warehouse_source.id = convert_bp.warehouse_id_source','INNER');

            $this->db->select('warehouse_destination.name warehouse_name_destination');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse_destination','warehouse_destination.id = convert_bp.warehouse_id_destination','INNER');

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

            $current_stocks=System_helper::get_variety_stock(array($data['item']['variety_id']));
            $data['item']['current_stock']=$current_stocks[$data['item']['variety_id']][0][$data['item']['warehouse_id_source']]['current_stock'];

            $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config') . ' raw_config');
            $this->db->select('raw_config.*');
            $this->db->where('raw_config.variety_id', $data['item']['variety_id']);
            $this->db->where('raw_config.pack_size_id', $data['item']['pack_size_id']);
            $this->db->where('raw_config.revision', 1);
            $result = $this->db->get()->row_array();

            $data['item']['unit_master_foil']=$result['masterfoil'];
            $data['item']['unit_foil']=$result['foil'];
            $data['item']['unit_sticker']=$result['sticker'];

            $data['title']="Convert (Bulk to Packet)";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
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
        $old_quantity_pack_actual=0;

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
            $item['warehouse_id_source']=$old_item['warehouse_id_source'];
            $item['warehouse_id_destination']=$old_item['warehouse_id_destination'];
            $item['pack_size_id']=$old_item['pack_size_id'];
            $old_value=$old_item['quantity_convert'];
            $old_quantity_pack_actual=$old_item['quantity_packet_actual'];
        }

        $item['source_pack_size_id']=0;
        $current_stocks=System_helper::get_variety_stock(array($item['variety_id']));
        $current_raw_stocks=System_helper::get_raw_stock(array($item['variety_id']));
        $current_foil_stocks=System_helper::get_raw_stock(array(0));

        //Getting Number Of Packet
        $pack_size_value = Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'), 'name value', array('status !="' . $this->config->item('system_status_delete') . '"', 'id =' . $item['pack_size_id']), 1);

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

        if(isset($current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['warehouse_id_source']]))
        {
            $stock_source=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['warehouse_id_source']]['current_stock'];
        }
        if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]))
        {
            $stock_destination=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]['current_stock'];
        }
        if($id>0)
        {
            if($item['quantity_convert']>$old_value)
            {
                $variance=$item['quantity_convert']-$old_value;
                if($variance>$stock_source)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This convert('.$item['variety_id'].'-'.$item['source_pack_size_id'].'-'.$item['warehouse_id_source'].'-'.$old_value.'-'.$item['quantity_convert'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
            else if($item['quantity_convert']<$old_value)
            {
                $variance=$old_value-$item['quantity_convert'];
                $quantity_packet_variance = (($variance*1000) / $pack_size_value['value']);
                if($quantity_packet_variance>$stock_destination)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['warehouse_id_destination'].'-'.$old_value.'-'.$item['quantity_convert'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
        }
        else
        {
            if($item['quantity_convert']>$stock_source)
            {
                $ajax['status']=false;
                $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['source_pack_size_id'].'-'.$item['warehouse_id_source'].'-'.$item['quantity_convert'].') will make current stock negative.';
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
        $old_required_mf=0;
        $old_required_f=0;
        $old_required_number_of_sticker=0;

        $expected_mf=0;
        $expected_f=0;
        $expected_sticker=0;

        if ($result['masterfoil'] > 0 && $result['foil']<=0 && $result['sticker']<=0)
        {
            $item['quantity_master_foil_expected']=(($result['masterfoil']*$item['quantity_pack_actual'])/1000);
            $item['quantity_foil_expected']=$expected_f;
            $item['quantity_sticker_expected']=$expected_sticker;
            $master_foil=$this->config->item('system_master_foil');
            if(isset($current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]))
            {
                $current_mf_stock=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['current_stock'];
                if($id>0)
                {
                    $old_required_mf=(($result['masterfoil']*$old_quantity_pack_actual)/1000);
                    if($item['quantity_master_foil_actual']>$old_required_mf)
                    {
                        $variance_mf=$item['quantity_master_foil_actual']-$old_required_mf;
                        if($variance_mf>$current_mf_stock)
                        {
                            $ajax['status'] = false;
                            $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$master_foil.'-'.$old_value.'-'.$item['quantity_convert'].') will make current raw stock negative.';
                            $this->json_return($ajax);
                        }

                    }
                }
                else
                {
                    if($item['quantity_master_foil_actual']>$current_mf_stock)
                    {
                        $ajax['status'] = false;
                        $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$master_foil.'-'.$item['quantity_convert'].') will make current raw stock negative.';
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
            $item['quantity_master_foil_expected']=$expected_mf;
            $item['quantity_foil_expected']=(($result['foil']*$item['quantity_pack_actual'])/1000);
            $item['quantity_sticker_expected']=($result['sticker']*$item['quantity_pack_actual']);

            $foil=$this->config->item('system_common_foil');
            $sticker=$this->config->item('system_sticker');

            if((isset($current_foil_stocks[0][0][$foil])) && (isset($current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker])))
            {
                $current_f_stock=$current_foil_stocks[0][0][$foil]['current_stock'];
                $current_sticker_stock=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['current_stock'];

                if($id>0)
                {
                    $old_required_f=(($result['foil']*$old_quantity_pack_actual)/1000);
                    $old_required_number_of_sticker=($result['sticker']*$old_quantity_pack_actual);
                    if(($item['quantity_foil_actual']>$old_required_f) || ($item['quantity_sticker_actual']>$old_required_number_of_sticker))
                    {
                        $variance_f=$item['quantity_foil_actual']-$old_required_f;
                        $variance_sticker=$item['quantity_sticker_actual']-$old_required_number_of_sticker;
                        if($variance_f>$current_f_stock || $variance_sticker>$current_sticker_stock)
                        {
                            $ajax['status'] = false;
                            $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$foil.'-'.' OR '.'-'.$sticker.'-'.$old_value.'-'.$item['quantity_convert'].') will make current raw stock negative.';
                            $this->json_return($ajax);
                        }

                    }
                }
                else
                {
                    if(($item['quantity_foil_actual']>$current_f_stock) || ($item['quantity_sticker_actual']>$current_sticker_stock))
                    {
                        $ajax['status'] = false;
                        $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$foil.'-'.' OR '.'-'.$sticker.'-'.$item['quantity_convert'].') will make current raw stock negative.';
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

        /*-- End-- Validation Checking */

        $this->db->trans_start(); //DB Transaction Handle START
        if($id>0)
        {
            $data=array(); //Main Data
            $data['date_convert']=System_helper::get_time($item['date_convert']);
            $data['quantity_convert']=$item['quantity_convert'];
            $data['quantity_packet_actual']=$item['quantity_pack_actual'];
            $data['quantity_master_foil_actual']=$item['quantity_master_foil_actual'];
            $data['quantity_foil_actual']=$item['quantity_foil_actual'];
            $data['quantity_sticker_actual']=$item['quantity_sticker_actual'];

            $data['quantity_master_foil_expected']=$item['quantity_master_foil_expected'];
            $data['quantity_foil_expected']=$item['quantity_foil_expected'];
            $data['quantity_sticker_expected']=$item['quantity_sticker_expected'];

            $data['remarks']=$item['remarks'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_counter', 'revision_counter+1', FALSE);
            Query_helper::update($this->config->item('table_sms_convert_bulk_to_pack'),$data,array('id='.$id));

            $data=array(); //Summary Data(for source warehouse)
            $data['out_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['warehouse_id_source']]['out_convert_bulk_pack']-$old_value+$item['quantity_convert'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['warehouse_id_source']]['current_stock']+$old_value-$item['quantity_convert'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['source_pack_size_id'],'warehouse_id='.$item['warehouse_id_source']));

            $data=array(); //Summary Data(for destination warehouse)
            $data['in_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]['in_convert_bulk_pack']-$old_quantity_pack_actual+$item['quantity_pack_actual'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]['current_stock']-$old_quantity_pack_actual+$item['quantity_pack_actual'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['warehouse_id_destination']));


            if($item['quantity_master_foil_actual']>0)
            {
                $data=array(); //Summary data (for master foil)
                $master_foil=$this->config->item('system_master_foil');
                $data['out_convert']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['out_convert']-$old_required_mf+$item['quantity_master_foil_actual'];
                $data['current_stock']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['current_stock']+$old_required_mf-$item['quantity_master_foil_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$master_foil.'"'));
            }

            if($item['quantity_foil_actual']>0)
            {
                $data=array(); //Summary data (for common foil)
                $foil=$this->config->item('system_common_foil');
                $data['out_convert']=$current_foil_stocks[0][0][$foil]['out_convert']-$old_required_f+$item['quantity_foil_actual'];
                $data['current_stock']=$current_foil_stocks[0][0][$foil]['current_stock']+$old_required_f-$item['quantity_foil_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.'0','pack_size_id='.$item['source_pack_size_id'],'packing_item= "'.$foil.'"'));
            }

            if($item['quantity_sticker_actual']>0)
            {
                $data=array(); //Summary data (for sticker)
                $sticker=$this->config->item('system_sticker');
                $data['out_convert']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['out_convert']-$old_required_number_of_sticker+$item['quantity_sticker_actual'];
                $data['current_stock']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['current_stock']+$old_required_number_of_sticker-$item['quantity_sticker_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$sticker.'"'));
            }

        }
        else
        {
            $data=array(); //Main Data
            $data['date_convert']=System_helper::get_time($item['date_convert']);
            $data['variety_id']=$item['variety_id'];
            $data['warehouse_id_source']=$item['warehouse_id_source'];
            $data['quantity_convert']=$item['quantity_convert'];
            $data['warehouse_id_destination']=$item['warehouse_id_destination'];
            $data['pack_size_id']=$item['pack_size_id'];
            $data['quantity_packet_actual']=$item['quantity_pack_actual'];
            $data['quantity_master_foil_actual']=$item['quantity_master_foil_actual'];
            $data['quantity_foil_actual']=$item['quantity_foil_actual'];
            $data['quantity_sticker_actual']=$item['quantity_sticker_actual'];

            $data['quantity_master_foil_expected']=$item['quantity_master_foil_expected'];
            $data['quantity_foil_expected']=$item['quantity_foil_expected'];
            $data['quantity_sticker_expected']=$item['quantity_sticker_expected'];

            $data['remarks']=$item['remarks'];
            $data['revision_counter']=1;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;

            Query_helper::add($this->config->item('table_sms_convert_bulk_to_pack'),$data);

            $data=array(); //Stock Summary Data(for Bulk and source warehouse)
            $data['out_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['warehouse_id_source']]['out_convert_bulk_pack']+$item['quantity_convert'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['warehouse_id_source']]['current_stock']-$item['quantity_convert'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['source_pack_size_id'],'warehouse_id='.$item['warehouse_id_source']));

            $data=array(); //Stock Summary Data(for packet and destination warehouse)
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]))
            {
                $data['in_convert_bulk_pack']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]['in_convert_bulk_pack']+$item['quantity_pack_actual'];
                $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]['current_stock']+$item['quantity_pack_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['warehouse_id_destination']));
            }
            else
            {
                $data['variety_id'] = $item['variety_id'];
                $data['pack_size_id'] = $item['pack_size_id'];
                $data['warehouse_id'] = $item['warehouse_id_destination'];
                $data['in_convert_bulk_pack']=$item['quantity_pack_actual'];
                $data['current_stock']=$item['quantity_pack_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$data);
            }


            if($item['quantity_master_foil_actual']>0)
            {
                $data=array(); //Summary data (for master foil)
                $master_foil=$this->config->item('system_master_foil');
                $data['out_convert']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['out_convert']+$item['quantity_master_foil_actual'];
                $data['current_stock']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['current_stock']-$item['quantity_master_foil_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;

                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$master_foil.'"'));
            }
            if($item['quantity_foil_actual']>0)
            {
                $data=array(); //Summary data (for Common foil)
                $foil=$this->config->item('system_common_foil');
                $data['out_convert']=$current_foil_stocks[0][0][$foil]['out_convert']+$item['quantity_foil_actual'];
                $data['current_stock']=$current_foil_stocks[0][0][$foil]['current_stock']-$item['quantity_foil_actual'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.'0','pack_size_id='.$item['source_pack_size_id'],'packing_item= "'.$foil.'"'));
            }
            if($item['quantity_sticker_actual']>0)
            {
                $data=array(); //Summary data (for Sticker)
                $sticker=$this->config->item('system_sticker');
                $data['out_convert']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['out_convert']+$item['quantity_sticker_actual'];
                $data['current_stock']=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['current_stock']-$item['quantity_sticker_actual'];
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

    public function get_warehouse_source_and_packsize()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = 0;
        $html_warehouse_source_container_id='#warehouse_id_source';
        $html_pack_size_container_id='#pack_size_id';

        //Getting Source Warehouse

        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary');
        $this->db->select('stock_summary.warehouse_id value');
        $this->db->select('warehouse.name text');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = stock_summary.warehouse_id','INNER');
        $this->db->where('stock_summary.variety_id',$variety_id);
        $this->db->where('stock_summary.pack_size_id',$pack_size_id);
        $this->db->where('stock_summary.current_stock >',0);
        $data_warehouse_source['items']=$this->db->get()->result_array();

        //Getting packsize

        $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config').' raw_config');
        $this->db->select('raw_config.pack_size_id value');
        $this->db->select('pack_size.name text');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack_size','pack_size.id = raw_config.pack_size_id','LEFT');
        $this->db->where('raw_config.variety_id',$variety_id);
        $this->db->where('raw_config.revision',1);
        $data_pack_size['items']=$this->db->get()->result_array();

        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_warehouse_source_container_id,"html"=>$this->load->view("dropdown_with_select",$data_warehouse_source,true));
        $ajax['system_content'][]=array("id"=>$html_pack_size_container_id,"html"=>$this->load->view("dropdown_with_select",$data_pack_size,true));

        $this->json_return($ajax);
    }

    public function check_variety_raw_config()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');

        $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config') . ' raw_config');
        $this->db->select('raw_config.*');
        $this->db->where('raw_config.variety_id', $variety_id);
        $this->db->where('raw_config.pack_size_id', $pack_size_id);
        $this->db->where('raw_config.revision', 1);
        $result = $this->db->get()->row_array();
        if($result)
        {
            if (($result['masterfoil'] > 0)||(($result['foil'] > 0) && ($result['sticker'] > 0)))
            {
                $this->json_return($result);
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='Raw Material Setup is not Valid.';
                $this->json_return($ajax);
            }

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']='Raw Material Setup is not done.';
            $this->json_return($ajax);
        }
        /*if (!($result['masterfoil'] > 0))
        {
            if (!($result['foil'] > 0 && $result['sticker'] > 0))
            {
                $ajax['status'] = false;
                $ajax['system_content'][] = array("id" => $html_container_id, "html" => '');
                $ajax['system_content'][] = array("id" => '#quantity_pack_actual_id', "html" => '');
                $ajax['system_message'] = 'Packing Materials is not setup for this variety';
                $this->json_return($ajax);
            }
        }

        $quantity_pack_expected = (($convert_quantity*1000) / $pack_size_value['value']);

        $ajax['status'] = true;
        $ajax['quantity_pack_expected']=$quantity_pack_expected;
        if ($result['masterfoil'] > 0)
        {
            $packing_item=$this->config->item('system_master_foil');
            $stock_result_mf=System_helper::get_raw_stock(array($variety_id));
            $stock_current_mf=0;
            if(isset($stock_result_mf[$variety_id][$pack_size_id][$packing_item]))
            {
                $stock_current_mf=$stock_result_mf[$variety_id][$pack_size_id][$packing_item]['current_stock'];
            }

            $expected_quantity_mf=(($result['masterfoil']*$quantity_pack_expected)/1000);
            $ajax['expected_quantity_mf']=$expected_quantity_mf;
            $ajax['expected_quantity_f']=0;
            $ajax['expected_quantity_sticker']=0;
            $ajax['unit_quantity_master_foil']=$result['masterfoil'];
            $ajax['unit_quantity_foil']=0;
            $ajax['unit_quantity_sticker']=0;
            $ajax['stock_current_mf']=' (Current Stock : '.$stock_current_mf.')';
        }
        elseif ($result['foil'] > 0 && $result['sticker'] > 0)
        {
            $packing_item=$this->config->item('system_common_foil');
            $stock_result_f=System_helper::get_raw_stock(0);
            $stock_current_f=0;
            if(isset($stock_result_f[0][0][$packing_item]))
            {
                $stock_current_f=$stock_result_f[0][0][$packing_item]['current_stock'];
            }

            $packing_item=$this->config->item('system_sticker');
            $stock_result_sticker=System_helper::get_raw_stock(array($variety_id));
            $stock_current_sticker=0;
            if(isset($stock_result_sticker[$variety_id][$pack_size_id][$packing_item]))
            {
                $stock_current_sticker=$stock_result_sticker[$variety_id][$pack_size_id][$packing_item]['current_stock'];
            }
            $expected_quantity_f=(($result['foil']*$quantity_pack_expected)/1000);
            $expected_quantity_sticker=($result['sticker']*$quantity_pack_expected);
            $ajax['expected_quantity_mf']=0;
            $ajax['expected_quantity_f']=$expected_quantity_f;
            $ajax['expected_quantity_sticker']=$expected_quantity_sticker;
            $ajax['unit_quantity_master_foil']=0;
            $ajax['unit_quantity_foil']=$result['foil'];
            $ajax['unit_quantity_sticker']=$result['sticker'];
            $ajax['stock_current_f']=' (Current Stock : '.$stock_current_f.')';
            $ajax['stock_current_sticker']=' (Current Stock : '.$stock_current_sticker.')';
        }
        else
        {
            $ajax['quantity_master_foil']=0;
            $ajax['quantity_foil']=0;
            $ajax['quantity_sticker']=0;
        }
        $this->json_return($ajax);*/

    }

    private function system_delete($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $user = User_helper::get_user();
            $time = time();
            $item=Query_helper::get_info($this->config->item('table_sms_convert_bulk_to_pack'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            // Getting current stocks and raw stocks
            $current_stocks=System_helper::get_variety_stock(array($item['variety_id']));
            $current_raw_stocks=System_helper::get_raw_stock(array($item['variety_id']));
            $current_foil_stocks=System_helper::get_raw_stock(array(0));

            /*--Start-- Validation Checking */

            //Negative Stock Checking
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]))
            {
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id_destination']]['current_stock'];

                if($item['quantity_pack_actual']>$current_stock)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Delete From Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['warehouse_id_destination'].'-'.$item['quantity_pack_actual'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='This Delete From Convert:('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['warehouse_id_destination
                '].' is absent in stock.)';
                $this->json_return($ajax);
            }
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
            $data['system_preference_items']= $this->get_preference();
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

    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
        $data['barcode']= 1;
        $data['date_convert']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['quantity_convert_total_kg']= 1;
        $data['warehouse_name_source']= 1;
        $data['warehouse_name_destination']= 1;
        $data['remarks']= 1;
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

    private function check_validation()
    {
        $id = $this->input->post("id");
        if(!($id>0))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('item[date_convert]','Date Convert','required');
            $this->form_validation->set_rules('item[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
            $this->form_validation->set_rules('item[warehouse_id_source]','Source Warehouse','required');
            $this->form_validation->set_rules('item[quantity_convert]','Convert Quantity (KG)','required');
            $this->form_validation->set_rules('item[warehouse_id_destination]','Destination Warehouse','required');
            $this->form_validation->set_rules('item[pack_size_id]',$this->lang->line('LABEL_PACK_SIZE'),'required');

            $this->form_validation->set_rules('item[quantity_pack_actual]','Actual Packet Quantity','required');
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }

}