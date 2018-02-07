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
            $result_warehouses=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $warehouses=array();


            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['crop_name']= 1;
            $data['system_preference_items']['crop_type']= 1;
            $data['system_preference_items']['variety']= 1;
            $data['system_preference_items']['pack_size']= 1;
            foreach($result_warehouses as $warehouse)
            {
                $warehouses[$warehouse['name']]=$warehouse['name'];
                $data['system_preference_items'][$warehouse['name']]= 1;
            }
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

        $warehouse_db=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
        $warehouses=array();
        foreach($warehouse_db as $result)
        {
            $warehouses[$result['name']]=$result['name'];
        }

        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        /*$this->db->select('stock_summary_variety.current_stock');
        $this->db->select('stock_summary_variety.warehouse_id');
        $this->db->select('stock_summary_variety.variety_id');
        $this->db->select('stock_summary_variety.pack_size_id');*/
        $this->db->select('stock_summary_variety.*');
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
        $varieties=array();
        $warehouses=array();
        $warehouse_stock=array();
        $warehouse_crop_stock=array();
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
            $warehouses[$result['warehouse_name']]=$result['warehouse_name'];

            $varieties[$result['variety_id']][$result['pack_size_id']]['crop_id']=$result['crop_id'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['crop_name']=$result['crop_name'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['crop_type_name']=$result['crop_type_name'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['variety_id']=$result['variety_id'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['variety_name']=$result['variety_name'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['pack_size_id']=$result['pack_size_id'];
            $varieties[$result['variety_id']][$result['pack_size_id']]['pack_size_name']=$pack_size_name;
            //$varieties[$result['variety_id']][$result['pack_size_id']]['current_stock']=0;
            if(isset($varieties[$result['variety_id']][$result['pack_size_id']][$result['warehouse_name']]))
            {
                $varieties[$result['variety_id']][$result['pack_size_id']][$result['warehouse_name']]+=$result['current_stock'];
            }
            else
            {
                $varieties[$result['variety_id']][$result['pack_size_id']][$result['warehouse_name']]=$result['current_stock'];
            }
            if(isset($warehouse_stock[$result['warehouse_name']]))
            {
                $warehouse_stock[$result['warehouse_name']]+=$result['current_stock'];
            }
            else
            {
                $warehouse_stock[$result['warehouse_name']]=$result['current_stock'];
            }
            if(isset($warehouse_crop_stock[$result['crop_id']][$result['warehouse_name']]))
            {
                $warehouse_crop_stock[$result['crop_id']][$result['warehouse_name']]+=$result['current_stock'];
            }
            else
            {
                $warehouse_crop_stock[$result['crop_id']][$result['warehouse_name']]=$result['current_stock'];
            }

        }

        $count=0;
        $prev_crop_name='';
        $prev_crop_id='';
        $prev_crop_type_name='';
        $prev_variety_name='';
        foreach($varieties as $variety)
        {
            foreach($variety as $pack)
            {
                if($count>0)
                {
                    if($prev_crop_name!=$pack['crop_name'])
                    {
                        $prev_crop_name=$pack['crop_name'];
                        $prev_crop_type_name=$pack['crop_type_name'];
                        $prev_variety_name=$pack['variety_name'];
                        $items[]=$this->get_total_crop($prev_crop_id,$warehouses,$warehouse_crop_stock);
                        $prev_crop_id=$pack['crop_id'];
                    }
                    elseif($prev_crop_type_name!=$pack['crop_type_name'])
                    {
                        $prev_crop_type_name=$pack['crop_type_name'];
                        $prev_variety_name=$pack['variety_name'];
                        $pack['crop_name']='';
                    }
                    elseif($prev_variety_name!=$pack['variety_name'])
                    {
                        $prev_variety_name=$pack['variety_name'];
                        $pack['crop_name']='';
                        $pack['crop_type_name']='';
                    }
                    else
                    {
                        $pack['crop_name']='';
                        $pack['crop_type_name']='';
                        $pack['variety_name']='';
                    }
                }
                else
                {
                    $prev_crop_id=$pack['crop_id'];
                    $prev_crop_name=$pack['crop_name'];
                    $prev_crop_type_name=$pack['crop_type_name'];
                    $prev_variety_name=$pack['variety_name'];
                }
                $count++;

                $items[]=$this->get_variety($pack, $warehouses);
            }
        }

        $items[]=$this->get_grand_total_variety($warehouse_db, $warehouse_stock);
        $this->json_return($items);
    }
    private function get_variety($info,$warehouses)
    {
        $row=array();
        $row['crop_name']=$info['crop_name'];
        $row['crop_type']=$info['crop_type_name'];
        $row['variety']=$info['variety_name'];
        $row['pack_size']=$info['pack_size_name'];
        //$row['current_stock']='';//$info['current_stock'];
        foreach($warehouses as $warehouse_name=>$stock)
        {
            if(isset($info[$warehouse_name]))
            {
                $row[$warehouse_name]=$info[$warehouse_name];
            }
            else
            {
                $row[$warehouse_name]='--';
            }
        }
        return $row;
    }
    private function get_total_crop($crop_id,$warehouses,$warehouse_crop_stock)
    {
        $info=array();
        $info['crop_name']='';
        $info['crop_type']='Crop Total';
        $info['variety']='';
        $info['pack_size']='';
        foreach($warehouses as $warehouse_name=>$stock)
        {
            if(isset($warehouse_crop_stock[$crop_id][$warehouse_name]))
            {
                $info[$warehouse_name]=$warehouse_crop_stock[$crop_id][$warehouse_name];
            }
            else
            {
                $info[$warehouse_name]='--';
            }
        }
        $info['current_stock_kg']='';
        return $info;
    }
    private function get_grand_total_variety($warehouse_db, $warehouse_stock)
    {
        $row=array();
        $row['crop_name']='Grand Total';
        $row['crop_type']='';
        $row['variety']='';
        $row['pack_size']='';
        foreach($warehouse_db as $result)
        {
            $row[$result['name']]=isset($warehouse_stock[$result['name']])?$warehouse_stock[$result['name']]:'--';
        }
        $row['current_stock_kg']=0;//number_format($current_stock_kg,3,'.','');
        return $row;
    }
}
