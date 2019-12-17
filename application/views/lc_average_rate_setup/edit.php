<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
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
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/edit/'.$item['variety_id'])
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <!--<input type="hidden" id="id" name="id" value="<?php /*echo $item['id']; */?>" />-->
    <input type="hidden" id="variety_id" name="variety_id" value="<?php echo $item['variety_id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['crop_name']?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['crop_type_name']?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['variety_name']?>
            </div>
        </div>
        <div class="clearfix"></div>
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="widget-header text-center" colspan="29">LC Information (Variety Wise)</th>
                </tr>
                <tr>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_LC_NO'); ?></th>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_DATE_RECEIVE'); ?></th>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th class="label-success text-center" colspan="2">Rate (Receive)</th>
                    <th class="label-success text-center" colspan="2">Rate (Complete)</th>
                </tr>
                <tr>
                    <th class="label-success text-center">Current</th>
                    <th class="label-success text-center">New</th>
                    <th class="label-success text-center">Current</th>
                    <th class="label-success text-center">New</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($items as $lc)
                {
                    ?>
                    <tr>
                        <td>
                            <?php echo Barcode_helper::get_barcode_lc($lc['lc_id'])?>
                        </td>
                        <td>
                            <?php echo System_helper::display_date($lc['date_receive'])?>
                        </td>
                        <td><?php echo $lc['pack_size']?$lc['pack_size']:'Bulk';?></td>
                        <td class="text-right"><?php echo $lc['rate_weighted_receive']?></td>
                        <td>
                            <input type="text" name="items[<?php echo $lc['id']?>][rate_weighted_receive]" class="form-control float_type_positive" value="<?php echo $lc['rate_weighted_receive']?>"/>
                        </td>
                        <td class="text-right"><?php echo $lc['rate_weighted_complete']?></td>
                        <td>
                            <input type="text" name="items[<?php echo $lc['id']?>][rate_weighted_complete]" class="form-control float_type_positive" value="<?php echo $lc['rate_weighted_complete']?>"/>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $(document).ready(function()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".datepicker").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        //$(".datepicker").datepicker({dateFormat : display_date_format});
    });
</script>