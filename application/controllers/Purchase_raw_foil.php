<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_raw_foil extends Root_Controller
{
    public $message;
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
        elseif($action=="details_print")
        {
            $this->system_details_print($id);
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
            $data['system_preference_items']=$this->get_preference();
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
        $this->db->order_by('purchase_foil.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['date_receive']=System_helper::display_date($item['date_receive']);
            $item['date_challan']=System_helper::display_date($item['date_challan']);
            $item['barcode']=Barcode_helper::get_barcode_raw_foil_purchase($item['id']);
            $item['quantity_total_receive']=number_format($item['quantity_receive'],3,'.','');
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $data['title']="Purchase Common Foil";
            $data["item"] = Array(
                'id'=>'',
                'date_receive' => '',
                'number_of_reel' =>'',
                'quantity_supply' =>'',
                'quantity_receive' =>'',
                'remarks' => '',
                'supplier_id' =>0,
                'challan_number' =>'',
                'date_challan' => '',
                'price_unit_tk' => '',
            );

            $data['suppliers']=Query_helper::get_info($this->config->item('table_login_basic_setup_supplier'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

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
            $item['variety_id']=0;
            $item['pack_size_id']=0;
            $packing_item=$this->config->item('system_common_foil');
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

            $current_stocks=Stock_helper::get_raw_stock(array($item['variety_id']));

            $data['item']['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];

            $data['suppliers']=Query_helper::get_info($this->config->item('table_login_basic_setup_supplier'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));

            $data['title']="Edit Purchase (Common Foil) :: ".Barcode_helper::get_barcode_raw_foil_purchase($item_id);
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
        $variety_id=0;
        $pack_size_id=0;
        $packing_item=$this->config->item('system_common_foil');
        $old_value=0;
        $current_stocks=Stock_helper::get_raw_stock(array($variety_id)); //Getting Current Stocks

        /*--Start-- Permission and negative stock checking */
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
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $old_value=$old_item['quantity_receive'];

            //Negative Stock Checking
            if(isset($current_stocks[$variety_id][$pack_size_id][$packing_item]))
            {
                if($old_value>$item['quantity_receive'])
                {
                    $current_stock=$current_stocks[$variety_id][$pack_size_id][$packing_item]['current_stock'];
                    $variance=$old_value-$item['quantity_receive'];
                    if($variance>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Update('.$variety_id.'-'.$pack_size_id.'-'.$packing_item.'-'.$old_value.'-'.$item['quantity_receive'].') will make current stock negative.';
                        $this->json_return($ajax);
                    }
                }
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
        /*--End-- Permission and negative stock checking */

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            /* --Start-- Item saving (In two table consequently)*/
            /*$data=array(); //Main data
            $data['date_receive']=System_helper::get_time($item['date_receive']);
            $data['supplier_id']=$item['supplier_id'];
            $data['challan_number']=$item['challan_number'];
            $data['date_challan']=System_helper::get_time($item['date_challan']);
            $data['remarks']=$item['remarks'];
            $data['number_of_reel']=$item['number_of_reel'];
            $data['quantity_supply']=$item['quantity_supply'];
            $data['quantity_receive']=$item['quantity_receive'];
            $data['price_unit_tk']=$item['price_unit_tk'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_purchase_raw_foil'),$data,array('id='.$id));*/

            // modify by maraj
            $item['date_receive']=System_helper::get_time($item['date_receive']);
            $item['date_challan']=System_helper::get_time($item['date_challan']);
            $item['user_updated']=$user->user_id;
            $item['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_purchase_raw_foil'),$item,array('id='.$id));

            $data=array(); //Summary data
            if(isset($current_stocks[$variety_id][$pack_size_id][$packing_item]))
            {
                $data['in_purchase']=$current_stocks[$variety_id][$pack_size_id][$packing_item]['in_purchase']-$old_value+$item['quantity_receive'];
                $data['current_stock']=$current_stocks[$variety_id][$pack_size_id][$packing_item]['current_stock']-$old_value+$item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$variety_id,'pack_size_id='.$pack_size_id,'packing_item= "'.$packing_item.'"'));
            }
            else
            {
                $data['variety_id'] = $variety_id;
                $data['pack_size_id'] = $pack_size_id;
                $data['in_purchase'] = $item['quantity_receive'];
                $data['packing_item'] = $packing_item;
                $data['current_stock'] = $item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_raw'),$data);
            }

            /* --End-- Item saving (In two table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In two table consequently)*/
            /*$data=array(); //Main Data
            $data['date_receive']=System_helper::get_time($item['date_receive']);
            $data['supplier_id']=$item['supplier_id'];
            $data['challan_number']=$item['challan_number'];
            $data['date_challan']=System_helper::get_time($item['date_challan']);
            $data['remarks']=$item['remarks'];
            $data['number_of_reel']=$item['number_of_reel'];
            $data['quantity_supply']=$item['quantity_supply'];
            $data['quantity_receive']=$item['quantity_receive'];
            $data['price_unit_tk']=$item['price_unit_tk'];
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            Query_helper::add($this->config->item('table_sms_purchase_raw_foil'),$data);*/

            // modify by maraj
            $item['date_receive']=System_helper::get_time($item['date_receive']);
            $item['date_challan']=System_helper::get_time($item['date_challan']);
            $item['user_created']=$user->user_id;
            $item['date_created']=$time;
            $item['status']=$this->config->item('system_status_active');
            Query_helper::add($this->config->item('table_sms_purchase_raw_foil'),$item);

            $data=array(); //Summary Data
            if(isset($current_stocks[$variety_id][$pack_size_id][$packing_item]))
            {
                $data['in_purchase']=$current_stocks[$variety_id][$pack_size_id][$packing_item]['in_purchase']+$item['quantity_receive'];
                $data['current_stock']=$current_stocks[$variety_id][$pack_size_id][$packing_item]['current_stock']+$item['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$variety_id,'pack_size_id='.$pack_size_id,'packing_item= "'.$packing_item.'"'));
            }
            else
            {
                $data['variety_id'] = $variety_id;
                $data['pack_size_id'] = $pack_size_id;
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
            $item['variety_id']=0;
            $item['pack_size_id']=0;
            $packing_item=$this->config->item('system_common_foil');

            $this->db->from($this->config->item('table_sms_purchase_raw_foil').' purchase_foil');
            $this->db->select('purchase_foil.*');
            $this->db->select('supplier.name supplier_name');
            $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = purchase_foil.supplier_id','LEFT');
            $this->db->select('created_user_info.name created_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' created_user_info','created_user_info.user_id = purchase_foil.user_created','INNER');
            $this->db->select('updated_user_info.name updated_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' updated_user_info','updated_user_info.user_id = purchase_foil.user_updated','LEFT');
            $this->db->where('purchase_foil.status !=',$this->config->item('system_status_delete'));
            $this->db->where('purchase_foil.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $current_stocks=Stock_helper::get_raw_stock(array($item['variety_id']));

            $data['item']['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];

            $data['title']="Details Purchase (Common Foil) :: ".Barcode_helper::get_barcode_raw_foil_purchase($item_id);
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
    private function system_details_print($id)
    {
        if((isset($this->permissions['action4']) && ($this->permissions['action4']==1)))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $item['variety_id']=0;
            $item['pack_size_id']=0;
            $packing_item=$this->config->item('system_common_foil');

            $this->db->from($this->config->item('table_sms_purchase_raw_foil').' purchase_foil');
            $this->db->select('purchase_foil.*');
            $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = purchase_foil.supplier_id','LEFT');
            $this->db->select('supplier.name supplier_name');
            $this->db->where('purchase_foil.status !=',$this->config->item('system_status_delete'));
            $this->db->where('purchase_foil.id',$item_id);
            $this->db->where('purchase_foil.quantity_supply > ',0);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Details Print Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $current_stocks=Stock_helper::get_raw_stock(array($item['variety_id']));

            $data['item']['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];

            $data['title']="Details Print Purchase (Common Foil)";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details_print",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details_print/'.$item_id);
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

            $current_stocks=Stock_helper::get_raw_stock(array($item['variety_id'])); // Getting current stocks
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
            $this->form_validation->set_rules('item[date_receive]',$this->lang->line('LABEL_DATE_RECEIVE'),'required');
            $this->form_validation->set_rules('item[supplier_id]',$this->lang->line('LABEL_SUPPLIER_NAME'),'required');
            $this->form_validation->set_rules('item[challan_number]',$this->lang->line('LABEL_CHALLAN_NUMBER'),'required');
            $this->form_validation->set_rules('item[date_challan]',$this->lang->line('LABEL_DATE_CHALLAN'),'required');
            $this->form_validation->set_rules('item[number_of_reel]',$this->lang->line('LABEL_NUMBER_OF_REEL'),'required');
            $this->form_validation->set_rules('item[quantity_supply]',$this->lang->line('LABEL_QUANTITY_SUPPLY'),'required');
            $this->form_validation->set_rules('item[quantity_receive]',$this->lang->line('LABEL_QUANTITY_RECEIVE'),'required');
            $this->form_validation->set_rules('item[price_unit_tk]',$this->lang->line('LABEL_PRICE_TAKA_UNIT'),'required');
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference();
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
        $data['date_receive']= 1;
        $data['supplier_name']= 1;
        $data['date_challan']= 1;
        $data['challan_number']= 1;
        $data['number_of_reel']= 1;
        $data['quantity_total_receive']= 1;
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
}