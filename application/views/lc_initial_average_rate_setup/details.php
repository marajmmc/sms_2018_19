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
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/details/'.$item['variety_id'])
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
                <th class="label-info" rowspan="2">Created By</th>
                <th class="label-info" rowspan="2">Created Time</th>
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
                    <td><?php echo $lc['pack_size']?></td>
                    <td class="text-right"><?php echo $lc['rate_weighted_receive_old']?></td>
                    <td class="text-right"><?php echo $lc['rate_weighted_receive']?></td>
                    <td class="text-right"><?php echo $lc['rate_weighted_complete_old']?></td>
                    <td class="text-right"><?php echo $lc['rate_weighted_complete']?></td>
                    <td><?php echo $lc['created_by']?></td>
                    <td><?php echo System_helper::display_date_time($lc['date_created'])?></td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>