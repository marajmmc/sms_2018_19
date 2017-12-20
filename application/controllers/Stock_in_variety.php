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
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=='delete')
        {
            $this->system_delete($id);
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
            $data['title']='Variety Stock In List';
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
        $this->db->select('variety.name variety_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('pack.name pack_name');
        $this->db->select('warehouse.name warehouse_name');
        $this->db->from($this->config->item('table_sms_stock_in_variety').' stock_in');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_in.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = stock_in.pack_size_id','LEFT');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = stock_in.warehouse_id','INNER');
        $this->db->where('stock_in.status',$this->config->item('system_status_active'));
        $this->db->or_where('stock_in.status',$this->config->item('system_status_delete'));
        $this->db->order_by('stock_in.date_stock_in','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if(!$item['pack_name'])
            {
                $item['pack_name']='Bulk';
                $item['quantity']=number_format($item['quantity'],3).' kg';
            }
            else
            {
                $item['pack_name']=$item['pack_name'].' gm';
                $item['quantity']=$item['quantity'].' packet';
            }
            if($item['purpose']==$this->config->item('system_purpose_variety_stock_in'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_IN');
            }
            if($item['purpose']==$this->config->item('system_purpose_variety_excess'))
            {
                $item['purpose']=$this->lang->line('LABEL_EXCESS');
            }
            $item['date_stock_in']=System_helper::display_date($item['date_stock_in']);
        }
        //print_r($items);exit;
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Variety Stock In";
            $data["item"] = Array(
                'id' => 0,
                'crop_id'=>0,
                'crop_type_id'=>0,
                'variety_id'=>0,
                'pack_size_id' => -1,
                'warehouse_id' => '',
                'quantity' => '',
                'date_stock_in' => $time,
                'purpose' => '',
                'remarks' => ''
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['varieties']=array();
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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

            $this->db->select('stock_in.*');
            $this->db->select('variety.name variety_name');
            $this->db->select('type.name crop_type_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('pack.name pack_name');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->select('summary.current_stock');
            $this->db->from($this->config->item('table_sms_stock_in_variety').' stock_in');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_in.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = stock_in.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = stock_in.warehouse_id','INNER');
            $this->db->join($this->config->item('table_sms_stock_summary_variety').' summary','summary.variety_id = stock_in.variety_id AND summary.pack_size_id = stock_in.pack_size_id AND summary.warehouse_id = stock_in.warehouse_id','INNER');
            $this->db->where('stock_in.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }
            if($data['item']['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already Deleted';
                $this->json_return($ajax);
            }
            if(!$data['item']['pack_name'])
            {
                $data['item']['pack_name']='Bulk';
            }
            else
            {
                $data['item']['pack_name']=$data['item']['pack_name'].' gm';
            }
            if($data['item']['purpose']==$this->config->item('system_purpose_variety_stock_in'))
            {
                $data['item']['purpose']=$this->lang->line('LABEL_STOCK_IN');
            }
            if($data['item']['purpose']==$this->config->item('system_purpose_variety_excess'))
            {
                $data['item']['purpose']=$this->lang->line('LABEL_EXCESS');
            }
            $data['title']="Edit Variety Stock In";
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
            $result_stock=Query_helper::get_info($this->config->item('table_sms_stock_in_variety'),'*',array('id='.$item_id),1);
            if(!$result_stock)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->json_return($ajax);
                die();
            }
            elseif($result_stock['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already Deleted';
                $this->json_return($ajax);
                die();
            }
            else
            {
                $this->db->trans_start();  //DB Transaction Handle START
                $s_i_data['status'] = $this->config->item('system_status_delete');
                $s_i_data['date_updated'] = $time;
                $s_i_data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_in_variety'),$s_i_data,array('id='.$item_id));

                $result_summary=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$result_stock['variety_id'],'pack_size_id ='.$result_stock['pack_size_id'],'warehouse_id ='.$result_stock['warehouse_id']),1);
                if($result_stock['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                {
                    $s_data['in_stock']=$result_summary['in_stock']-$result_stock['quantity'];
                }
                elseif($result_stock['purpose']==$this->config->item('system_purpose_variety_excess'))
                {
                    $s_data['in_excess']=$result_summary['in_excess']-$result_stock['quantity'];
                }
                $s_data['current_stock']=$result_summary['current_stock']-$result_stock['quantity'];
                $s_data['date_updated'] = $time;
                $s_data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result_summary['id']));
                $this->db->trans_complete();   //DB Transaction Handle END
                if ($this->db->trans_status() === TRUE)
                {
                    $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                    $this->system_list();
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->json_return($ajax);
                }
            }
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
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data = $this->input->post('item');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $result_stock=Query_helper::get_info($this->config->item('table_sms_stock_in_variety'),'*',array('id='.$id),1);
                if(!$result_stock)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Invalid Try';
                    $this->json_return($ajax);
                    die();
                }
                else
                {
                    if($result_stock['quantity']==$data['quantity'])
                    {
                        $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                        $this->system_list();
                    }
                    else
                    {
                        $data['date_updated'] = $time;
                        $data['user_updated'] = $user->user_id;
                        Query_helper::update($this->config->item('table_sms_stock_in_variety'),$data,array('id='.$id));
                        $difference=$data['quantity']-$result_stock['quantity'];
                        $result_summary=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$result_stock['variety_id'],'pack_size_id ='.$result_stock['pack_size_id'],'warehouse_id ='.$result_stock['warehouse_id']),1);
                        if($result_stock['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                        {
                            $s_data['in_stock']=$result_summary['in_stock']+$difference;
                        }
                        elseif($result_stock['purpose']==$this->config->item('system_purpose_variety_excess'))
                        {
                            $s_data['in_excess']=$result_summary['in_excess']+$difference;
                        }
                        $s_data['current_stock']=$result_summary['current_stock']+$difference;
                        $s_data['date_updated'] = $time;
                        $s_data['user_updated'] = $user->user_id;
                        Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result_summary['id']));
                    }
                }
            }
            else
            {
                $data['date_stock_in'] = System_helper::get_time($data['date_stock_in']);
                $data['status'] = $this->config->item('system_status_active');
                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_sms_stock_in_variety'),$data);

                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$data['variety_id'],'pack_size_id ='.$data['pack_size_id'],'warehouse_id ='.$data['warehouse_id']),1);
                if($result)
                {
                    if($data['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=$data['quantity']+$result['in_stock'];
                    }
                    elseif($data['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=$data['quantity']+$result['in_excess'];
                    }
                    $s_data['current_stock'] = $data['quantity']+$result['current_stock'];
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result['id']));
                }
                else
                {
                    $s_data['variety_id'] = $data['variety_id'];
                    $s_data['pack_size_id'] = $data['pack_size_id'];
                    $s_data['warehouse_id'] = $data['warehouse_id'];
                    if($data['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=$data['quantity'];
                    }
                    elseif($data['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=$data['quantity'];
                    }
                    $s_data['current_stock'] = $data['quantity'];
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$s_data);
                }
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
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
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation()
    {
        $id=$this->input->post('id');
        $this->load->library('form_validation');
        if($id==0)
        {
            $this->form_validation->set_rules('item[variety_id]',$this->lang->line('LABEL_VARIETY'),'required');
            $this->form_validation->set_rules('item[pack_size_id]','Pack Size','required');
            $this->form_validation->set_rules('item[warehouse_id]',$this->lang->line('LABEL_WAREHOUSE'),'required');
            $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
            $this->form_validation->set_rules('item[date_stock_in]',$this->lang->line('LABEL_DATE_STOCK_IN'),'required');
        }
        $this->form_validation->set_rules('item[quantity]',$this->lang->line('LABEL_QUANTITY'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}