<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_in_raw_sticker extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Stock_in_raw_sticker');
        $this->controller_url='stock_in_raw_sticker';
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
            $data['title']='Stock In (Sticker) List';
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

        $this->db->from($this->config->item('table_sms_stock_in_raw_sticker').' stock_in_sticker');
        $this->db->select('stock_in_sticker.*');
        $this->db->where('stock_in_sticker.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('stock_in_sticker.date_stock_in','DESC');
        $this->db->order_by('stock_in_sticker.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_stock_in']=System_helper::display_date($item['date_stock_in']);
            $item['barcode']=Barcode_helper::get_barcode_raw_sticker_stock_in($item['id']);
            $item['quantity_total_kg']=number_format($item['quantity_total'],3,'.','');
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock In sticker";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_in' => $time,
                'purpose' => '',
                'remarks' => ''
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['stock_in_sticker']=array();
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
            $data['item']=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_stock_in_raw_sticker').' sticker_stock_in');
            $this->db->select('sticker_stock_in.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id, sticker_details.quantity');
            $this->db->join($this->config->item('table_sms_stock_in_raw_sticker_details').' sticker_details','sticker_details.stock_in_id = sticker_stock_in.id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = sticker_details.variety_id','INNER');
            $this->db->select('v_pack_size.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = sticker_details.pack_size_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('sticker_stock_in.id',$item_id);
            $this->db->where('sticker_details.revision',1);
            $this->db->order_by('sticker_details.id','ASC');
            $data['stock_in_sticker']=$this->db->get()->result_array();

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['title']="Edit Stock In (sticker)";
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
        $old_item=array();
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
            $old_item=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
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

        // Getting old quantities and current stocks and counting total quantity
        $variety_ids=array();
        $old_quantities=array();
        $current_stocks=array();
        $quantity_total=0;
        if(isset($items))
        {
            foreach($items as $item)
            {
                $variety_ids[$item['variety_id']]=$item['variety_id'];
                $quantity_total+=$item['quantity'];
            }
            $current_stocks=System_helper::get_raw_stock($variety_ids);

            if($id>0)
            {
                $results=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_sticker_details'),'*',array('stock_in_id ='.$id,'revision ='.'1'));
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
            if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['quantity']=='' ||(!($item['quantity']>=0)))
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
                    $old_value=$old_quantities[$item['variety_id']][$item['pack_size_id']]['quantity'];
                    if($old_value>$item['quantity'])
                    {
                        $variance=$old_value-$item['quantity'];
                        $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                        if($variance>$current_stock)
                        {
                            $ajax['status']=false;
                            $ajax['system_message']='This Update('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
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
            $data['date_stock_in']=System_helper::get_time($item_head['date_stock_in']);
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_stock_in_raw_sticker'),$data,array('id='.$id));

            $data=array(); //Details data
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_in_raw_sticker_details'),$data,array('revision=1','stock_in_id='.$id));

            $this->db->where('stock_in_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_stock_in_raw_sticker_details'));

            foreach($items as $item)
            {
                $data=array(); //Details data
                $data['stock_in_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity'];
                $data['revision']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_in_raw_sticker_details'),$data,false);

                //summary calculation
                $data=array(); //Summary data
                $data['in_stock']=0;
                $data['in_excess']=0;
                if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']]))
                {
                    $variance=$item['quantity']-$old_quantities[$item['variety_id']][$item['pack_size_id']]['quantity'];
                    if($old_item['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $data['in_excess']=$variance;
                    }
                    else
                    {
                        $data['in_stock']=$variance;
                    }
                }
                else//new entry
                {
                    if($old_item['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $data['in_excess']=$item['quantity'];
                    }
                    else
                    {
                        $data['in_stock']=$item['quantity'];
                    }
                }
                //fixing current stock table
                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
                {
                    $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']+$data['in_excess']+$data['in_stock'];
                    $data['in_excess']=$data['in_excess']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_excess'];
                    $data['in_stock']=$data['in_stock']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_stock'];
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));
                }
                else
                {
                    $data['variety_id'] = $item['variety_id'];
                    $data['pack_size_id'] = $item['pack_size_id'];
                    $data['packing_item'] = $packing_item;
                    $data['current_stock'] = $data['in_excess']+$data['in_stock'];
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
            $data['date_stock_in']=System_helper::get_time($item_head['date_stock_in']);
            $data['purpose']=$item_head['purpose'];
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            $item_id=Query_helper::add($this->config->item('table_sms_stock_in_raw_sticker'),$data);

            foreach($items as $item)
            {
                $data=array(); //Details Data
                $data['stock_in_id']=$item_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity'];
                $data['revision']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_in_raw_sticker_details'),$data,false);

                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
                {
                    $data=array(); //Summary Data
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $data['in_stock']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_stock']);
                    }
                    elseif($item_head['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $data['in_excess']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_excess']);
                    }
                    $data['current_stock'] = ($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']);
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
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $data['in_stock']=$item['quantity'];
                    }
                    elseif($item_head['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $data['in_excess']=$item['quantity'];
                    }
                    $data['current_stock'] = $item['quantity'];
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
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            //$data['item']=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            $this->db->from($this->config->item('table_sms_stock_in_raw_sticker').' raw_sticker');
            $this->db->select('raw_sticker.*');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_created','ui_created.user_id = raw_sticker.user_created','LEFT');
            $this->db->select('ui_created.name user_created_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated','ui_updated.user_id = raw_sticker.user_updated','LEFT');
            $this->db->select('ui_updated.name user_updated_full_name');
            $this->db->where('raw_sticker.id',$item_id);
            $this->db->where('raw_sticker.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_stock_in_raw_sticker_details').' master_details');
            $this->db->select('master_details.variety_id, master_details.pack_size_id, master_details.quantity');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = master_details.variety_id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = master_details.pack_size_id','LEFT');
            $this->db->select('v_pack_size.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->where('master_details.stock_in_id',$item_id);
            $this->db->where('master_details.revision',1);
            $this->db->order_by('master_details.id','ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="Stock In (Sticker) Details :: ".Barcode_helper::get_barcode_raw_sticker_stock_in($item_id);
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

            $data['item']=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_stock_in_raw_sticker_details').' master_details');
            $this->db->select('master_details.variety_id, master_details.pack_size_id, master_details.quantity');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = master_details.variety_id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' v_pack_size','v_pack_size.id = master_details.pack_size_id','LEFT');
            $this->db->select('v_pack_size.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->where('master_details.stock_in_id',$item_id);
            $this->db->where('master_details.revision',1);
            $this->db->where('master_details.quantity > 0');
            $this->db->order_by('master_details.id','ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="Stock In (Sticker) Print :: ".Barcode_helper::get_barcode_raw_sticker_stock_in($item_id);
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
            $item=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);

            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_stock_in_raw_sticker').' sticker_stock_in');
            $this->db->select('sticker_stock_in.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id,sticker_details.quantity');
            $this->db->join($this->config->item('table_sms_stock_in_raw_sticker_details').' sticker_details','sticker_details.stock_in_id = sticker_stock_in.id','INNER');
            $this->db->where('sticker_stock_in.id',$item_id);
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

            //Negative Stock Checking

            foreach($results as $result)
            {
                if(isset($current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]))
                {
                    $current_stock=$current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock'];
                    if($result['quantity']>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Delete('.$result['variety_id'].'-'.$result['pack_size_id'].'-'.$packing_item.'-'.$result['quantity'].') will make current stock negative.';
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
            $data=array(); //Main data
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_stock_in_raw_sticker'),$data,array('id='.$item_id));

            foreach($results as $result)
            {
                $data=array(); //Summary data
                $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock']-$result['quantity']);
                if($result['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                {
                    $data['in_stock']=$current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['in_stock']-$result['quantity'];
                }
                elseif($result['purpose']==$this->config->item('system_purpose_variety_excess'))
                {
                    $data['in_excess']=$current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['in_excess']-$result['quantity'];
                }
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
            $this->form_validation->set_rules('item[date_stock_in]',$this->lang->line('LABEL_date_stock_in'),'required');
            $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
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
        $data['date_stock_in']= 1;
        $data['purpose']= 1;
        $data['quantity_total_kg']= 1;
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