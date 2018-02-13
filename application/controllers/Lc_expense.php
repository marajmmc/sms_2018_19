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
        $this->permissions=User_helper::get_permission('Lc_expense');
        $this->controller_url='lc_expense';
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['fiscal_year']= 1;
            $data['system_preference_items']['month']= 1;
            $data['system_preference_items']['date_opening']= 1;
            $data['system_preference_items']['date_expected']= 1;
            $data['system_preference_items']['principal_name']= 1;
            $data['system_preference_items']['currency_name']= 1;
            $data['system_preference_items']['lc_number']= 1;
            $data['system_preference_items']['consignment_name']= 1;
            $data['system_preference_items']['price_release_other_currency']= 1;
            $data['system_preference_items']['quantity_receive_kg']= 1;
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

            $data['title']="LC Expense List";
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
        $this->db->select('fy.name fiscal_year');
        $this->db->select('principal.name principal_name');
        $this->db->select('currency.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
        $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
        $this->db->where('lco.status_open =',$this->config->item('system_status_active'));
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
            $item['price_release_other_currency']=number_format($result['price_release_other_currency'],2);
            $item['quantity_receive_kg']=number_format($result['quantity_receive_kg'],3,'.','');
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
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->select('fy.name fiscal_year');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->select('currency.name currency_name');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_setup_bank_account').' ba','ba.id = lco.bank_account_id','INNER');
            $this->db->join($this->config->item('table_login_setup_bank').' bank','bank.id = ba.bank_id','INNER');
            $this->db->select("CONCAT_WS(' ( ',ba.account_number,  CONCAT_WS('', bank.name,' - ',ba.branch_name,')')) bank_account_number");
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open =',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Expense Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }

            $data['items']=Query_helper::get_info($this->config->item('table_sms_direct_cost_items'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
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
                System_helper::invalid_try('Update Expense Non Exists',$id);
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
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->select('fy.name fiscal_year');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
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
                System_helper::invalid_try('Edit Expense Complete Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }

            $data['items']=Query_helper::get_info($this->config->item('table_sms_direct_cost_items'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
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

            if($item_head['status_open']!=$this->config->item('system_status_closed'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Closed is required.';
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
            $data_lc['status_open']=$this->config->item('system_status_closed');
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
       /*$items=$this->input->post('items');
       f((sizeof($items)>0))
       {
           foreach($items as $item)
           {
               /// empty checking
               if(!($item['quantity_expense']>=0))
               {
                   $this->message='Invalid input (variety info :: '.$item['variety_id'].').';
                   return false;
               }
           }
       }*/
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['fiscal_year']= 1;
            $data['system_preference_items']['month']= 1;
            $data['system_preference_items']['date_opening']= 1;
            $data['system_preference_items']['date_expected']= 1;
            $data['system_preference_items']['principal_name']= 1;
            $data['system_preference_items']['currency_name']= 1;
            $data['system_preference_items']['lc_number']= 1;
            $data['system_preference_items']['consignment_name']= 1;
            $data['system_preference_items']['price_release_other_currency']= 1;
            $data['system_preference_items']['quantity_receive_kg']= 1;
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
