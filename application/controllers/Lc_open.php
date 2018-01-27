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
        $this->permissions=User_helper::get_permission('Lc_open');
        $this->controller_url='lc_open';
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
        elseif($action=="details_all_lc")
        {
            $this->system_details_all_lc($id);
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['fiscal_year_name']= 1;
            $data['system_preference_items']['month_name']= 1;
            $data['system_preference_items']['date_opening']= 1;
            $data['system_preference_items']['date_expected']= 1;
            $data['system_preference_items']['principal_name']= 1;
            $data['system_preference_items']['currency_name']= 1;
            $data['system_preference_items']['lc_number']= 1;
            $data['system_preference_items']['consignment_name']= 1;
            $data['system_preference_items']['price_other_cost_total_currency']= 1;
            $data['system_preference_items']['quantity_total_kg']= 1;
            $data['system_preference_items']['price_variety_total_currency']= 1;
            $data['system_preference_items']['price_total_currency']= 1;
            $data['system_preference_items']['status_forward']= 1;
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
        $this->db->where('lc.status_forward',$this->config->item('system_status_no'));
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
            $item['barcode']=Barcode_helper::get_barcode_lc($result['id']);
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
    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list_all"'),1);
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
        $this->db->select('fy.name fiscal_year_name');
        $this->db->select('principal.name principal_name');
        $this->db->select('currency.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lc.fiscal_year_id','INNER');
        $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
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
            $item['barcode']=Barcode_helper::get_barcode_lc($result['id']);
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
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New LC";
            $data['item']['id']=0;
            $data['item']['fiscal_year_id']=0;
            $data['item']['month_id']=date('n');
            $data['item']['date_opening']=time();
            $data['item']['date_expected']='';
            $data['item']['principal_id']=0;
            $data['item']['currency_id']=0;
            $data['item']['lc_number']='';
            $data['item']['consignment_name']='';
            $data['item']['remarks']='';
            $data['item']['price_other_cost_total_currency']=0;
            $data['item']['quantity_total_kg']=0;
            $data['item']['price_variety_total_currency']=0;
            $data['item']['price_total_currency']=0;
            $data['items']=array();

            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
            $data['currencies']=Query_helper::get_info($this->config->item('table_sms_setup_currency'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering'));
            $data['principals']=Query_helper::get_info($this->config->item('table_login_basic_setup_principal'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));

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
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->select('fy.name fiscal_year_name');
            //$this->db->join($this->config->item('table_sms_setup_currency').' sc','currency.id = lco.currency_id','INNER');
            //$this->db->select('currency.name currency_name');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->select('principal.name principal_name');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_yes'))
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
            $this->db->select('pack.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->join($this->config->item('table_login_setup_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->where('lcd.lc_id',$item_id);
            $data['items']=$this->db->get()->result_array();

            // get drop down info
            $data['currencies']=Query_helper::get_info($this->config->item('table_sms_setup_currency'),array('id value','name text','amount_rate_budget'),array('status="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));

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

            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));


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
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $time=time();
        $item=$this->input->post('item');
        $items=$this->input->post('items');
        $result_pack_size=Query_helper::get_info($this->config->item('table_login_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
        $pack_sizes=array();
        foreach($result_pack_size as $pack_size)
        {
            $pack_sizes[$pack_size['value']]['value']=$pack_size['value'];
            $pack_sizes[$pack_size['value']]['text']=$pack_size['text'];
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {

            $lc_open_result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$lc_open_result)
            {
                $this->db->trans_complete();
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($lc_open_result['status_forward']==$this->config->item('system_status_yes'))
            {
                $this->db->trans_complete();
                $ajax['status']=false;
                $ajax['system_message']='LC already forwarded.';
                $this->json_return($ajax);
                die();
            }

            $result=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$id));
            $old_varieties=array();
            if($result)
            {
                foreach($result as $row)
                {
                    $old_varieties[$row['variety_id']][$row['pack_size_id']]=$row;
                }
            }
            $data=array();
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_sms_lc_open_histories'),$data, array('lc_id='.$id,'revision=1'), false);

            $this->db->where('lc_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sms_lc_open_histories'));

            $price_variety_total_currency=0;
            $quantity_total_kg=0;
            foreach($items as $variety)
            {
                $price_variety_total_currency+=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                if($variety['pack_size_id']==0)
                {
                    $quantity_total_kg+=$variety['quantity_lc'];
                }
                else
                {
                    if(isset($pack_sizes[$variety['pack_size_id']]['text']))
                    {
                        $quantity_total_kg+=(($pack_sizes[$variety['pack_size_id']]['text']*$variety['quantity_lc'])/1000);
                    }
                }
                if(isset($old_varieties[$variety['variety_id']][$variety['pack_size_id']]))
                {
                    $lc_detail_id=$old_varieties[$variety['variety_id']][$variety['pack_size_id']]['id'];
                    $old_variety_quantity=$old_varieties[$variety['variety_id']][$variety['pack_size_id']]['quantity_lc'];
                    $old_variety_currency=$old_varieties[$variety['variety_id']][$variety['pack_size_id']]['price_unit_lc_currency'];
                    if(($old_variety_quantity!=$variety['quantity_lc']) || ($old_variety_currency!=$variety['price_unit_lc_currency']))
                    {
                        $data=array();
                        $data['quantity_lc']=$variety['quantity_lc'];
                        $data['price_unit_lc_currency']=$variety['price_unit_lc_currency'];
                        $data['price_total_lc_currency']=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                        $this->db->set('revision_count', 'revision_count+1', FALSE);
                        Query_helper::update($this->config->item('table_sms_lc_details'),$data, array('id='.$lc_detail_id), false);
                    }
                }
                else
                {
                    $data=array();
                    $data['lc_id']=$id;
                    $data['variety_id']=$variety['variety_id'];
                    $data['pack_size_id']=$variety['pack_size_id'];
                    $data['quantity_lc']=$variety['quantity_lc'];
                    $data['price_unit_lc_currency']=$variety['price_unit_lc_currency'];
                    $data['price_total_lc_currency']=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                    $data['revision_count']=1;
                    Query_helper::add($this->config->item('table_sms_lc_details'),$data, false);
                }

                $data=array();
                $data['lc_id']=$id;
                $data['variety_id']=$variety['variety_id'];
                $data['pack_size_id']=$variety['pack_size_id'];
                $data['quantity']=$variety['quantity_lc'];
                $data['price_unit_currency']=$variety['price_unit_lc_currency'];
                $data['price_total_currency']=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                $data['revision'] = 1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_lc_open_histories'),$data, false);
            }

            $item['date_opening']=System_helper::get_time($item['date_opening']);
            $item['date_expected']=System_helper::get_time($item['date_expected']);
            $item['quantity_total_kg']=$quantity_total_kg;
            $item['price_variety_total_currency']=$price_variety_total_currency;
            $item['price_total_currency']=($price_variety_total_currency+$item['price_other_cost_total_currency']);
            $item['date_updated']=$time;
            $item['user_updated']=$user->user_id;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_sms_lc_open'),$item,array('id='.$id));
        }
        else
        {
            $price_variety_total_currency=0;
            $quantity_total_kg=0;
            if($items)
            {
                foreach($items as $variety)
                {
                    $price_variety_total_currency+=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                    if($variety['pack_size_id']==0)
                    {
                        $quantity_total_kg+=$variety['quantity_lc'];
                    }
                    else
                    {
                        if(isset($pack_sizes[$variety['pack_size_id']]['text']))
                        {
                            $quantity_total_kg+=(($pack_sizes[$variety['pack_size_id']]['text']*$variety['quantity_lc'])/1000);
                        }
                    }
                }
            }

            $item['date_opening']=System_helper::get_time($item['date_opening']);
            $item['date_expected']=System_helper::get_time($item['date_expected']);
            $item['quantity_total_kg'] = $quantity_total_kg;
            $item['price_variety_total_currency'] = $price_variety_total_currency;
            $item['price_total_currency'] = ($price_variety_total_currency+$item['price_other_cost_total_currency']);
            $item['status'] = $this->config->item('system_status_active');
            $item['revision_count'] = 1;
            $item['user_created'] = $user->user_id;
            $item['date_created'] = time();
            $lc_id=Query_helper::add($this->config->item('table_sms_lc_open'),$item);
            //varieties
            foreach($items as $variety)
            {
                $data=array();
                $data['lc_id']=$lc_id;
                $data['variety_id']=$variety['variety_id'];
                $data['pack_size_id']=$variety['pack_size_id'];
                $data['quantity_lc']=$variety['quantity_lc'];
                $data['price_unit_lc_currency']=$variety['price_unit_lc_currency'];
                $data['price_total_lc_currency']=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                $data['revision_count']=1;
                Query_helper::add($this->config->item('table_sms_lc_details'),$data, false);

                $data=array();
                $data['lc_id']=$lc_id;
                $data['variety_id']=$variety['variety_id'];
                $data['pack_size_id']=$variety['pack_size_id'];
                $data['quantity']=$variety['quantity_lc'];
                $data['price_unit_currency']=$variety['price_unit_lc_currency'];
                $data['price_total_currency']=($variety['quantity_lc']*$variety['price_unit_lc_currency']);
                $data['revision']=1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_sms_lc_open_histories'),$data, false);
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
            $this->db->select('fy.name fiscal_year_name');
            $this->db->select('currency.name currency_name');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
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
            $this->db->where('lcd.quantity_lc >0');
            $this->db->order_by('lcd.id ASC');
            $data['items']=$this->db->get()->result_array();

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
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_details_all_lc($id)
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
            $this->db->select('fy.name fiscal_year_name');
            $this->db->select('currency.name currency_name');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
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
            $this->db->where('lcd.quantity_lc >0');
            $this->db->order_by('lcd.id ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="LC Details :: ".Barcode_helper::get_barcode_lc($item_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details_all_lc",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details_all_lc/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_delete($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$item_id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Delete Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($result['status_release']==$this->config->item('system_status_complete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already LC Released.';
                $this->json_return($ajax);
            }
            $this->db->trans_start();  //DB Transaction Handle START
            Query_helper::update($this->config->item('table_sms_lc_open'),array('status'=>$this->config->item('system_status_delete')),array("id = ".$item_id));
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
    private function system_lc_forward($id)
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
            $this->db->select('fy.name fiscal_year_name');
            $this->db->select('currency.name currency_name');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Forwarded LC Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Already forwarded this LC :: '. Barcode_helper::get_barcode_lc($item_id);
                $this->json_return($ajax);
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
            $this->db->where('lcd.quantity_lc >0');
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
        if($id>0)
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || !(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            System_helper::invalid_try('Forward Access Denied',$id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $data=$this->input->post('item');
        if($data['status_forward']==$this->config->item('system_status_yes'))
        {
            $lc_open_result=Query_helper::get_info($this->config->item('table_sms_lc_open'),'*',array('id ='.$id),1);
            if(!$lc_open_result)
            {
                System_helper::invalid_try('Forwarded LC Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }
            else
            {
                $time=time();
                $data['date_forward_updated']=$time;
                $data['user_forward_updated']=$user->user_id;
                //$this->db->set('revision_count', 'revision_count+1', FALSE);
                $update_lc=Query_helper::update($this->config->item('table_sms_lc_open'),$data,array('id='.$id));
                if($update_lc)
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
        else
        {
            $ajax['status']=false;
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
    }
    private function check_validation()
    {
        $items=$this->input->post('items');
        if((sizeof($items)>0))
        {
            $duplicate_variety=array();
            $status_duplicate_variety=false;
            foreach($items as $variety)
            {
                /// empty checking
                if(!(($variety['variety_id']>0) && ($variety['pack_size_id']>=0) && ($variety['quantity_lc']>=0) && ($variety['price_unit_lc_currency']>=0)))
                {
                    $this->message='Invalid input (variety info :: '.$variety['variety_id'].').';
                    return false;
                }
                // duplicate variety checking
                if(isset($duplicate_variety[$variety['variety_id']][$variety['pack_size_id']]))
                {
                    $duplicate_variety[$variety['variety_id']][$variety['pack_size_id']]+=1;
                    $status_duplicate_variety=true;
                }
                else
                {
                    $duplicate_variety[$variety['variety_id']][$variety['pack_size_id']]=1;
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

        $this->load->library('form_validation');
        $id = $this->input->post("id");
        $item = $this->input->post("item");
        if($id==0)
        {
            $this->form_validation->set_rules('item[fiscal_year_id]',$this->lang->line('LABEL_FISCAL_YEAR'),'required');
            $this->form_validation->set_rules('item[month_id]',$this->lang->line('LABEL_MONTH'),'required');
            $this->form_validation->set_rules('item[date_opening]',$this->lang->line('LABEL_DATE_OPENING'),'required');
            $this->form_validation->set_rules('item[principal_id]',$this->lang->line('LABEL_PRINCIPAL_NAME'),'required');
            if(!isset($item['date_opening']) || !strtotime($item['date_opening']))
            {
                $this->message='LC opening date is not correct formation.';
                return false;
            }
        }
        if(!isset($item['date_expected']) || !strtotime($item['date_expected']))
        {
            $this->message='LC expected date is not correct formation.';
            return false;
        }
        $this->form_validation->set_rules('item[date_expected]',$this->lang->line('LABEL_DATE_EXPECTED'),'required');
        $this->form_validation->set_rules('item[lc_number]',$this->lang->line('LABEL_LC_NUMBER'),'required');
        $this->form_validation->set_rules('item[currency_id]',$this->lang->line('LABEL_CURRENCY_NAME'),'required');
        $this->form_validation->set_rules('item[consignment_name]',$this->lang->line('LABEL_CONSIGNMENT_NAME'),'required');
        $this->form_validation->set_rules('item[price_other_cost_total_currency]',$this->lang->line('LABEL_OTHER_COST_CURRENCY'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
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
        $this->db->join($this->config->item('table_login_setup_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$principal_id.' AND vp.revision = 1','INNER');
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['barcode']= 1;
            $data['system_preference_items']['fiscal_year_name']= 1;
            $data['system_preference_items']['month_name']= 1;
            $data['system_preference_items']['date_opening']= 1;
            $data['system_preference_items']['date_expected']= 1;
            $data['system_preference_items']['principal_name']= 1;
            $data['system_preference_items']['currency_name']= 1;
            $data['system_preference_items']['lc_number']= 1;
            $data['system_preference_items']['consignment_name']= 1;
            $data['system_preference_items']['price_other_cost_total_currency']= 1;
            $data['system_preference_items']['quantity_total_kg']= 1;
            $data['system_preference_items']['price_variety_total_currency']= 1;
            $data['system_preference_items']['price_total_currency']= 1;
            $data['system_preference_items']['status_forward']= 1;
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list_all"'),1);
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

            $data['title']="Set Preference";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/preference_all_lc",$data,true));
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

}
