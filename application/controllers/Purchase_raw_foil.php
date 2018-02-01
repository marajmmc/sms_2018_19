<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_raw_foil extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Purchase_raw_foil');
        $this->controller_url='purchase_raw_foil';
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
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="delete")
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
            $data['title']='Purchase (Common Foil) List';
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

        $this->db->from($this->config->item('table_sms_purchase_raw_foil').' purchase_foil');
        $this->db->select('purchase_foil.*');
        $this->db->select('supplier.name supplier_name');
        $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = purchase_foil.supplier_id','INNER');
        $this->db->where('purchase_foil.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('purchase_foil.date_purchase','DESC');
        $this->db->order_by('purchase_foil.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['date_purchase']=System_helper::display_date($item['date_purchase']);
            $item['barcode']=Barcode_helper::get_barcode_purchase_foil($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Purchase Common Foil";
            $data["item"] = Array(
                'id'=>'',
                'date_purchase' => $time,
                'quantity_supply' =>'',
                'quantity_receive' =>'',
                'remarks' => '',
                'supplier_id' =>0
            );

            $data['suppliers']=Query_helper::get_info($this->config->item('table_login_basic_setup_supplier'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
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
            $this->db->from($this->config->item('table_sms_purchase_raw_foil').' purchase_foil');
            $this->db->select('purchase_foil.*');
            $this->db->select('supplier.name supplier_name');
            $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = purchase_foil.supplier_id','LEFT');
            $this->db->where('purchase_foil.status !=',$this->config->item('system_status_delete'));
            $this->db->where('purchase_foil.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['suppliers']=Query_helper::get_info($this->config->item('table_login_basic_setup_supplier'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Edit Purchase (Common Foil)";
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time = time();
        $item = $this->input->post('item');

        $item['variety_id']=0;
        $item['pack_size_id']=0;
        $packing_item=$this->config->item('system_common_foil');

        /*--Start-- Permission Checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $old_item=Query_helper::get_info($this->config->item('table_sms_purchase_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
            if(!$old_item)
            {
                System_helper::invalid_try('Save Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
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
        /*--End-- Permission Checking */

        // Getting old value and current stocks and negative current stock checking
        $current_stocks=System_helper::get_raw_stock(array($item['variety_id']));

        $old_value=0;
        if($id>0)
        {
            $old_item=Query_helper::get_info($this->config->item('table_sms_purchase_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
            $old_value=$old_item['quantity_receive'];

            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                if($old_value>$item['quantity_receive'])
                {
                    $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                    $variance=$old_value-$item['quantity_receive'];
                    if($variance>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Update('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$old_value.'-'.$item['quantity_receive'].') will make current stock negative.';
                        $this->json_return($ajax);
                    }
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            /* --Start-- Item saving (In two table consequently)*/
            $data=array();
            $data['date_purchase']=System_helper::get_time($item['date_purchase']);
            $data['supplier_id']=$item['supplier_id'];
            $data['remarks']=$item['remarks'];
            $data['quantity_supply']=$item['quantity_supply'];
            $data['quantity_receive']=$item['quantity_receive'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            Query_helper::update($this->config->item('table_sms_purchase_raw_foil'),$data,array('id='.$id));

            $data=array(); //Summary Data
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                $data['in_purchase']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_purchase']-$old_value+$item['quantity_receive'];
                $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']-$old_value+$item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));

            }
            else
            {
                $data['variety_id'] = $item['variety_id'];
                $data['pack_size_id'] = $item['pack_size_id'];
                $data['in_purchase'] = $item['quantity_receive'];
                $data['packing_item'] = $packing_item;
                $data['current_stock'] = $item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_raw'),$data);
            }

            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In two table consequently)*/
            $data=array(); //Main Data
            $data['date_purchase']=System_helper::get_time($item['date_purchase']);
            $data['supplier_id']=$item['supplier_id'];
            $data['remarks']=$item['remarks'];
            $data['quantity_supply']=$item['quantity_supply'];
            $data['quantity_receive']=$item['quantity_receive'];
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            Query_helper::add($this->config->item('table_sms_purchase_raw_foil'),$data);

            $data=array(); //Summary Data
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                $data['in_purchase']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_purchase']+$item['quantity_receive'];
                $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']+$item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));
            }
            else
            {
                $data['variety_id'] = $item['variety_id'];
                $data['pack_size_id'] = $item['pack_size_id'];
                $data['packing_item'] = $packing_item;
                $data['in_purchase']=$item['quantity_receive'];
                $data['current_stock']=$item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_raw'),$data);
            }


            /* --End-- Item saving (In two table consequently)*/
        }
        $this->db->trans_complete();   //DB Transaction Handle END
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

    private function system_details($id)
    {
        $this->system_list();
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
            $packing_item=$this->config->item('system_common_foil');

            $item=Query_helper::get_info($this->config->item('table_sms_purchase_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $item['variety_id']=0;
            $item['pack_size_id']=0;
            // Getting current stocks
            $current_stocks=System_helper::get_raw_stock(array($item['variety_id']));
            /*--Start-- Validation Checking */
            //Negative Stock Checking
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                if($item['quantity_receive']>$current_stock)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Delete From Common Foil ('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$item['quantity_receive'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='This Delete From Common Foil ('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.' is absent in stock.)';
                $this->json_return($ajax);
            }

            /*--End-- Validation Checking */

            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_purchase_raw_foil'),$data,array('id='.$item_id));

            $data=array(); //Summary data
            $data['current_stock']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']-$item['quantity_receive']);
            $data['in_purchase']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_purchase']-$item['quantity_receive'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));

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

    private function check_validation()
    {
        $id = $this->input->post("id");
        if(!($id>0))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('item[date_purchase]',$this->lang->line('LABEL_DATE_PURCHASE'),'required');
            $this->form_validation->set_rules('item[supplier_id]',$this->lang->line('LABEL_SUPPLIER'),'required');
            $this->form_validation->set_rules('item[quantity_supply]',$this->lang->line('LABEL_QUANTITY_SUPPLY'),'required');
            $this->form_validation->set_rules('item[quantity_receive]',$this->lang->line('LABEL_QUANTITY_RECEIVE'),'required');
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }
}