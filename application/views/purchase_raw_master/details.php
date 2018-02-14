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

        <div class="row show-grid">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Created Time :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo System_helper::display_date_time($item['date_created']);?>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Created By :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['created_by'];?>
                </div>
            </div>
        </div>

        <?php if($item['date_updated']){?>
            <div class="row show-grid">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Updated Time :</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <?php echo System_helper::display_date_time($item['date_updated']);?>
                    </div>
                </div>
            </div>

            <div class="row show-grid">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Updated By :</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <?php echo $item['updated_by'];?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo nl2br($item['remarks']);?>
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
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_NUMBER_OF_REEL'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_DIFFERENCE');?></th>
                    <th style="min-width: 150px; text-align: right;">Unit Price (Tk)</th>
                    <th style="min-width: 150px; text-align: right;">Total Price (Tk)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $quantity_total_supply=0;
                $quantity_total_receive=0;
                $quantity_total_difference=0;
                $total_tk=0;
                $price_total=0;
                $total_unit_price=0;
                foreach($purchase_master as $index=>$master)
                {
                    $price_total=($master['quantity_receive']*$master['price_unit_tk']);
                    $quantity_total_supply+=$master['quantity_supply'];
                    $quantity_total_receive+=$master['quantity_receive'];
                    $quantity_total_difference=($quantity_total_supply-$quantity_total_receive);
                    $total_tk+=$price_total;
                    $total_unit_price+=$master['price_unit_tk'];
                    ?>
                    <tr>
                        <td>
                            <label><?php echo $master['crop_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $master['crop_type_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $master['variety_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $master['pack_size_name']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php $current_stock=System_helper::get_raw_stock(array($master['variety_id'])); if(isset($current_stock)){echo $current_stock[$master['variety_id']][$master['pack_size_id']][$CI->config->item('system_master_foil')]['current_stock'];}else{echo 0;}?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $master['number_of_reel']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $master['quantity_supply']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $master['quantity_receive']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo ($master['quantity_supply']-$master['quantity_receive']);?></label>
                        </td class="text-right">
                        <td class="text-right">
                            <label><?php echo $master['price_unit_tk']; ?></label>
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
                    <td colspan="6" class="text-right"><label class="control-label"><?php echo $this->lang->line('LABEL_TOTAL')?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo $quantity_total_supply.' (KG)';?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo $quantity_total_receive.' (KG)';?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo $quantity_total_difference.' (KG)';?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo $total_unit_price;?></label></td>
                    <td class="text-right"><label class="control-label"><?php echo number_format($total_tk,2);?></label></td>
                </tr>
                </tfoot>

            </table>
        </div>
    </div>

    <div class="clearfix"></div>