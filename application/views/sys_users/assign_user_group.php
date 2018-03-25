<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_assign_user_group'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['user_id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="user_group" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USER_GROUP');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="user_group" name="user_group" class="form-control">
                    <option value="0"><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($user_groups as $user_group)
                    {?>
                        <option value="<?php echo $user_group['value']?>" <?php if($user_group['value']==$item['user_group']){ echo "selected";} ?>><?php echo $user_group['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
