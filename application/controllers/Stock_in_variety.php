<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_in_variety extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Stock_in_variety');
        $this->controller_url='stock_in_variety';
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
            $data['title']='Stock In List';
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
        $this->db->select('stock_in.*');
        $this->db->from($this->config->item('table_sms_stock_in_variety').' stock_in');
        $this->db->where('stock_in.status',$this->config->item('system_status_active'));
        $this->db->order_by('stock_in.date_stock_in','DESC');
        $this->db->order_by('stock_in.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_stock_in']=System_helper::display_date($item['date_stock_in']);


            if($item['purpose']==$this->config->item('system_purpose_variety_stock_in'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_IN_PURPOSE_IN');
            }
            elseif($item['purpose']==$this->config->item('system_purpose_variety_excess'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_IN_EXCESS');
            }
            $item['quantity_total']=$item['quantity_total'].' KG';
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock In Here";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_in' => $time,
                'purpose' => '',
                'remarks' => ''
            );

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['varieties']=array();
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time = time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_validation_edit())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_validation_add())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
        }

        $data = $this->input->post('item');
        if($id>0)
        {

        }
        else
        {
            $this->db->trans_begin(); //DB Transaction Handle START
            $items=$this->input->post('items');
            /*--Start-- Minimum variety entry checking*/
            if(!$items)
            {
                $ajax['status']=false;
                $ajax['system_message']='At least one variety need to stock in.';
                $this->json_return($ajax);
            }
            /*--End-- Minimum variety entry checking*/

            /* --Start-- for checking incomplete entry (add more row)*/
            foreach($items as $item)
            {
                if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['warehouse_id']==0 || $item['quantity']=='')
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Unfinished stock in entry.';
                    $this->json_return($ajax);
                }
            }
            /* --End-- for checking incomplete entry (add more row)*/

            /* --Start-- for counting total quantity of stock in*/
            $pack_size=array();
            $packs=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            foreach($packs as $pack)
            {
                $pack_size[$pack['value']]=$pack['text'];
            }
            $data['quantity_total']=0;
            foreach($items as $item)
            {
                if($item['pack_size_id']!=0)
                {
                    $data['quantity_total']+=(($pack_size[$item['pack_size_id']])*($item['quantity'])/1000);

                }else
                {
                    $data['quantity_total']+=$item['quantity'];
                }
            }
            /* --End-- for counting total quantity of stock in*/

            /* --Start-- Item saving (In three table consequently)*/
            $data['date_stock_in']=System_helper::get_time($data['date_stock_in']);
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            $item_id=Query_helper::add($this->config->item('table_sms_stock_in_variety'),$data);
            foreach($items as $item)
            {
                $data_details['stock_in_id']=$item_id;
                $data_details['variety_id']=$item['variety_id'];
                $data_details['pack_size_id']=$item['pack_size_id'];
                $data_details['warehouse_id']=$item['warehouse_id'];
                $data_details['quantity']=$item['quantity'];
                $data_details['user_created']=$user->user_id;
                $data_details['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_in_variety_details'),$data_details);
                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$data_details['variety_id'],'pack_size_id ='.$data_details['pack_size_id'],'warehouse_id ='.$data_details['warehouse_id']),1);
                if($result)
                {
                    if($data['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=$data_details['quantity']+$result['in_stock'];
                    }
                    elseif($data['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=$data_details['quantity']+$result['in_excess'];
                    }
                    $s_data['current_stock'] = $data_details['quantity']+$result['current_stock'];
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result['id']));
                }
                else
                {
                    $s_data['variety_id'] = $data_details['variety_id'];
                    $s_data['pack_size_id'] = $data_details['pack_size_id'];
                    $s_data['warehouse_id'] = $data_details['warehouse_id'];
                    if($data['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=$data_details['quantity'];
                    }
                    elseif($data['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=$data_details['quantity'];
                    }
                    $s_data['current_stock'] = $data_details['quantity'];
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$s_data);
                }
            }
            /* --End-- Item saving (In three table consequently)*/

            if($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();

                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
            else
            {
                $this->db->trans_commit();

                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
        }
    }
    private function check_validation_add()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date_stock_in]',$this->lang->line('LABEL_DATE_STOCK_IN'),'required');
        $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}