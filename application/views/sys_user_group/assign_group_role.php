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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_assign_group_role');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item_id; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-xs-12" style="overflow-x: auto;">
            <table class="table table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $CI->lang->line("NAME");?></th>
                    <?php
                    for($i=0;$i<$this->config->item('system_max_actions');$i++)
                    {
                        ?>
                        <th><label><input type="checkbox" data-type='task_action<?php echo $i;?>' class="task_header_all"> <?php echo $CI->lang->line('LABEL_ACTION'.$i); ?></label></th>
                    <?php
                    }
                    ?>
                </tr>
                </thead>

                <tbody>
                <?php
                if(sizeof($modules_tasks)>0)
                {
                    foreach($modules_tasks as $module_task)
                    {
                        ?>
                        <tr>
                            <td>
                                <?php echo $module_task['prefix'];
                                if($module_task['module_task']['type']=='TASK')
                                {
                                ?>
                                <label style="font-weight: normal;"><input type="checkbox" data-id='<?php echo $module_task['module_task']['id'];?>' class="task_action_all">
                                    <?php
                                    }
                                    ?>
                                    <?php echo $module_task['module_task']['name'];?></label>
                            </td>
                            <?php
                            for($i=0;$i<$this->config->item('system_max_actions');$i++)
                            {
                                ?>
                                <td>
                                    <?php
                                    if($module_task['module_task']['type']=='TASK')
                                    {
                                        ?>
                                        <input type="checkbox" title="<?php echo $CI->lang->line('LABEL_ACTION'.$i); ?>" class="task_action<?php echo $i;?> task_action_<?php echo $module_task['module_task']['id'];?>"  <?php if(in_array($module_task['module_task']['id'],$role_status['action'.$i])){echo 'checked';}?> value="1" name='tasks[<?php echo $module_task['module_task']['id'];?>][action<?php echo $i; ?>]'>
                                    <?php
                                    }
                                    ?>
                                </td>
                            <?php
                            }
                            ?>
                        </tr>
                    <?php
                    }
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="20" class="text-center alert-danger">
                            <?php echo $CI->lang->line("NO_DATA_FOUND"); ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off("click", ".task_action_all");
        $(document).off("click", ".task_header_all");
        $(document).on("click",'.task_action_all',function()
        {
            if($(this).is(':checked'))
            {
                var class_text='.task_action';
                var data_id=$(this).attr('data-id');
                $(class_text+'_'+data_id).prop('checked',true);
                $(class_text+'_'+data_id+class_text+'3').prop('checked',false);
            }
            else
            {
                $('.task_action_'+$(this).attr('data-id')).prop('checked', false);
            }
        });
        $(document).on("click",'.task_header_all',function()
        {
            if($(this).is(':checked'))
            {
                $('.'+$(this).attr('data-type')).prop('checked', true);
            }
            else
            {
                $('.'+$(this).attr('data-type')).prop('checked', false);
            }
        });
    });
</script>
