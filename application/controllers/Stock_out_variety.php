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
        elseif($action=='details')
        {
            $this->system_details($id);
        }
        elseif($action=='delete')
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
        $this->db->select('variety.name variety_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('pack.name pack_name');
        $this->db->select('warehouse.name warehouse_name');
        $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_out.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = stock_out.pack_size_id','LEFT');
        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = stock_out.warehouse_id','INNER');
        $this->db->where('stock_out.status',$this->config->item('system_status_active'));
        $this->db->order_by('stock_out.date_stock_out','DESC');
        $this->db->order_by('stock_out.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if(!$item['pack_name'])
            {
                $item['pack_name']='Bulk';
                $item['quantity']=number_format($item['quantity'],3).' kg';
            }
            else
            {
                $item['pack_name']=$item['pack_name'].' gm';
                $item['quantity']=$item['quantity'].' packet';
            }

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
            $data['title']="Stock Out Here";
            $data["item"] = Array(
                'id'=>'',
                'date_stock_out' => $time,
                'purpose' => '',
                'remarks' => ''
            );
            
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['varieties']=array();
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['packs']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

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
            $this->db->select('variety.name variety_name');
            $this->db->select('type.name crop_type_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('pack.name pack_name');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->select('summary.current_stock');
            $this->db->select('district.name district_name');
            $this->db->select('territory.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');

            $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_out.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = stock_out.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = stock_out.warehouse_id','INNER');
            $this->db->join($this->config->item('table_sms_stock_summary_variety').' summary','summary.variety_id = stock_out.variety_id AND summary.pack_size_id = stock_out.pack_size_id AND summary.warehouse_id = stock_out.warehouse_id','INNER');
            
            $this->db->join($this->config->item('table_login_csetup_cus_info').' customer','customer.customer_id = stock_out.customer_id AND customer.revision=1','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_districts').' district','district.id = customer.district_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = district.territory_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = territory.zone_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','LEFT');
            $this->db->where('stock_out.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }
            if($data['item']['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already Deleted';
                $this->json_return($ajax);
            }
            if(!$data['item']['pack_name'])
            {
                $data['item']['pack_name']='Bulk';
            }
            else
            {
                $data['item']['pack_name']=$data['item']['pack_name'].' gm';
            }
            $data['title']="Edit Stock Out";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
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

            $this->db->select('stock_out.*');
            $this->db->select('variety.name variety_name');
            $this->db->select('type.name crop_type_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('pack.name pack_name');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->select('summary.current_stock');
            $this->db->select('district.name district_name');
            $this->db->select('territory.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');

            $this->db->from($this->config->item('table_sms_stock_out_variety').' stock_out');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = stock_out.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = variety.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = stock_out.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = stock_out.warehouse_id','INNER');
            $this->db->join($this->config->item('table_sms_stock_summary_variety').' summary','summary.variety_id = stock_out.variety_id AND summary.pack_size_id = stock_out.pack_size_id AND summary.warehouse_id = stock_out.warehouse_id','INNER');
            
            $this->db->join($this->config->item('table_login_csetup_cus_info').' customer','customer.customer_id = stock_out.customer_id AND customer.revision=1','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_districts').' district','district.id = customer.district_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = district.territory_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = territory.zone_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','LEFT');
            $this->db->where('stock_out.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }
            if(!$data['item']['pack_name'])
            {
                $data['item']['pack_name']='Bulk';
            }
            else
            {
                $data['item']['pack_name']=$data['item']['pack_name'].' gm';
            }
            $data['title']="Edit Stock Out";
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
            $result_stock=Query_helper::get_info($this->config->item('table_sms_stock_out_variety'),'*',array('id='.$item_id),1);
            if(!$result_stock)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->json_return($ajax);
            }
            elseif($result_stock['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already Deleted';
                $this->json_return($ajax);
            }
            else
            {
                $result_summary=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'id,current_stock',array('variety_id ='.$result_stock['variety_id'],'pack_size_id ='.$result_stock['pack_size_id'],'warehouse_id ='.$result_stock['warehouse_id']),1);

                $this->db->trans_start();  //DB Transaction Handle START
                
                $data_delete=array();
                $data_delete['status'] = $this->config->item('system_status_delete');
                $data_delete['date_updated'] = $time;
                $data_delete['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_out_variety'),$data_delete,array('id='.$item_id));

                $data_summary=array();
                $data_summary['current_stock']=$result_summary['current_stock']+$result_stock['quantity'];
                $data_summary['date_updated'] = $time;
                $data_summary['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data_summary,array('id='.$result_summary['id']));
                
                $this->db->trans_complete();   //DB Transaction Handle END
                if ($this->db->trans_status() === TRUE)
                {
                    $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                    $this->system_list();
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->json_return($ajax);
                }
            }
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

        $data = $this->input->post('item');
        
        if($id>0)
        {
            $result=Query_helper::get_info($this->config->item('table_sms_stock_out_variety'),'*',array('id='.$id,'status="'.$this->config->item('system_status_active').'"'),1);
            if(!$result)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->json_return($ajax);
            }
            else
            {
                if($result['quantity']==$data['quantity'] && $result['remarks']==$data['remarks'] && $result['date_stock_out']==System_helper::get_time($data['date_stock_out']))
                {
                    $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                    $this->system_list();
                }
                else
                {
                    $stock_summary=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'current_stock',array('variety_id ='.$result['variety_id'],'pack_size_id ='.$result['pack_size_id'],'warehouse_id ='.$result['warehouse_id']),1);
                    $current_stock=$stock_summary['current_stock']+$result['quantity']-$data['quantity'];
                    if($current_stock<0)
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='Stock Out exceed. Please contact store incharge.';
                        $this->json_return($ajax);
                    }

                    $this->db->trans_start(); //DB Transaction Handle START

                    $data['date_stock_out'] = System_helper::get_time($data['date_stock_out']);
                    $data['date_updated'] = $time;
                    $data['user_updated'] = $user->user_id;
                    Query_helper::update($this->config->item('table_sms_stock_out_variety'),$data,array('id='.$id));
                    
                    $data_summary=array();
                    $data_summary['date_updated']=$time;
                    $data_summary['user_updated']=$user->user_id;
                    $data_summary['current_stock']=$current_stock;
                    $data_summary[$result['purpose']]=$data['quantity'];
                    Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data_summary,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'warehouse_id='.$result['warehouse_id']));

                    $this->db->trans_complete(); //DB Transaction Handle START
                    if ($this->db->trans_status() === TRUE)
                    {
                        $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                        $this->system_list();
                    }
                    else
                    {
                        $ajax['status']=false;
                        $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                        $this->json_return($ajax);
                    }
                }
            }
        }
        else
        {
            $this->db->trans_begin(); //DB Transaction Handle START
            $items=$this->input->post('items');
            if(!$items)
            {
                $ajax['status']=false;
                $ajax['system_message']='At least one variety need to stock out.';
                $this->json_return($ajax);
            }
            
            $data['date_stock_out']=System_helper::get_time($data['date_stock_out']);
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['status']=$this->config->item('system_status_active');

            $data_summary=array();
            $purpose=$data['purpose'];
            $data_summary['date_updated']=$time;
            $data_summary['user_updated']=$user->user_id;

            foreach($items as $item)
            {
                if($item['variety_id']==0 || $item['pack_size_id']<0 || $item['warehouse_id']==0 || $item['quantity']=='')
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Unfinished stock out entry.';
                    $this->json_return($ajax);
                }
            }
            foreach($items as $item)
            {
                $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'current_stock',array('variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id'],'warehouse_id ='.$item['warehouse_id']),1);
                if(!$result)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='You submit stock out entries with <b>Not Found</b> status stock';
                    $this->json_return($ajax);
                }
                $current_stock=$result['current_stock']-$item['quantity'];
                if($current_stock<0)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Stock Out exceeded. Please contact with store incharge.';
                    $this->json_return($ajax);
                }
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['warehouse_id']=$item['warehouse_id'];
                $data['quantity']=$item['quantity'];
                Query_helper::add($this->config->item('table_sms_stock_out_variety'),$data);
                
                $data_summary[$purpose]=$data['quantity'];
                $data_summary['current_stock']=$current_stock;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data_summary,array('variety_id='.$data['variety_id'],'pack_size_id='.$data['pack_size_id'],'warehouse_id='.$data['warehouse_id']));
            }
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
        $this->form_validation->set_rules('item[quantity]',$this->lang->line('LABEL_QUANTITY'),'required');
        $this->form_validation->set_rules('item[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_OUT'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
