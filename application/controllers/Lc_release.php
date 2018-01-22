<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_release extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Lc_release');
        $this->controller_url='lc_release';
        $this->load->helper('barcode');
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
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            $this->system_save_preference();
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
            $result=Query_helper::get_info($this->config->item('table_sms_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['items']['barcode']= 1;
            $data['items']['fiscal_year_name']= 1;
            $data['items']['month_name']= 1;
            $data['items']['date_opening']= 1;
            $data['items']['date_expected']= 1;
            $data['items']['principal_name']= 1;
            $data['items']['currency_name']= 1;
            $data['items']['lc_number']= 1;
            $data['items']['consignment_name']= 1;
            $data['items']['price_other_cost_total_currency']= 1;
            $data['items']['quantity_total_kg']= 1;
            $data['items']['price_variety_total_currency']= 1;
            $data['items']['price_total_currency']= 1;
            $data['items']['status_forward']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $data['preferences']=json_decode($result['preferences'],true);
                    foreach($data['items'] as $key=>$value)
                    {
                        if(isset($data['preferences'][$key]))
                        {
                            $data['items'][$key]=$value;
                        }
                        else
                        {
                            $data['items'][$key]=0;
                        }
                    }
                }
            }

            $data['title']="LC Release List";
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
        $this->db->select('fy.name fiscal_year_name');
        $this->db->select('principal.name principal_name');
        $this->db->select('currency.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lc.fiscal_year_id','INNER');
        $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->where('lc.status_forward',$this->config->item('system_status_yes'));
        $this->db->where('lc.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('lc.fiscal_year_id','DESC');
        $this->db->order_by('lc.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $results=$this->db->get()->result_array();

        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=Barcode_helper::get_barcode_lc_open($result['id']);
            $item['fiscal_year_name']=$result['fiscal_year_name'];
            $item['month_name']=$this->lang->line("LABEL_MONTH_$result[month_id]");
            $item['date_opening']=System_helper::display_date($result['date_opening']);
            $item['date_expected']=System_helper::display_date($result['date_expected']);
            $item['principal_name']=$result['principal_name'];
            $item['currency_name']=$result['currency_name'];
            $item['lc_number']=$result['lc_number'];
            $item['consignment_name']=$result['consignment_name'];
            $item['quantity_total_kg']=number_format($result['quantity_total_kg'],3);
            $item['price_other_cost_total_currency']=number_format($result['price_other_cost_total_currency'],2);
            $item['price_variety_total_currency']=number_format($result['price_variety_total_currency'],2);
            $item['price_total_currency']=number_format($result['price_total_currency'],2);
            $item['status_forward']=$result['status_forward'];
            $items[]=$item;
        }
        $this->json_return($items);
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
            $this->db->select('fy.name fiscal_year_name');
            $this->db->select('currency.name currency_name');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status !=',$this->config->item('system_status_delete'));
            $this->db->where('lco.status_forward',$this->config->item('system_status_yes'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }

            if($data['item']['status_release']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You Can not Modify LC Because LC Release Completed.';
                $this->json_return($ajax);
                die();
            }

            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->select('pack.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->where('lcd.lc_id',$item_id);
            $data['items']=$this->db->get()->result_array();

            $data['title']="LC Release :: ".Barcode_helper::get_barcode_lc_open($item_id);
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
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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

        $lc_open_result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"', 'status_forward = "'.$this->config->item('system_status_yes').'"'),1);
        if(!$lc_open_result)
        {
            System_helper::invalid_try('Update Non Exists',$id);
            $ajax['status']=false;
            $ajax['system_message']='Invalid LC.';
            $this->json_return($ajax);
        }
        if($lc_open_result['status_release']==$this->config->item('system_status_complete'))
        {
            $ajax['status']=false;
            $ajax['system_message']='You Can Not Modify LC Because LC Release Completed.';
            $this->json_return($ajax);
            die();
        }
        $result=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$id, 'quantity_order>0'));
        $old_varieties=array();
        if($result)
        {
            foreach($result as $row)
            {
                $old_varieties[$row['variety_id']][$row['pack_size_id']]=$row;
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $time=time();
        $data=$this->input->post('item');
        $varieties=$this->input->post('varieties');
        $price_release_total_currency=0;
        foreach($varieties as $variety)
        {
            if(isset($old_varieties[$variety['variety_id']][$variety['pack_size_id']]))
            {
                $lc_detail_id=$old_varieties[$variety['variety_id']][$variety['pack_size_id']]['lc_detail_id'];
                $variety_currency=$old_varieties[$variety['variety_id']][$variety['pack_size_id']]['price_currency'];
                $price_release_total_currency+=($variety['quantity_release']*$variety_currency);

                $variety_data=array();
                $variety_data['quantity_release']=$variety['quantity_release'];
                $this->db->set('revision', 'revision+1', FALSE);
                Query_helper::update($this->config->item('table_sms_lc_details'),$variety_data, array('id='.$lc_detail_id));
                unset($variety_data['revision']);
                $variety_data['lc_detail_id'] = $lc_detail_id;
                Query_helper::add($this->config->item('table_sms_lc_detail_revisions'),$variety_data);
            }
        }

        $lc_data['price_total_currency']=$price_release_total_currency;
        $lc_data['price_total_taka']=$data['price_total_taka'];
        $lc_data['date_release_updated']=$time;
        $lc_data['user_release_updated']=$user->user_id;
        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_sms_lc_open'),$lc_data,array('id='.$id));

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
        $varieties=$this->input->post('varieties');
        if((sizeof($varieties)>0))
        {
            foreach($varieties as $variety)
            {
                /// empty checking
                if(!($variety['quantity_release']>=0))
                {
                    $this->message='Please properly data insert (variety info :: '.$variety['variety_id'].'). Try again.';
                    return false;
                }
            }
        }
        return true;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_sms_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['items']['id']= 1;
            $data['items']['fiscal_year_name']= 1;
            $data['items']['month_name']= 1;
            $data['items']['date_opening']= 1;
            $data['items']['date_expected']= 1;
            $data['items']['principal_name']= 1;
            $data['items']['currency_name']= 1;
            $data['items']['lc_number']= 1;
            $data['items']['consignment_name']= 1;
            $data['items']['price_total_currency']= 1;
            $data['items']['other_cost_currency']= 1;
            $data['items']['status_received']= 1;
            $data['items']['status_release']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $data['preferences']=json_decode($result['preferences'],true);
                    foreach($data['items'] as $key=>$value)
                    {
                        if(isset($data['preferences'][$key]))
                        {
                            $data['items'][$key]=$value;
                        }
                        else
                        {
                            $data['items'][$key]=0;
                        }
                    }
                }
            }

            $data['title']="Set Preference";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/preference",$data,true));
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
    private function system_save_preference()
    {
        if($this->input->post('item'))
        {
            $items=$this->input->post('item');
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_PLEASE_SELECT_ANY_ONE");
            $this->json_return($ajax);
            die();
        }

        $user = User_helper::get_user();
        if(!(isset($this->permissions['action0']) && ($this->permissions['action0']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        else
        {
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START

            $result=Query_helper::get_info($this->config->item('table_sms_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            if($result)
            {
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;
                $data['preferences']=json_encode($items);
                Query_helper::update($this->config->item('table_sms_setup_user_preference'),$data,array('id='.$result['id']),false);
            }
            else
            {
                $data['user_id']=$user->user_id;
                $data['controller']=$this->controller_url;
                $data['method']='list';
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['preferences']=json_encode($items);
                Query_helper::add($this->config->item('table_sms_setup_user_preference'),$data,false);
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
    }
}
