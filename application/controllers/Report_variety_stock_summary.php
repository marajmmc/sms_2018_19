<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_variety_stock_summary extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Report_variety_stock_summary');
        $this->controller_url='report_variety_stock_summary';
    }
    public function index($action="search")
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
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
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            /*discussion :: warehouse, crop, pack_size, warehouse :: status needed or no need*/
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']=System_helper::display_date(time());
            $data['title']="Variety Stock Summary Report Search";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
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
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            /*$data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['fiscal_year_name']= 1;
            $data['system_preference_items']['month_name']= 1;
            $data['system_preference_items']['date_opening']= 1;
            $data['system_preference_items']['date_expected']= 1;
            $data['system_preference_items']['principal_name']= 1;
            $data['system_preference_items']['currency_name']= 1;
            $data['system_preference_items']['lc_number']= 1;
            $data['system_preference_items']['consignment_name']= 1;
            $data['system_preference_items']['price_open_other_currency']= 1;
            $data['system_preference_items']['quantity_open_kg']= 1;
            $data['system_preference_items']['price_open_variety_currency']= 1;
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
            }*/

            $item_head = $this->input->post('report');

            $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
            $this->db->select('stock_summary_variety.*');

            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=stock_summary_variety.warehouse_id','INNER');
            $this->db->select('warehouse.name warehouse_name');

            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
            $this->db->select('pack.name pack_size_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_variety.variety_id','INNER');
            $this->db->select('v.name variety_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' croptype','croptype.id=v.crop_type_id','INNER');
            $this->db->select('croptype.id crop_type_id, croptype.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=croptype.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');

            if($item_head['warehouse_id']>0 && is_numeric($item_head['warehouse_id']))
            {
                $this->db->where('stock_summary_variety.warehouse_id',$item_head['warehouse_id']);
            }

            if($item_head['variety_id']>0 && is_numeric($item_head['variety_id']))
            {
                $this->db->where('stock_summary_variety.variety_id',$item_head['variety_id']);
            }

            if($item_head['pack_size_id']>0 && is_numeric($item_head['pack_size_id']))
            {
                $this->db->where('stock_summary_variety.pack_size_id',$item_head['pack_size_id']);
            }

            if($item_head['crop_type_id']>0 && is_numeric($item_head['crop_type_id']))
            {
                $this->db->where('v.crop_type_id',$item_head['crop_type_id']);
            }

            if($item_head['crop_id']>0 && is_numeric($item_head['crop_id']))
            {
                $this->db->where('croptype.crop_id',$item_head['crop_id']);
            }
            $results=$this->db->get()->result_array();
            $items=array();
            $warehouse=array();
            foreach($results as $result)
            {
                if($result['pack_size_name']==0)
                {
                    $pack_size_name="Bulk";
                }
                else
                {
                    $pack_size_name=$result['pack_size_name'];
                }
                $warehouse[$result['warehouse_id']]=$result['warehouse_name'];
                /*$items[$result['crop_id']]['crop_name']=$result['crop_name'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['crop_type_name']=$result['crop_type_name'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['variety_name']=$result['variety_name'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['pack_size_name']=$pack_size_name;
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['warehouse_name']=$result['warehouse_name'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_stock']=$result['in_stock'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_excess']=$result['in_excess'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_transfer_warehouse']=$result['in_transfer_warehouse'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_convert_bulk_pack']=$result['in_convert_bulk_pack'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_purchase']=$result['in_purchase'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_sales_return']=$result['in_sales_return'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_sample']=$result['out_stock_sample'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_rnd']=$result['out_stock_rnd'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_demonstration']=$result['out_stock_demonstration'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_short_inventory']=$result['out_stock_short_inventory'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_transfer_warehouse']=$result['out_transfer_warehouse'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_convert_bulk_pack']=$result['out_convert_bulk_pack'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_sales']=$result['out_sales'];
                $items[$result['crop_id']]['crop_type'][$result['crop_type_id']]['variety'][$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['current_stock']=$result['current_stock'];*/


                $items[$result['variety_id']]['crop_name']=$result['crop_name'];
                $items[$result['variety_id']]['crop_type_name']=$result['crop_type_name'];
                $items[$result['variety_id']]['variety_name']=$result['variety_name'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['pack_size_name']=$pack_size_name;
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['warehouse_name']=$result['warehouse_name'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_stock']=$result['in_stock'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_excess']=$result['in_excess'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_transfer_warehouse']=$result['in_transfer_warehouse'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_convert_bulk_pack']=$result['in_convert_bulk_pack'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_purchase']=$result['in_purchase'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['in_sales_return']=$result['in_sales_return'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_sample']=$result['out_stock_sample'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_rnd']=$result['out_stock_rnd'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_demonstration']=$result['out_stock_demonstration'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_stock_short_inventory']=$result['out_stock_short_inventory'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_transfer_warehouse']=$result['out_transfer_warehouse'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_convert_bulk_pack']=$result['out_convert_bulk_pack'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['out_sales']=$result['out_sales'];
                $items[$result['variety_id']]['pack_size'][$result['pack_size_id']]['warehouse'][$result['warehouse_id']]['current_stock']=$result['current_stock'];

            }
            $data['items']=$items;
            $data['warehouse']=$warehouse;
            $data['title']="Variety Stock Summary Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $item_head = $this->input->post('report');

        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('stock_summary_variety.*');

        $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id=stock_summary_variety.warehouse_id','INNER');
        $this->db->select('warehouse.name warehouse_name');

        $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id=stock_summary_variety.pack_size_id','INNER');
        $this->db->select('pack.name pack_size_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=stock_summary_variety.variety_id','INNER');
        $this->db->select('v.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' croptype','croptype.id=v.crop_type_id','INNER');
        $this->db->select('croptype.id crop_type_id, croptype.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=croptype.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');

        if($item_head['warehouse_id']>0 && is_numeric($item_head['warehouse_id']))
        {
            $this->db->where('stock_summary_variety.warehouse_id',$item_head['warehouse_id']);
        }

        if($item_head['variety_id']>0 && is_numeric($item_head['variety_id']))
        {
            $this->db->where('stock_summary_variety.variety_id',$item_head['variety_id']);
        }

        if($item_head['pack_size_id']>0 && is_numeric($item_head['pack_size_id']))
        {
            $this->db->where('stock_summary_variety.pack_size_id',$item_head['pack_size_id']);
        }

        if($item_head['pack_size_id']>0 && is_numeric($item_head['pack_size_id']))
        {
            $this->db->where('v.pack_size_id',$item_head['crop_id']);
        }

        if($item_head['crop_id']>0 && is_numeric($item_head['crop_id']))
        {
            $this->db->where('croptype.crop_id',$item_head['crop_id']);
        }
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $items[$result['crop_id']]['crop_name']=$result['crop_name'];
            $items[$result['crop_id']]['crop_type']=$result['crop_name'];
        }

        /*$this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->select('fy.name fiscal_year_name');
        $this->db->select('principal.name principal_name');
        $this->db->select('currency.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lc.fiscal_year_id','INNER');
        $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->where('lc.status_open_forward',$this->config->item('system_status_no'));
        $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
        $this->db->order_by('lc.fiscal_year_id','DESC');
        $this->db->order_by('lc.id','DESC');
        $results=$this->db->get()->result_array();

        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_lc($result['id']);
            $item['fiscal_year_name']=$result['fiscal_year_name'];
            $item['month_name']=$this->lang->line("LABEL_MONTH_$result[month_id]");
            $item['date_opening']=System_helper::display_date($result['date_opening']);
            $item['date_expected']=System_helper::display_date($result['date_expected']);
            $item['principal_name']=$result['principal_name'];
            $item['currency_name']=$result['currency_name'];
            $item['lc_number']=$result['lc_number'];
            $item['consignment_name']=$result['consignment_name'];
            $item['quantity_open_kg']=number_format($result['quantity_open_kg'],3);
            $item['price_open_other_currency']=number_format($result['price_open_other_currency'],2);
            $item['price_open_variety_currency']=number_format($result['price_open_variety_currency'],2);
            $item['status_open_forward']=$result['status_open_forward'];
            $items[]=$item;
        }*/
        $items[]=[];
        $this->json_return($items);
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['fiscal_year_name']= 1;
            $data['system_preference_items']['month_name']= 1;
            $data['system_preference_items']['date_opening']= 1;
            $data['system_preference_items']['date_expected']= 1;
            $data['system_preference_items']['principal_name']= 1;
            $data['system_preference_items']['currency_name']= 1;
            $data['system_preference_items']['lc_number']= 1;
            $data['system_preference_items']['consignment_name']= 1;
            $data['system_preference_items']['price_open_other_currency']= 1;
            $data['system_preference_items']['quantity_open_kg']= 1;
            $data['system_preference_items']['price_open_variety_currency']= 1;
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
}
