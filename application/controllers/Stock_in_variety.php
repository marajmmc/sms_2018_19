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
        $this->load->helper('barcode_helper');
        $this->lang->load('purpose');
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
            $item['generated_id']=Barcode_helper::get_barcode_stock_in($item['id']);
            $item['purpose']=$this->lang->line('PURPOSE_'.$item['purpose']);
            $item['quantity_total']=$item['quantity_total'];
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock In";
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
            $data['stock_in_varieties']=array();
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
            $data['item']=Query_helper::get_info($this->config->item('table_sms_stock_in_variety'),'*',array('status ="'.$this->config->item('system_status_active').'"','id ='.$item_id),1);

            $this->db->select('stock_in.*');
            $this->db->select('type.name crop_type_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('variety.name variety_name');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->select('ware_house.name ware_house_name');
            $this->db->select('stock_in_details.variety_id, stock_in_details.pack_size_id, stock_in_details.warehouse_id, stock_in_details.quantity');
            $this->db->from($this->config->item('table_sms_stock_in_variety').' stock_in');
            $this->db->join($this->config->item('table_sms_stock_in_variety_details').' stock_in_details','stock_in_details.stock_in_id = stock_in.id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_in_details.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = stock_in_details.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = stock_in_details.warehouse_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('stock_in.id',$item_id);
            $this->db->where('stock_in_details.revision',1);
            $data['stock_in_varieties']=$this->db->get()->result_array();
            foreach($data['stock_in_varieties'] as &$result)
            {
                if($result['pack_size_id']==0)
                {
                    $result['pack_size_name']='Bulk';
                }
            }
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['varieties']=array();
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Edit Stock In";
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
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
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
        $items=$this->input->post('items');
        if(isset($items))
        {
            /* --Start-- for checking incomplete entry (add more row) & Duplicate Entry Checking*/
            $duplicate_entry_checker=array();
            foreach($items as $item)
            {
                if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['warehouse_id']==0 || $item['quantity']<0)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Unfinished stock in entry.';
                    $this->json_return($ajax);
                }

                if(isset($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                {
                    $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]=false;
                }
                else
                {
                    $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]=true;
                }
                if($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]==false)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Please You are trying to entry duplicate variety.';
                    $this->json_return($ajax);
                }

            }

            /* --Start-- for counting total quantity of stock in*/
            $pack_size=array();
            $packs=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            foreach($packs as $pack)
            {
                $pack_size[$pack['value']]=$pack['text'];
            }
            $quantity_total=0;
            foreach($items as $item)
            {
                if($item['pack_size_id']!=0)
                {
                    $quantity_total+=(($pack_size[$item['pack_size_id']])*($item['quantity'])/1000);

                }else
                {
                    $quantity_total+=$item['quantity'];
                }
            }
            /* --End-- for counting total quantity of stock in*/
        }
        else
        {
            /*--Start-- Minimum variety entry checking*/
            $ajax['status']=false;
            $ajax['system_message']='At least one variety need to stock in.';
            $this->json_return($ajax);
            /*--End-- Minimum variety entry checking*/
        }
        $this->db->trans_start();  //DB Transaction Handle START
        $item_head = $this->input->post('item');
        if($id>0)
        {
            $data=array();
            /* --Start-- Item saving (In three table consequently)*/
            $data['date_stock_in']=System_helper::get_time($item_head['date_stock_in']);
            $data['purpose']=$item_head['purpose'];
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_active');
            Query_helper::update($this->config->item('table_sms_stock_in_variety'),$data,array('id='.$id));

            /*Getting Old details data of selected row in which revision 1 exist*/
            /*** it can be used as replace of old input taking*****/
            $results=Query_helper::get_info($this->config->item('table_sms_stock_in_variety_details'),'*',array('stock_in_id ='.$id,'revision ='.'1'));
            $old_items=array();
            foreach($results as $result)
            {
                $old_items[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]=$result;
            }
            foreach($items as $item)
            {
                if(isset($old_items[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                {
                    if($old_items[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['quantity']!=$item['quantity'])
                    {
                        $data_details=array();
                        $data_details_old=array();
                        $this->db->where('stock_in_id',$id);
                        $this->db->where('variety_id',$item['variety_id']);
                        $this->db->where('pack_size_id',$item['pack_size_id']);
                        $this->db->where('warehouse_id',$item['warehouse_id']);
                        $this->db->set('revision', 'revision+1', FALSE);
                        $data_details_old['date_updated'] = $time;
                        $data_details_old['user_updated'] = $user->user_id;
                        $this->db->update($this->config->item('table_sms_stock_in_variety_details'),$data_details_old);

                        $data_details['stock_in_id']=$id;
                        $data_details['variety_id']=$item['variety_id'];
                        $data_details['pack_size_id']=$item['pack_size_id'];
                        $data_details['warehouse_id']=$item['warehouse_id'];
                        $data_details['quantity']=$item['quantity'];
                        $data_details['revision']=1;
                        $data_details['user_created']=$user->user_id;
                        $data_details['date_created']=$time;
                        Query_helper::add($this->config->item('table_sms_stock_in_variety_details'),$data_details);
                    }
                }
                else
                {
                    $data_details=array();
                    $data_details['stock_in_id']=$id;
                    $data_details['variety_id']=$item['variety_id'];
                    $data_details['pack_size_id']=$item['pack_size_id'];
                    $data_details['warehouse_id']=$item['warehouse_id'];
                    $data_details['quantity']=$item['quantity'];
                    $data_details['revision']=1;
                    $data_details['user_created']=$user->user_id;
                    $data_details['date_created']=$time;
                    Query_helper::add($this->config->item('table_sms_stock_in_variety_details'),$data_details);
                }
                /****** It will be changed.... have to get current stock by variety ids*******/
                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id'],'warehouse_id ='.$item['warehouse_id']),1);
                $s_data=array(); //it will be for summary data
                // For getting previous quantity amount to update in_stock and in_excess column
                $old_quantity=$this->input->post('old_quantity'); /////****** have to ommit... it will come from query
                if($result)
                {
                    ///// ****when old quantity exist then it works...... ***////
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($item['quantity']>$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']])
                            {
                                $s_data['in_stock']=($result['in_stock']+($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]));
                            }
                            elseif($item['quantity']<$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']])
                            {
                                $s_data['in_stock']=($result['in_stock']-($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']));
                            }
                        }
                        else
                        {
                            //This condition will be effective when same variety_id,packsize_id and warehouse_id was added in previous invoice
                            $s_data['in_stock']=$result['in_stock']+$item['quantity'];
                        }
                    }
                    elseif($item_head['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($item['quantity']>$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']])
                            {
                                $s_data['in_excess']=$result['in_excess']+($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                            }
                            elseif($item['quantity']<$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']])
                            {
                                $s_data['in_excess']=$result['in_excess']-($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                        }else
                        {
                            //This condition will be effective when same variety_id,packsize_id and warehouse_id was added in summary stock table from any previous invoice

                            $s_data['in_excess']=$result['in_excess']+$item['quantity'];
                        }
                    }
                    if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                    {
                        if($item['quantity']>$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']])
                        {
                            $s_data['current_stock']=$result['current_stock']+($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                        }
                        elseif($item['quantity']<$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']])
                        {
                            /****** From here invalid quantity check will be omitted.... *******/
                            $invalid_quantity_check=($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            if($invalid_quantity_check>$result['current_stock'])
                            {
                                $ajax['status']=false;
                                $ajax['system_message']='Please You are trying to input invalid quantity.';
                                $this->json_return($ajax);
                            }
                            else
                            {
                                $s_data['current_stock']=$result['current_stock']-($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                        }
                    }
                    else
                    {
                        //This condition will be effective when same variety_id,packsize_id and warehouse_id was added in summary stock table from any previous invoice
                        $s_data['current_stock']=$result['current_stock']+$item['quantity'];
                    }
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result['id']));
                }
                else
                {

                    ///// ****  when old quantity not exist then it works...... ***////

                    $s_data['variety_id'] = $item['variety_id'];
                    $s_data['pack_size_id'] = $item['pack_size_id'];
                    $s_data['warehouse_id'] = $item['warehouse_id'];
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=$item['quantity'];
                    }
                    elseif($item_head['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=$item['quantity'];
                    }
                    $s_data['current_stock'] = $item['quantity'];
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$s_data);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In three table consequently)*/
            $data=array();
            $data['date_stock_in']=System_helper::get_time($item_head['date_stock_in']);
            $data['purpose']=$item_head['purpose'];
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            $item_id=Query_helper::add($this->config->item('table_sms_stock_in_variety'),$data);
            foreach($items as $item)
            {
                $data_details=array();
                $data_details['stock_in_id']=$item_id;
                $data_details['variety_id']=$item['variety_id'];
                $data_details['pack_size_id']=$item['pack_size_id'];
                $data_details['warehouse_id']=$item['warehouse_id'];
                $data_details['quantity']=$item['quantity'];
                $data_details['user_created']=$user->user_id;
                $data_details['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_in_variety_details'),$data_details);

                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id'],'warehouse_id ='.$item['warehouse_id']),1);
                if($result)
                {
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=($item['quantity']+$result['in_stock']);
                    }
                    elseif($item_head['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=($item['quantity']+$result['in_excess']);
                    }
                    $s_data['current_stock'] = ($item['quantity']+$result['current_stock']);
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result['id']));
                }
                else
                {
                    $s_data['variety_id'] = $item['variety_id'];
                    $s_data['pack_size_id'] = $item['pack_size_id'];
                    $s_data['warehouse_id'] = $item['warehouse_id'];
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_stock_in'))
                    {
                        $s_data['in_stock']=$item['quantity'];
                    }
                    elseif($item_head['purpose']==$this->config->item('system_purpose_variety_excess'))
                    {
                        $s_data['in_excess']=$item['quantity'];
                    }
                    $s_data['current_stock'] = $item['quantity'];
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$s_data);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
        }
        $this->db->trans_complete();   //DB Transaction Handle END
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