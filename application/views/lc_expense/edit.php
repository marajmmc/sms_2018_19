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
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/edit/'.$item['id'])
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
        <?php
        echo $CI->load->view("info_basic",'',true);
        echo $CI->load->view("info_basic",array('accordion'=>array('header'=>'+LC Info','div_id'=>'accordion_lc_info','collapse'=>'in','data'=>$info_lc)),true);
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_LC_EXPENSE');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_expense]" id="remarks_expense" class="form-control" ><?php echo $item['remarks_expense'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_RELEASE_OTHER_VARIETY_TAKA');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8 text-right">
                <label class="control-label"><?php echo number_format($item['price_release_other_variety_taka'],2);?></label>
            </div>
        </div>
        <?php
        if($item['status_release']==$this->config->item('system_status_complete'))
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[price_complete_other_variety_taka]" id="price_complete_other_variety_taka" class="form-control float_type_positive" value="<?php echo $item['price_complete_other_variety_taka'];?>" />
                </div>
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8 text-right">
                    <label class="control-label"><?php echo number_format($item['price_complete_other_variety_taka'],2);?></label>
                </div>
            </div>
        <?php
        }
        ?>

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
                            <label class="control-label" id="lbl_price_complete_dc_taka"><?php echo number_format($price_complete_dc_taka,2)?></label>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right"><?php echo $CI->lang->line('LABEL_PRICE_COMPLETE_OTHER_VARIETY_TAKA');?>: </th>
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
