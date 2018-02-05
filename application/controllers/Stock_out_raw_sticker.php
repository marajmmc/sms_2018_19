<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_out_raw_sticker extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Stock_out_raw_sticker');
        $this->controller_url='stock_out_raw_sticker';
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
            $data['system_preference_items']['date_stock_out']= 1;
            $data['system_preference_items']['purpose']= 1;
            $data['system_preference_items']['quantity_total']= 1;
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

            $data['title']='Stock Out (Sticker) List';
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
        $this->db->from($this->config->item('table_sms_stock_out_raw_sticker').' stock_out');
        $this->db->select('stock_out.*');
        $this->db->where('stock_out.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('stock_out.date_stock_out','DESC');
        $this->db->order_by('stock_out.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_stock_out']=System_helper::display_date($item['date_stock_out']);
            $item['barcode']=Barcode_helper::get_barcode_raw_sticker_stock_out($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock Out (Sticker)";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_out' => $time,
                'purpose' => '',
                'remarks' => '',
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['stock_out_sticker']=array();
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
            $data['item']=Query_helper::get_info($this->config->item('table_sms_stock_out_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_stock_out_raw_sticker').' sticker_stock_out');
            $this->db->select('sticker_stock_out.*');
            $this->db->select('sticker_details.variety_id, sticker_details.pack_size_id, sticker_details.quantity');
            $this->db->join($this->config->item('table_sms_stock_out_raw_sticker_details').' sticker_details','sticker_details.stock_out_id = sticker_stock_out.id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = sticker_details.variety_id','INNER');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = sticker_details.pack_size_id','LEFT');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('sticker_stock_out.id',$item_id);
            $this->db->where('sticker_details.revision',1);
            $this->db->order_by('sticker_details.id','ASC');
            $data['stock_out_sticker']=$this->db->get()->result_array();

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['title']="Edit Stock Out (Sticker)";
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
            $old_item=Query_helper::get_info($this->config->item('table_sms_stock_out_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
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

        // Getting old quantities and current stocks and counting total
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
                $results=Query_helper::get_info($this->config->item('table_sms_stock_out_raw_sticker_details'),'*',array('stock_out_id ='.$id,'revision ='.'1'));
                foreach($results as $result)
                {
                    $old_quantities[$result['variety_id']][$result['pack_size_id']]=$result;
                }
            }
        }
        else
        {
            /*--Start-- Minimum variety entry checking*/
            $ajax['status']=false;
            $ajax['system_message']='At least one variety need to stock out.';
            $this->json_return($ajax);
            /*--End-- Minimum variety entry checking*/
        }

        /*--Start-- Validation Checking*/
        //checking incomplete entry (add more row) & Duplicate Entry Checking & Negative current stock checking
        $duplicate_entry_checker=array();
        foreach($items as $item)
        {
            if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['quantity']=='' ||(!($item['quantity']>=0)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Unfinished stock Out entry.';
                $this->json_return($ajax);
            }
            if(isset($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]))
            {
                $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]=false;
            }else
            {
                $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]=true;
            }
            if($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']]==false)
            {
                $ajax['status']=false;
                $ajax['system_message']='Please You are trying to entry duplicate variety.';
                $this->json_return($ajax);
            }


            // Negative current stock checking

            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']]))
                {
                    $old_value=$old_quantities[$item['variety_id']][$item['pack_size_id']]['quantity'];
                    if($item['quantity']>$old_value)
                    {
                        $variance=$item['quantity']-$old_value;
                        if($variance>$current_stock)
                        {
                            $ajax['status']=false;
                            $ajax['system_message']='This Update('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                            $this->json_return($ajax);
                        }
                    }
                }
                else
                {
                    if($item['quantity']>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Insert('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['quantity'].' will make current stock negative.)';
                        $this->json_return($ajax);
                    }
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='This Item('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['quantity'].' is absent in stock.)';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            /* --Start-- Item saving (In three table consequently)*/

            $data=array();//main data
            $data['date_stock_out']=System_helper::get_time($item_head['date_stock_out']);
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_stock_out_raw_sticker'),$data,array('id='.$id));

            $data=array();//Details data
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_out_raw_sticker_details'),$data,array('revision=1','stock_out_id='.$id));

            $this->db->where('stock_out_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_stock_out_raw_sticker_details'));

            foreach($items as $item)
            {
                $data=array();//Details data
                $data['stock_out_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity'];
                $data['revision']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_out_raw_sticker_details'),$data);
                $data=array(); //summary data
                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
                {
                    $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                    if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']]))
                    {
                        $old_value=$old_quantities[$item['variety_id']][$item['pack_size_id']]['quantity'];
                        if($old_item['purpose']==$this->config->item('system_purpose_raw_stock_damage'))
                        {
                            $data['out_stock_damage']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['out_stock_damage']-$old_value+$item['quantity']);
                        }
                        $data['current_stock']=($current_stock-$item['quantity']+$old_value);
                    }else
                    {
                        if($old_item['purpose']==$this->config->item('system_purpose_raw_stock_damage'))
                        {
                            $data['out_stock_damage']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['out_stock_damage']+$item['quantity']);
                        }
                        $data['current_stock']=($current_stock-$item['quantity']);
                    }
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Item:('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['quantity'].' is absent in stock.)';
                    $this->json_return($ajax);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In three table consequently)*/
            $data=array();//Main Data
            $data['date_stock_out']=System_helper::get_time($item_head['date_stock_out']);
            $data['purpose']=$item_head['purpose'];
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            $item_id=Query_helper::add($this->config->item('table_sms_stock_out_raw_sticker'),$data);
            foreach($items as $item)
            {
                $data=array(); //Details Data
                $data['stock_out_id']=$item_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity'];
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_out_raw_sticker_details'),$data);
                $data=array(); //Summary Data
                if($item_head['purpose']==$this->config->item('system_purpose_raw_stock_damage'))
                {
                    $data['out_stock_damage']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['out_stock_damage']);
                }

                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                $data['current_stock']=($current_stock-$item['quantity']);
                $data['date_updated']=$time;
                $data['user_updated']=$user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));
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
            $packing_item=$this->config->item('system_master_foil');
            $item=Query_helper::get_info($this->config->item('table_sms_stock_out_raw_sticker'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_stock_out_raw_sticker').' stock_out');
            $this->db->select('stock_out.*');
            $this->db->select('stock_out_details.variety_id, stock_out_details.pack_size_id,stock_out_details.quantity');
            $this->db->join($this->config->item('table_sms_stock_out_raw_sticker_details').' stock_out_details','stock_out_details.stock_out_id = stock_out.id','INNER');
            $this->db->where('stock_out.id',$item_id);
            $this->db->where('stock_out_details.revision',1);
            $this->db->order_by('stock_out_details.id','ASC');
            $results=$this->db->get()->result_array();

            // Getting current stocks
            $variety_ids=array();
            foreach($results as $result)
            {
                $variety_ids[$result['variety_id']]=$result['variety_id'];
            }
            $current_stocks=System_helper::get_raw_stock($variety_ids);

            // Validation Checking
            foreach($results as $result)
            {
                if(!(isset($current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock'])))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Delete('.$result['variety_id'].'-'.$result['pack_size_id'].'-'.$result['quantity'].' is absent in stock.)';
                    $this->json_return($ajax);
                }
            }

            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_stock_out_raw_sticker'),$data,array('id='.$item_id));

            foreach($results as $result)
            {
                $data=array();
                $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['current_stock']+$result['quantity']);
                if($result['purpose']==$this->config->item('system_purpose_raw_stock_damage'))
                {
                    $data['out_stock_damage']=$current_stocks[$result['variety_id']][$result['pack_size_id']][$packing_item]['out_stock_damage']-$result['quantity'];
                }
                Query_helper::update($this->config->item('table_sms_stock_summary_sticker'),$data,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'packing_item="'.$packing_item.'"'));
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

    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['date_stock_out']= 1;
            $data['system_preference_items']['purpose']= 1;
            $data['system_preference_items']['quantity_total']= 1;
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
            $this->form_validation->set_rules('item[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_OUT'),'required');
            $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }

}