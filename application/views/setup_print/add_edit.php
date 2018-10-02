<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
        if(isset($CI->permissions['action1'])&&($CI->permissions['action1']==1))
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Controller<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[controller]" id="controller" class="form-control" value="<?php echo $item['controller'];?>" />
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Method<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[method]" id="method" class="form-control" value="<?php echo $item['method'];?>" />
                </div>
            </div>
            <?php
        }
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[purpose]" id="purpose" class="form-control" value="<?php echo $item['purpose'];?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Width<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[width]" id="width" class="form-control float_type_positive" value="<?php echo $item['width'];?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Height<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[height]" id="height" class="form-control float_type_positive" value="<?php echo $item['height'];?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Row Per Page<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[row_per_page]" id="row_per_page" class="form-control integer_type_positive" value="<?php echo $item['row_per_page'];?>" />
            </div>
        </div>
        <?php
        if($item['id']>0)
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Header Image</label>
                </div>
                <div class="col-xs-4">
                    <input type="file" class="browse_button" data-preview-container="#image_header" data-preview-width="300" name="image_header">
                </div>
                <div class="col-xs-4" id="image_header">
                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_header_location']; ?>" alt="<?php echo $item['image_header_name']; ?>">
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Footer Image</label>
                </div>
                <div class="col-xs-4">
                    <input type="file" class="browse_button" data-preview-container="#image_footer" data-preview-width="300" name="image_footer">
                </div>
                <div class="col-xs-4" id="image_footer">
                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_footer_location']; ?>" alt="<?php echo $item['image_footer_name']; ?>">
                </div>
            </div>
            <?php
        }
        ?>

    </div>
    <div class="clearfix"></div>
</form>
<script>


    $(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(":file").filestyle({input: false,buttonText: "<?php echo $CI->lang->line('UPLOAD');?>", buttonName: "btn-danger"});
    })
</script>