<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_open extends Root_Controller
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
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="delete")
        {
            $this->system_delete($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="details_grn_print")
        {
            $this->system_details_grn_print($id);
        }
        elseif($action=="forward")
        {
            $this->system_lc_forward($id);
        }
        elseif($action=="save_forward")
        {
            $this->system_save_lc_forward();
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
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Pending LC List";
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

        $this->db->where('lc.status_open_forward',$this->config->item('system_status_no'));
        $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
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
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New LC";
            $data['item']['id']=0;
            $data['item']['date_opening']=time();
            $data['item']['date_expected']='';
            $data['item']['principal_id']=0;
            $data['item']['currency_id']=0;
            $data['item']['bank_account_id']=0;
            $data['item']['lc_number']='';
            $data['item']['consignment_name']='';
            $data['item']['remarks_open']='';
            $data['item']['price_open_other_currency']='';
            $data['item']['quantity_open_kg']=0;
            $data['item']['price_open_variety_currency']=0;
            $data['items']=array();

            $data['currencies']=Query_helper::get_info($this->config->item('table_login_setup_currency'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $data['principals']=Query_helper::get_info($this->config->item('table_login_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));

            $this->db->from($this->config->item('table_login_setup_bank_account').' ba');
            $this->db->select('ba.id value');
            $this->db->select("CONCAT_WS(' ( ',ba.account_number,  CONCAT_WS('', bank.name,' - ',ba.branch_name,')')) text");
            $this->db->join($this->config->item('table_login_setup_bank').' bank','bank.id=ba.bank_id AND bank.status='.'"'.$this->config->item('system_status_active').'"','INNER');
            $this->db->join($this->config->item('table_login_setup_bank_account_purpose').' bap','bap.bank_account_id=ba.id AND bap.revision=1 AND bap.purpose ="'.$this->config->item('system_bank_account_purpose_lc').'" AND bap.status='.'"'.$this->config->item('system_status_active').'"','INNER');
            $this->db->where('ba.status',$this->config->item('system_status_active'));
            $this->db->where('ba.account_type_expense',1);
            $data['bank_accounts']=$this->db->get()->result_array();

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
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
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
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
            if($data['item']['status_open_forward']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC already forwarded';
                $this->json_return($ajax);
            }

            // item details table
            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->where('lcd.lc_id',$item_id);
            $this->db->order_by('lcd.id','ASC');
            $data['items']=$this->db->get()->result_array();

            //get bank account
            $this->db->from($this->config->item('table_login_setup_bank_account').' ba');
            $this->db->select('ba.id value');
            $this->db->select("CONCAT_WS(' ( ',ba.account_number,  CONCAT_WS('', bank.name,' - ',ba.branch_name,')')) text");
            $this->db->join($this->config->item('table_login_setup_bank').' bank','bank.id=ba.bank_id','INNER');
            $this->db->join($this->config->item('table_login_setup_bank_account_purpose').' bap','bap.bank_account_id=ba.id AND bap.revision=1 AND bap.purpose ="'.$this->config->item('system_bank_account_purpose_lc').'"','INNER');
            $this->db->where('ba.status !=',$this->config->item('system_status_delete'));
            $this->db->where('ba.account_type_expense',1);
            $data['bank_accounts']=$this->db->get()->result_array();

            // get drop down info
            $data['currencies']=Query_helper::get_info($this->config->item('table_login_setup_currency'),array('id value','name text','amount_rate_budget'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering'));

            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.id value,v.name');
            $this->db->select("CONCAT_WS(' ( ',vp.name_import,  CONCAT_WS('', v.name,')')) text");
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->where('v.status',$this->config->item('system_status_active'));
            $this->db->where('v.whose','ARM');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
            $data['varieties']=array();
            foreach($results as $result)
            {
                $data['varieties'][$result['value']]=$result;
            }

            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));


            $data['title']="Edit LC :: ". Barcode_helper::get_barcode_lc($item_id);
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
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status_open != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__,$id,'Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($result['status_open_forward']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC already forwarded.';
                $this->json_return($ajax);
            }
            if($result['status_open']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC already completed.';
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

        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
        $pack_sizes=array();
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]['value']=$result['value'];
            $pack_sizes[$result['value']]['text']=$result['text'];
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            $results=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$id));
            $old_varieties=array();
            if($results)
            {
                foreach($results as $result)
                {
                    $old_varieties[$result['variety_id']][$result['pack_size_id']]=$result;
                }
            }

            $data=array();
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_lc_open_histories'),$data, array('lc_id='.$id,'revision=1'), false);

            $this->db->where('lc_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_lc_open_histories'));

            $price_open_variety_currency=0;
            $quantity_open_kg=0;
            foreach($items as $item)
            {
                $price_open_variety_currency+=($item['quantity_open']*$item['price_unit_currency']);
                if($item['pack_size_id']==0)
                {
                    $quantity_open_kg+=$item['quantity_open'];
                }
                else
                {
                    if(isset($pack_sizes[$item['pack_size_id']]['text']))
                    {
                        $quantity_open_kg+=(($pack_sizes[$item['pack_size_id']]['text']*$item['quantity_open'])/1000);
                    }
                }
                if(isset($old_varieties[$item['variety_id']][$item['pack_size_id']]))
                {
                    $lc_detail_id=$old_varieties[$item['variety_id']][$item['pack_size_id']]['id'];
                    $old_variety_quantity_open=$old_varieties[$item['variety_id']][$item['pack_size_id']]['quantity_open'];
                    $old_variety_open_unit_currency=$old_varieties[$item['variety_id']][$item['pack_size_id']]['price_unit_currency'];
                    if(($old_variety_quantity_open!=$item['quantity_open']) || ($old_variety_open_unit_currency!=$item['price_unit_currency']))
                    {
                        $data=array();
                        $data['quantity_open']=$item['quantity_open'];
                        $data['price_unit_currency']=$item['price_unit_currency'];
                        $this->db->set('revision_open_count', 'revision_open_count+1', FALSE);
                        Query_helper::update($this->config->item('table_sms_lc_details'),$data, array('id='.$lc_detail_id),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['lc_id']=$id;
                    $data['variety_id']=$item['variety_id'];
                    $data['pack_size_id']=$item['pack_size_id'];
                    $data['quantity_open']=$item['quantity_open'];
                    $data['price_unit_currency']=$item['price_unit_currency'];
                    $data['revision_open_count']=1;
                    Query_helper::add($this->config->item('table_sms_lc_details'),$data, false);
                }

                $data=array();
                $data['lc_id']=$id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity_open'];
                $data['price_unit_currency']=$item['price_unit_currency'];
                $data['revision'] = 1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_lc_open_histories'),$data, false);
            }

            $item_head['date_opening']=System_helper::get_time($item_head['date_opening']);
            $item_head['month_id']=date('n',$item_head['date_opening']);
            $item_head['date_expected']=System_helper::get_time($item_head['date_expected']);
            $item_head['quantity_open_kg']=$quantity_open_kg;
            $item_head['price_open_variety_currency']=$price_open_variety_currency;
            $item_head['date_open_updated']=$time;
            $item_head['user_open_updated']=$user->user_id;
            $this->db->set('revision_open_count', 'revision_open_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_lc_open'),$item_head,array('id='.$id));
        }
        else
        {
            $price_open_variety_currency=0;
            $quantity_open_kg=0;
            if($items)
            {
                foreach($items as $item)
                {
                    $price_open_variety_currency+=($item['quantity_open']*$item['price_unit_currency']);
                    if($item['pack_size_id']==0)
                    {
                        $quantity_open_kg+=$item['quantity_open'];
                    }
                    else
                    {
                        if(isset($pack_sizes[$item['pack_size_id']]['text']))
                        {
                            $quantity_open_kg+=(($pack_sizes[$item['pack_size_id']]['text']*$item['quantity_open'])/1000);
                        }
                    }
                }
            }

            $item_head['date_opening']=System_helper::get_time($item_head['date_opening']);
            $item_head['month_id']=date('n',$item_head['date_opening']);
            $item_head['date_expected']=System_helper::get_time($item_head['date_expected']);
            $item_head['quantity_open_kg'] = $quantity_open_kg;
            $item_head['price_open_variety_currency'] = $price_open_variety_currency;
            $item_head['status_open'] = $this->config->item('system_status_active');
            $item_head['revision_open_count'] = 1;
            $item_head['user_open_created'] = $user->user_id;
            $item_head['date_open_created'] = time();
            $lc_id=Query_helper::add($this->config->item('table_sms_lc_open'),$item_head);
            //varieties
            foreach($items as $item)
            {
                $data=array();
                $data['lc_id']=$lc_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity_open']=$item['quantity_open'];
                $data['price_unit_currency']=$item['price_unit_currency'];
                $data['revision_open_count']=1;
                Query_helper::add($this->config->item('table_sms_lc_details'),$data, false);

                $data=array();
                $data['lc_id']=$lc_id;
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['quantity']=$item['quantity_open'];
                $data['price_unit_currency']=$item['price_unit_currency'];
                $data['revision']=1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_lc_open_histories'),$data, false);
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

            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$item_id, 'status_open != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__,$item_id,'Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($result['status_release']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Released.';
                $this->json_return($ajax);
            }
            if($result['status_open']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Completed.';
                $this->json_return($ajax);
            }
            $this->db->trans_start();  //DB Transaction Handle START
            Query_helper::update($this->config->item('table_sms_lc_open'),array('status_open'=>$this->config->item('system_status_delete')),array("id = ".$item_id));
            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $this->message='LC Delete Successful';
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
    private function system_lc_forward($id)
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
            if($data['item']['status_open_forward']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already forwarded this LC :: '. Barcode_helper::get_barcode_lc($item_id);
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already completed this LC :: '. Barcode_helper::get_barcode_lc($item_id);
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
            $this->db->where('lcd.lc_id',$item_id);
            $this->db->where('lcd.quantity_open >0');
            $this->db->order_by('lcd.id ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="LC Forward :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_lc_forward()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if($id>0)
        {
            if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$item_head['date_awb'])
            {
                $ajax['status']=false;
                $ajax['system_message']='AWB Date is required.';
                $this->json_return($ajax);
            }
            if(!trim($item_head['awb_number']))
            {
                $ajax['status']=false;
                $ajax['system_message']='AWB Number is required.';
                $this->json_return($ajax);
            }
            if($item_head['status_open_forward']!=$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Forward LC is required.';
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status_open != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__,$id,'Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($result['status_open_forward']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already LC Forwarded.';
                $this->json_return($ajax);
            }
            if($result['status_open']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Completed.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item_head['date_awb']=System_helper::get_time($item_head['date_awb']);
        $item_head['date_open_forward']=$time;
        $item_head['user_open_forward']=$user->user_id;
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
    private function check_validation()
    {
        $this->load->library('form_validation');
        $id = $this->input->post("id");
        $item = $this->input->post("item");
        if($id==0)
        {
            if(!isset($item['date_opening']) || !strtotime($item['date_opening']))
            {
                $this->message=$this->lang->line('LABEL_DATE_OPENING'). ' field is required.';
                return false;
            }
            if(!isset($item['principal_id']) || !is_numeric($item['principal_id']))
            {
                $this->message=$this->lang->line('LABEL_PRINCIPAL_NAME'). ' field is required.';
                return false;
            }
            //$this->form_validation->set_rules('item[fiscal_year_id]',$this->lang->line('LABEL_FISCAL_YEAR'),'required');
            //$this->form_validation->set_rules('item[month_id]',$this->lang->line('LABEL_MONTH'),'required');
            $this->form_validation->set_rules('item[date_opening]',$this->lang->line('LABEL_DATE_OPENING'),'required');
            $this->form_validation->set_rules('item[principal_id]',$this->lang->line('LABEL_PRINCIPAL_NAME'),'required');

        }
        if(!isset($item['date_expected']) || !strtotime($item['date_expected']))
        {
            $this->message=$this->lang->line('LABEL_DATE_EXPECTED'). ' field is required.';
            return false;
        }
        $this->form_validation->set_rules('item[lc_number]',$this->lang->line('LABEL_LC_NUMBER'),'required');
        $this->form_validation->set_rules('item[bank_account_id]',$this->lang->line('LABEL_BANK_ACCOUNT_NUMBER'),'required');
        $this->form_validation->set_rules('item[currency_id]',$this->lang->line('LABEL_CURRENCY_NAME'),'required');
        $this->form_validation->set_rules('item[price_open_other_currency]',$this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY'),'required');
        //validation condition wrong
        /*
        if(!$item['price_open_other_currency']>=0)
        {
            $this->message=$this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY').' field is required.';
            return false;
        }*/
        $this->form_validation->set_rules('item[consignment_name]',$this->lang->line('LABEL_CONSIGNMENT_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        $items=$this->input->post('items');
        if((sizeof($items)>0))
        {
            $duplicate_variety=array();
            $status_duplicate_variety=false;
            foreach($items as $item)
            {
                /// empty checking
                if(!(($item['variety_id']>0) && ($item['pack_size_id']>=0) && ($item['quantity_open']>=0) && ($item['price_unit_currency']>=0)))
                {
                    $this->message='Invalid input (variety info :: '.$item['variety_id'].').';
                    return false;
                }
                // duplicate variety checking
                if(isset($duplicate_variety[$item['variety_id']][$item['pack_size_id']]))
                {
                    $duplicate_variety[$item['variety_id']][$item['pack_size_id']]+=1;
                    $status_duplicate_variety=true;
                }
                else
                {
                    $duplicate_variety[$item['variety_id']][$item['pack_size_id']]=1;
                }
            }
            if($status_duplicate_variety==true)
            {
                $this->message='Invalid input, variety duplicate entry.';
                return false;
            }
        }
        else
        {
            $this->message='Variety information is empty.';
            return false;
        }

        return true;
    }
    public function get_dropdown_arm_varieties_by_principal_id()
    {
        $principal_id = $this->input->post('principal_id');
        $html_container_id='.variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id value,v.name');
        //$this->db->select('vp.name_import  v.name text');
        $this->db->select("CONCAT_WS(' ( ',vp.name_import,  CONCAT_WS('', v.name,')')) text");
        $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$principal_id.' AND vp.revision = 1','INNER');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('v.ordering ASC');
        $data['items']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
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
            $result['value_1']=System_helper::display_date($lc_info['date_receive']);
            $result['label_2']=$this->lang->line('LABEL_NUMBER_LOT');
            $result['value_2']=$lc_info['lot_number'];
            $info_basic[]=$result;
        }
        return $info_basic;
    }
}
