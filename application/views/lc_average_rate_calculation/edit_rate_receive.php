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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_rate_receive');?>" method="post">
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
        echo $CI->load->view("info_basic",'',true);
        echo $CI->load->view("info_basic",array('accordion'=>array('header'=>'+LC Info','div_id'=>'accordion_lc_info','data'=>$info_lc)),true);
        ?>

        <div class="row show-grid">
            <div class="widget-header">
                <div class="title">
                    LC Varieties List
                </div>
                <div class="clearfix"></div>
            </div>
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="label-info" ><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th class="label-info" ><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th class="label-info" ><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="label-info" ><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-info" >Rate(saved)</th>
                        <th class="label-info" >Rate(Calculated)</th>
                        <th class="label-info" >Rate(new)</th>

                    </tr>

                    </thead>
                    <tbody id="items_container">
                    <?php

                    foreach($items as $variety_ifo)
                    {
                        ?>
                        <tr>
                            <td><label><?php echo $variety_ifo['crop_name'];?></label></td>
                            <td><label><?php echo $variety_ifo['crop_type_name'];?></label></td>
                            <td><label><?php echo $variety_ifo['variety_name']; ?> (<?php echo $variety_ifo['variety_name_import']; ?>)</label></td>
                            <td class="text-center"> <label><?php if($variety_ifo['pack_size_id']==0){echo 'Bulk';}else{echo $variety_ifo['pack_size'];} ?></label></td>
                            <td class="text-center"> <label><?php echo System_helper::get_string_amount($rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_total_rate_weighted_receive']); ?></label></td>
                            <td class="text-center"> <label><?php echo System_helper::get_string_amount($rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_total_rate_weighted_receive_calculated']);?></label></td>
                            <td>
                                <input type="text" name="items[<?php echo $variety_ifo['id'];?>][rate_weighted_receive]" class="form-control float_type_positive" value="" />
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $(document).ready(function()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    })
</script>