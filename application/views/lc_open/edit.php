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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fiscal_years['text']?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $principals['text'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_expected]" id="date_expected" class="form-control datepicker date_large" value="<?php echo System_helper::display_date($item['date_expected']);?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[lc_number]" id="lc_number" class="form-control" value="<?php echo $item['lc_number'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?><span style="color:#FF0000">*</span></label>
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[consignment_name]" id="consignment_name" class="form-control" ><?php echo $item['consignment_name'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[other_cost_currency]" id="other_cost_currency" class="form-control float_type_positive" value="<?php echo $item['other_cost_currency'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control" ><?php echo $item['remarks'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="widget-header">
                <div class="title">
                   Add Varieties
                </div>
                <div class="clearfix"></div>
            </div>
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                            <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                            <th style="min-width: 100px;">KG</th>
                            <th style="min-width: 100px;">Unit Price (Currency)</th>
                            <th style="min-width: 150px;">Total Price (Currency)</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody id="items_container">
                    <?php
                        $total_kg='0.000';
                        $total_currency='0.000';
                        $grand_total_currency='0.000';
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
                                    <input type="text" value="<?php echo $value['quantity_order']; ?>" class="form-control float_type_positive quantity order_quantity_total" id="quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][quantity_order]">
                                    <input type="hidden" value="<?php echo $value['quantity_order']; ?>" class="form-control" id="old_quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][old_quantity_order]">
                                </td>
                                <td class="text-right">
                                    <label class="control-label total_price" id="total_quantity_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $item_per_kg; ?>
                                    </label>
                                </td>
                                <td>
                                    <input type="text" value="<?php echo $value['price_currency']; ?>" class="form-control float_type_positive price" id="price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][price_currency]">
                                    <input type="hidden" value="<?php echo $value['price_currency']; ?>" class="form-control" id="price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][old_price_currency]">
                                </td>
                                <td class="text-right">
                                    <label class="control-label total_price" id="total_price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $item_per_currency; ?>
                                    </label>
                                </td>
                                <td>

                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php echo number_format($total_kg,3);?></label></th>
                        <th class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_grand_total"><?php echo number_format($total_currency,2);?></label></th>
                        <th class="text-right"></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_grand_total_currency">
                                <?php
                                $grand_total_currency=($total_currency+$item['other_cost_currency']);
                                echo number_format($grand_total_currency,2);?></label></th>
                        <th>&nbsp;</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-xs-4">
                    <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($items);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
                </div>
                <div class="col-xs-4">

                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <input type="hidden" class="lc_detail_id" id="lc_detail_id" value="0" />
                <select class="form-control variety" id="varieties_container">
                    <?php
                        if($item['id']>0)
                        {
                            ?>
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                <?php
                                    foreach($varieties as $variety)
                                    {
                                        ?><option value="<?php echo $variety['value']; ?>"><?php echo $variety['text']; ?></option><?php
                                    }
                                ?>
                            <?php
                        }
                    ?>
                </select>
            </td>
            <td>
                <select class="form-control quantity_type">
                    <option value="-1"><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="0" data-pack-size-name="0">Bulk</option>
                    <?php
                        foreach($packs as $pack)
                        {
                            ?>
                            <option value="<?php echo $pack['value']?>" data-pack-size-name="<?php echo $pack['text'];?>"><?php echo $pack['text'];?></option>
                            <?php
                        }
                    ?>
                </select>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive quantity" value=""/>
            </td>
            <td class="text-right">
                <label class="control-label total_quantity_kg">0.000</label>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive price" value=""/>
            </td>
            <td class="text-right">
                <label class="control-label total_price">0.000</label>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function calculate_total()
    {
        $("#lbl_quantity_kg_grand_total").html('0.000')
        $("#lbl_price_grand_total").html('0.000')
        $("#lbl_price_grand_total_currency").html('0.000')
        var other_cost_currency=parseFloat($("#other_cost_currency").val());
        if(isNaN(other_cost_currency))
        {
            other_cost_currency=0;
        }
        var quantity_kg_grand_total=0;
        var price_currency_grand_total=0;
        var id='';

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
        var price_other_cost_currency_grand_total=(price_currency_grand_total+other_cost_currency);
        $("#lbl_quantity_kg_grand_total").html(number_format(quantity_kg_grand_total,3))
        $("#lbl_price_grand_total").html(number_format(price_currency_grand_total,3))
        $("#lbl_price_grand_total_currency").html(number_format(price_other_cost_currency_grand_total,3))
    }
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});

        $(document).off("click", ".system_button_add_more");
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .lc_detail_id').attr('name','varieties['+current_id+'][lc_detail_id]');
            $(content_id+' .variety').attr('name','varieties['+current_id+'][variety_id]');

            $(content_id+' .quantity_type').attr('id','quantity_type_id_'+current_id);
            $(content_id+' .quantity_type').attr('data-current-id',current_id);
            $(content_id+' .quantity_type').attr('name','varieties['+current_id+'][quantity_type_id]');

            $(content_id+' .quantity').addClass('order_quantity_total');
            $(content_id+' .quantity').attr('id','quantity_id_'+current_id);
            $(content_id+' .quantity').attr('data-current-id',current_id);
            $(content_id+' .quantity').attr('name','varieties['+current_id+'][quantity_order]');

            $(content_id+' .total_quantity_kg').attr('id','total_quantity_kg_'+current_id);
            $(content_id+' .total_quantity_kg').attr('data-current-id',current_id);
            //$(content_id+' .total_quantity_kg').attr('name','varieties['+current_id+'][quantity_order]');

            $(content_id+' .price').attr('id','price_id_'+current_id);
            $(content_id+' .price').attr('data-current-id',current_id);
            $(content_id+' .price').attr('name','varieties['+current_id+'][price_currency]');

            $(content_id+' .total_price').attr('id','total_price_id_'+current_id);
            $(content_id+' .total_price').attr('data-current-id',current_id);
            $(content_id+' .total_price').attr('name','varieties['+current_id+'][amount_price_total_order]');
            var html=$(content_id).html();
            $("#items_container").append(html);

        });

        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).off("input", ".price");
        $(document).off("input", ".order_quantity_total");
        $(document).off("input", ".quantity");
        $(document).off("input", ".quantity_type");
        $(document).off("input", ".other_cost_currency");


        //////// onchange Pack Size empty relative field.
        /*$(document).on("change",".quantity_type",function()
        {
            var current_id = $(this).attr("data-current-id");
            $("#quantity_id_"+current_id).val('')
            $("#total_quantity_kg_"+current_id).html('')
            $("#price_id_"+current_id).val('')
            $("#total_price_id_"+current_id).html('')

            $("#total_quantity_kg_"+current_id).html('0.00')
            $("#total_price_id_"+current_id).html('0.00')
        });*/

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
                $("#lbl_price_grand_total_currency").html('0.000');
                calculate_total();
            }
            else
            {
                calculate_total();
            }
        });
        //////// calculate total price currency with other cost.
        $(document).on("input",".other_cost_currency",function()
        {
            calculate_total();
        });
    });

</script>
