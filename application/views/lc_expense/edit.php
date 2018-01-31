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
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_ACCOUNT_NUMBER');?></label></th>
                    <th><label class="control-label"><?php echo $item['bank_account_number'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['currency_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_other_cost_total_currency'],2);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right">Release Others Costs (Currency)</label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_other_cost_total_release_currency'],2);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RELEASE');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks_release'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RECEIVE');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks_receive'];?></label></th>
                </tr>
                </thead>
            </table>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_EXPENSE');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks_expense]" id="remarks_expense" class="form-control" ><?php echo $item['remarks_expense'];?></textarea>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_TOTAL_TAKA');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[price_total_all_taka]" id="price_total_all_taka" class="form-control float_type_positive" value="<?php echo $item['price_total_all_taka'];?>" />
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="col-xs-offset-2 col-xs-8">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Expense Head</th>
                        <th>Amount (Taka)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $serial=1;
                    $price_total_expense_head_taka=0;
                    $amount=0;
                    foreach($items as $row)
                    {
                        $amount=isset($cost_item[$row['id']])?$cost_item[$row['id']]:0;
                        $price_total_expense_head_taka+=$amount;
                        ?>
                        <tr>
                            <td><?php echo $serial?></td>
                            <td><?php echo $row['name']?></td>
                            <td>
                                <input type="text" name="items[<?php echo $row['id']?>][amount]" id="amount" class="form-control float_type_positive amount" value="<?php echo $amount;?>" />
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
                            <label class="control-label" id="lbl_price_total_expense_head_taka"><?php echo number_format($price_total_expense_head_taka,2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">Total (Taka): </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_total_all_taka"><?php echo number_format($item['price_total_all_taka'],2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">Grand Total (Taka): </th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_grand_total"><?php echo number_format(($item['price_total_all_taka']+$price_total_expense_head_taka),2)?></label>
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
        var price_total_expense_head_taka=0;
        var price_total_all_taka=0;
        var amount=0;
        $('.amount').each(function(index, element)
        {
            if(!isNaN($(this).val()) && $(this).val()!='')
            {
                amount=parseFloat($(this).val());
            }
            price_total_expense_head_taka+=parseFloat(amount);
        });
        $('#lbl_price_total_expense_head_taka').html(number_format(price_total_expense_head_taka,2));

        if(isNaN($('#price_total_all_taka').val()) || $('#price_total_all_taka').val()=='')
        {
            var price_total_all_taka=price_total_expense_head_taka;
        }
        else
        {
            var price_total_all_taka=(parseFloat($('#price_total_all_taka').val())+price_total_expense_head_taka);
        }

        $('#lbl_grand_total').html(number_format(price_total_all_taka,2));
    }
    $(document).ready(function()
    {
        $(document).off('input','.amount');
        $(document).on('input', '.amount', function()
        {
            calculate_total()
        })

        $(document).off('input','#price_total_all_taka');
        $(document).on('input', '#price_total_all_taka', function()
        {
            $("#lbl_price_total_all_taka").html(number_format($(this).val(),2))
            calculate_total();
        })
    })
</script>
