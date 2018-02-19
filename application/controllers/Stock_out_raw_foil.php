<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_out_raw_foil extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Stock_out_raw_foil');
        $this->controller_url='stock_out_raw_foil';
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['date_stock_out']= 1;
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

            $data['title']='Stock Out (Common Foil) List';
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

        $this->db->from($this->config->item('table_sms_stock_out_raw_foil').' stock_out_foil');
        $this->db->select('stock_out_foil.*');
        $this->db->where('stock_out_foil.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('stock_out_foil.date_stock_out','DESC');
        $this->db->order_by('stock_out_foil.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['date_stock_out']=System_helper::display_date($item['date_stock_out']);
            $item['barcode']=Barcode_helper::get_barcode_raw_foil_stock_out($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock Out (Common Foil)";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_out' => $time,
                'purpose' =>'',
                'quantity' =>'',
                'remarks' => ''
            );

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

            $this->db->from($this->config->item('table_sms_stock_out_raw_foil').' stock_out_foil');
            $this->db->select('stock_out_foil.*');
            $this->db->where('stock_out_foil.status !=',$this->config->item('system_status_delete'));
            $this->db->where('stock_out_foil.id',$item_id);
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

            $data['title']="Edit Stock Out (Common Foil)";
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

        /*--Start-- Permission checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $old_item=Query_helper::get_info($this->config->item('table_sms_stock_out_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
            if(!$old_item)
            {
                System_helper::invalid_try('Save Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $old_value=$old_item['quantity'];
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
        /*--End-- Permission checking */

        //Negative Stock checking
        $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'];
        if($id>0)
        {
            if($item['quantity']>$old_value)
            {
                $variance=$item['quantity']-$old_value;
                if($variance>$current_stock)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Stock Out Common Foil('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                    $this->json_return($ajax);
                }
            }
        }
        else
        {
            if($item['quantity']>$current_stock)
            {
                $ajax['status']=false;
                $ajax['system_message']='This Stock Out Common Foil('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.'-'.$item['quantity'].') will make current stock negative.';
                $this->json_return($ajax);
            }
        }


        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            /* --Start-- Item saving (In two table consequently)*/
            $data=array(); //Main data
            $data['date_stock_out']=System_helper::get_time($item['date_stock_out']);
            $data['purpose']=$old_item['purpose'];
            $data['remarks']=$item['remarks'];
            $data['quantity']=$item['quantity'];
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_stock_out_raw_foil'),$data,array('id='.$id));

            $data=array(); //Summary Data
            $data['out_stock_damage']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['out_stock_damage']-$old_value+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']+$old_value-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));
            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In two table consequently)*/
            $data=array(); //Main Data
            $data['date_stock_out']=System_helper::get_time($item['date_stock_out']);
            $data['purpose']=$item['purpose'];
            $data['quantity']=$item['quantity'];
            $data['remarks']=$item['remarks'];
            $data['revision_count']=1;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            Query_helper::add($this->config->item('table_sms_stock_out_raw_foil'),$data);

            $data=array(); //Summary Data
            $data['out_stock_damage']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['out_stock_damage']+$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']-$item['quantity'];
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_summary_raw'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'packing_item= "'.$packing_item.'"'));
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

            //$data['item']=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            $this->db->from($this->config->item('table_sms_stock_out_raw_foil').' raw_foil');
            $this->db->select('raw_foil.*');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_created','ui_created.user_id = raw_foil.user_created','LEFT');
            $this->db->select('ui_created.name user_created_full_name, ui_created.date_created');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_updated','ui_updated.user_id = raw_foil.user_updated','LEFT');
            $this->db->select('ui_updated.name user_updated_full_name, ui_updated.date_updated');
            $this->db->where('raw_foil.id',$item_id);
            $this->db->where('raw_foil.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $data['title']="Stock Out (Common Foil) Details :: ".Barcode_helper::get_barcode_raw_foil_stock_out($item_id);
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

            $data['item']=Query_helper::get_info($this->config->item('table_sms_stock_in_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Print View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $data['title']="Stock Out (Common Foil) Print :: ".Barcode_helper::get_barcode_raw_foil_stock_in($item_id);
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

            $item=Query_helper::get_info($this->config->item('table_sms_stock_out_raw_foil'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
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

            // Validation Checking
            if(!(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock'])))
            {
                $ajax['status']=false;
                $ajax['system_message']='This Delete('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$packing_item.' is absent in stock.)';
                $this->json_return($ajax);
            }

            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_sms_stock_out_raw_foil'),$data,array('id='.$item_id));

            $data=array(); //Summary data
            $data['out_stock_damage']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['out_stock_damage']-$item['quantity'];
            $data['current_stock']=$current_stocks[$item['variety_id']][$item['pack_size_id']][$packing_item]['current_stock']+$item['quantity'];
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
            $data['system_preference_items']['date_stock_out']= 1;
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
            $this->form_validation->set_rules('item[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_OUT'),'required');
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