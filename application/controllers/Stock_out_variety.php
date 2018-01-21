<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_out_variety extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Stock_out_variety');
        $this->controller_url='stock_out_variety';
        $this->load->helper('barcode_helper');
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
            $data['title']='Stock Out List';
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
        $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
        $this->db->select('stock_out.*');
        $this->db->where('stock_out.status',$this->config->item('system_status_active'));
        $this->db->order_by('stock_out.date_stock_out','DESC');
        $this->db->order_by('stock_out.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_stock_out']=System_helper::display_date($item['date_stock_out']);
            $item['barcode']=Barcode_helper::get_barcode_stock_out($item['id']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $time=time();
            $data['title']="Stock Out";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_out' => $time,
                'purpose' => '',
                'remarks' => '',
                'division_id' => '',
                'zone_id' => '',
                'territory_id' => '',
                'district_id' => '',
                'customer_id' => '',
                'customer_name' => ''
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['varieties']=array();
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['stock_out_varieties']=array();
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
            $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
            $this->db->select('stock_out.*');
            $this->db->select('customer_info.name customers_name');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' customer_info','customer_info.customer_id = stock_out.customer_id','LEFT');
            $this->db->select('districts.name district_name, districts.id district_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = customer_info.district_id','LEFT');
            $this->db->select('territory.name territory_name, territory.id territory_id');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = districts.territory_id','LEFT');
            $this->db->select('zones.name zone_name, zones.id zone_id');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territory.zone_id','LEFT');
            $this->db->select('divisions.name division_name, divisions.id division_id');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','LEFT');
            $this->db->where('stock_out.id',$item_id);
            $this->db->where('stock_out.status',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
            $this->db->select('stock_out.*');
            $this->db->select('stock_out_details.variety_id, stock_out_details.pack_size_id, stock_out_details.warehouse_id, stock_out_details.quantity');
            $this->db->join($this->config->item('table_sms_stock_out_variety_details').' stock_out_details','stock_out_details.stock_out_id = stock_out.id','INNER');
            $this->db->select('variety.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_out_details.variety_id','INNER');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = stock_out_details.pack_size_id','LEFT');
            $this->db->select('ware_house.name ware_house_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = stock_out_details.warehouse_id','INNER');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('stock_out.id',$item_id);
            $this->db->where('stock_out_details.revision',1);
            $data['stock_out_varieties']=$this->db->get()->result_array();
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Edit Stock Out";
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
        /*--Start-- Permission Checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $old_item=Query_helper::get_info($this->config->item('table_sms_stock_out_variety'),'*',array('status ="'.$this->config->item('system_status_active').'"','id ='.$id),1);
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

        // Getting old quantities and current stocks
        $items=$this->input->post('items');
        $variety_ids=array();
        $old_quantities=array();
        $current_stocks=array();
        if(isset($items))
        {
            foreach($items as $item)
            {
                $variety_ids[$item['variety_id']]=$item['variety_id'];
            }
            $current_stocks=System_helper::get_variety_stock($variety_ids);

            if($id>0)
            {
                $results=Query_helper::get_info($this->config->item('table_sms_stock_out_variety_details'),'*',array('stock_out_id ='.$id,'revision ='.'1'));
                foreach($results as $result)
                {
                    $old_quantities[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]=$result;
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
            if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['warehouse_id']==0 || $item['quantity']=='' ||(!($item['quantity']>=0)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Unfinished stock Out entry.';
                $this->json_return($ajax);
            }
            if(isset($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
            {
                $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]=false;
            }else
            {
                $duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]=true;
            }
            if($duplicate_entry_checker[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]==false)
            {
                $ajax['status']=false;
                $ajax['system_message']='Please You are trying to entry duplicate variety.';
                $this->json_return($ajax);
            }


            // Negative current stock checking
            if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock']))
            {
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'];
                if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                {
                    $old_value=$old_quantities[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['quantity'];
                    if($item['quantity']>$old_value)
                    {
                        $variance=$item['quantity']-$old_value;
                        if($variance>$current_stock)
                        {
                            $ajax['status']=false;
                            $ajax['system_message']='This Update('.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['warehouse_id'].'-'.$old_value.'-'.$item['quantity'].') will make current stock negative.';
                            $this->json_return($ajax);
                        }
                    }
                }
                else
                {
                    if($item['quantity']>$current_stock)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='This Insert'.$item['variety_id'].' Pack size id: '.$item['pack_size_id'].' Warehouse id: '.$item['warehouse_id'].' Quantity: '.$item['quantity'].' will make current stock negative.';
                        $this->json_return($ajax);
                    }
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='This Item'.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['warehouse_id'].'-'.$item['quantity'].' is absent in stock.';
                $this->json_return($ajax);
            }
        }

        /* --Start-- for counting total quantity of stock out*/
        $pack_size=array();
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        foreach($results as $result)
        {
            $pack_size[$result['value']]=$result['text'];
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
        /* --End-- for counting total quantity of stock out*/

        $this->db->trans_start();  //DB Transaction Handle START
        $item_head = $this->input->post('item');
        if($id>0)
        {
            /* --Start-- Item saving (In three table consequently)*/

            $data=array();//main data
            $data['date_stock_out']=System_helper::get_time($item_head['date_stock_out']);
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            Query_helper::update($this->config->item('table_sms_stock_out_variety'),$data,array('id='.$id));

            $data=array();//Details data
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_sms_stock_out_variety_details'),$data,array('revision=1','stock_out_id='.$id));

            $this->db->where('stock_out_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_stock_out_variety_details'));

            foreach($items as $item)
            {
                $data=array();//Details data
                $data['stock_out_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['warehouse_id']=$item['warehouse_id'];
                $data['quantity']=$item['quantity'];
                $data['revision']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_out_variety_details'),$data);
                $data=array(); //summary data
                if(isset($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                {
                    $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'];
                    if(isset($old_quantities[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                    {
                        $old_value=$old_quantities[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['quantity'];
                        if($item_head['purpose']==$this->config->item('system_purpose_variety_rnd'))
                        {
                            $data['out_stock_rnd']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_rnd']-$item['quantity']+$old_value);
                        }
                        elseif($item_head['purpose']==$this->config->item('system_purpose_variety_short_inventory'))
                        {
                            $data['out_stock_short_inventory']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_short_inventory']-$item['quantity']+$old_value);
                        }
                        elseif($item_head['purpose']==$this->config->item('system_purpose_variety_demonstration'))
                        {
                            $data['out_stock_demonstration']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_demonstration']-$item['quantity']+$old_value);
                        }
                        elseif($item_head['purpose']==$this->config->item('system_purpose_variety_sample'))
                        {
                            $data['out_stock_sample']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_sample']-$item['quantity']+$old_value);
                        }
                        $data['current_stock']=($current_stock-$item['quantity']+$old_value);
                    }else
                    {
                        if($item_head['purpose']==$this->config->item('system_purpose_variety_rnd'))
                        {
                            $data['out_stock_rnd']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_rnd']-$item['quantity']);
                        }
                        elseif($item_head['purpose']==$this->config->item('system_purpose_variety_short_inventory'))
                        {
                            $data['out_stock_short_inventory']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_short_inventory']-$item['quantity']);
                        }
                        elseif($item_head['purpose']==$this->config->item('system_purpose_variety_demonstration'))
                        {
                            $data['out_stock_demonstration']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_demonstration']-$item['quantity']);
                        }
                        elseif($item_head['purpose']==$this->config->item('system_purpose_variety_sample'))
                        {
                            $data['out_stock_sample']=($current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_sample']-$item['quantity']);
                        }
                        $data['current_stock']=($current_stock-$item['quantity']);
                    }
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['warehouse_id']));
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='This Item: '.$item['variety_id'].'-'.$item['pack_size_id'].'-'.$item['warehouse_id'].'-'.$item['quantity'].' is absent in stock.';
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
            $data['customer_id']=$item_head['customer_id'];
            $data['customer_name']=$item_head['customer_name'];
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');
            $item_id=Query_helper::add($this->config->item('table_sms_stock_out_variety'),$data);
            foreach($items as $item)
            {
                $data=array(); //Details Data
                $data['stock_out_id']=$item_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['warehouse_id']=$item['warehouse_id'];
                $data['quantity']=$item['quantity'];
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_out_variety_details'),$data);
                $data=array(); //Summary Data
                if($item_head['purpose']==$this->config->item('system_purpose_variety_rnd'))
                {
                    $data['out_stock_rnd']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_rnd']);
                }
                elseif($item_head['purpose']==$this->config->item('system_purpose_variety_short_inventory'))
                {
                    $data['out_stock_short_inventory']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_short_inventory']);
                }
                elseif($item_head['purpose']==$this->config->item('system_purpose_variety_demonstration'))
                {
                    $data['out_stock_demonstration']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_demonstration']);
                }
                elseif($item_head['purpose']==$this->config->item('system_purpose_variety_sample'))
                {
                    $data['out_stock_sample']=($item['quantity']+$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['out_stock_sample']);
                }
                $current_stock=$current_stocks[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]['current_stock'];
                $data['current_stock']=($current_stock-$item['quantity']);
                $data['date_updated']=$time;
                $data['user_updated']=$user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id'],'warehouse_id='.$item['warehouse_id']));
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
    private function check_validation()
    {
        $id = $this->input->post("id");
        $data=$this->input->post('item');
        if(!($id>0))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('item[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_IN'),'required');
            $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
            if($data['purpose']==$this->config->item('system_purpose_variety_sample'))
            {
                $this->form_validation->set_rules('item[customer_name]',$this->lang->line('LABEL_CUSTOMER_NAME'),'required');
            }
            if($this->form_validation->run() == FALSE)
            {
                $this->message=validation_errors();
                return false;
            }
        }
        return true;
    }
}
