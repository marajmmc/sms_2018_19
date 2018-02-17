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

//    public function get_pack_size()
//    {
//        $variety_id = $this->input->post('variety_id');
//        $html_container_id='#pack_size_id';
//        if($this->input->post('html_container_id'))
//        {
//            $html_container_id=$this->input->post('html_container_id');
//        }
//        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary');
//        $this->db->select('stock_summary.pack_size_id value');
//        $this->db->select('v_pack_size.name text');
//        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = stock_summary.pack_size_id','LEFT');
//        $this->db->where('stock_summary.variety_id',$variety_id);
//        $this->db->group_by('stock_summary.pack_size_id');
//        $items=$this->db->get()->result_array();
//        $data['items']=$items;
//        $ajax['status']=true;
//        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
//        $this->json_return($ajax);
//    }

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
//
//        echo $variety_id.'<br/>';
//        echo $pack_size_id.'<br/>';
//        echo $quantity.'<br/>';
//
//        exit;

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
}