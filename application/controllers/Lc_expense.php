<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_expense extends Root_Controller
{
    private  $message;
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
        elseif($action=="expense")
        {
            $this->system_expense($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
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
            $data['title']="LC List For Expense";
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
        $item=array();
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->select('fy.name fiscal_year_name');
        $this->db->select('principal.name principal_name');
        $this->db->select('currency.name currency_name');
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lc.year_id','INNER');
        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->order_by('lc.year_id','DESC');
        $this->db->order_by('lc.id','DESC');
        $items=$this->db->get()->result_array();
//        print_r($items);
//        exit;
        foreach($items as &$item)
        {
            $item['month_name']=$this->lang->line("LABEL_MONTH_$item[month_id]");
            $item['date_opening']=System_helper::display_date($item['date_opening']);
            $item['date_expected']=System_helper::display_date($item['date_expected']);

        }
        $this->json_return($items);
    }

    private function system_expense($id)
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->select('lc_open.*');
            $this->db->select('year.name year_name');
            $this->db->select('principal.name principal_name');
            $this->db->select('currency.name currency_name');
            $this->db->from($this->config->item('table_sms_lc_open').' lc_open');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' year','year.id = lc_open.year_id','LEFT');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc_open.principal_id','LEFT');
            $this->db->join($this->config->item('table_sms_setup_currency').' currency','currency.id = lc_open.currency_id','LEFT');
            $this->db->where('lc_open.id',$item_id);
            $this->db->where('lc_open.status',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            $data['item']['month_name']=date("F", mktime(0, 0, 0,$data['item']['month_id'],1, 2000));
            $this->db->select('lc_details.*');
            $this->db->select('variety.name variety_name');
            $this->db->select('pack_size.name pack_size_name');
            $this->db->from($this->config->item('table_sms_lc_details').' lc_details');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' variety','variety.id = lc_details.variety_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_classification_vpack_size').' pack_size','pack_size.id = lc_details.quantity_type_id','LEFT');
            $this->db->where('lc_details.lc_id',$item_id);
            $items=$this->db->get()->result_array();
//            print_r($items);
//            exit;
            foreach($items as &$item)
            {
                if($item['quantity_type_id']!=0)
                {
                    $item['total_quantity_in_kg']=(($item['pack_size_name']*$item['quantity_order'])/1000);
                }else
                {
                    $item['total_quantity_in_kg']=$item['quantity_order'];
                    $item['pack_size_name']='Bulk';
                }
                $item['total_price_in_currency']=($item['total_quantity_in_kg']*$item['price_currency']);
            }
            $data['items']=$items;
            $items_cost=Query_helper::get_info($this->config->item('table_sms_direct_cost_items'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));

            $this->db->select('lc_expense.cost_item_id, lc_expense.amount');
            $this->db->from($this->config->item('table_sms_lc_expense').' lc_expense');
            $this->db->where('lc_expense.lc_id',$item_id);
            $costs_result_old=$this->db->get()->result_array();
            $direct_cost_items_old=array();
            foreach($costs_result_old as $result)
            {
                $direct_cost_items_old[$result['cost_item_id']]=$result['amount'];
            }
            foreach($items_cost as &$item_cost)
            {
                if(isset($direct_cost_items_old[$item_cost['id']]))
                {
                    $item_cost['amount']=$direct_cost_items_old[$item_cost['id']];
                }else
                {
                    $item_cost['amount']=0;
                }
            }
            $data['items_cost']=$items_cost;
            $data['lc_id']=$item_id;
            $data['title']="Add Expense For LC".$data['item']['lc_number'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/expense",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/expense/'.$item_id);
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
        $lc_id = $this->input->post("lc_id");
        $user = User_helper::get_user();
        $time = time();
        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $items=$this->input->post('items');
        $this->db->trans_start();  //DB Transaction Handle START

        $this->db->select('lc_expense.cost_item_id, lc_expense.amount');
        $this->db->from($this->config->item('table_sms_lc_expense').' lc_expense');
        $this->db->where('lc_expense.lc_id',$lc_id);
        $results=$this->db->get()->result_array();
        $old_items=array();
        foreach($results as $result)
        {
            $old_items[$result['cost_item_id']]['cost_item_id']=$result['cost_item_id'];
            $old_items[$result['cost_item_id']]['amount']=$result['amount'];
        }
        if($results)
        {
            foreach($items as $key=>$item)
            {
                $data=array();
                if(isset($old_items[$key]))
                {
                    if($old_items[$key]['amount']!=$item)
                    {
                        $this->db->where('lc_id',$lc_id);
                        $this->db->where('cost_item_id',$key);
                        $this->db->set('revision_counter', 'revision_counter+1', FALSE);
                        $data['amount']=$item;
                        $data['date_updated'] = $time;
                        $data['user_updated'] = $user->user_id;
                        $this->db->update($this->config->item('table_sms_lc_expense'),$data);
                    }
                }else
                {
                    $data['lc_id']=$lc_id;
                    $data['cost_item_id']=$key;
                    $data['amount']=$item;
                    $data['revision_counter']=1;
                    $data['user_created']=$user->user_id;
                    $data['date_created']=$time;
                    Query_helper::add($this->config->item('table_sms_lc_expense'),$data);
                }
            }

        }else
        {
            foreach($items as $key=>$item)
            {
                $data=array();
                $data['lc_id']=$lc_id;
                $data['cost_item_id']=$key;
                $data['amount']=$item;
                $data['revision_counter']=1;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_sms_lc_expense'),$data);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_commit();
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
    }
}
