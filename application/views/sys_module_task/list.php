<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

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
                    <th><?php echo $CI->lang->line("ID"); ?></th>
                    <th><?php echo $CI->lang->line("NAME"); ?></th>
                    <th><?php echo $CI->lang->line("TYPE"); ?></th>
                    <th><?php echo $CI->lang->line("LABEL_ORDER"); ?></th>
                    <th><?php echo $CI->lang->line("LABEL_CONTROLLER_NAME"); ?></th>
                </tr>
            </thead>

            <tbody>
            <?php
                if(sizeof($items)>0)
                {
                    foreach($items as $item)
                    {
                        ?>
                        <tr>
                            <td><?php echo $item['module_task']['id']; ?></td>
                            <td><?php echo $item['prefix']; ?><a href="<?php echo site_url($CI->controller_url.'/index/edit/'.$item['module_task']['id']); ?>"><?php echo $item['module_task']['name']; ?></a></td>
                            <td><?php if($item['module_task']['type']=='TASK'){echo $CI->lang->line('TASK');}else{echo $CI->lang->line('MODULE');} ?></td>
                            <td><?php echo $item['module_task']['ordering']; ?></td>
                            <td><?php echo $item['module_task']['controller']; ?></td>
                        </tr>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="20" class="text-center alert-danger">
                            <?php echo $CI->lang->line('NO_DATA_FOUND'); ?>
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
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
