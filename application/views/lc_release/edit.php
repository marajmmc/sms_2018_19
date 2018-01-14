<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
    if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
            'id'=>'button_action_save_new',
            'data-form'=>'#save_form'
        );
    }
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_CLEAR"),
        'id'=>'button_action_clear',
        'data-form'=>'#save_form'
    );
}
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

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fiscal_years['text']?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $principals['text'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['lc_number'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="currency_id" name="item[currency_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($currencies as $currency)
                    {?>
                        <option value="<?php echo $currency['value']?>" <?php if(($currency['value']==$item['currency_id'])){ echo "selected";}?>><?php echo $currency['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['consignment_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['other_cost_currency'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['remarks'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="21" class="text-center label-success">Varieties Information</th>
                        </tr>
                        <tr>
                            <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                            <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th class="label-info" rowspan="2">Unit Price (Currency)</th>
                            <th class="label-primary text-center" colspan="3">Order Information</th>
                            <th class="label-warning text-center" colspan="3">Actual Information</th>
                        </tr>
                        <tr>
                            <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                            <th class="label-primary text-center">KG</th>
                            <th class="label-primary text-center">Total Price (Currency)</th>

                            <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                            <th class="label-warning text-center">KG</th>
                            <th class="label-warning text-center">Total Price (Currency)</th>
                        </tr>
                    </thead>
                    <tbody id="items_container">
                    <?php
                        $total_kg='0.000';
                        $total_currency='0.000';
                        foreach($items as $index=>$value)
                        {
                            $item_per_kg='0.000';
                            $item_per_currency='0.000';
                            if($value['quantity_type_id']==0)
                            {
                                $item_per_kg = number_format(($value['quantity_order']),3);
                            }
                            else
                            {
                                $item_per_kg = number_format((($packs[$value['quantity_type_id']]['text']*$value['quantity_order'])/1000),3);
                            }
                            $item_per_currency=number_format(($value['quantity_order']*$value['price_currency']),3);
                            $total_kg+=$item_per_kg;
                            $total_currency+=($value['quantity_order']*$value['price_currency']);
                            ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="varieties[<?php echo $index+1;?>][lc_detail_id]" value="<?php echo $value['id']; ?>" />
                                    <label><?php echo $varieties[$value['variety_id']]['text']; ?></label>
                                    <input type="hidden" name="varieties[<?php echo $index+1;?>][variety_id]" value="<?php echo $value['variety_id']; ?>">
                                </td>
                                <td>
                                    <label><?php if($value['quantity_type_id']==0){echo 'Bulk';}else{echo $packs[$value['quantity_type_id']]['text'];} ?></label>
                                    <input type="hidden" name="varieties[<?php echo $index+1;?>][quantity_type_id]" id="quantity_type_id_<?php echo $index+1;?>" value="<?php echo $value['quantity_type_id']; ?>" class="quantity_type" data-pack-size-name="<?php if($value['quantity_type_id']==0){echo 0;}else{echo $packs[$value['quantity_type_id']]['text'];} ?>">
                                </td>
                                <td>
                                    <label class="pull-right"><?php echo number_format($value['price_currency'],3); ?></label>
                                </td>
                                <td>
                                    <label class="pull-right"><?php echo number_format($value['quantity_order'],3); ?></label>
                                </td>
                                <td class="text-right">
                                    <label class="pull-right"><?php echo number_format($item_per_kg,3); ?></label>
                                </td>
                                <td class="text-right">
                                    <label class="pull-right"><?php echo $item_per_currency?></label>
                                </td>
                                <td>
                                    <input type="text" value="<?php echo $value['quantity_order']; ?>" class="form-control float_type_positive quantity order_quantity_total" id="quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][quantity_order]">
                                    <input type="hidden" value="<?php echo $value['quantity_order']; ?>" class="form-control" id="old_quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][old_quantity_order]">
                                </td>
                                <td class="text-right">
                                    <label class="control-label total_price" id="total_quantity_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $item_per_kg; ?>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <label class="control-label total_price" id="total_price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $item_per_currency; ?>
                                    </label>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL')?></th>
                        <th>&nbsp;</th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php echo number_format($total_kg,3);?></label></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_grand_total"><?php echo number_format($total_currency,2);?></label></th>
                        <th>&nbsp;</th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php echo number_format($total_kg,3);?></label></th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php echo number_format($total_kg,3);?></label></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<script type="text/javascript">
    function calculate_total()
    {
        $("#lbl_quantity_kg_grand_total").html('0.000')
        $("#lbl_price_grand_total").html('0.000')

        var quantity_kg_grand_total=0;
        var price_currency_grand_total=0;
        /*var lbl_quantity_kg_grand_total='0.00';
         var lbl_price_grand_total='0.00';*/
        //var get_current_id='';
        var id='';
        //console.log($('#items_container .order_quantity_total').length)
        $('#items_container .order_quantity_total').each(function(index,element)
        {
            id = $(element).attr('data-current-id');
            if($('#items_container #quantity_type_id_'+id).val()!='-1')
            {
                var price=parseFloat($("#price_id_"+id).val());
                var quantity=parseFloat($("#quantity_id_"+id).val());
                if(isNaN(price))
                {
                    price=0;
                }
                if(isNaN(quantity))
                {
                    quantity=0;
                }

                var total_quantity='0.000';
                var pack_size=parseFloat($("#quantity_type_id_"+id).attr('data-pack-size-name'))
                if(pack_size==0)
                {
                    total_quantity=parseFloat($("#quantity_id_"+id).val())
                }
                else
                {
                    total_quantity=parseFloat((pack_size*$("#quantity_id_"+id).val())/1000)
                }
                quantity_kg_grand_total+=total_quantity;
                $("#total_quantity_kg_"+id).html(number_format(total_quantity,3));

                var total_price=quantity*price;
                price_currency_grand_total+=total_price;
                $("#total_price_id_"+id).html(number_format(total_price,3,'.',','));
            }
            else
            {

                alert('Please select pack size. Try again.');
                return false;
            }


        });
        $("#lbl_quantity_kg_grand_total").html(number_format(quantity_kg_grand_total,3))
        $("#lbl_price_grand_total").html(number_format(price_currency_grand_total,3))
    }
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off("input", ".price");
        $(document).off("blur", ".order_quantity_total");
        $(document).off("input", ".quantity");
        $(document).off("input", ".quantity_type");

        $(document).off("input", "'#items_container .order_quantity_total'");
        //////// calculate total quantity.
        $(document).on("input",".order_quantity_total",function()
        {
            calculate_total();
        });
        //////// calculate total price currency.
        $(document).on("input",".price",function()
        {
            calculate_total();
        });
        //////// packsize onchange quantity & price change.
        $(document).on("input",".quantity_type",function()
        {
            if($(this).val()=='-1')
            {
                var current_id=parseInt($(this).attr('data-current-id'));
                $("#quantity_id_"+current_id).val('');
                $("#total_quantity_kg_"+current_id).html('0.000');
                $("#price_id_"+current_id).val('');
                $("#total_price_id_"+current_id).html('0.000');
                calculate_total();
            }
            else
            {
                calculate_total();
            }
        });
    });

</script>
