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

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_receive']);?>
            </div>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SUPPLIER_NAME');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['supplier_name'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHALLAN_NUMBER');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['challan_number'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CHALLAN');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_challan']);?>
            </div>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?> :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['remarks'];?>
        </div>
    </div>

    <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?></th>
                <th style="min-width: 150px; text-align: right;">Unit Price (Tk)</th>
                <th style="min-width: 150px; text-align: right;">Total Price (Tk)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $quantity_total=0;
            $total_tk=0;
            $price_total=0;
            foreach($purchase_sticker as $index=>$sticker)
            {
                $price_total=($sticker['quantity_receive']*$sticker['price_unit_tk']);
                $quantity_total+=$sticker['quantity_receive'];
                $total_tk+=$price_total;
                ?>
                <tr>
                    <td>
                        <label><?php echo $sticker['crop_name']; ?></label>
                    </td>
                    <td>
                        <label><?php echo $sticker['crop_type_name']; ?></label>
                    </td>
                    <td>
                        <label><?php echo $sticker['variety_name']; ?></label>
                    </td>
                    <td>
                        <label><?php echo $sticker['pack_size_name']; ?></label>
                    </td>
                    <td class="text-right">
                        <label><?php $current_stock=System_helper::get_raw_stock(array($sticker['variety_id'])); if(isset($current_stock)){echo $current_stock[$sticker['variety_id']][$sticker['pack_size_id']][$CI->config->item('system_sticker')]['current_stock'];}else{echo 0;}?></label>
                    </td>
                    <td class="text-right">
                        <label><?php echo $sticker['quantity_supply']; ?></label>
                    </td>
                    <td class="text-right">
                        <label><?php echo $sticker['quantity_receive']; ?></label>
                    </td>
                    <td>
                        <label><?php echo $sticker['price_unit_tk']; ?></label>
                    </td>
                    <td class="text-right">
                        <label><?php echo number_format($price_total,2); ?></label>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>

            <tfoot>
            <tr>
                <th colspan="6" class="text-right">Grand Total Quantity</th>
                <th class="text-right"><label class="control-label" id="lbl_quantity_receive_total"><?php echo number_format(($quantity_total),3,'.','')?></label></th>
                <th class="text-right">Grand Total (Tk)</th>
                <th class="text-right"><label class="control-label" id="lbl_price_total_tk"><?php echo number_format($total_tk,2)?></label></th>
                <th class="text-right"></th>
            </tr>
            </tfoot>

        </table>
    </div>
</div>

<div class="clearfix"></div>