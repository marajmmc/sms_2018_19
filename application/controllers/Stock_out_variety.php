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
        $items=array();
        $this->db->select('stock_out.*');
        $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
        $this->db->where('stock_out.status',$this->config->item('system_status_active'));
        $this->db->order_by('stock_out.date_stock_out','DESC');
        $this->db->order_by('stock_out.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_stock_out']=System_helper::display_date($item['date_stock_out']);
            $item['generated_id']=System_helper::get_generated_id($this->config->item('system_id_prefix_stock_out'),$item['id']);

            if($item['purpose']==$this->config->item('system_purpose_variety_sample'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_OUT_PURPOSE_SAMPLE');
            }
            elseif($item['purpose']==$this->config->item('system_purpose_variety_short_inventory'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_OUT_PURPOSE_SHORT');
            }
            elseif($item['purpose']==$this->config->item('system_purpose_variety_demonstration'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_OUT_PURPOSE_DEMONSTRATION');
            }
            elseif($item['purpose']==$this->config->item('system_purpose_variety_rnd'))
            {
                $item['purpose']=$this->lang->line('LABEL_STOCK_OUT_PURPOSE_RND');
            }
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add",$data,true));
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


            $this->db->select('stock_out.*');
            $this->db->select('customer_info.name customers_name');
            $this->db->select('districts.name district_name, districts.id district_id');
            $this->db->select('territory.name territory_name, territory.id territory_id');
            $this->db->select('zones.name zone_name, zones.id zone_id');
            $this->db->select('divisions.name division_name, divisions.id division_id');
            $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' customer_info','customer_info.customer_id = stock_out.customer_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = customer_info.district_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = districts.territory_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territory.zone_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','LEFT');
            $this->db->where('stock_out.id',$item_id);
            $this->db->where('stock_out.status',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            $this->db->select('stock_out.*');
            $this->db->select('type.name crop_type_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('variety.name variety_name');
            $this->db->select('v_pack_size.name pack_size_name');
            $this->db->select('ware_house.name ware_house_name');
            $this->db->select('stock_out_details.variety_id, stock_out_details.pack_size_id, stock_out_details.warehouse_id, stock_out_details.quantity');
            $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
            $this->db->join($this->config->item('table_sms_stock_out_variety_details').' stock_out_details','stock_out_details.stock_out_id = stock_out.id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_out_details.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' v_pack_size','v_pack_size.id = stock_out_details.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' ware_house','ware_house.id = stock_out_details.warehouse_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('stock_out.id',$item_id);
            $this->db->where('stock_out_details.revision',1);
            $data['stock_out_varieties']=$this->db->get()->result_array();
            foreach($data['stock_out_varieties'] as &$result)
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
            $data['title']="Edit Stock Out";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add",$data,true));
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
            if(!$this->check_validation_edit())
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
            /* --Start-- for checking incomplete entry (add more row)*/
            foreach($items as $item)
            {
                if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['warehouse_id']==0 || $item['quantity']=='')
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Unfinished stock Out entry.';
                    $this->json_return($ajax);
                }
            }
            /* --End-- for checking incomplete entry (add more row)*/

            /* --Start-- Duplicate Entry Checking*/
            $duplicate_entry_checker=array();
            foreach($items as $item)
            {
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
            }
            /* --End-- Duplicate Entry Checking*/

            /* --Start-- for counting total quantity of stock out*/
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
            /* --End-- for counting total quantity of stock out*/

            /*--Start-- When Stock out quantity entry exceeded current stock quantity */
            foreach($items as $item)
            {
                $valid_quantity_checker=0;
                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id'],'warehouse_id ='.$item['warehouse_id']),1);
                if(!$result)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='One of your submitted variety is out of Stock';
                    $this->json_return($ajax);
                }
                if($id>0)
                {
                    $old_quantity=$this->input->post('old_quantity');
                    if(isset($old_quantity))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>$item['quantity'])
                            {
                                $valid_quantity_checker=$result['current_stock']+($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]<$item['quantity'])
                            {
                                $valid_quantity_checker=$result['current_stock']-($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                            }
                        }
                    }
                }else
                {
                    $valid_quantity_checker=$result['current_stock']-$item['quantity'];

                }
                if($valid_quantity_checker<0)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Stock Out exceeded. Please less your stockout quantity or Contact with store in-charge.';
                    $this->json_return($ajax);
                }
            }
            /*--End-- When Stock out quantity entry exceeded current stock quantity */
        }else
        {
            /*--Start-- Minimum variety entry checking*/
            $ajax['status']=false;
            $ajax['system_message']='At least one variety need to stock out.';
            $this->json_return($ajax);
            /*--End-- Minimum variety entry checking*/
        }
        $this->db->trans_start();  //DB Transaction Handle START
        $item_head = $this->input->post('item');
        if($id>0)
        {
            $data=array();
            /* --Start-- Item saving (In three table consequently)*/
            $data['date_stock_out']=System_helper::get_time($item_head['date_stock_out']);
            $data['purpose']=$item_head['purpose'];
            $data['remarks']=$item_head['remarks'];
            $data['quantity_total']=$quantity_total;
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_active');
            Query_helper::update($this->config->item('table_sms_stock_out_variety'),$data,array('id='.$id));

            /*Getting Old details data of selected row in which revision 1 exist*/

            $results=Query_helper::get_info($this->config->item('table_sms_stock_out_variety_details'),'*',array('stock_out_id ='.$id,'revision ='.'1'));
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
                        $this->db->where('stock_out_id',$id);
                        $this->db->where('variety_id',$item['variety_id']);
                        $this->db->where('pack_size_id',$item['pack_size_id']);
                        $this->db->where('warehouse_id',$item['warehouse_id']);
                        $this->db->set('revision', 'revision+1', FALSE);
                        $data_details_old['date_updated'] = $time;
                        $data_details_old['user_updated'] = $user->user_id;
                        $this->db->update($this->config->item('table_sms_stock_out_variety_details'),$data_details_old);
                        $data_details['stock_out_id']=$id;
                        $data_details['variety_id']=$item['variety_id'];
                        $data_details['pack_size_id']=$item['pack_size_id'];
                        $data_details['warehouse_id']=$item['warehouse_id'];
                        $data_details['quantity']=$item['quantity'];
                        $data_details['revision']=1;
                        $data_details['user_created']=$user->user_id;
                        $data_details['date_created']=$time;
                        Query_helper::add($this->config->item('table_sms_stock_out_variety_details'),$data_details);
                    }
                }else
                {
                    $data_details=array();
                    $data_details['stock_out_id']=$id;
                    $data_details['variety_id']=$item['variety_id'];
                    $data_details['pack_size_id']=$item['pack_size_id'];
                    $data_details['warehouse_id']=$item['warehouse_id'];
                    $data_details['quantity']=$item['quantity'];
                    $data_details['revision']=1;
                    $data_details['user_created']=$user->user_id;
                    $data_details['date_created']=$time;
                    Query_helper::add($this->config->item('table_sms_stock_out_variety_details'),$data_details);
                }
                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id'],'warehouse_id ='.$item['warehouse_id']),1);
                $s_data=array(); //it will be for summary data
                // For getting previous quantity amount to update in_stock and in_excess column
                $old_quantity=$this->input->post('old_quantity');
                if($result)
                {
                    //$valid_quantity_checker=0;
                    $old_quantity=$this->input->post('old_quantity');
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_rnd'))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>$item['quantity'])
                            {
                                $s_data['out_stock_rnd']=$result['out_stock_rnd']+($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]<$item['quantity'])
                            {
                                $s_data['out_stock_rnd']=$result['out_stock_rnd']-($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                            }
                        }else
                        {
                            $s_data['out_stock_rnd']=($result['out_stock_rnd']-$item['quantity']);
                        }
                    }
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_short_inventory'))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>$item['quantity'])
                            {
                                $s_data['out_stock_short_inventory']=$result['out_stock_short_inventory']+($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]<$item['quantity'])
                            {
                                $s_data['out_stock_short_inventory']=$result['out_stock_short_inventory']-($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                            }
                        }else
                        {
                            $s_data['out_stock_short_inventory']=($result['out_stock_short_inventory']-$item['quantity']);
                        }
                    }
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_demonstration'))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>$item['quantity'])
                            {
                                $s_data['out_stock_demonstration']=$result['out_stock_demonstration']+($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]<$item['quantity'])
                            {
                                $s_data['out_stock_demonstration']=$result['out_stock_demonstration']-($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                            }
                        }else
                        {
                            $s_data['out_stock_demonstration']=($result['out_stock_demonstration']-$item['quantity']);
                        }
                    }
                    if($item_head['purpose']==$this->config->item('system_purpose_variety_sample'))
                    {
                        if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                        {
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>$item['quantity'])
                            {
                                $s_data['out_stock_sample']=$result['out_stock_sample']+($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                            }
                            if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]<$item['quantity'])
                            {
                                $s_data['out_stock_sample']=$result['out_stock_sample']-($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                            }
                        }else
                        {
                            $s_data['out_stock_sample']=($result['out_stock_sample']-$item['quantity']);
                        }
                    }

                    if(isset($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]))
                    {
                        if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]>$item['quantity'])
                        {
                            $s_data['current_stock']=$result['current_stock']+($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]-$item['quantity']);
                        }
                        if($old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]<$item['quantity'])
                        {
                            $s_data['current_stock']=$result['current_stock']-($item['quantity']-$old_quantity[$item['variety_id']][$item['pack_size_id']][$item['warehouse_id']]);
                        }
                    }else
                    {
                        $s_data['current_stock']=($result['current_stock']-$item['quantity']);
                    }
                    $s_data['date_updated'] = $time;
                    $s_data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result['id']));
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='One of your submitted variety is absent in stock.Please Check it';
                    $this->json_return($ajax);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
        }
        else
        {
            /* --Start-- Item saving (In three table consequently)*/
            $data=array();
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
                $data_details=array();
                $data_details['stock_out_id']=$item_id;
                $data_details['variety_id']=$item['variety_id'];
                $data_details['pack_size_id']=$item['pack_size_id'];
                $data_details['warehouse_id']=$item['warehouse_id'];
                $data_details['quantity']=$item['quantity'];
                $data_details['user_created']=$user->user_id;
                $data_details['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_stock_out_variety_details'),$data_details);
                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'*',array('variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id'],'warehouse_id ='.$item['warehouse_id']),1);

                $purpose=$item_head['purpose'];
                $s_data=array();
                $s_data[$purpose]=$item['quantity'];
                $s_data['current_stock']=$result['current_stock']-$item['quantity'];
                $s_data['date_updated']=$time;
                $s_data['user_updated']=$user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$s_data,array('id='.$result['id']));
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
        $data=$this->input->post('item');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_OUT'),'required');
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
        return true;
    }
    private function check_validation_edit()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_OUT'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
