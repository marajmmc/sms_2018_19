<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lc_variety_init_avg_rate_setup extends Root_Controller
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
        $this->lang->language['LABEL_RATE_SAVED']='Rate Saved';
        $this->lang->language['LABEL_RATE_EDITABLE']='Rate Editable';
        $this->lang->language['LABEL_REVISION_COUNT']='Number Of Edit';
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
        elseif($action=="save")
        {
            $this->system_save();
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
        $data = array();
        if($method == 'list')
        {
            $data['id']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['revision_count']= 1;
            $data['rate_saved']= 1;
            $data['rate_editable']= 1;
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
            $data['title']="LC Variety Initial Average Rate Setup List";
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
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select("v.id variety_id,v.name variety_name");
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->join($this->config->item('table_sms_lc_variety_initial_average_rate_setup').' setup','setup.variety_id=v.id','LEFT');
        $this->db->select('setup.revision_count, setup.rate_average');
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
            $item['rate_saved']=$item['rate_average'];
            $item['rate_editable']=$item['rate_average'];
        }
        $this->json_return($items);
    }

    private function system_save()
    {
        $user = User_helper::get_user();
        $time=time();
        $items=$this->input->post('items');

        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $results=Query_helper::get_info($this->config->item('table_sms_lc_variety_initial_average_rate_setup'),'*',array('status != "'.$this->config->item('system_status_delete').'"'));
        $old_items=array();
        foreach($results as $result)
        {
            $old_items[$result['variety_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $variety_id=>$rate_editable)
        {
            if(isset($old_items[$variety_id]))
            {
                if($old_items[$variety_id]['rate_average']!=$rate_editable)
                {
                    $data=array();
                    $data['user_updated']=$user->user_id;
                    $data['date_updated']=$time;
                    $data['rate_average']=$rate_editable;
                    $this->db->set('revision_count','revision_count+1',false);
                    Query_helper::update($this->config->item('table_sms_lc_variety_initial_average_rate_setup'),$data,array("id = ".$old_items[$variety_id]['id']));
                }
            }
            else
            {
                $data=array();
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['revision_count']=1;
                $data['variety_id']=$variety_id;
                $data['rate_average']=$rate_editable;
                Query_helper::add($this->config->item('table_sms_lc_variety_initial_average_rate_setup'),$data,false);
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
}
