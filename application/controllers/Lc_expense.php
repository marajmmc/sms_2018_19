<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_expense extends Root_Controller
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
        elseif($action=="list_all")
        {
            $this->system_list_all();
        }
        elseif($action=="get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="details_grn_print")
        {
            $this->system_details_grn_print($id);
        }
        elseif($action=="expense_complete")
        {
            $this->system_expense_complete($id);
        }
        elseif($action=="save_expense_complete")
        {
            $this->system_save_expense_complete();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="set_preference_all_lc")
        {
            $this->system_set_preference_all_lc();
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
            $data['system_preference_items']=$this->get_preference();
            $data['title']="Pending List";
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
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');
        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');

        $this->db->where('lc.status_open_forward',$this->config->item('system_status_yes'));
        $this->db->where('lc.status_open =',$this->config->item('system_status_active'));
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
            $item['principal_name']=$result['principal_name'];
            $item['currency_name']=$result['currency_name'];
            $item['lc_number']=$result['lc_number'];
            $item['quantity_open_kg']=number_format($result['quantity_open_kg'],3,'.','');
            $item['status_release']=$result['status_release'];
            $item['status_received']=$result['status_receive'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']=$this->get_preference_all_lc();
            $data['title']="All LC List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/list_all");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_all()
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
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');
        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');
        $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
        $this->db->where('lc.status_open_forward',$this->config->item('system_status_yes'));
        $this->db->order_by('lc.id','DESC');
        $this->db->limit($pagesize,$current_records);
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
            $item['date_expected']=System_helper::display_date($result['date_expected']);
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
    private function system_edit($id)
    {
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || (isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
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
            if($data['item']['status_open']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Completed.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Lc_helper::get_view_info_basic($data['item']);
            $data['info_lc']=$this->get_view_info_lc($data['item']);

            $data['items']=Query_helper::get_info($this->config->item('table_login_setup_direct_cost_items'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $results=Query_helper::get_info($this->config->item('table_sms_lc_expense'),'*',array('lc_id ='.$item_id),0,0,array(''));
            $data['dc']=array();
            foreach($results as $result)
            {
                $data['dc'][$result['dc_id']]=$result['amount'];
            }

            $data['title']="LC Expense :: ".Barcode_helper::get_barcode_lc($item_id);
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
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        if($id>0)
        {
            if(!((isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || (isset($this->permissions['action2']) && ($this->permissions['action2']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status_open = "'.$this->config->item('system_status_active').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__,$id,'Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $results=Query_helper::get_info($this->config->item('table_sms_lc_expense'),'*',array('lc_id ='.$id),0,0,array(''));
        $dc_item=array();
        foreach($results as $result)
        {
            $dc_item[$result['dc_id']]=$result['amount'];
        }

        foreach($items as $item_id=>$item)
        {
            if(isset($dc_item[$item_id]))
            {
                if($dc_item[$item_id]!=$item['amount'])
                {
                    $data=array();
                    $data['amount']=$item['amount'];
                    $data['date_updated']=$time;
                    $data['user_updated']=$user->user_id;
                    $this->db->set('revision_count', 'revision_count+1', FALSE);
                    Query_helper::update($this->config->item('table_sms_lc_expense'),$data,array('lc_id='.$id,'dc_id='.$item_id));
                }
            }
            else
            {
                $data=array();
                $data['lc_id']=$id;
                $data['dc_id']=$item_id;
                $data['amount']=$item['amount'];
                $data['revision_count']=1;
                $data['date_created']=$time;
                $data['user_created']=$user->user_id;
                Query_helper::add($this->config->item('table_sms_lc_expense'),$data);
            }
        }
        $item_head['date_expense_updated']=$time;
        $item_head['user_expense_updated']=$user->user_id;
        $this->db->set('revision_expense_count', 'revision_expense_count+1', FALSE);
        Query_helper::update($this->config->item('table_sms_lc_open'),$item_head,array('id='.$id));
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
    //Function= Lc_open details same
    //view =Lc_open details +additional view if lc completed
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
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
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
    //open,release,expense grn print task same but receive has extra condition
    //open,release,expense,receive details_grn_print view is same
    private function system_details_grn_print($id)
    {
        if((isset($this->permissions['action4']) && ($this->permissions['action4']==1)))
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
            /*if($data['item']['status_open_forward']!=$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not see this LC. LC not forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_release']!=$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not open this LC. LC release pending.';
                $this->json_return($ajax);
            }
            if($data['item']['revision_receive_count']==0)
            {
                $ajax['status']=false;
                $ajax['system_message']='You have to complete your (LC) edit receive.';
                $this->json_return($ajax);
            }*/
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
            $this->db->order_by('lcd.id','ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="LC Receive :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details_grn_print",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details_grn_print/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_expense_complete($id)
    {
        if((isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
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
            if($data['item']['status_release']!=$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not open this LC. LC release pending.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']!=$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not open this LC. LC receive pending.';
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC already completed.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Lc_helper::get_view_info_basic($data['item']);
            $data['info_lc']=$this->get_view_info_lc($data['item']);


            $data['items']=Query_helper::get_info($this->config->item('table_login_setup_direct_cost_items'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $results=Query_helper::get_info($this->config->item('table_sms_lc_expense'),'*',array('lc_id ='.$item_id),0,0,array(''));
            $data['dc']=array();
            foreach($results as $result)
            {
                $data['dc'][$result['dc_id']]=$result['amount'];
            }

            $data['title']="LC Expense :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/expense_complete",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/expense_complete/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_expense_complete()
    {
        if((isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            $item_id=$this->input->post('id');
            $user = User_helper::get_user();
            $time=time();
            $item_head=$this->input->post('item');

            if($item_head['status_open']!=$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC completed is required.';
                $this->json_return($ajax);
            }

            /*fetching data from lc */
            $result_lc=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$item_id, 'status_open != "'.$this->config->item('system_status_delete').'"'),1);

            /*check validation
                1. not exist
                2. already closed
                3. already not received
                4. $result_lc['price_release_other_currency']+$result_lc['price_release_variety_currency'] == 0 checking
            */
            $dc_expenses=Query_helper::get_info($this->config->item('table_sms_lc_expense'),'*',array('lc_id ='.$item_id),0,0,array(''));
            $price_complete_dc_taka=0;
            foreach($dc_expenses as $item)
            {
                $price_complete_dc_taka+=$item['amount'];
            }
            $results=Query_helper::get_info($this->config->item('table_sms_lc_expense_varieties'),'*',array('lc_id ='.$item_id),0,0,array(''));
            $dc_expenses_varieties=array();
            foreach($results as $result)
            {
                $dc_expenses_varieties[$result['variety_id']][$result['pack_size_id']][$result['dc_id']]=$result;
            }
            $data_lc=array();
            $data_lc['status_open']=$this->config->item('system_status_complete');
            $data_lc['rate_currency']=$result_lc['price_complete_other_variety_taka']/($result_lc['price_release_other_currency']+$result_lc['price_release_variety_currency']);
            $data_lc['price_complete_dc_taka']=$price_complete_dc_taka;
            $data_lc['price_complete_total_taka']=$result_lc['price_complete_other_variety_taka']+$price_complete_dc_taka;
            $data_lc['date_expense_completed']=$time;
            $data_lc['user_expense_completed']=$user->user_id;

            $result_varieties=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id ='.$item_id, 'quantity_open >0'));


            $this->db->trans_start();  //DB Transaction Handle START

            Query_helper::update($this->config->item('table_sms_lc_open'),$data_lc,array('id='.$item_id));

            foreach($result_varieties as $variety)
            {
                $data=array();
                $data['price_unit_complete_currency']=($variety['price_unit_currency']*$variety['quantity_release'])/$variety['quantity_receive'];
                $data['price_unit_complete_taka']=($data['price_unit_complete_currency']*$data_lc['rate_currency']);
                $data['price_complete_variety_taka']=($data['price_unit_complete_taka']*$variety['quantity_receive']);
                $data['price_complete_other_taka']=($result_lc['price_release_other_currency']/$result_lc['price_release_variety_currency'])*$data['price_complete_variety_taka'];
                $data['price_dc_expense_taka']=($price_complete_dc_taka/($result_lc['price_release_variety_currency']*$data_lc['rate_currency']) * $data['price_complete_variety_taka']);
                $data['price_total_taka']=($data['price_complete_variety_taka']+$data['price_complete_other_taka']+$data['price_dc_expense_taka']);
                Query_helper::update($this->config->item('table_sms_lc_details'),$data,array('id='.$variety['id']));

                foreach($dc_expenses as $item)
                {
                    if(isset($dc_expenses_varieties[$variety['variety_id']][$variety['pack_size_id']][$item['dc_id']]))
                    {
                        $data_dcv=array();
                        $data_dcv['amount']=$data['price_dc_expense_taka']*($item['amount']/$price_complete_dc_taka);
                        $data_dcv['date_created']=$time;
                        $data_dcv['user_created']=$user->user_id;
                        Query_helper::update($this->config->item('table_sms_lc_expense_varieties'),$data_dcv,array('id='.$dc_expenses_varieties[$variety['variety_id']][$variety['pack_size_id']][$item['dc_id']]['id']));
                    }
                    else
                    {
                        $data_dcv=array();
                        $data_dcv['lc_id']=$item_id;
                        $data_dcv['dc_id']=$item['dc_id'];
                        $data_dcv['variety_id']=$variety['variety_id'];
                        $data_dcv['pack_size_id']=$variety['pack_size_id'];
                        $data_dcv['amount']=$data['price_dc_expense_taka']*($item['amount']/$price_complete_dc_taka);
                        $data_dcv['date_created']=$time;
                        $data_dcv['user_created']=$user->user_id;
                        Query_helper::add($this->config->item('table_sms_lc_expense_varieties'),$data_dcv);
                    }

                }
            }

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
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id','ID','required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference();
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
        $data['principal_name']= 1;
        $data['currency_name']= 1;
        $data['lc_number']= 1;
        $data['quantity_open_kg']= 1;
        $data['status_release']= 1;
        $data['status_received']= 1;
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
    private function system_set_preference_all_lc()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference_all_lc();
            $data['preference_method_name']='list_all';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_all_lc');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function get_preference_all_lc()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list_all"'),1);
        $data['barcode']= 1;
        $data['fiscal_year']= 1;
        $data['month']= 1;
        $data['date_opening']= 1;
        $data['principal_name']= 1;
        $data['currency_name']= 1;
        $data['lc_number']= 1;
        $data['quantity_open_kg']= 1;
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
    //same as Lc_Open
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
            $result['value_1']=System_helper::display_date($lc_info['date_packing_list']);
            $result['label_2']=$this->lang->line('LABEL_NUMBER_LOT');
            $result['value_2']=$lc_info['lot_number'];
            $info_basic[]=$result;
        }
        return $info_basic;
    }
}
