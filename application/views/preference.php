<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI=& get_instance();
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    ?>
    <div class="col-xs-12" style="margin-bottom: 20px;">
        <div class="col-xs-2 ">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="barcode" <?php if($items['barcode']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_BARCODE'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="fiscal_year_name" <?php if($items['fiscal_year_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="month_name" <?php if($items['month_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_MONTH'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="date_opening" <?php if($items['date_opening']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="date_expected" <?php if($items['date_expected']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="principal_name" <?php if($items['principal_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="currency_name" <?php if($items['currency_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="lc_number" <?php if($items['lc_number']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="consignment_name" <?php if($items['consignment_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_other_cost_release_total_currency" <?php if($items['price_other_cost_release_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_variety_release_total_currency" <?php if($items['price_variety_release_total_currency']){echo 'checked';}?> value="1"><span class="label label-success">KG</span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_variety_release_total_currency" <?php if($items['price_variety_release_total_currency']){echo 'checked';}?> value="1"><span class="label label-success">Variety (Currency)</span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="price_release_total_currency" <?php if($items['price_release_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="status_release" <?php if($items['status_release']){echo 'checked';}?> value="1"><span class="label label-success">Forwarded</span></label>
            </div>
        </div>
    </div>
<?php
}
?>