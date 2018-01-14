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
            $result=Query_helper::get_info($this->config->item('table_login_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
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
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->select('fy.name fiscal_year_name');
        $this->db->select('principal.name principal_name');
        $this->db->select('sc.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lc.year_id','INNER');
        $this->db->join($this->config->item('table_sms_setup_currency').' sc','sc.id = lc.currency_id','INNER');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->order_by('lc.year_id','DESC');
        $this->db->order_by('lc.id','DESC');
        $results=$this->db->get()->result_array();

        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['fiscal_year_name']=$result['fiscal_year_name'];
            $item['month_name']=$this->lang->line("LABEL_MONTH_$result[month_id]");
            $item['date_opening']=System_helper::display_date($result['date_opening']);
            $item['date_expected']=System_helper::display_date($result['date_expected']);
            $item['principal_name']=$result['principal_name'];
            $item['currency_name']=$result['currency_name'];
            $item['lc_number']=$result['lc_number'];
            $item['consignment_name']=$result['consignment_name'];
            $item['price_total_currency']=$result['price_total_currency'];
            $item['other_cost_currency']=$result['other_cost_currency'];
            $item['status_received']=$result['status_received'];
            $item['status_release']=$result['status_release'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $data['item']=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$item_id),1);
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC - Data Not Found. Please Try Again.';
                $this->json_return($ajax);
            }

            $fiscal_year_id=isset($data['item']['year_id'])?$data['item']['year_id']:'';
            $principal_id=isset($data['item']['principal_id'])?$data['item']['principal_id']:'';
            /*if($data['item']['status_received']==$this->config->item('system_status_yes'))
            {
                if(!(isset($this->permissions['action3'])&&($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Already product received, you can not edit this LC';
                    $this->json_return($ajax);
                }
            }*/

            $data['items']=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$item_id));
            //print_r($data['items']);exit;

            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array("id=$fiscal_year_id",'status ="'.$this->config->item('system_status_active').'"'),1,0,array('id ASC'));
            $data['currencies']=Query_helper::get_info($this->config->item('table_sms_setup_currency'),array('id value','name text','amount_rate_budget'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering'));
            /*$data['currency_rates']=array();
            foreach($data['currencies'] as $rate)
            {
                $data['currency_rates'][$rate['value']]=$rate['amount_rate_budget'];
            }*/
            $data['principals']=Query_helper::get_info($this->config->item('table_login_basic_setup_principal'),array('id value','name text'),array("id=$principal_id",'status !="'.$this->config->item('system_status_delete').'"'),1,0,array('ordering'));

            $results=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
            $data['packs']=array();
            foreach($results as $result)
            {
                $data['packs'][$result['value']]=$result;
            }

            /*get armalik variety*/
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.id value,v.name');
            $this->db->select('vp.name_import text');
            $this->db->join($this->config->item('table_login_setup_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->where('v.status',$this->config->item('system_status_active'));
            $this->db->where('v.whose','ARM');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
            $data['varieties']=array();
            foreach($results as $result)
            {
                $data['varieties'][$result['value']]=$result;
            }

            $data['title']="Edit LC (".$data['item']['lc_number'].')';
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
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        $time=time();
        $data=$this->input->post('item');
        $varieties=$this->input->post('varieties');
        if($varieties)
        {
            if(!$this->check_validation_for_varieties())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
                die();
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            if(!$this->check_validation())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_lc_open').' lc');
            $this->db->select('lc.*');
            $this->db->select('lc_details.*');
            $this->db->join($this->config->item('table_sms_lc_details').' lc_details','lc_details.lc_id = lc.id','LEFT');
            $this->db->where('lc.id',$id);
            $this->db->where('lc.status',$this->config->item('system_status_active'));
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC - LC Number Not Found.Please Try Again.';
                $this->json_return($ajax);
                die();
            }
            if($result['status_release']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You Are Not Modify LC Because LC Release Completed. Please Try Again.';
                $this->json_return($ajax);
                die();
            }

            $price_total_currency=0;
            foreach($varieties as $v)
            {
                /*if($v['quantity_type_id']==0)
                {
                    $price_total_currency+=$v['price_currency'];
                }
                else
                {
                    $price_total_currency+=($v['quantity_order']*$v['price_currency']);
                }*/
                $price_total_currency+=($v['quantity_order']*$v['price_currency']);
            }
            foreach($varieties as $v)
            {
                if($v['lc_detail_id']>0)
                {
                    if($v['old_quantity_order']!=$v['quantity_order'] && $v['old_price_currency']!=$v['price_currency'])
                    {
                        $v_data=array();
                        $v_data['lc_id']=$id;
                        $v_data['variety_id']=$v['variety_id'];
                        $v_data['quantity_type_id']=$v['quantity_type_id'];
                        $v_data['quantity_order']=$v['quantity_order'];
                        $v_data['price_currency']=$v['price_currency'];
                        $v_data['date_updated'] = $time;
                        $v_data['user_updated'] = $user->user_id;
                        $this->db->set('revision', 'revision+1', FALSE);
                        Query_helper::update($this->config->item('table_sms_lc_details'),$v_data, array('id='.$v['lc_detail_id']));
                        unset($v_data['lc_id']);
                        unset($v_data['revision']);
                        unset($v_data['date_updated']);
                        unset($v_data['user_updated']);
                        $v_data['lc_detail_id'] = $v['lc_detail_id'];
                        Query_helper::add($this->config->item('table_sms_lc_detail_revisions'),$v_data);
                    }
                }
                else
                {
                    $v_data=array();
                    $v_data['lc_id']=$id;
                    $v_data['variety_id']=$v['variety_id'];
                    $v_data['quantity_type_id']=$v['quantity_type_id'];
                    $v_data['quantity_order']=$v['quantity_order'];
                    $v_data['price_currency']=$v['price_currency'];
                    /*$v_data['amount_price_total_order']=$v['quantity_order']*$v['amount_price_order']*$data['amount_currency_rate'];*/
                    $v_data['revision']=1;
                    $v_data['date_created'] = $time;
                    $v_data['user_created'] = $user->user_id;
                    $lc_detail_id=Query_helper::add($this->config->item('table_sms_lc_details'),$v_data);
                    unset($v_data['lc_id']);
                    unset($v_data['revision']);
                    $v_data['lc_detail_id'] = $lc_detail_id;
                    Query_helper::add($this->config->item('table_sms_lc_detail_revisions'),$v_data);
                }
            }

            $data['date_expected']=System_helper::get_time($data['date_expected']);
            $data['price_total_currency']=$price_total_currency;
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            $this->db->set('revision', 'revision+1', FALSE);
            Query_helper::update($this->config->item('table_sms_lc_open'),$data,array('id='.$id));
        }
        else
        {
            if(!$this->check_validation())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
            if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
            {
                $price_total_currency=0;
                if($varieties)
                {
                    foreach($varieties as $variety)
                    {
                        /*if($variety['quantity_type_id']==0)
                        {
                            $price_total_currency+=$variety['price_currency'];
                        }
                        else
                        {
                            $price_total_currency+=($variety['quantity_order']*$variety['price_currency']);
                        }*/
                        $price_total_currency+=($variety['quantity_order']*$variety['price_currency']);
                    }
                }
                $data['date_expected']=System_helper::get_time($data['date_expected']);
                $data['date_opening']=System_helper::get_time($data['date_opening']);
                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                $data['status'] = $this->config->item('system_status_active');
                $data['price_total_currency'] = $price_total_currency;
                $data['price_total_taka'] = 0;
                $data['other_cost_taka'] = 0;
                $lc_id=Query_helper::add($this->config->item('table_sms_lc_open'),$data);
                //varieties
                if($varieties)
                {
                    foreach($varieties as $v)
                    {
                        $v_data=array();
                        $v_data['lc_id']=$lc_id;
                        $v_data['variety_id']=$v['variety_id'];
                        $v_data['quantity_type_id']=$v['quantity_type_id'];
                        $v_data['quantity_order']=$v['quantity_order'];
                        $v_data['price_currency']=$v['price_currency'];
                        //$v_data['amount_price_total_order']=$v['quantity_order']*$v['amount_price_order']*$data['amount_currency_rate'];
                        $v_data['revision']=1;
                        $v_data['date_created'] = $time;
                        $v_data['user_created'] = $user->user_id;
                        $lc_detail_id=Query_helper::add($this->config->item('table_sms_lc_details'),$v_data);
                        unset($v_data['lc_id']);
                        unset($v_data['revision']);
                        $v_data['lc_detail_id'] = $lc_detail_id;
                        Query_helper::add($this->config->item('table_sms_lc_detail_revisions'),$v_data);
                    }
                }
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
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
            $this->form_validation->set_rules('item[year_id]',$this->lang->line('LABEL_FISCAL_YEAR'),'required');
            $this->form_validation->set_rules('item[month_id]',$this->lang->line('LABEL_MONTH'),'required');
            $this->form_validation->set_rules('item[date_opening]',$this->lang->line('LABEL_DATE_OPENING'),'required');
            $this->form_validation->set_rules('item[principal_id]',$this->lang->line('LABEL_PRINCIPAL_NAME'),'required');
            if(!isset($item['date_opening']) || !strtotime($item['date_opening']))
            {
                $this->message='LC opening date is not correct. Please Try again.';
                return false;
            }
        }
        if(!isset($item['date_expected']) || !strtotime($item['date_expected']))
        {
            $this->message='LC expected date is not correct. Please Try again.';
            return false;
        }
        $this->form_validation->set_rules('item[date_expected]',$this->lang->line('LABEL_DATE_EXPECTED'),'required');
        $this->form_validation->set_rules('item[lc_number]',$this->lang->line('LABEL_LC_NUMBER'),'required');
        $this->form_validation->set_rules('item[currency_id]',$this->lang->line('LABEL_CURRENCY_NAME'),'required');
        $this->form_validation->set_rules('item[consignment_name]',$this->lang->line('LABEL_CONSIGNMENT_NAME'),'required');
        $this->form_validation->set_rules('item[other_cost_currency]',$this->lang->line('LABEL_OTHER_COST_CURRENCY'),'required');
        //$this->form_validation->set_rules('item[amount_currency_rate]',$this->lang->line('LABEL_CURRENCY_RATE'),'required');
        //$this->form_validation->set_rules('item[status]',$this->lang->line('STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_for_varieties()
    {
        $varieties=$this->input->post('varieties');
        if(!(sizeof($varieties)>0))
        {
            return true;
        }
        else
        {
            $duplicate_variety=array();
            $status_duplicate_variety=false;
            foreach($varieties as $variety)
            {
                if(!(($variety['variety_id']>0) && ($variety['quantity_type_id']>=0) && ($variety['quantity_order']>0) && ($variety['price_currency']>0)))
                {
                    $this->message='Please properly data insert (variety info). Try again.';
                    return false;
                }
                if(isset($duplicate_variety[$variety['variety_id']][$variety['quantity_type_id']]))
                {
                    $duplicate_variety[$variety['variety_id']][$variety['quantity_type_id']]+=1;
                    $status_duplicate_variety=true;
                }
                else
                {
                    $duplicate_variety[$variety['variety_id']][$variety['quantity_type_id']]=1;
                }
            }
            if($status_duplicate_variety==true)
            {
                $this->message='You have a mistake (variety duplicate entry). Try again.';
                return false;
            }
        }
        return true;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_login_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
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
        $items=array();
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

            $result=Query_helper::get_info($this->config->item('table_login_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            if($result)
            {
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;
                $data['preferences']=json_encode($items);
                Query_helper::update($this->config->item('table_login_setup_user_preference'),$data,array('id='.$result['id']),false);
            }
            else
            {
                $data['user_id']=$user->user_id;
                $data['controller']=$this->controller_url;
                $data['method']='list';
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['preferences']=json_encode($items);
                Query_helper::add($this->config->item('table_login_setup_user_preference'),$data,false);
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
