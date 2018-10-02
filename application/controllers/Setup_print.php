<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_print extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_print');
        $this->controller_url='setup_print';
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
        elseif($action=="details")
        {
            $this->system_details($id);
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

            $data['title']="Printing Setup List";
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
        $items=Query_helper::get_info($this->config->item('table_system_setup_print'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="New Printing Setup";
            $data['item']['id']=0;
            $data['item']['controller']='';
            $data['item']['method']='';
            $data['item']['purpose']='';
            $data['item']['width']=8.27;
            $data['item']['height']=11.69;
            $data['item']['row_per_page']=10;

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
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['item']=Query_helper::get_info($this->config->item('table_system_setup_print'),'*',array('id ='.$item_id),1);
            $data['title']="Edit (".$data['item']['purpose'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add_edit',$data,true));
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
        if($id>0)
        {
            $path='images/print/'.$id;
//            $dir=(FCPATH).$path;
//            if(!is_dir($dir))
//            {
//                mkdir($dir, 0777);
//            }
            $uploaded_images = System_helper::upload_file($path);
            if(array_key_exists('image_header',$uploaded_images))
            {
                if($uploaded_images['image_header']['status'])
                {
                    $item_head['image_header_name']=$uploaded_images['image_header']['info']['file_name'];
                    $item_head['image_header_location']=$path.'/'.$uploaded_images['image_header']['info']['file_name'];
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_images['image_header']['message'];
                    $this->json_return($ajax);
                }
            }
            if(array_key_exists('image_footer',$uploaded_images))
            {
                if($uploaded_images['image_footer']['status'])
                {
                    $item_head['image_footer_name']=$uploaded_images['image_footer']['info']['file_name'];
                    $item_head['image_footer_location']=$path.'/'.$uploaded_images['image_footer']['info']['file_name'];
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_images['image_footer']['message'];
                    $this->json_return($ajax);
                }
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            $data=$item_head;
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_system_setup_print'),$data,array('id='.$id));
        }
        else
        {
            $data=$item_head;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            Query_helper::add($this->config->item('table_system_setup_print'),$data);
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
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['item']=Query_helper::get_info($this->config->item('table_system_setup_print'),'*',array('id ='.$item_id),1);

            $data['title']="Details (".$data['item']['purpose'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/details',$data,true));
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
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[purpose]',$this->lang->line('LABEL_PURPOSE'),'required');
        $this->form_validation->set_rules('item[width]','Width','required');
        $this->form_validation->set_rules('item[height]','Height','required');
        $this->form_validation->set_rules('item[row_per_page]','Row Per Page','required');

        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
        {
            $this->form_validation->set_rules('item[controller]','Controller','required');
            $this->form_validation->set_rules('item[method]','Method','required');
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        return true;
    }

}
