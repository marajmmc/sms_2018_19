<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
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
                        <?php echo $CI->lang->line('ALL_SELECT_CHECKBOX'); ?>
                    </label>
                </div>
            </div>
            <!--<div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[id]" <?php /*if($items['id']){echo 'checked';}*/?> checked><span class="label label-success"><?php /*echo $CI->lang->line('ID'); */?></span></label>
                </div>
            </div>-->
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[fiscal_year_name]" <?php if($items['fiscal_year_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[month_name]" <?php if($items['month_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_MONTH'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[date_opening]" <?php if($items['date_opening']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[date_expected]" <?php if($items['date_expected']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[principal_name]" <?php if($items['principal_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[currency_name]" <?php if($items['currency_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[lc_number]" <?php if($items['lc_number']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[consignment_name]" <?php if($items['consignment_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[price_total_currency]" <?php if($items['price_total_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[other_cost_currency]" <?php if($items['other_cost_currency']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[status_received]" <?php if($items['status_received']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_RECEIVED_STATUS'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[status_release]" <?php if($items['status_release']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_RELEASE_STATUS'); ?></span></label>
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
