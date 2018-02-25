<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
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

    <div class="col-md-12">
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?></label></th>
                <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_raw_foil_purchase($item['id']);?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SUPPLIER_NAME');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['supplier_name'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_receive']);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CHALLAN_NUMBER');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['challan_number'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CHALLAN');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_challan']);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CREATED_BY');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['created_by'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CREATED_TIME');?></label></th>
                <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></th>
            </tr>
            <?php
            if($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_UPDATED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['updated_by'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_UPDATED_TIME');?></label></th>
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
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
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
                    <th class="widget-header text-center" colspan="30">Product Details</th>
                </tr>
                <tr>
                    <th rowspan="2" class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_ITEM'); ?></th>
                    <th class="text-right" rowspan="2"><?php echo $CI->lang->line('LABEL_NUMBER_OF_REEL'); ?></th>
                    <th class="text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY');?></th>
                    <th class="text-center" colspan="2"><?php echo $CI->lang->line('LABEL_PRICE_TAKA_TOTAL');?></th>
                </tr>
                <tr>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_DIFFERENCE'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PRICE_TAKA_UNIT');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PRICE_TAKA_TOTAL');?></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-right">1</td>
                        <td>Common Foil</td>
                        <td class="text-right"><?php echo $item['number_of_reel']; ?></td>
                        <td class="text-right"><?php echo number_format($item['quantity_supply'],3,'.',''); ?></td>
                        <td class="text-right"><?php echo number_format($item['quantity_receive'],3,'.',''); ?></td>
                        <td class="text-right"><?php echo number_format(($item['quantity_receive']-$item['quantity_supply']),3,'.','');?></td>
                        <td class="text-right"><?php echo number_format($item['price_unit_tk'],2); ?></td>
                        <td class="text-right"><?php echo number_format(($item['quantity_receive']*$item['price_unit_tk']),2); ?></td>
                    </tr>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><label class="control-label"><?php echo $this->lang->line('LABEL_TOTAL')?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo number_format($item['number_of_reel']);?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo number_format($item['quantity_supply'],3,'.','');?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo number_format($item['quantity_receive'],3,'.','');?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo number_format(($item['quantity_receive']-$item['quantity_supply']),3,'.','');?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo $this->lang->line('LABEL_PRICE_TAKA_TOTAL')?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo number_format(($item['quantity_receive']*$item['price_unit_tk']),2);?></label></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

    <div class="clearfix"></div>