<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_lc_lc extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->load->helper('lc');
    }
    public function index($action="search",$id=0)
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
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['principals']=Query_helper::get_info($this->config->item('table_login_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));

            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']='';

            $data['title']="LC Report Search";
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
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }

            $data['options']=$reports;

            $data['system_preference_items']= $this->get_preference_transfer();
            $data['title']="LC Wise Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

            $ajax['status']=true;
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

    private function get_preference_transfer()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_lc"'),1);

        $data['barcode']= 1;
        $data['fiscal_year']= 1;
        $data['month']= 1;
        $data['date_opening']= 1;
        $data['principal_name']= 1;
        $data['lc_number']= 1;
        $data['date_expected']= 1;
        $data['date_awb']= 1;
        $data['date_forwarded_time']= 1;
        $data['date_release']= 1;
        $data['date_released_time']= 1;
        $data['date_receive']= 1;
        $data['date_received_time']= 1;
        $data['date_completed_time']= 1;
        $data['currency_name']= 1;
        $data['quantity_open_kg']= 1;
        $data['status_open_forward']= 1;
        $data['status_release']= 1;
        $data['status_received']= 1;
        $data['status_open']= 1;
        if($result)
        {
            if($result['preferences']!=null)
            {
                $preferences=json_decode($result['preferences'],true);
                foreach($data as $key=>$value)
                {
                    if(isset($preferences[$key]))
                    {
                        $data[$key]=$value;
                    }
                    else
                    {
                        $data[$key]=0;
                    }
                }
            }
        }
        return $data;
    }
    private function system_get_items()
    {
        $date_type=$this->input->post('date_type');

        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');

        $principal_id=$this->input->post('principal_id');

        $status_open_forward=$this->input->post('status_open_forward');
        $status_release=$this->input->post('status_release');
        $status_received=$this->input->post('status_received');
        $status_open=$this->input->post('status_open');

        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');
        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');

        $this->db->group_by('lc.id');
        $this->db->order_by('lc.id','DESC');

        $this->db->where('lc.'.$date_type.'>='.$date_start.' and lc.'.$date_type.'<='.$date_end);

        if($status_open_forward)
        {
            $this->db->where('lc.status_open_forward',$status_open_forward);
        }
        if($status_release)
        {
            $this->db->where('lc.status_release',$status_release);
        }
        if($status_received)
        {
            $this->db->where('lc.status_received',$status_received);
        }
        if($status_open)
        {
            $this->db->where('lc.status_open',$status_open);
        }
        else
        {
            $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
        }

        if($principal_id)
        {
            $this->db->where('lc.principal_id',$principal_id);
        }
        
        $results=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_lc($result['id']);
            $item['fiscal_year']=$result['fiscal_year'];
            $item['month']=$this->lang->line("LABEL_MONTH_$result[month_id]");
            $item['date_opening']=System_helper::display_date($result['date_opening']);
            $item['date_expected']=System_helper::display_date($result['date_expected']);
            $item['date_awb']=System_helper::display_date($result['date_awb']);
            $item['date_forwarded_time']=System_helper::display_date_time($result['date_open_forward']);
            $item['date_release']=System_helper::display_date_time($result['date_release']);
            $item['date_released_time']=System_helper::display_date_time($result['date_release_completed']);
            $item['date_receive']=System_helper::display_date($result['date_receive']);
            $item['date_received_time']=System_helper::display_date_time($result['date_receive_completed']);
            $item['date_completed_time']=System_helper::display_date_time($result['date_receive_completed']);
            $item['principal_name']=$result['principal_name'];
            $item['currency_name']=$result['currency_name'];
            $item['lc_number']=$result['lc_number'];
            $item['consignment_name']=$result['consignment_name'];
            $item['quantity_open_kg']=number_format($result['quantity_open_kg'],3,'.','');
            $item['price_open_other_currency']=number_format($result['price_open_other_currency'],2);
            $item['price_open_variety_currency']=number_format($result['price_open_variety_currency'],2);
            $item['status_open_forward']=$result['status_open_forward'];
            $item['status_release']=$result['status_release'];
            $item['status_received']=$result['status_receive'];
            $item['status_open']=$result['status_open'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference_transfer();
            $data['preference_method_name']='search_lc';
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

            $this->db->from($this->config->item('table_sms_lc_open').' lco');
            $this->db->select('lco.*');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lco.date_opening AND fy.date_end>lco.date_opening','INNER');
            $this->db->select('fy.name fiscal_year');
            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->select('currency.name currency_name');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->select('principal.name principal_name');

            $this->db->join($this->config->item('table_login_setup_bank_account').' ba','ba.id = lco.bank_account_id','INNER');
            $this->db->join($this->config->item('table_login_setup_bank').' bank','bank.id = ba.bank_id','INNER');
            $this->db->select("CONCAT_WS(' ( ',ba.account_number,  CONCAT_WS('', bank.name,' - ',ba.branch_name,')')) bank_account_number");

            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__,$item_id,'Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Lc_helper::get_view_info_basic($data['item']);
            $data['info_lc']=$this->get_view_info_lc($data['item']);

            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->select('v.id variety_id, v.name variety_name');

            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->select('vp.name_import variety_name_import');

            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->select('pack.name pack_size');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','LEFT');
            $this->db->select('crop_type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','LEFT');
            $this->db->select('crop.name crop_name');

            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = lcd.receive_warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');

            $this->db->where('lcd.lc_id',$item_id);
            $this->db->where('lcd.quantity_open >0');
            $this->db->order_by('lcd.id ASC');
            $data['items']=$this->db->get()->result_array();

            $this->db->from($this->config->item('table_sms_lc_expense').' lce');
            $this->db->select('lce.*');
            $this->db->join($this->config->item('table_login_setup_direct_cost_items').' dci','dci.id=lce.dc_id','INNER');
            $this->db->select('dci.name dc_name');
            $this->db->where('lce.lc_id',$item_id);
            $data['dc_items']=$this->db->get()->result_array();

            $results=Query_helper::get_info($this->config->item('table_sms_lc_expense_varieties'),'*',array('lc_id ='.$item_id),0,0,array(''));
            $dc_expenses_varieties=array();
            foreach($results as $result)
            {
                $dc_expenses_varieties[$result['variety_id']][$result['pack_size_id']][$result['dc_id']]=$result;
            }
            $data['dc_expense_varieties']=$dc_expenses_varieties;

            $data['title']="LC Details :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=true;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function get_view_info_lc($lc_info)
    {
        $permission_delete=false;
        if((isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $permission_delete=true;
        }
        $info_basic=array();

        $result=array();
        $result['label_1']=$this->lang->line('LABEL_FISCAL_YEAR');
        $result['value_1']=$lc_info['fiscal_year'];
        $result['label_2']=$this->lang->line('LABEL_MONTH');
        $result['value_2']=date("F", mktime(0, 0, 0,  $lc_info['month_id'],1, 2000));
        $info_basic[]=$result;
        $result=array();
        $result['label_1']=$this->lang->line('LABEL_PRINCIPAL_NAME');
        $result['value_1']=$lc_info['principal_name'];
        $result['label_2']=$this->lang->line('LABEL_LC_NUMBER');
        $result['value_2']=$lc_info['lc_number'];
        $info_basic[]=$result;
        $result=array();
        $result['label_1']=$this->lang->line('LABEL_DATE_OPENING');
        $result['value_1']=System_helper::display_date($lc_info['date_opening']);
        $result['label_2']=$this->lang->line('LABEL_CONSIGNMENT_NAME');
        $result['value_2']=$lc_info['consignment_name'];
        $info_basic[]=$result;
        $result=array();
        $result['label_1']=$this->lang->line('LABEL_DATE_EXPECTED');
        $result['value_1']=System_helper::display_date($lc_info['date_expected']);
        $result['label_2']=$this->lang->line('LABEL_BANK_ACCOUNT_NUMBER');
        $result['value_2']=$lc_info['bank_account_number'];
        $info_basic[]=$result;

        if($permission_delete)
        {
            //hidden in receive task
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_CURRENCY_NAME');
            $result['value_1']=$lc_info['currency_name'];
            if($lc_info['status_open']==$this->config->item('system_status_complete'))
            {
                $result['label_2']=$this->lang->line('LABEL_CURRENCY_RATE');
                $result['value_2']=number_format($lc_info['rate_currency'],2);;
            }
            $info_basic[]=$result;
            //hidden in receive task
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');
            $result['value_1']=number_format($lc_info['price_open_other_currency'],2);;
            if($lc_info['status_release']==$this->config->item('system_status_complete'))
            {
                $result['label_2']=$this->lang->line('LABEL_PRICE_RELEASE_OTHER_CURRENCY');
                $result['value_2']=number_format($lc_info['price_release_other_currency'],2);;
            }
            $info_basic[]=$result;
            //hidden in receive task
            if($lc_info['status_open_forward']==$this->config->item('system_status_yes'))
            {
                $result=array();
                $result['label_1']='AWB Date';
                $result['value_1']=System_helper::display_date($lc_info['date_awb']);
                $result['label_2']='AWB Number';
                $result['value_2']=$lc_info['awb_number'];
                $info_basic[]=$result;
            }
        }
        if($lc_info['status_release']==$this->config->item('system_status_complete'))
        {
            $result=array();
            $result['label_1']='Release Date';
            $result['value_1']=System_helper::display_date($lc_info['date_release']);
            $info_basic[]=$result;
        }
        if($lc_info['status_receive']==$this->config->item('system_status_complete'))
        {
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_DATE_PACKING_LIST');
            $result['value_1']=System_helper::display_date($lc_info['date_packing_list']);
            $result['label_2']=$this->lang->line('LABEL_NUMBER_PACKING_LIST');
            $result['value_2']=$lc_info['packing_list_number'];
            $info_basic[]=$result;
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_DATE_RECEIVE');
            $result['value_1']=System_helper::display_date($lc_info['date_receive']);
            $result['label_2']=$this->lang->line('LABEL_NUMBER_LOT');
            $result['value_2']=$lc_info['lot_number'];
            $info_basic[]=$result;
        }
        return $info_basic;
    }
}
