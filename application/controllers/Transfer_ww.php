<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer_ww extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Transfer_ww');
        $this->controller_url='transfer_ww';
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
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['date_transfer']= 1;
            $data['system_preference_items']['quantity']= 1;
            $data['system_preference_items']['remarks']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }

            $data['title']='Transfer (Warehouse to Warehouse) List';
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
        $this->db->from($this->config->item('table_sms_transfer_warehouse_variety').' transfer_warehouse');
        $this->db->select('transfer_warehouse.*');
        $this->db->where('transfer_warehouse.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('transfer_warehouse.date_transfer','DESC');
        $this->db->order_by('transfer_warehouse.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_transfer']=System_helper::display_date($item['date_transfer']);
            $item['barcode']=Barcode_helper::get_barcode_transfer_warehouse_to_warehouse($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Transfer (Warehouse to Warehouse)";
            $data["item"] = Array
            (
                'id' => 0,
                'date_transfer' => $time,
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
            $this->db->from($this->config->item('table_sms_transfer_warehouse_variety').' transfer_warehouse');
            $this->db->select('transfer_warehouse.*');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = transfer_warehouse.variety_id','LEFT');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = transfer_warehouse.pack_size_id','LEFT');
            $this->db->select('source_ware_house.name source_ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' source_ware_house','source_ware_house.id = transfer_warehouse.source_warehouse_id','LEFT');
            $this->db->select('destination_ware_house.name destination_ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' destination_ware_house','destination_ware_house.id = transfer_warehouse.destination_warehouse_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','LEFT');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','LEFT');
            $this->db->where('transfer_warehouse.id',$item_id);
            $this->db->where('transfer_warehouse.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('transfer_warehouse.id','ASC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $current_stocks=System_helper::get_variety_stock(array($data['item']['variety_id']));
            $data['item']['current_stock']=$current_stocks[$data['item']['variety_id']][$data['item']['pack_size_id']][$data['item']['source_warehouse_id']]['current_stock'];

            $data['title']="Transfer (Warehouse to Warehouse)";
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

    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_sms_transfer_warehouse_variety').' transfer_warehouse');
            $this->db->select('transfer_warehouse.*');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = transfer_warehouse.variety_id','LEFT');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = transfer_warehouse.pack_size_id','LEFT');
            $this->db->select('source_ware_house.name source_ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' source_ware_house','source_ware_house.id = transfer_warehouse.source_warehouse_id','LEFT');
            $this->db->select('destination_ware_house.name destination_ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' destination_ware_house','destination_ware_house.id = transfer_warehouse.destination_warehouse_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','LEFT');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','LEFT');
            $this->db->where('transfer_warehouse.id',$item_id);
            $this->db->where('transfer_warehouse.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('transfer_warehouse.id','ASC');
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('Details Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $data['title']="Transfer (Warehouse to Warehouse)";

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
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

        // Getting old value and current stocks
        $item=$this->input->post('item');
        $old_value=0;
        if($id>0)
        {
            $old_item=Query_helper::get_info($this->config->item('table_sms_transfer_warehouse_variety'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
            $item['variety_id']=$old_item['variety_id'];
            $item['pack_size_id']=$old_item['pack_size_id'];
            $item['source_warehouse_id']=$old_item['source_warehouse_id'];
            $item['destination_warehouse_id']=$old_item['destination_warehouse_id'];
            $old_value=$old_item['quantity'];
        }
        $current_stocks=System_helper::get_variety_stock(array($item['variety_id']));

        /*-- Start-- Validation Checking */

        //Checking Same warehouse ID(source and destination)
        if($item['source_warehouse_id']==$item['destination_warehouse_id'])
        {
            $ajax['status']=false;
            $ajax['system_message']='Source warehouse and destination warehouse can not be same';
            $this->json_return($ajax);
        }

        //Negative Stock Checking For Source Warehouse and destination warehouse
        $stock_source=0;
        $stock_destination=0;
        if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]))
        {
            $stock_source=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['current_stock'];

        }
        if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]))
        {
            $stock_destination=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock'];
        }
        if($id>0)
        {
            if($item['quantity']>$old_value)
            {
                $variance=$item['quantity']-$old_value;
                if($variance>$stock_source)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Transfer('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['source_warehouse_id'].'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
            else if($item['quantity']<$old_value)
            {
                $variance=$old_value-$item['quantity'];
                if($variance>$stock_destination)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Update Transfer('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['destination_warehouse_id'].'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
        }
        else
        {
            if($item['quantity']>$stock_source)
            {
                $ajax['status']=false;
                $ajax['system_message']='This Transfer('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['source_warehouse_id'].'-'.$item['quantity'].') will make current stock negative.';
                $this->json_return($ajax);
            }
        }

        /*-- End-- Validation Checking */

        $this->db->trans_start(); //DB Transaction Handle START
        if($id>0)
        {
            $data=array(); //Main Data
            $data['date_transfer']=System_helper::get_time($item['date_transfer']);
            $data['quantity']=$item['quantity'];
            $data['remarks']=$item['remarks'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_counter', 'revision_counter+1', FALSE);
            Query_helper::update($this->config->item('table_sms_transfer_warehouse_variety'),$data,array('id='.$id));

            $data=array(); //Summary Data(for source warehouse)
            $data['out_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['out_transfer_warehouse']-$old_value+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['current_stock']+$old_value-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['source_warehouse_id']));

            $data=array(); //Summary Data(for destination warehouse)
            $data['in_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['in_transfer_warehouse']-$old_value+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock']-$old_value+$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['destination_warehouse_id']));

        }
        else
        {
            $data=array(); //Main Data
            $data['date_transfer']=System_helper::get_time($item['date_transfer']);
            $data['variety_id']=$item['variety_id'];
            $data['pack_size_id']=$item['pack_size_id'];
            $data['source_warehouse_id']=$item['source_warehouse_id'];
            $data['destination_warehouse_id']=$item['destination_warehouse_id'];
            $data['quantity']=$item['quantity'];
            $data['remarks']=$item['remarks'];
            $data['revision_counter']=1;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            Query_helper::add($this->config->item('table_sms_transfer_warehouse_variety'),$data);

            $data=array(); //Summary Data(for source warehouse)
            $data['out_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['out_transfer_warehouse']+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['current_stock']-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['source_warehouse_id']));

            $data=array(); //Summary Data(for destination warehouse)
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]))
            {
                $data['in_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['in_transfer_warehouse']+$item['quantity'];
                $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock']+$item['quantity'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['destination_warehouse_id']));
            }
            else
            {
                $data['variety_id'] = $item['variety_id'];
                $data['pack_size_id'] = $item['pack_size_id'];
                $data['warehouse_id'] = $item['destination_warehouse_id'];
                $data['in_transfer_warehouse']=$item['quantity'];
                $data['current_stock']=$item['quantity'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$data);
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

    public function get_pack_size()
    {
        $variety_id = $this->input->post('variety_id');
        $html_container_id='#pack_size_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary');
        $this->db->select('stock_summary.pack_size_id value');
        $this->db->select('v_pack_size.name text');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = stock_summary.pack_size_id','LEFT');
        $this->db->where('stock_summary.variety_id',$variety_id);
        $this->db->group_by('stock_summary.pack_size_id');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if($item['value']==0)
            {
                $item['text']='Bulk';
            }
        }
        $data['items']=$items;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }

    public function get_source_warehouse()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');
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
            $item=Query_helper::get_info($this->config->item('table_sms_transfer_warehouse_variety'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            // Getting current stocks
            $current_stocks=System_helper::get_variety_stock(array($item['variety_id']));

            /*--Start-- Validation Checking */

            //Negative Stock Checking
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]))
            {
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock'];
                if($item['quantity']>$current_stock)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Delete From Transfer('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['source_warehouse_id'].'-'.$item['quantity'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='This Delete From Transfer:('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['source_warehouse_id'].' is absent in stock.)';
                $this->json_return($ajax);
            }

            /*--End-- Validation Checking */

            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_transfer_warehouse_variety'),$data,array('id='.$item_id));

            $data=array(); //Summary data for source warehouse
            $data['out_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['out_transfer_warehouse']-$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['source_warehouse_id']]['current_stock']+$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['source_warehouse_id']));

            $data=array(); //Summary data for destination warehouse
            $data['in_transfer_warehouse']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['in_transfer_warehouse']-$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['destination_warehouse_id']]['current_stock']-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['destination_warehouse_id']));

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status()===true)
            {
                $this->message=$this->lang->line("MSG_DELETED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_SAVED_FAIL');
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['date_transfer']= 1;
            $data['system_preference_items']['quantity']= 1;
            $data['system_preference_items']['remarks']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }
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

    private function check_validation()
    {
        $id = $this->input->post("id");
        if(!($id>0))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('item[date_transfer]','Transfer Date','required');
            $this->form_validation->set_rules('item[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
            $this->form_validation->set_rules('item[pack_size_id]',$this->lang->line('LABEL_PACK_SIZE'),'required');
            $this->form_validation->set_rules('item[source_warehouse_id]','Source Warehouse','required');
            $this->form_validation->set_rules('item[destination_warehouse_id]','Destination Warehouse','required');
            $this->form_validation->set_rules('item[quantity]',$this->lang->line('LABEL_QUANTITY'),'required');
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }
}