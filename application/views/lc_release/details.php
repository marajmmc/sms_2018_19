<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
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
                <th class="widget-header header_caption"><label class="control-label pull-right">Forward By</label></th>
                <th class="header_value"><label class="control-label"><?php echo $item['user_full_name']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right">Forwarded Time</label></th>
                <th class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_open_forward']);?></label></th>
            </tr>
            </thead>
        </table>
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['fiscal_year']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_ACCOUNT_NUMBER');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['bank_account_number'];?></label></th>
            </tr>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['currency_name'];?></label></th>
            </tr>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption" style="vertical-align: top;"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_open']);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption" style="vertical-align: top;"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RELEASE');?></label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_release']);?></label></th>
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
                    <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details  :: ( Release Status: <?php echo $item['status_release']?> )</th>
                </tr>
                <tr>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_UNIT');?></th>
                    <th class="label-primary text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER');?></th>
                    <th class="label-warning text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?></th>
                </tr>
                <tr>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>

                    <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="label-warning text-center"><?php echo $CI->lang->line('KG');?></th>
                    <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>
                </tr>
                </thead>
                <?php
                if(!empty($items))
                {
                    ?>
                    <tbody>
                    <?php
                    $quantity_total_open=0;
                    $quantity_total_open_kg=0;
                    $quantity_total_release=0;
                    $quantity_total_release_kg=0;
                    foreach($items as $index=>$data)
                    {
                        $quantity_release=$data['quantity_release'];
                        $price_release_currency=($data['quantity_release']*$data['price_unit_currency']);
                        if($data['pack_size_id']==0)
                        {
                            $quantity_open_kg=$data['quantity_open'];
                            $quantity_release_kg=$quantity_release;
                        }
                        else
                        {
                            $quantity_open_kg=(($data['pack_size']*$data['quantity_open'])/1000);
                            $quantity_release_kg=(($data['pack_size']*$quantity_release)/1000);
                        }
                        $quantity_total_open+=$data['quantity_open'];
                        $quantity_total_open_kg+=$quantity_open_kg;
                        $quantity_total_release+=$quantity_release;
                        $quantity_total_release_kg+=$quantity_release_kg;
                        ?>
                        <tr>
                            <td><strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong></td>
                            <td class="text-center"> <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?></td>
                            <td class="text-center"><?php echo $data['price_unit_currency']?></td>
                            <td class="text-right"><label class="control-label"><?php echo $data['quantity_open']?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($quantity_open_kg,3,'.','')?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format(($data['quantity_open']*$data['price_unit_currency']),2)?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo $quantity_release; ?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($quantity_release_kg,3,'.',''); ?></label></td>
                            <td class="text-right"><label class="control-label"><?php echo number_format($price_release_currency,2); ?></label></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?> & <?php echo $this->lang->line('LABEL_PRICE_CURRENCY_TOTAL')?></th>
                        <th class="text-right"><label class="control-label"><?php echo $quantity_total_open;?></label></th>
                        <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_open_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_variety_currency'],2);?></label></th>
                        <th class="text-right"><label class="control-label"><?php echo $quantity_total_release;?></label></th>
                        <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label"> <?php echo number_format($item['price_release_variety_currency'],2); ?></label></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2)?></label></th>
                        <th colspan="2">&nbsp;</th>
                        <th class="text-right"> <?php echo number_format($item['price_release_other_currency'],2); ?> </th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                        <th class="text-right">
                            <label class="control-label"><?php echo number_format(($item['price_open_other_currency']+$item['price_open_variety_currency']),2);?></label>
                        </th>
                        <th colspan="2">&nbsp;</th>
                        <th class="text-right"> <label class="control-label"><?php echo number_format(($item['price_release_other_currency']+$item['price_release_variety_currency']),2);?></label></th>
                    </tr>
                    <tr>
                        <th colspan="8" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_TAKA')?></th>
                        <th class="text-right"><?php echo number_format($item['price_release_other_variety_taka'],2);?></th>
                    </tr>
                    </tfoot>
                <?php
                }
                else
                {
                    ?>
                    <tfoot>
                    <tr>
                        <td class="widget-header text-center" colspan="21"><strong><?php echo $CI->lang->line('NO_DATA_FOUND');?></strong></td>
                    </tr>
                    </tfoot>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
<div class="clearfix"></div>