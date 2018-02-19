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
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_stock_in']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PURPOSE');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['purpose'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo $item['remarks']?></label></th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="widget-header text-center" colspan="30">Product & Price Details :: <?php //echo Barcode_helper::get_barcode_raw_master_stock_in($item['id']);?> </th>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial=0;
                $quantity_total=0;
                foreach($items as $item)
                {
                    ++$serial;
                    $quantity_total+=$item['quantity'];
                ?>
                    <tr>
                        <td><?php echo $serial;?></td>
                        <td><?php echo $item['crop_name'];?></td>
                        <td><?php echo $item['crop_type_name'];?></td>
                        <td><?php echo $item['variety_name'];?></td>
                        <td class="text-right"><?php echo $item['pack_size'];?></td>
                        <td class="text-right"><?php echo $item['quantity'];?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th  class="text-right" colspan="5"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                    <th class="text-right"><?php echo $quantity_total;?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>