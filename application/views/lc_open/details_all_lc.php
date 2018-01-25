<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_all')
);

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered table-responsive ">
                <thead>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['fiscal_year_name']?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['currency_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_other_cost_total_currency'],2);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['remarks'];?></label></th>
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
                            <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details :: (Forwarded: <?php echo $item['status_forward']?>)</th>
                        </tr>
                        <tr>
                            <th class="bg-danger" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                            <th class="bg-danger text-center" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th class="bg-danger text-right" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                            <th class="bg-danger text-right" style="min-width: 100px;">KG</th>
                            <th class="bg-danger text-right" style="min-width: 100px;">Unit Price (Currency)</th>
                            <th class="bg-danger text-right" style="min-width: 150px;">Total Price (Currency)</th>
                        </tr>
                    </thead>
                    <tbody id="items_container">
                    <?php
                    $quantity_lc_kg=number_format(0,3);
                    foreach($items as $index=>$value)
                    {
                        if($value['pack_size_id']==0)
                        {
                            $quantity_lc_kg=number_format($value['quantity_lc'],3);
                        }
                        else
                        {
                            $quantity_lc_kg=number_format((($value['quantity_lc']*$value['pack_size_name'])/1000),3);
                        }
                        ?>
                        <tr>
                            <td>
                                <label><?php echo $value['variety_name']; ?> (<?php echo $value['variety_name_import']; ?>)</label>
                            </td>
                            <td class="text-center">
                                <label><?php if($value['pack_size_id']==0){echo 'Bulk';}else{echo $value['pack_size_name'];} ?></label>
                            </td>
                            <td class="text-right">
                                <label><?php echo number_format($value['quantity_lc'],3); ?></label>
                            </td>
                            <td class="text-right">
                                <label><?php echo number_format($quantity_lc_kg,3); ?></label>
                            </td>
                            <td class="text-right">
                                <label><?php echo number_format($value['price_unit_lc_currency'],2); ?></label>
                            </td>
                            <td class="text-right">
                                <label class="control-label price_total_lc_currency" id="price_total_lc_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <?php echo number_format($value['price_total_lc_currency'],2); ?>
                                </label>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_total_kg"><?php echo number_format(($item['quantity_total_kg']),3)?></label></th>
                        <th class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_variety_total_currency"><?php echo number_format($item['price_variety_total_currency'],2)?></label></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                        <th class="text-right">
                            <label class="control-label"><?php echo number_format($item['price_other_cost_total_currency'],2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_total_currency"> <?php echo number_format(($item['price_total_currency']),2)?></label>
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
    <div class="clearfix"></div>
</form>

