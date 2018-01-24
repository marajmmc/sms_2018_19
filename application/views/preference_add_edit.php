<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
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
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_preference');?>" method="post">
    <input type="hidden" id="id" name="id" value="" />
    <input type="hidden" id="method_name" name="preference[method_name]" value="list" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-12 text-center">
                <div class="checkbox  btn btn-danger">
                    <label>
                        <input type="checkbox" class="allSelectCheckbox" name="" checked>
                        Select All
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[barcode]" <?php if($items['barcode']){echo 'checked';}?>  value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_BARCODE'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[fiscal_year_name]" <?php if($items['fiscal_year_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[month_name]" <?php if($items['month_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_MONTH'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[date_opening]" <?php if($items['date_opening']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[date_expected]" <?php if($items['date_expected']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[principal_name]" <?php if($items['principal_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[currency_name]" <?php if($items['currency_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[lc_number]" <?php if($items['lc_number']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[consignment_name]" <?php if($items['consignment_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" name="items[price_other_cost_total_currency]" <?php if($items['price_other_cost_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" name="items[quantity_total_kg]" <?php if($items['quantity_total_kg']){echo 'checked';}?> value="1"><span class="label label-success">KG<?php //echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" name="items[price_variety_total_currency]" <?php if($items['price_variety_total_currency']){echo 'checked';}?> value="1"><span class="label label-success">Variety Currency<?php //echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" name="items[price_total_currency]" <?php if($items['price_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" class="system_jqx_column" name="items[status_forward]" <?php if($items['status_forward']){echo 'checked';}?> value="1"><span class="label label-success">Forwarded<?php //echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
                </div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>


<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).on("click",'.allSelectCheckbox',function()
        {
            if($(this).is(':checked'))
            {
                $('input:checkbox').prop('checked', true);
            }
            else
            {
                $('input:checkbox').prop('checked', false);
            }
        });
    });

</script>
