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
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ID');?></label></th>
                <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_raw_foil_stock_in($item['id']);?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['purpose'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_stock_in']);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['user_created_full_name'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME');?></label></th>
                <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></th>
            </tr>
            <?php
            if($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['user_updated_full_name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?></label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <?php
            if($item['remarks'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks']);?></label></th>
                </tr>
            <?php
            }
            ?>
            </thead>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="widget-header text-center" colspan="30">Product Details </th>
                </tr>
                <tr>
                    <th class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_ITEM'); ?></th>
                    <th class="text-right" style="width: 20%;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-right">1</td>
                    <td>Common Foil</td>
                    <td class="text-right"><?php echo number_format($item['quantity'],3,'.','');?></td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th  class="text-right" colspan="2"><?php echo $CI->lang->line('LABEL_TOTAL_KG');?></th>
                    <th class="text-right"><?php echo number_format($item['quantity'],3,'.','');?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>