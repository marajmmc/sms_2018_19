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
        $items=array();
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

    private function system_save()
    {
        $id=$this->input->post('id');
        $user=User_helper::get_user();
        $time = time();
        $item=$this->input->post('item');
        $old_value=0;
        $item['source_pack_size_id']=0;
        $current_stocks=System_helper::get_variety_stock(array($item['variety_id']));

        /*--Start-- Permission Checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                $this->json_return($ajax);
            }

            // have to code
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
            $stock_destination=$current_stocks[$item['variety_id']][$item['source_pack_size_id']][$item['destination_warehouse_id']]['current_stock'];
        }
        if($id>0)
        {
            //have to code
        }
        else
        {
            if($item['quantity']>$stock_source)
            {
                $ajax['status']=false;
                $ajax['system_message']='This Transfer('.$item['variety_id'].'-'.$item['source_pack_size_id'].'-'.$item['source_warehouse_id'].'-'.$item['quantity'].') will make current stock negative.';
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

        $current_raw_stocks=System_helper::get_raw_stock(array($item['variety_id']));
        if ($result['masterfoil'] > 0)
        {
            $master_foil=$this->config->item('system_master_foil');
            if(isset($current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]))
            {
                $required_mf=(($result['masterfoil']*$item['number_of_actual_packet'])/1000);
                $current_mf_stock=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$master_foil]['current_stock'];
                if($required_mf>$current_mf_stock)
                {
                    $ajax['status'] = false;
                    $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].$master_foil.$item['quantity'].') will make current raw stock negative.';
                    $this->json_return($ajax);
                }
            }
            else
            {
                $ajax['status'] = false;
                $ajax['system_message']='This Raw Materials('.$item['variety_id'].'-'.$item['pack_size_id'].$master_foil.') is absent in stock.';
                $this->json_return($ajax);
            }
        }
        elseif ($result['foil'] > 0 && $result['sticker'] > 0)
        {
            $foil=$this->config->item('system_common_foil');
            $sticker=$this->config->item('system_sticker');

            $current_foil_stocks=System_helper::get_raw_stock(array(0));

            if((isset($current_foil_stocks[0][0][$foil])) && (isset($current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker])))
            {
                $required_f=(($result['foil']*$item['number_of_actual_packet'])/1000);
                $required_number_of_sticker=($result['sticker']*$item['number_of_actual_packet']);
                $current_f_stock=$current_foil_stocks[0][0][$foil]['current_stock'];
                $current_sticker_stock=$current_raw_stocks[$item['variety_id']][$item['pack_size_id']][$sticker]['current_stock'];
                if(($required_f>$current_f_stock) || ($required_number_of_sticker>$current_sticker_stock))
                {
                    $ajax['status'] = false;
                    $ajax['system_message']='This Convert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$foil.' OR '.$sticker.$item['quantity'].') will make current raw stock negative.';
                    $this->json_return($ajax);
                }

            }
            else
            {
                $ajax['status'] = false;
                $ajax['system_message']='This Raw Materials('.$item['variety_id'].'-'.$item['pack_size_id'].$foil.' OR '.$sticker.') is absent in stock.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Packing materials setup is not correct for this pack size';
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