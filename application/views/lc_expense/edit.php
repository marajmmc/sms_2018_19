<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array
(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_expense_complete');?>" method="post">
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
            <table class="table table-bordered table-responsive system_header_view_table">
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
                    <th><label class="control-label"><?php echo $item['bank_account_number'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['currency_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');?></label></th>
                    <th class="header_value"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2);?></label></th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_RELEASE_OTHER_CURRENCY');?></label></th>
                    <th class="header_value"><label class="control-label"><?php echo number_format($item['price_release_other_currency'],2);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                    <th class="header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_open']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RELEASE');?></label></th>
                    <th class="header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_release']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RECEIVE');?></label></th>
                    <th class="header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_receive']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_EXPENSE');?></label></th>
                    <th class="header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_expense']);?></label></th>
                </tr>
                </thead>
            </table>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_RELEASE_OTHER_VARIETY_TAKA');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8 text-right">
                    <label class="control-label"><?php echo number_format($item['price_release_other_variety_taka'],2);?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8 text-right">
                    <label class="control-label"><?php echo number_format($item['price_complete_other_variety_taka'],2);?></label>
                </div>
            </div>
            <?php
            if($item['status_release']==$this->config->item('system_status_complete'))
            {
                ?>

            <?php
            }
            else
            {
                ?>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?> </label>
                    </div>
                    <div class="col-sm-4 col-xs-8 text-right">
                        <label class="control-label"><?php echo number_format($item['price_complete_other_variety_taka'],2);?></label>
                    </div>
                </div>
            <?php
            }
            ?>

        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="col-xs-offset-2 col-xs-8">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Expense Head</th>
                        <th class="text-right">Amount (Taka)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $serial=1;
                    $price_complete_dc_taka=0;
                    $amount=0;
                    foreach($items as $row)
                    {
                        $amount=isset($dc[$row['id']])?$dc[$row['id']]:0;
                        $price_complete_dc_taka+=$amount;
                        ?>
                        <tr>
                            <td><?php echo $serial?></td>
                            <td><?php echo $row['name']?></td>
                            <td class="text-right">
                                <label class="control-label"><?php echo number_format($amount,2);?></label>
                            </td>
                        </tr>
                    <?php
                        ++$serial;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total Expense(Taka): </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_complete_dc_taka"><?php echo number_format($price_complete_dc_taka,2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right"><?php echo $this->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?>: </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_complete_other_variety_taka"><?php echo number_format($item['price_complete_other_variety_taka'],2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">Grand Total (Taka): </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_grand_total"><?php echo number_format(($item['price_complete_other_variety_taka']+$price_complete_dc_taka),2)?></label>
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    function calculate_total()
    {
        var price_complete_dc_taka=0;
        var price_complete_other_variety_taka=0;
        var amount=0;
        $('.amount').each(function(index, element)
        {
            if(!isNaN($(this).val()) && $(this).val()!='')
            {
                amount=parseFloat($(this).val());
            }
            price_complete_dc_taka+=parseFloat(amount);
        });
        $('#lbl_price_complete_dc_taka').html(number_format(price_complete_dc_taka,2));

        if(isNaN($('#price_complete_other_variety_taka').val()) || $('#price_complete_other_variety_taka').val()=='')
        {
            var price_complete_other_variety_taka=price_complete_dc_taka;
        }
        else
        {
            var price_complete_other_variety_taka=(parseFloat($('#price_complete_other_variety_taka').val())+price_complete_dc_taka);
        }

        $('#lbl_grand_total').html(number_format(price_complete_other_variety_taka,2));
    }
    $(document).ready(function()
    {
        $(document).off('input','.amount');
        $(document).on('input', '.amount', function()
        {
            calculate_total()
        })

        $(document).off('input','#price_complete_other_variety_taka');
        $(document).on('input', '#price_complete_other_variety_taka', function()
        {
            $("#lbl_price_complete_other_variety_taka").html(number_format($(this).val(),2))
            calculate_total();
        })
    })
</script>
