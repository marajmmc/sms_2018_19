<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_average_rate_setup extends Root_Controller
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
        $this->lang->language['LABEL_LC_NO']='LC Number';
        $this->lang->language['LABEL_NUMBER_OF_LC']='Number of LC';
        $this->lang->language['LABEL_NUMBER_OF_LC_RATE_RECEIVE']='Number of (Rate Receive) LC';
    }
    public function index($action="list",$id=0,$id1=0)
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
            $this->system_edit($id,$id1);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
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
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list')
        {
            $data['id']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['number_of_lc']= 1;
            $data['number_of_lc_rate_receive']= 1;
        }
        else
        {

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
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="LC Average Rate Setup Variety List";
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
        $this->db->select('COUNT(details.lc_id) as number_of_lc');
        $this->db->select('COUNT(CASE WHEN details.rate_weighted_receive > 0 THEN rate_weighted_receive END) as number_of_lc_rate_receive');
        $this->db->join($this->config->item('table_sms_lc_open').' lc','lc.id = details.lc_id','INNER');
        $this->db->where('lc.status_receive', $this->config->item('system_status_complete'));
        $this->db->where('details.quantity_open >0');
        $this->db->group_by('details.variety_id');
        //$this->db->group_by('details.variety_id, details.pack_size_id');
        $results=$this->db->get()->result_array();
        $info_lc=array();
        foreach($results as $result)
        {
            $info_lc[$result['variety_id']]['number_of_lc']=$result['number_of_lc'];
            $info_lc[$result['variety_id']]['number_of_lc_rate_receive']=$result['number_of_lc_rate_receive'];
        }

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id,crop.name crop_name');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['id']=$item['variety_id'];
            $item['number_of_lc']=isset($info_lc[$item['variety_id']]['number_of_lc'])?$info_lc[$item['variety_id']]['number_of_lc']:0;
            $item['number_of_lc_rate_receive']=isset($info_lc[$item['variety_id']]['number_of_lc_rate_receive'])?$info_lc[$item['variety_id']]['number_of_lc_rate_receive']:0;
        }
        $this->json_return($items);
    }
    private function system_edit($id)
    {
        if((isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if($id>0)
            {
                $variety_id=$id;
            }
            else
            {
                $variety_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.id variety_id,v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->where('v.status',$this->config->item('system_status_active'));
            $this->db->where('v.whose','ARM');
            $this->db->where('v.id',$variety_id);
            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('crop_type.ordering','ASC');
            $this->db->order_by('crop_type.id','ASC');
            $this->db->order_by('v.ordering','ASC');
            $this->db->order_by('v.id','ASC');
            $data['item']=$this->db->get()->row_array();

            $this->db->from($this->config->item('table_sms_lc_details').' details');
            $this->db->select('details.*');
            $this->db->join($this->config->item('table_sms_lc_open').' lc','lc.id = details.lc_id','INNER');
            $this->db->select('lc.date_receive');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = details.variety_id','INNER');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = details.pack_size_id','LEFT');
            $this->db->select('pack.name as pack_size');
            $this->db->where('details.variety_id',$variety_id);
            $this->db->where('lc.status_receive', $this->config->item('system_status_complete'));
            $this->db->where('details.quantity_open >0');
            $this->db->order_by('details.id ASC');
            $data['items']=$this->db->get()->result_array();

            $data['title']="LC Average Rate Set (Add/Edit)";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$variety_id);
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
        $variety_id = $this->input->post("variety_id");
        $user = User_helper::get_user();
        $time=time();
        $items=$this->input->post('items');
        if(!((isset($this->permissions['action2']) && ($this->permissions['action2']==1))))
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

        $this->db->from($this->config->item('table_sms_lc_details').' details');
        $this->db->select('details.*');
        $this->db->join($this->config->item('table_sms_lc_open').' lc','lc.id = details.lc_id','INNER');
        $this->db->select('lc.date_receive');
        $this->db->where('details.variety_id',$variety_id);
        $this->db->where('lc.status_receive', $this->config->item('system_status_complete'));
        $this->db->where('details.quantity_open >0');
        $this->db->order_by('details.id ASC');
        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {
                if(isset($items[$result['id']]))
                {
                    $rate_weighted_receive_old=$result['rate_weighted_receive'];
                    $rate_weighted_receive=$items[$result['id']]['rate_weighted_receive']?$items[$result['id']]['rate_weighted_receive']:0;
                    $rate_weighted_complete_old=$result['rate_weighted_complete'];
                    $rate_weighted_complete=$items[$result['id']]['rate_weighted_complete']?$items[$result['id']]['rate_weighted_complete']:0;
                    if(($rate_weighted_receive_old!=$rate_weighted_receive) || ($rate_weighted_complete_old!=$rate_weighted_complete))
                    {
                        $data=array();
                        $data['rate_weighted_receive']=$rate_weighted_receive;
                        $data['rate_weighted_complete']=$rate_weighted_complete;
                        Query_helper::update($this->config->item('table_sms_lc_details'),$data,array('id='.$result['id']));

                        $data=array();
                        $data['lc_id']=$result['lc_id'];
                        $data['lc_details_id']=$result['id'];
                        $data['date_receive']=$result['date_receive'];
                        $data['variety_id']=$result['variety_id'];
                        $data['rate_weighted_receive_old']=$rate_weighted_receive_old;
                        $data['rate_weighted_receive']=$rate_weighted_receive;
                        $data['rate_weighted_complete_old']=$rate_weighted_complete_old;
                        $data['rate_weighted_complete']=$rate_weighted_complete;
                        $data['date_created']=$time;
                        $data['user_created']=$user->user_id;
                        Query_helper::add($this->config->item('table_sms_lc_variety_average_rates_histories'),$data, false);
                    }
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
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $variety_id=$id;
            }
            else
            {
                $variety_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.id variety_id,v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->where('v.status',$this->config->item('system_status_active'));
            $this->db->where('v.whose','ARM');
            $this->db->where('v.id',$variety_id);
            $this->db->order_by('crop.ordering','ASC');
            $this->db->order_by('crop.id','ASC');
            $this->db->order_by('crop_type.ordering','ASC');
            $this->db->order_by('crop_type.id','ASC');
            $this->db->order_by('v.ordering','ASC');
            $this->db->order_by('v.id','ASC');
            $data['item']=$this->db->get()->row_array();

            $this->db->from($this->config->item('table_sms_lc_variety_average_rates_histories').' details');
            $this->db->select('details.*');
            $this->db->join($this->config->item('table_sms_lc_details').' lc_details','lc_details.id = details.lc_details_id','INNER');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id = details.user_created AND user_info.revision=1','INNER');
            $this->db->select('user_info.name as created_by');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lc_details.pack_size_id','LEFT');
            $this->db->select('pack.name as pack_size');
            $this->db->where('details.variety_id',$variety_id);
            $data['items']=$this->db->get()->result_array();

            $data['title']="LC Average Rate Setup Details";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$variety_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=true;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        /*$this->load->library('form_validation');
        $item = $this->input->post("item");
        if(!isset($item['date']) || !strtotime($item['date']))
        {
            $this->message=$this->lang->line('LABEL_DATE'). ' field is required.';
            return false;
        }
        if(!$item['rate_weighted_receive'])
        {
            $this->message=$this->lang->line('LABEL_RATE_WEIGHTED_RECEIVE'). ' field is required.';
            return false;
        }
        if(!$item['rate_weighted_complete'])
        {
            $this->message=$this->lang->line('LABEL_RATE_WEIGHTED_COMPLETE'). ' field is required.';
            return false;
        }*/
        return true;
    }
}
