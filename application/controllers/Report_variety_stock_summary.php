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
            $data['system_preference_items']['crop_name']= 1;
            $data['system_preference_items']['crop_type']= 1;
            $data['system_preference_items']['variety']= 1;
            $data['system_preference_items']['pack_size']= 1;
            $data['system_preference_items']['warehouse']= 1;
            //$data['system_preference_items']['current_stock']= 1;
            $data['system_preference_items']['current_stock_kg']= 1;
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

            $results=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $warehouses=array();
            foreach($results as $result)
            {
                $warehouses[$result['name']]=$result['name'];
            }
            $data['warehouses']=$warehouses;

            $item_head = $this->input->post('report');
            $keys=',';
            foreach($item_head as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }
            $data['keys']=trim($keys,',');

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
        $warehouse_id=$this->input->post('warehouse_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $crop_id=$this->input->post('crop_id');

        $result_warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
        $warehouses=array();
        foreach($result_warehouses as $result)
        {
            $warehouses[$result['name']]=$result['name'];
        }

        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('stock_summary_variety.current_stock, stock_summary_variety.warehouse_id');
        //$this->db->select('SUM(stock_summary_variety.current_stock) current_stock');

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

        $this->db->order_by('crop.id, croptype.id, v.id, pack.id, warehouse.id');
        //$this->db->group_by('stock_summary_variety.variety_id, stock_summary_variety.pack_size_id, stock_summary_variety.warehouse_id');
        //$this->db->group_by('stock_summary_variety.variety_id, stock_summary_variety.pack_size_id');

        if($warehouse_id>0 && is_numeric($warehouse_id))
        {
            $this->db->where('stock_summary_variety.warehouse_id',$warehouse_id);
        }

        if($variety_id>0 && is_numeric($variety_id))
        {
            $this->db->where('stock_summary_variety.variety_id',$variety_id);
        }

        if($pack_size_id>=0 && is_numeric($pack_size_id))
        {
            $this->db->where("stock_summary_variety.pack_size_id ='".$pack_size_id."'");
        }

        if($crop_type_id>0 && is_numeric($crop_type_id))
        {
            $this->db->where('v.crop_type_id',$crop_type_id);
        }

        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('croptype.crop_id',$crop_id);
        }
        $results=$this->db->get()->result_array();




        $items=array();
        $warehouse_quantity=array();

        $current_stock=0;
        $current_stock_kg=0;

        $crop_name='';
        $crop_type_name='';
        $variety_name='';
        $crop_warehouse=array();
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
            $item=array();
            if($crop_name!=$result['crop_name'])
            {
                $item['crop_name']=$result['crop_name'];
                $item['crop_type']=$result['crop_type_name'];
                $item['variety']=$result['variety_name'];
                $crop_name=$result['crop_name'];
                $crop_type_name=$result['crop_type_name'];

                $items[]=$this->get_quantity_total_variety('crop',$result['warehouse_name'], $current_stock);
                $current_stock=0;
            }
            else if($crop_type_name!=$result['crop_type_name'])
            {
                $crop_type_name=$result['crop_type_name'];
                $variety_name=$result['variety_name'];
            }
            else if($variety_name!=$result['variety_name'])
            {
                $variety_name=$result['variety_name'];
            }
            else
            {
                /*$item['crop_name']='';
                $item['crop_type']='';
                $item['variety']='';*/
            }


            $item['variety']=$result['variety_name'];
            $item['pack_size']=$pack_size_name;
            $current_stock+=$result['current_stock'];
            if(isset($warehouses[$result['warehouse_name']]))
            {
                //$item[$result['warehouse_id']]=$result['warehouse_name'];
                $item[$result['warehouse_name']]=number_format($result['current_stock'],3,'.','');
                if(isset($warehouse_quantity[$result['warehouse_name']]))
                {
                    $warehouse_quantity[$result['warehouse_name']]+=$result['current_stock'];
                }
                else
                {
                    $warehouse_quantity[$result['warehouse_name']]=$result['current_stock'];
                }
            }
            //$item['warehouse_name']=$result['warehouse_name'];
            //$item['current_stock']=number_format($result['current_stock'],3,'.','');
            $item['current_stock_kg']=number_format(($result['current_stock']/1000),3,'.','');
            $current_stock_kg+=($result['current_stock']/1000);
            $items[]=$item;
        }
        $row=array();
        $row['crop_name']='Grand Total';
        $row['crop_type']='';
        $row['variety']='';
        $row['pack_size']='';
        foreach($result_warehouses as $result)
        {
            $row[$result['name']]=isset($warehouse_quantity[$result['name']])?number_format($warehouse_quantity[$result['name']],3,'.',''):'--';
        }
        $row['current_stock_kg']=number_format($current_stock_kg,3,'.','');
        $items[]=$row;
        $this->json_return($items);
    }
    private function get_quantity_total_variety($total_type,$warehouse_name, $quantity)
    {
        $item=array();
        $item['crop_name']='';
        if($total_type=="crop")
        {
            $item['crop_type']='Crop Total';
        }

        $item['variety']='';
        $item['pack_size']='';
        /*foreach($crop_warehouse as $crop)
        {
            foreach($crop as $warehouse)
            {
                //$item[$warehouse]=$quantity;
            }
        }*/
        $item[$warehouse_name]=$quantity;

        return $item;
    }
    /*private function system_set_preference()
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
    }*/
}
