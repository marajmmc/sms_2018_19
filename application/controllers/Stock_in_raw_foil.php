<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_in_raw_foil extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Stock_in_raw_foil');
        $this->controller_url='stock_in_raw_foil';
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
            $data['system_preference_items']['date_stock_in']= 1;
            $data['system_preference_items']['purpose']= 1;
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

            $data['title']='Stock In (Common Foil) List';
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

        $this->db->from($this->config->item('table_sms_stock_in_raw_foil').' stock_in_foil');
        $this->db->select('stock_in_foil.*');
        $this->db->where('stock_in_foil.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('stock_in_foil.date_stock_in','DESC');
        $this->db->order_by('stock_in_foil.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['date_stock_in']=System_helper::display_date($item['date_stock_in']);
            $item['barcode']=Barcode_helper::get_barcode_raw_foil_stock_in($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock In Common Foil";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_in' => $time,
                'purpose' =>'',
                'quantity' =>'',
                'remarks' => ''
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
            $item['variety_id']=0;
            $item['pack_size_id']=0;
            $packing_item=$this->config->item('system_common_foil');

            $this->db->from($this->config->item('table_sms_stock_in_raw_foil').' stock_in_foil');
            $this->db->select('stock_in_foil.*');
            $this->db->where('stock_in_foil.status !=',$this->config->item('system_status_delete'));
            $this->db->where('stock_in_foil.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $current_stocks=System_helper::get_raw_stock(array($item['variety_id']));

            $data['item']['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];

            $data['title']="Edit Stock In (Common Foil)";
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
        $old_value=0;
        $current_stocks=System_helper::get_raw_stock(array($item['variety_id']));
        $old_item=array();

        /*--Start-- Permission and negative stock checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $old_item=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
            if(!$old_item)
            {
                System_helper::invalid_try('Save Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $old_value=$old_item['quantity'];

            //Negative Stock Checking
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                if($old_value>$item['quantity'])
                {
                    $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                    $variance=$old_value-$item['quantity'];
                    if($variance>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Update('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
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
            $data=array(); //Main data
            $data['date_stock_in']=System_helper::get_time($item['date_stock_in']);
            $data['purpose']=$old_item['purpose'];
            $data['remarks']=$item['remarks'];
            $data['quantity']=$item['quantity'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_stock_in_raw_foil'),$data,array('id='.$id));

            //summary calculation
            $data=array();
            $data['in_stock']=0;
            $data['in_excess']=0;

            //Checking variance
            if($old_value>0)
            {
                $variance=$item['quantity']-$old_value;
                if($old_item['purpose']==$this->config->item('system_purpose_variety_excess'))
                {
                    $data['in_excess']=$variance;
                }
                else
                {
                    $data['in_stock']=$variance;
                }
            }
            else
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

            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In two table consequently)*/
            $data=array(); //Main Data
            $data['date_stock_in']=System_helper::get_time($item['date_stock_in']);
            $data['purpose']=$item['purpose'];
            $data['remarks']=$item['remarks'];
            $data['quantity']=$item['quantity'];
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            Query_helper::add($this->config->item('table_sms_stock_in_raw_foil'),$data);

            $data=array(); //Summary Data
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                if($item['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                {
                    $data['in_stock']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_stock']);
                }
                elseif($item['purpose']==$this->config->item('system_purpose_variety_excess'))
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
                $data['variety_id'] = $item['variety_id'];
                $data['pack_size_id'] = $item['pack_size_id'];
                $data['packing_item'] = $packing_item;
                if($item['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                {
                    $data['in_stock']=$item['quantity'];
                }
                elseif($item['purpose']==$this->config->item('system_purpose_variety_excess'))
                {
                    $data['in_excess']=$item['quantity'];
                }
                $data['current_stock']=$item['quantity'];
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

            $item=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item)
            {
                System_helper::invalid_try('Delete Not Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $item['variety_id']=0;
            $item['pack_size_id']=0;
            $current_stocks=System_helper::get_raw_stock(array($item['variety_id'])); //Getting current stock

            /*--Start-- Validation Checking */

            //Negative Stock Checking
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]))
            {
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
                if($item['quantity']>$current_stock)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Delete From Common Foil ('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$item['quantity'].') will make current stock negative.';
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
            Query_helper::update($this->config->item('table_sms_stock_in_raw_foil'),$data,array('id='.$item_id));

            $data=array(); //Summary data
            $data['current_stock']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']-$item['quantity']);
            if($item['purpose']==$this->config->item('system_purpose_variety_stock_in'))
            {
                $data['in_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_stock']-$item['quantity'];
            }
            elseif($item['purpose']==$this->config->item('system_purpose_variety_excess'))
            {
                $data['in_excess']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['in_excess']-$item['quantity'];
            }
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

    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['date_stock_in']= 1;
            $data['system_preference_items']['purpose']= 1;
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
            $this->form_validation->set_rules('item[date_stock_in]',$this->lang->line('LABEL_DATE_STOCK_IN'),'required');
            $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
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