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
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));


$controller_folder=dir(APPPATH.'controllers');
$array=array();
while(($controller=$controller_folder->read())!==false)
{
    if($controller=='.' || $controller=='..')
    {
        continue;
    }
    $array[]=pathinfo($controller,PATHINFO_FILENAME);
}
$controller_folder->close();
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
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
                <label for="name" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[name]" id="name" class="form-control" value="<?php echo $item['name']; ?>"/>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="type" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="type" name="item[type]" class="form-control" tabindex="-1">
                    <option value="MODULE"
                        <?php
                        if($item['type']=='MODULE')
                        {
                            echo ' selected';
                        }
                        ?> >Module
                    </option>
                    <option value="TASK"
                        <?php
                        if($item['type']=='TASK')
                        {
                            echo ' selected';
                        }
                        ?> >Task</option>
                </select>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="parent" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARENT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="parent" name="item[parent]" data-placeholder="Select" class="form-control" tabindex="-1">
                    <option value="0"><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach($modules as $module)
                    {
                        ?>
                        <option value='<?php echo $module['module_task']['id']; ?>' <?php if($module['module_task']['id']==$item['parent']){ echo ' selected';} ?>><?php echo $module['prefix'].$module['module_task']['name']; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="controller" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONTROLLER_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[controller]" id="controller" class="form-control" value="<?php echo $item['controller'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="ordering" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[ordering]" id="ordering" class="form-control" value="<?php echo $item['ordering'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="status" class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="item[status]" class="form-control" tabindex="-1">
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>"
                        <?php
                        if($item['status']==$CI->config->item('system_status_active'))
                        {
                            echo ' selected';
                        }
                        ?> ><?php echo $CI->lang->line('ACTIVE') ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"
                        <?php
                        if($item['status']==$CI->config->item('system_status_inactive'))
                        {
                            echo ' selected';
                        }
                        ?> ><?php echo $CI->lang->line('INACTIVE') ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_status_delete'); ?>"
                        <?php
                        if($item['status']==$CI->config->item('system_status_delete'))
                        {
                            echo ' selected';
                        }
                        ?> ><?php echo $CI->lang->line('DELETE') ?>
                    </option>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="status" class="control-label pull-right">App Notification<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_notification" name="item[status_notification]" class="form-control" tabindex="-1">
                    <option value="<?php echo $CI->config->item('system_status_no'); ?>"
                        <?php
                        if($item['status_notification']==$CI->config->item('system_status_no'))
                        {
                            echo ' selected';
                        }
                        ?> ><?php echo $CI->lang->line('LABEL_NO') ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_status_yes'); ?>"
                        <?php
                        if($item['status_notification']==$CI->config->item('system_status_yes'))
                        {
                            echo ' selected';
                        }
                        ?> ><?php echo $CI->lang->line('LABEL_YES') ?>
                    </option>
                </select>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var availableTags=<?php echo json_encode($array); ?>;
        $('#controller').autocomplete({source:availableTags});
    });
</script>
