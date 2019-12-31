<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_average_rate_calculation extends Root_Controller
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
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_NUMBER_OF_VARIETY']='Number of Variety';
        $this->lang->language['LABEL_NUMBER_OF_LC_RATE_RECEIVE']='Number of (Rate Receive) LC';
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Receive LC List (Average Rate Calculator)";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $this->db->from($this->config->item('table_sms_lc_details').' details');
        $this->db->select('details.*');
        $this->db->select('COUNT(details.variety_id) as number_of_variety');
        $this->db->select('COUNT(CASE WHEN details.rate_weighted_receive > 0 THEN rate_weighted_receive END) as number_of_lc_rate_receive');
        $this->db->join($this->config->item('table_sms_lc_open').' lc','lc.id = details.lc_id','INNER');
        $this->db->where('lc.status_receive', $this->config->item('system_status_complete'));
        $this->db->where('details.quantity_open >0');
        $this->db->group_by('details.lc_id');
        //$this->db->group_by('details.variety_id, details.pack_size_id');
        $results=$this->db->get()->result_array();
        $info_lc=array();
        foreach($results as $result)
        {
            $info_lc[$result['lc_id']]['number_of_variety']=$result['number_of_variety'];
            $info_lc[$result['lc_id']]['number_of_lc_rate_receive']=$result['number_of_lc_rate_receive'];
        }
        //$config_date_start=$data['item']['date']=System_helper::get_time(Lc_helper::$LC_DATE_INITIAL_AVERAGE_RATE);
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');
        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');

        $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
        $this->db->where('lc.status_receive',$this->config->item('system_status_complete'));
        //$this->db->where('lc.status_rate_weighted_receive',$this->config->item('system_status_no'));
        //$this->db->where('lc.date_receive > ',$config_date_start);
        $this->db->order_by('lc.id','DESC');
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_lc($result['id']);
            $item['fiscal_year']=$result['fiscal_year'];
            $item['month']=$this->lang->line("LABEL_MONTH_$result[month_id]");
            $item['date_opening']=System_helper::display_date($result['date_opening']);
            $item['date_receive']=System_helper::display_date($result['date_receive']);
            $item['principal_name']=$result['principal_name'];
            $item['currency_name']=$result['currency_name'];
            $item['lc_number']=$result['lc_number'];
            $item['quantity_open_kg']=number_format($result['quantity_open_kg'],3,'.','');
            $item['status_release']=$result['status_release'];
            $item['status_received']=$result['status_receive'];
            $item['number_of_variety']=isset($info_lc[$result['id']])?$info_lc[$result['id']]['number_of_variety']:0;
            $item['number_of_lc_rate_receive']=isset($info_lc[$result['id']])?$info_lc[$result['id']]['number_of_lc_rate_receive']:0;
            $items[]=$item;
        }
        $this->json_return($items);
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
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__,$item_id,'Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']!=$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC receive not completed.';
                $this->json_return($ajax);
            }

            $data['info_basic']=Lc_helper::get_view_info_basic($data['item']);
            $data['info_lc']=$this->get_view_info_lc($data['item']);

            $this->db->from($this->config->item('table_sms_lc_details').' details');
            $this->db->select('details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = details.variety_id','INNER');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = details.pack_size_id','LEFT');
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','LEFT');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','LEFT');
            $this->db->select('crop.name crop_name');
            $this->db->where('details.lc_id',$item_id);
            $this->db->where('details.quantity_open >0');
            $this->db->order_by('details.id ASC');
            $data['items']=$this->db->get()->result_array();
            $variety_ids=array();
            $variety_ids[0]=0;
            foreach($data['items'] as $result)
            {
                $variety_ids[$result['variety_id']]=$result['variety_id'];
            }
            $pack_sizes=array();
            $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array());
            foreach($results as $result)
            {
                $pack_sizes[$result['value']]=$result['text'];
            }
            $pack_sizes['0']=1000;
            $date_end=$data['item']['date_receive'];
            $lc_id=$data['item']['id'];

            $data['stock_opening']=$this->get_opening_stock($date_end, $variety_ids, $pack_sizes);
            $data['previous_rates']=$this->get_previous_rates($date_end,$variety_ids);
            $data['receive_rates']=$this->get_receive_rates($data['item'],$data['items']);

            /*echo '<pre>';
            print_r($data['receive_rates']);
            echo '</pre>';*/

            $data['title']="LC Details & LC Average Rate Calculation :: ".Barcode_helper::get_barcode_lc($item_id);
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
            $ajax['status']=true;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
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
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
        $data['barcode']= 1;
        $data['fiscal_year']= 1;
        $data['month']= 1;
        $data['date_opening']= 1;
        $data['date_receive']= 1;
        $data['principal_name']= 1;
        $data['currency_name']= 1;
        $data['lc_number']= 1;
        $data['quantity_open_kg']= 1;
        $data['number_of_variety']= 1;
        $data['number_of_lc_rate_receive']= 1;
        /*$data['status_release']= 1;
        $data['status_received']= 1;*/
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
    private function get_view_info_lc($lc_info)
    {
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
        //hidden in open complete task
        $result=array();
        $result['label_1']=$this->lang->line('LABEL_CURRENCY_NAME');
        $result['value_1']=$lc_info['currency_name'];
        if($lc_info['status_open']==$this->config->item('system_status_complete'))
        {
            $result['label_2']=$this->lang->line('LABEL_CURRENCY_RATE');
            $result['value_2']=number_format($lc_info['rate_currency'],2);;
        }
        $info_basic[]=$result;
        //hidden in release task
        $result=array();
        $result['label_1']=$this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');
        $result['value_1']=number_format($lc_info['price_open_other_currency'],2);
        /*if($lc_info['status_release']==$this->config->item('system_status_complete'))
        {
            $result['label_2']=$this->lang->line('LABEL_PRICE_RELEASE_OTHER_CURRENCY');
            $result['value_2']=number_format($lc_info['price_release_other_currency'],2);
        }*/
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
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_PRICE_RELEASE_OTHER_CURRENCY');
            $result['value_1']=number_format($lc_info['price_release_other_currency'],2);
            $result['label_2']=$this->lang->line('LABEL_PRICE_RELEASE_VARIETY_CURRENCY');
            $result['value_2']=number_format($lc_info['price_release_variety_currency'],2);
            $info_basic[]=$result;
        }
        return $info_basic;
    }
    private function get_opening_stock($date_end, $variety_ids, $pack_sizes)
    {
        $stocks=array();
        //purpose == in stock,in excess,in delivery_short
        $this->db->from($this->config->item('table_sms_stock_in_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_in.date_stock_in <='.$date_end.' then details.quantity ELSE 0 END) stock_in',false);

        $this->db->join($this->config->item('table_sms_stock_in_variety').' stock_in','stock_in.id=details.stock_in_id','INNER');
        $this->db->where('stock_in.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']+=(($result['stock_in']*$pack_sizes[$result['pack_size_id']])/1000);
        }

        //lc calculation
        $this->db->from($this->config->item('table_sms_lc_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN lco.date_receive <='.$date_end.' then details.quantity_receive ELSE 0 END) in_opening',false);

        $this->db->join($this->config->item('table_sms_lc_open').' lco','lco.id=details.lc_id','INNER');
        $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
        $this->db->where('lco.status_receive',$this->config->item('system_status_complete'));
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->where('details.quantity_open >',0);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        //echo $this->db->last_query();

        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']+=(($result['in_opening']*$pack_sizes[$result['pack_size_id']])/1000);
        }

        //convert bulk to pack in out
        $this->db->from($this->config->item('table_sms_convert_bulk_to_pack').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN details.date_convert <='.$date_end.' then details.quantity_convert ELSE 0 END) out_convert_bulk_pack_opening',false);
        $this->db->select('SUM(CASE WHEN details.date_convert <='.$date_end.' then details.quantity_packet_actual ELSE 0 END) in_convert_bulk_pack_opening',false);

        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']+=(($result['in_convert_bulk_pack_opening']*$pack_sizes[$result['pack_size_id']])/1000);
        }
        //transfer ww in and out no need to calculate
        //out stock sample,rnd,demonstration, short
        $this->db->from($this->config->item('table_sms_stock_out_variety_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        $this->db->select('SUM(CASE WHEN stock_out.date_stock_out <='.$date_end.' then details.quantity ELSE 0 END) out_opening',false);

        $this->db->join($this->config->item('table_sms_stock_out_variety').' stock_out','stock_out.id=details.stock_out_id','INNER');
        $this->db->where('stock_out.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.revision',1);
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']-=(($result['out_opening']*$pack_sizes[$result['pack_size_id']])/1000);
        }
        //TO
        //out transfer to outlet
        $this->db->from($this->config->item('table_sms_transfer_wo_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        //hq out
        $this->db->select('SUM(CASE WHEN wo.date_delivery <='.$date_end.' then details.quantity_approve ELSE 0 END) out_hq',false);
        //outlet in
        $this->db->select('SUM(CASE WHEN wo.status_receive ="'.$this->config->item('system_status_received').'" and wo.date_receive <='.$date_end.' then details.quantity_receive ELSE 0 END) in_outlet',false);
        //air=out hq-expected_in outlet
        $this->db->select('SUM(CASE WHEN wo.status_receive ="'.$this->config->item('system_status_received').'" and wo.date_receive <='.$date_end.' then details.quantity_approve ELSE 0 END) expected_in_outlet',false);

        $this->db->join($this->config->item('table_sms_transfer_wo').' wo','wo.id=details.transfer_wo_id','INNER');
        $this->db->where('wo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('wo.status_delivery',$this->config->item('system_status_delivered'));
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']-=(($result['out_hq']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_outlet_kg']+=(($result['in_outlet']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_to_kg']+=((($result['out_hq']-$result['expected_in_outlet'])*$pack_sizes[$result['pack_size_id']])/1000);

        }
        //TR
        $this->db->from($this->config->item('table_sms_transfer_ow_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        //hq in
        $this->db->select('SUM(CASE WHEN ow.status_receive ="'.$this->config->item('system_status_received').'" and ow.date_receive <='.$date_end.' then details.quantity_receive ELSE 0 END) in_hq',false);
        //outlet out
        $this->db->select('SUM(CASE WHEN ow.date_delivery <='.$date_end.' then details.quantity_approve ELSE 0 END) out_outlet',false);
        //air=out_outlet-expected in hq
        $this->db->select('SUM(CASE WHEN ow.status_receive ="'.$this->config->item('system_status_received').'" and ow.date_receive <='.$date_end.' then details.quantity_approve ELSE 0 END) expected_in_hq',false);

        $this->db->join($this->config->item('table_sms_transfer_ow').' ow','ow.id=details.transfer_ow_id','INNER');
        $this->db->where('ow.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('ow.status_delivery',$this->config->item('system_status_delivered'));
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]=$this->initialize_row('','','',$pack_sizes[$result['pack_size_id']]);
            }
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_hq_kg']+=(($result['in_hq']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_outlet_kg']-=(($result['out_outlet']*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_tr_kg']+=((($result['out_outlet']-$result['expected_in_hq'])*$pack_sizes[$result['pack_size_id']])/1000);
        }
        //TS
        $this->db->from($this->config->item('table_sms_transfer_oo_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');
        //out outlet
        $this->db->select('SUM(CASE WHEN oo.date_delivery <='.$date_end.' then details.quantity_approve ELSE 0 END) out_oo_opening',false);
        //in outlet
        $this->db->select('SUM(CASE WHEN oo.status_receive ="'.$this->config->item('system_status_received').'" and oo.date_receive <='.$date_end.' then details.quantity_receive ELSE 0 END) in_oo_opening',false);
        //air out_oo_opening-expected_in_oo_opening
        $this->db->select('SUM(CASE WHEN oo.status_receive ="'.$this->config->item('system_status_received').'" and oo.date_receive <='.$date_end.' then details.quantity_approve ELSE 0 END) expected_in_oo_opening',false);
        $this->db->join($this->config->item('table_sms_transfer_oo').' oo','oo.id=details.transfer_oo_id','INNER');
        $this->db->where('oo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('details.status !=',$this->config->item('system_status_delete'));
        $this->db->where('oo.status_delivery',$this->config->item('system_status_delivered'));
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_outlet_kg']+=((($result['in_oo_opening']-$result['out_oo_opening'])*$pack_sizes[$result['pack_size_id']])/1000);

            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_ts_kg']+=((($result['out_oo_opening']-$result['expected_in_oo_opening'])*$pack_sizes[$result['pack_size_id']])/1000);
        }
        //sales
        $this->db->from($this->config->item('table_pos_sale_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id');

        $this->db->select('SUM(CASE WHEN sale.date_sale <='.$date_end.' then details.quantity ELSE 0 END) sale_opening',false);
        $this->db->select('SUM(CASE WHEN sale.date_sale <='.$date_end.' and sale.status="'.$this->config->item('system_status_inactive').'" then details.quantity ELSE 0 END) sale_cancel_opening',false);

        $this->db->join($this->config->item('table_pos_sale').' sale','sale.id=details.sale_id','INNER');
        $this->db->where('sale.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('details.variety_id',$variety_ids);
        //$this->db->or_where_in('details.pack_size_id',$pack_sizes);
        $this->db->group_by('details.variety_id');
        $this->db->group_by('details.pack_size_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_outlet_kg']-=((($result['sale_opening']-$result['sale_cancel_opening'])*$pack_sizes[$result['pack_size_id']])/1000);
        }

        return $stocks;
    }
    private function initialize_row($crop_name,$crop_type_name,$variety_name,$pack_size)
    {
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        //$row['stock_total_kg']=0;
        //$row['stock_total_pkt']=0;
        $row['stock_total_kg']=0;
        //$row['stock_hq_pkt']=0;
        $row['stock_hq_kg']=0;
        //$row['stock_outlet_pkt']=0;
        $row['stock_outlet_kg']=0;
        //$row['stock_to_pkt']=0;
        $row['stock_to_kg']=0;
        //$row['stock_tr_pkt']=0;
        $row['stock_tr_kg']=0;
        //$row['stock_ts_pkt']=0;
        $row['stock_ts_kg']=0;
        return $row;
    }
    private function get_previous_rates($date_receive,$variety_ids)
    {
        $this->db->from($this->config->item('table_sms_lc_details') . ' details');
        $this->db->select('MAX( details.id ) AS id');
        $this->db->select('details.variety_id, details.pack_size_id');
        $this->db->join($this->config->item('table_sms_lc_open') . ' lc','lc.id=details.lc_id','INNER');
        $this->db->select('lc.date_receive');
        $this->db->where_in('details.variety_id', $variety_ids);
        $this->db->where('details.quantity_open >0');
        $this->db->where('lc.date_receive < '.$date_receive);
        $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
        $this->db->where('lc.status_receive',$this->config->item('system_status_complete'));
        $this->db->group_by('details.variety_id, details.pack_size_id');
        $sub_query=$this->db->get_compiled_select();

        $this->db->from($this->config->item('table_sms_lc_details') . ' details');
        $this->db->select('details.*');
        $this->db->join($this->config->item('table_sms_lc_open') . ' lc','lc.id=details.lc_id','INNER');
        //$this->db->join('('.$sub_query.') details_max','details_max.variety_id = details.variety_id AND details_max.pack_size_id = details.pack_size_id AND details_max.date_receive= lc.date_receive','INNER');
        $this->db->join('('.$sub_query.') details_max','details_max.id = details.id','INNER');
        $results=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $rates=array();
        foreach($results as $result)
        {
            $rates[$result['variety_id']][$result['pack_size_id']]=$result;
        }

        return $rates;
    }
    private function get_receive_rates($lc_info, $lc_details)
    {
        $variety_rates=array();
        $rate_currency=$lc_info['price_complete_other_variety_taka']/($lc_info['price_release_other_currency']+$lc_info['price_release_variety_currency']);
        foreach($lc_details as $result)
        {
            /*if($result['pack_size_id']==0)
            {
                $price_variety_taka = ($result['quantity_release'] * $result['price_unit_currency']) * $rate_currency;
                $price_other_taka = ($lc_info['price_release_other_currency']/$lc_info['price_release_variety_currency'])*$price_variety_taka; //(air fright)
                $total_taka = ($price_variety_taka+$price_other_taka);
                $variety_rates[$result['variety_id']][$result['pack_size_id']] = $total_taka/$result['quantity_receive'];
            }
            else
            {
                $price_variety_taka = ($result['quantity_release'] * $result['price_unit_currency']) * $rate_currency;
                $price_other_taka = ($lc_info['price_release_other_currency']/$lc_info['price_release_variety_currency'])*$price_variety_taka; //(air fright)
                $total_taka = ($price_variety_taka+$price_other_taka);
                $variety_rates[$result['variety_id']][$result['pack_size_id']] = $total_taka/$result['quantity_receive'];
            }*/
            $price_variety_taka = ($result['quantity_release'] * $result['price_unit_currency']) * $rate_currency;
            $price_other_taka = ($lc_info['price_release_other_currency']/$lc_info['price_release_variety_currency'])*$price_variety_taka; //(air fright)
            $total_taka = ($price_variety_taka+$price_other_taka);
            $variety_rates[$result['variety_id']][$result['pack_size_id']] = $total_taka/$result['quantity_receive'];
        }
        return $variety_rates;
    }
}
