<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_sticker extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Purchase_sticker');
        $this->controller_url='purchase_sticker';
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
            $data['title']='Purchase (Sticker) List';
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

        $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' purchase_sticker');
        $this->db->select('purchase_sticker.*');
        $this->db->select('supplier.name supplier_name');
        $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = purchase_sticker.supplier_id','INNER');
        $this->db->where('purchase_sticker.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('purchase_sticker.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_receive']=System_helper::display_date($item['date_receive']);
            $item['date_challan']=System_helper::display_date($item['date_challan']);
            $item['barcode']=Barcode_helper::get_barcode_raw_sticker_purchase($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $data['title']="Purchase Sticker";
            $data["item"] = Array(
                'id'=>'',
                'date_receive' => '',
                'remarks' => '',
                'supplier_id' =>0,
                'challan_number' =>'',
                'date_challan' => '',
            );

            $data['suppliers']=Query_helper::get_info($this->config->item('table_login_basic_setup_supplier'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['varieties']=array();
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['purchase_sticker']=array();
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
            $data['item']=Query_helper::get_info($this->config->item('table_sms_purchase_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' sticker_purchase');
            $this->db->select('sticker_purchase.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id, sticker_details.quantity_supply, sticker_details.quantity_receive, sticker_details.price_unit_tk');
            $this->db->join($this->config->item('table_sms_purchase_raw_sticker_details').' sticker_details','sticker_details.purchase_id = sticker_purchase.id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = sticker_details.variety_id','INNER');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = sticker_details.pack_size_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('sticker_purchase.id',$item_id);
            $this->db->where('sticker_details.revision',1);
            $this->db->order_by('sticker_details.id','ASC');
            $data['purchase_sticker']=$this->db->get()->result_array();

            $data['suppliers']=Query_helper::get_info($this->config->item('table_login_basic_setup_supplier'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['title']="Edit Purchase (Sticker)";
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
        $items=$this->input->post('items');
        $item_head = $this->input->post('item');
        $packing_item=$this->config->item('system_sticker');

        /*--Start-- Permission Checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $old_item=Query_helper::get_info($this->config->item('table_sms_purchase_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
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

        // Getting old quantities and current stocks and counting total received quantity and supplied quantity
        $variety_ids=array();
        $old_quantities=array();
        $current_stocks=array();
        $quantity_total_receive=0;
        $quantity_total_supply=0;
        if(isset($items))
        {
            foreach($items as $item)
            {
                $variety_ids[$item['variety_id']]=$item['variety_id'];

                $quantity_total_receive+=$item['quantity_receive'];
                $quantity_total_supply+=$item['quantity_supply'];
            }
            $current_stocks=System_helper::get_raw_stock($variety_ids);

            if($id>0)
            {
                $results=Query_helper::get_info($this->config->item('table_sms_purchase_raw_sticker_details'),'*',array('purchase_id ='.$id,'revision ='.'1'));
                foreach($results as $result)
                {
                    $old_quantities[$result['variety_id']][$result['pack_size_id']]=$result;
                }
            }
        }
        else
        {
            //Minimum variety entry checking
            $ajax['status']=false;
            $ajax['system_message']='At least one variety need to stock in.';
            $this->json_return($ajax);
        }
        /*--Start-- Validation Checking*/
        //checking incomplete entry (add more row) & Duplicate Entry Checking
        $duplicate_entry_checker=array();
        foreach($items as $item)
        {
            if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['quantity_supply']=='' ||(!($item['quantity_supply']>=0)) || $item['quantity_receive']=='' ||(!($item['quantity_receive']>=0)) || $item['price_unit_tk']=='' ||(!($item['price_unit_tk']>=0)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Unfinished stock in entry.';
                $this->json_return($ajax);
            }

            if(isset($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]))
            {
                $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]=false;
            }
            else
            {
                $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]=true;
            }
            if($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]==false)
            {
                $ajax['status']=false;
                $ajax['system_message']='You are trying to entry duplicate variety.';
                $this->json_return($ajax);
            }

        }

        // Negative Stock Checking
        if($id>0)
        {
            foreach($items as $item)
            {
                if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']]))
                {
                    $old_value=$old_quantities[$item['variety_id']][$item['pack_size_id']]['quantity_receive'];
                    if($old_value>$item['quantity_receive'])
                    {
                        $variance=$old_value-$item['quantity_receive'];
                        $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                        if($variance>$current_stock)
                        {
                            $ajax['status']=false;
                            $ajax['system_message']='This Update('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$old_value.'-'.$item['quantity_receive'].') will make current stock negative.';
                            $this->json_return($ajax);
                        }
                    }
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            /* --Start-- Item saving (In three table consequently)*/
            $data=array(); //Main data
            $data['date_receive']=System_helper::get_time($item_head['date_receive']);
            $data['supplier_id']=$item_head['supplier_id'];
            $data['challan_number']=$item_head['challan_number'];
            $data['date_challan']=System_helper::get_time($item_head['date_challan']);
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total_supply']=$quantity_total_supply;
            $data['quantity_total_receive']=$quantity_total_receive;
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_purchase_raw_sticker'),$data,array('id='.$id));

            $data=array(); //Details data
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_sms_purchase_raw_sticker_details'),$data,array('revision=1','purchase_id='.$id));

            $this->db->where('purchase_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_purchase_raw_sticker_details'));

            foreach($items as $item)
            {
                $data=array(); //Details data
                $data['purchase_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity_supply']=$item['quantity_supply'];
                $data['quantity_receive']=$item['quantity_receive'];
                $data['price_unit_tk']=$item['price_unit_tk'];
                $data['revision']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_purchase_raw_sticker_details'),$data,false);

                //summary calculation
                $data=array(); //Summary data
                $data['in_purchase']=0;
                if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']]))
                {
                    $variance=$item['quantity_receive']-$old_quantities[$item['variety_id']][$item['pack_size_id']]['quantity_receive'];

                    $data['in_purchase']=$variance;
                }
                else//new entry
                {
                    $data['in_purchase']=$item['quantity_receive'];
                }
                //fixing current stock table
                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
                {
                    $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']+$data['in_purchase'];
                    $data['in_purchase']=$data['in_purchase']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_purchase'];
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));

                }
                else
                {
                    $data['variety_id'] = $item['variety_id'];
                    $data['pack_size_id'] = $item['pack_size_id'];
                    $data['packing_item'] = $packing_item;
                    $data['current_stock'] = $data['in_purchase'];
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_raw'),$data);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In three table consequently)*/
            $data=array(); //Main Data
            $data['date_receive']=System_helper::get_time($item_head['date_receive']);
            $data['supplier_id']=$item_head['supplier_id'];
            $data['challan_number']=$item_head['challan_number'];
            $data['date_challan']=System_helper::get_time($item_head['date_challan']);
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total_supply']=$quantity_total_supply;
            $data['quantity_total_receive']=$quantity_total_receive;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            $item_id=Query_helper::add($this->config->item('table_sms_purchase_raw_sticker'),$data);

            foreach($items as $item)
            {
                $data=array(); //Details Data
                $data['purchase_id']=$item_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity_supply']=$item['quantity_supply'];
                $data['quantity_receive']=$item['quantity_receive'];
                $data['price_unit_tk']=$item['price_unit_tk'];
                $data['revision']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_purchase_raw_sticker_details'),$data,false);

                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
                {
                    $data=array(); //Summary Data
                    $data['in_purchase']=($item['quantity_receive']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_purchase']);
                    $data['current_stock'] = ($item['quantity_receive']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']);
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));

                }
                else
                {
                    $data=array(); //Summary Data
                    $data['variety_id'] = $item['variety_id'];
                    $data['pack_size_id'] = $item['pack_size_id'];
                    $data['packing_item'] = $packing_item;
                    $data['in_purchase']=$item['quantity_receive'];
                    $data['current_stock'] = $item['quantity_receive'];
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_raw'),$data);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
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
            $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' sticker_purchase');
            $this->db->select('sticker_purchase.*');
            $this->db->select('supplier.name supplier_name');
            $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = sticker_purchase.supplier_id','INNER');
            $this->db->select('created_user_info.name created_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' created_user_info','created_user_info.user_id = sticker_purchase.user_created','INNER');
            $this->db->select('updated_user_info.name updated_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' updated_user_info','updated_user_info.user_id = sticker_purchase.user_updated','LEFT');
            $this->db->where('sticker_purchase.id',$item_id);
            $this->db->where('sticker_purchase.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('Details Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' sticker_purchase');
            $this->db->select('sticker_purchase.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id, sticker_details.quantity_supply, sticker_details.quantity_receive, sticker_details.price_unit_tk');
            $this->db->join($this->config->item('table_sms_purchase_raw_sticker_details').' sticker_details','sticker_details.purchase_id = sticker_purchase.id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = sticker_details.variety_id','INNER');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = sticker_details.pack_size_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('sticker_purchase.id',$item_id);
            $this->db->where('sticker_details.revision',1);
            $this->db->order_by('sticker_details.id','ASC');
            $data['purchase_sticker']=$this->db->get()->result_array();

            $data['title']="Details Purchase (Sticker)";
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

            $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' sticker_purchase');
            $this->db->select('sticker_purchase.*');
            $this->db->select('supplier.name supplier_name');
            $this->db->join($this->config->item('table_login_basic_setup_supplier').' supplier','supplier.id = sticker_purchase.supplier_id','INNER');
            $this->db->where('sticker_purchase.id',$item_id);
            $this->db->where('sticker_purchase.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('Details Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' sticker_purchase');
            $this->db->select('sticker_purchase.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id, sticker_details.quantity_supply, sticker_details.quantity_receive,sticker_details.price_unit_tk');
            $this->db->join($this->config->item('table_sms_purchase_raw_sticker_details').' sticker_details','sticker_details.purchase_id = sticker_purchase.id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = sticker_details.variety_id','INNER');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = sticker_details.pack_size_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('sticker_purchase.id',$item_id);
            $this->db->where('sticker_details.revision',1);
            $this->db->order_by('sticker_details.id','ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="Details Purchase (Sticker)";
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
            $packing_item=$this->config->item('system_sticker');
            $item=Query_helper::get_info($this->config->item('table_sms_purchase_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);

            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_purchase_raw_sticker').' sticker_purchase');
            $this->db->select('sticker_purchase.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id,sticker_details.quantity_supply,sticker_details.quantity_receive');
            $this->db->join($this->config->item('table_sms_purchase_raw_sticker_details').' sticker_details','sticker_details.purchase_id = sticker_purchase.id','INNER');
            $this->db->where('sticker_purchase.id',$item_id);
            $this->db->where('sticker_details.revision',1);
            $this->db->order_by('sticker_details.id','ASC');
            $results=$this->db->get()->result_array();

            // Getting current stocks
            $variety_ids=array();
            foreach($results as $result)
            {
                $variety_ids[$result['variety_id']]=$result['variety_id'];
            }

            $current_stocks=System_helper::get_raw_stock($variety_ids);

            /*--Start-- Validation Checking */

            //Negative Current Stock Checking

            foreach($results as $result)
            {
                if(isset($current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock']))
                {
                    $current_stock=$current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock'];
                    if($result['quantity_receive']>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Delete('.$result['variety_id'].'-'.$result['pack_size_id'].'-'.$packing_item.'-'.$result['quantity_receive'].') will make current stock negative.';
                        $this->json_return($ajax);
                    }
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Delete('.$result['variety_id'].'-'.$result['pack_size_id'].'-'.$packing_item.'-'.$result['quantity'].' is absent in stock.)';
                    $this->json_return($ajax);
                }
            }

            /*--End-- Validation Checking */

            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_purchase_raw_sticker'),$data,array('id='.$item_id));

            foreach($results as $result)
            {
                $data=array(); //Summary data
                $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock']-$result['quantity_receive']);
                $data['in_purchase']=$current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['in_purchase']-$result['quantity_receive'];
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'packing_item="'.$packing_item.'"'));
            }

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
            $this->form_validation->set_rules('item[date_challan]','Challan Date','required');
            $this->form_validation->set_rules('item[challan_number]','Challan Number','required');
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