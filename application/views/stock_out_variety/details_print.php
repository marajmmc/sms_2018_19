<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'onClick'=>"window.print()"
    );
}

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

$width=8.27*100;
$height=11.69*100/2;
$row_per_page=20;
$header_image=base_url('images/print/header.jpg');
$footer_image=base_url('images/print/footer.jpg');
$result=Query_helper::get_info($CI->config->item('table_system_setup_print'),'*',array('controller ="' .$this->controller_url.'"','method ="details_print"'),1);
if($result)
{
    $width=$result['width']*100;
    $height=$result['height']*100;
    $row_per_page=$result['row_per_page'];
    $header_image=$CI->config->item('system_base_url_picture_setup_print').$result['image_header_location'];
    $footer_image=$CI->config->item('system_base_url_picture_setup_print').$result['image_footer_location'];
}

$total_records=sizeof($items);
$num_pages=ceil($total_records/$row_per_page);
?>