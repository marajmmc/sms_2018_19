<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_lc_variety extends Root_Controller
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
        $this->lang->language['LABEL_LC_BARCODE']="Barcode(s)";
        $this->lang->language['LABEL_LC_LAST_BARCODE']="Last Barcode";
        $this->lang->language['LABEL_QUANTITY_OPEN']="Order Qty (Kg)";
        $this->lang->language['LABEL_QUANTITY_RELEASE']="Release Qty (Kg)";
        $this->lang->language['LABEL_QUANTITY_RECEIVE']="Receive Qty (Kg)";
        $this->lang->language['LABEL_QUANTITY_PKT']='Quantity (pkt)';
        $this->lang->language['LABEL_QUANTITY_KG']='Quantity (kg)';
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
        elseif($action=="get_items_variety")
        {
            $this->system_get_items_variety();
        }
        elseif($action=="get_items_quantity")
        {
            $this->system_get_items_quantity();
        }
        elseif($action=="set_preference_lc")
        {
            $this->system_set_preference('list_variety');
        }
        elseif($action=="set_preference_quantity")
        {
            $this->system_set_preference('list_quantity');
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
    private function get_preference_headers($method)
    {
        $data['id']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        if($method=='list_variety')
        {
            //$data['lc_last_barcode']= 1;
            $data['lc_barcode']= 1;
            $data['quantity_open']= 1;
            $data['quantity_release']= 1;
            $data['quantity_receive']= 1;
        }
        else
        {
            $data['quantity_pkt']= 1;
            $data['quantity_kg']= 1;
        }
        return $data;
    }
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_'.$method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
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

            $data['title']="Variety Wise LC Report Search";
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

            $report_type=$reports['report_type'];
            if($report_type=='lc')
            {
                $method='list_variety';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $data['title']="Variety Wise LC Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_variety",$data,true));
            }
            elseif($report_type=='quantity')
            {
                $method='list_quantity';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $data['title']="Quantity Wise LC Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_quantity",$data,true));
            }


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
    private function system_get_items_variety()
    {
        $date_type=$this->input->post('date_type');
        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');
        $principal_id=$this->input->post('principal_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $status_received=$this->input->post('status_receive');
        $status_open=$this->input->post('status_open');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select("v.id variety_id,v.name variety_name");
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('v.whose','ARM');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');

        $varieties=$this->db->get()->result_array();
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }

        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');

        $this->db->join($this->config->item('table_sms_lc_details').' details','details.lc_id = lc.id','INNER');
        $this->db->select('details.variety_id, details.pack_size_id, details.quantity_open, details.quantity_release, details.quantity_receive');

        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack_size','pack_size.id = details.pack_size_id','LEFT');
        $this->db->select("pack_size.id pack_size_id,IF(`details`.`pack_size_id` = 0,'Bulk',pack_size.`name`) pack_size");

        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');

        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');

        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');

        $this->db->where_in('details.variety_id',$variety_ids);
        $this->db->where('details.quantity_open >0');
        $this->db->where('lc.status_open_forward',$this->config->item('system_status_yes'));
        $this->db->where('lc.status_release',$this->config->item('system_status_complete'));
        $this->db->where('lc.'.$date_type.'>='.$date_start.' and lc.'.$date_type.'<='.$date_end);
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        if($status_received)
        {
            $this->db->where('lc.status_receive',$status_received);
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
        //$this->db->group_by('lc.id');
        $this->db->group_by('lc.id,details.variety_id,details.pack_size_id');
        $results=$this->db->get()->result_array();

        $variety_lc=array();
        $variety_lc_info=array();
        foreach($results as $result)
        {
            $variety_lc[$result['variety_id']][$result['pack_size_id']]=$result;
            $variety_lc_info[$result['variety_id']][$result['pack_size_id']][]=$result;
        }

        $method='list_variety';
        $items=array();
        foreach($varieties as $variety)
        {
            if(isset($variety_lc[$variety['variety_id']]))
            {
                foreach($variety_lc[$variety['variety_id']] as $details)
                {
                    if(isset($variety_lc_info[$variety['variety_id']][$details['pack_size_id']]))
                    {
                        $lc_info=$variety_lc_info[$variety['variety_id']][$details['pack_size_id']];
                        for($i=0; $i<sizeof($lc_info); $i++)
                        {
                            $info=$this->initialize_row($variety['crop_name'],$variety['crop_type_name'],$variety['variety_name'],$details['pack_size'],$method);
                            $info['crop_name']=$variety['crop_name'];
                            $info['crop_type_name']=$variety['crop_type_name'];
                            $info['variety_name']=$variety['variety_name'];
                            $info['pack_size']=$details['pack_size'];
                            $info['id']=$lc_info[$i]['id'];
                            $info['lc_barcode']=Barcode_helper::get_barcode_lc($lc_info[$i]['id']);
                            if($details['pack_size_id']==0)
                            {
                                $info['quantity_open']=$lc_info[$i]['quantity_open'];
                                $info['quantity_release']=$lc_info[$i]['quantity_release'];
                                $info['quantity_receive']=$lc_info[$i]['quantity_receive'];
                            }
                            else
                            {
                                $info['quantity_open']=($details['pack_size']*$lc_info[$i]['quantity_open'])/1000;
                                $info['quantity_release']=($details['pack_size']*$lc_info[$i]['quantity_release'])/1000;
                                $info['quantity_receive']=($details['pack_size']*$lc_info[$i]['quantity_receive'])/1000;
                            }
                            $items[]=$info;
                        }
                    }
                }
            }
        }
        $this->json_return($items);
    }
    private function system_get_items_quantity()
    {

        $date_type=$this->input->post('date_type');

        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');

        $principal_id=$this->input->post('principal_id');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');

        $status_received=$this->input->post('status_receive');
        $status_open=$this->input->post('status_open');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select("v.id variety_id,v.name variety_name");
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->where('v.whose','ARM');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');

        $varieties=$this->db->get()->result_array();
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }

        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');

        $this->db->join($this->config->item('table_sms_lc_details').' details','details.lc_id = lc.id','INNER');
        $this->db->select('details.variety_id, details.pack_size_id, SUM(details.quantity_receive) quantity_pkt, SUM((pack.name*details.quantity_receive)/1000) quantity_kg');

        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = details.pack_size_id','LEFT');
        $this->db->select("pack.id pack_size_id,IF(`details`.`pack_size_id` = 0,'Bulk',pack.`name`) pack_size");

        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');

        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');

        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');


        $this->db->where_in('details.variety_id',$variety_ids);
        $this->db->where('details.quantity_open >0');
        $this->db->where('lc.status_open_forward',$this->config->item('system_status_yes'));
        $this->db->where('lc.status_release',$this->config->item('system_status_complete'));
        $this->db->where('lc.'.$date_type.'>='.$date_start.' and lc.'.$date_type.'<='.$date_end);
        $this->db->group_by('details.variety_id,details.pack_size_id');
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        if($status_received)
        {
            $this->db->where('lc.status_receive',$status_received);
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
        $variety_lc=array();
        foreach($results as $result)
        {
            $variety_lc[$result['variety_id']][$result['pack_size_id']]=$result;
        }
        $items=array();
        foreach($varieties as $variety)
        {
            if(isset($variety_lc[$variety['variety_id']]))
            {
                foreach($variety_lc[$variety['variety_id']] as $details)
                {
                    $info['crop_name']=$variety['crop_name'];
                    $info['crop_type_name']=$variety['crop_type_name'];
                    $info['variety_name']=$variety['variety_name'];
                    $info['pack_size']=$details['pack_size'];
                    $info['id']=$details['id'];
                    if($details['pack_size_id']==0)
                    {
                        $info['quantity_pkt']='';
                        $info['quantity_kg']=$details['quantity_pkt'];
                    }
                    else
                    {
                        $info['quantity_pkt']=$details['quantity_pkt'];
                        $info['quantity_kg']=$details['quantity_kg'];
                    }

                    $items[]=$info;
                }
            }
        }
        $this->json_return($items);
    }
    private function reset_row($info)
    {
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
            {
                $info[$key]='';
            }
        }
        return $info;
    }
    private function get_row($info)
    {
        $row=array();
        foreach($info  as $key=>$r)
        {
            $row[$key]=$info[$key];
        }
        return $row;
    }
    private function initialize_row($crop_name,$crop_type_name,$variety_name,$pack_size,$method)
    {
        $row=$this->get_preference_headers($method);
        foreach($row  as $key=>$r)
        {
            $row[$key]='';
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        return $row;
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

            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','LEFT');
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
