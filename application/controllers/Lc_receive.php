<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_receive extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Lc_receive');
        $this->controller_url='lc_receive';
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
        elseif($action=="details_print")
        {
            $this->system_details_print($id);
        }
        elseif($action=="receive_complete")
        {
            $this->system_receive_complete($id);
        }
        elseif($action=="save_receive_complete")
        {
            $this->system_save_receive_complete();
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
            $data['title']="LC Receive List";
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
        $this->db->from($this->config->item('table_sms_lc_open').' lco');
        $this->db->select('lco.*');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
        $this->db->select('fy.name fiscal_year');
        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
        $this->db->select('currency.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
        $this->db->select('principal.name principal_name');
        $this->db->where('lco.status_open_forward',$this->config->item('system_status_yes'));
        $this->db->where('lco.status_release',$this->config->item('system_status_complete'));
        $this->db->where('lco.status_receive',$this->config->item('system_status_pending'));
        $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
        $this->db->order_by('lco.fiscal_year_id','DESC');
        $this->db->order_by('lco.id','DESC');
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
            $item['quantity_release_kg']=number_format($result['quantity_release_kg'],3);
            $item['quantity_receive_kg']=number_format($result['quantity_receive_kg'],3);
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
            $this->db->select('fy.name fiscal_year');
            $this->db->select('currency.name currency_name');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->join($this->config->item('table_login_setup_bank_account').' ba','ba.id = lco.bank_account_id','INNER');
            $this->db->join($this->config->item('table_login_setup_bank').' bank','bank.id = ba.bank_id','INNER');
            $this->db->select("CONCAT_WS(' ( ',ba.account_number,  CONCAT_WS('', bank.name,' - ',ba.branch_name,')')) bank_account_number");
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = lco.user_release_completed','INNER');
            $this->db->select('ui.name user_full_name');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open_forward',$this->config->item('system_status_yes'));
            $this->db->where('lco.status_release',$this->config->item('system_status_complete'));
            $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Received Completed.';
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_closed'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Closed.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->where('lcd.lc_id',$item_id);
            $this->db->where('lcd.quantity_open >0');
            $this->db->order_by('lcd.id','ASC');
            $data['items']=$this->db->get()->result_array();

            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="LC Receive :: ".Barcode_helper::get_barcode_lc($item_id);
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
            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status_open != "'.$this->config->item('system_status_delete').'"', 'status_open_forward = "'.$this->config->item('system_status_yes').'"', 'status_release = "'.$this->config->item('system_status_complete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Receive Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($result['status_receive']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You Can Not Modify LC Because LC Receive Completed.';
                $this->json_return($ajax);
            }
            if($result['status_open']==$this->config->item('system_status_closed'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Closed.';
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

        $results=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$id,'quantity_open > 0'));
        $old_varieties=array();
        if($results)
        {
            foreach($results as $result)
            {
                $old_varieties[$result['variety_id']][$result['pack_size_id']]=$result;
            }
        }

        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('id ASC'));
        $pack_sizes=array();
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]['value']=$result['value'];
            $pack_sizes[$result['value']]['text']=$result['text'];
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['date_updated'] = $time;
        $data['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_sms_lc_receive_histories'),$data, array('lc_id='.$id,'revision=1'), false);

        $this->db->where('lc_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_sms_lc_receive_histories'));

        $quantity_receive_kg=0;
        foreach($items as $item)
        {
            if(isset($old_varieties[$item['variety_id']][$item['pack_size_id']]))
            {
                $lc_detail_id=$old_varieties[$item['variety_id']][$item['pack_size_id']]['id'];

                if($item['pack_size_id']==0)
                {
                    $quantity_receive_kg+=$item['quantity_receive'];
                }
                else
                {
                    if(isset($pack_sizes[$item['pack_size_id']]['text']))
                    {
                        $quantity_receive_kg+=(($pack_sizes[$item['pack_size_id']]['text']*$item['quantity_receive'])/1000);
                    }
                }

                $data=array();
                $data['receive_warehouse_id']=$item['receive_warehouse_id'];
                $data['quantity_receive']=$item['quantity_receive'];
                $data['carton_number_receive']=$item['carton_number_receive'];
                $data['carton_size_receive']=$item['carton_size_receive'];
                $this->db->set('revision_receive_count', 'revision_receive_count+1', FALSE);
                Query_helper::update($this->config->item('table_sms_lc_details'),$data, array('id='.$lc_detail_id), false);

                $data=array();
                $data['lc_id']=$id;
                $data['variety_id']=$old_varieties[$item['variety_id']][$item['pack_size_id']]['variety_id'];
                $data['pack_size_id']=$old_varieties[$item['variety_id']][$item['pack_size_id']]['pack_size_id'];
                $data['warehouse_id']=$item['receive_warehouse_id'];
                $data['quantity']=$item['quantity_receive'];
                $data['revision'] = 1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_lc_receive_histories'),$data, false);
            }
        }
        $item_head['date_receive']=System_helper::get_time($item_head['date_receive']);
        $item_head['date_packing_list']=System_helper::get_time($item_head['date_packing_list']);
        $item_head['quantity_receive_kg']=$quantity_receive_kg;
        $item_head['date_receive_updated']=$time;
        $item_head['user_receive_updated']=$user->user_id;
        $this->db->set('revision_receive_count', 'revision_receive_count+1', FALSE);
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
    private function system_receive_complete($id)
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
            $this->db->select('fy.name fiscal_year');
            $this->db->select('currency.name currency_name');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = lco.user_release_completed','INNER');
            $this->db->select('ui.name user_full_name');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open_forward',$this->config->item('system_status_yes'));
            $this->db->where('lco.status_release',$this->config->item('system_status_complete'));
            $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Receive Complete Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['revision_receive_count']==0)
            {
                $ajax['status']=false;
                $ajax['system_message']='You have to complete your (LC) edit receive.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Received Completed.';
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_closed'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Closed.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = lcd.receive_warehouse_id','LEFT');
            $this->db->where('lcd.lc_id',$item_id);
            $this->db->where('lcd.quantity_open >0');
            $this->db->order_by('lcd.id','ASC');
            $data['items']=$this->db->get()->result_array();
            /*$item_zero_count=0;
            foreach($data['items'] as $item)
            {
                if($item['quantity_receive']==0)
                {
                    ++$item_zero_count;
                }
            }
            if(count($data['items']) == $item_zero_count)
            {
                $ajax['status']=false;
                $ajax['system_message']='You have to complete your (LC) edit receive.';
                $this->json_return($ajax);
            }*/

            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="LC Receive :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/receive_complete",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/receive_complete/'.$item_id);
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
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->select('fy.name fiscal_year');
            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->select('currency.name currency_name');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = lco.user_release_completed','INNER');
            $this->db->select('ui.name user_full_name');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open_forward',$this->config->item('system_status_yes'));
            $this->db->where('lco.status_release',$this->config->item('system_status_complete'));
            $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Receive Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['revision_receive_count']==0)
            {
                $ajax['status']=false;
                $ajax['system_message']='You have to complete your (LC) edit receive.';
                $this->json_return($ajax);
            }
            if($data['item']['status_receive']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Received Completed.';
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_closed'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Closed.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = lcd.receive_warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('lcd.lc_id',$item_id);
            $this->db->where('lcd.quantity_open >0');
            $this->db->order_by('lcd.id','ASC');
            $data['items']=$this->db->get()->result_array();

            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="LC Receive :: ".Barcode_helper::get_barcode_lc($item_id);
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
    private function system_details_print($id)
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
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->select('fy.name fiscal_year');
            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->select('currency.name currency_name');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.user_id = lco.user_release_completed','INNER');
            $this->db->select('ui.name user_full_name');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open_forward',$this->config->item('system_status_yes'));
            $this->db->where('lco.status_release',$this->config->item('system_status_complete'));
            $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Receive Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_closed'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Already Closed.';
                $this->json_return($ajax);
            }

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
            $data['warehouses']=Query_helper::get_info($this->config->item('table_login_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="LC Receive :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details_print",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details_print/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_receive_complete()
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
            if($item_head['status_receive']!=$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Receive LC is required.';
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status_open != "'.$this->config->item('system_status_delete').'"', 'status_open_forward = "'.$this->config->item('system_status_yes').'"', 'status_release = "'.$this->config->item('system_status_complete').'"', 'status_receive = "'.$this->config->item('system_status_pending').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Receive Completed LC Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($result['revision_receive_count']==0)
            {
                $ajax['status']=false;
                $ajax['system_message']='You have to complete your (LC) edit receive.';
                $this->json_return($ajax);
            }

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $results=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$id,'quantity_receive > 0'));
        if(!$result)
        {
            $ajax['status']=false;
            $ajax['system_message']='You have to complete your (LC) edit receive.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($results as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }
        $current_stocks=System_helper::get_variety_stock($variety_ids);

        foreach($results as $result)
        {
            if(isset($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['receive_warehouse_id']]))
            {
                $data=array();
                $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['receive_warehouse_id']]['current_stock']+$result['quantity_receive']);
                $data['in_lc']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['receive_warehouse_id']]['in_lc']+$result['quantity_receive']);
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'warehouse_id='.$result['receive_warehouse_id']));
            }
            else
            {
                $data=array();
                $data['variety_id'] = $result['variety_id'];
                $data['pack_size_id'] = $result['pack_size_id'];
                $data['warehouse_id'] = $result['receive_warehouse_id'];
                $data['current_stock'] = $result['quantity_receive'];
                $data['in_lc'] = $result['quantity_receive'];
                $data['date_updated'] = $time;
                $data['user_updated'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_stock_summary_variety'),$data);
            }
        }

        $item_head['date_receive_completed']=$time;
        $item_head['user_receive_completed']=$user->user_id;
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
        $items=$this->input->post('items');
        if((sizeof($items)>0))
        {
            foreach($items as $item)
            {
                /// empty checking
                if(!($item['receive_warehouse_id']>0 && is_numeric($item['receive_warehouse_id'])))
                {
                    $this->message='Warehouse is empty (variety info :: '.$item['variety_id'].').';
                    return false;
                }
                if(!(($item['quantity_receive']>0)))
                {
                    $this->message='Invalid input (variety info :: '.$item['variety_id'].').';
                    return false;
                }
            }
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id','ID','required');
        $this->form_validation->set_rules('item[date_packing_list]',$this->lang->line('LABEL_DATE_PACKING_LIST'),'required');
        $this->form_validation->set_rules('item[packing_list_number]',$this->lang->line('LABEL_NUMBER_PACKING_LIST'),'required');
        $this->form_validation->set_rules('item[lot_number]',$this->lang->line('LABEL_NUMBER_LOT'),'required');
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
            $data['system_preference_items']= $this->get_preference();
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
        $data['date_expected']= 1;
        $data['principal_name']= 1;
        $data['currency_name']= 1;
        $data['lc_number']= 1;
        $data['consignment_name']= 1;
        $data['quantity_release_kg']= 1;
        $data['quantity_receive_kg']= 1;
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
}
