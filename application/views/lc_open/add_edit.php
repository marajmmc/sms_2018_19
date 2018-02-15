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
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
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
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $item['fiscal_year_name']?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="fiscal_year_id" class="form-control" name="item[fiscal_year_id]">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        $time=time();
                        $selected='';
                        foreach($fiscal_years as $year)
                        {
                            if($time>=$year['date_start'] && $time<=$year['date_end'])
                            {
                                ?>
                                <option value="<?php echo $year['value']?>" selected="selected"><?php echo $year['text'];?></option>
                            <?php
                            }
                            else
                            {
                                ?>
                                <option value="<?php echo $year['value']?>"><?php echo $year['text'];?></option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="month_id" class="form-control" name="item[month_id]" >
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        for($i=1;$i<13;$i++)
                        {
                            ?>
                            <option value="<?php echo $i;?>"><?php echo date("F", mktime(0, 0, 0,  $i,1, 2000));?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_opening]" id="date_opening" class="form-control datepicker date_large" value="<?php echo System_helper::display_date($item['date_opening']);?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $item['principal_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="principal_id" name="item[principal_id]" class="form-control" >
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($principals as $principal)
                        {
                            ?>
                            <option value="<?php echo $principal['value']?>" ><?php echo $principal['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_ACCOUNT_NUMBER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="bank_account_id" name="item[bank_account_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($bank_accounts as $bank_account)
                    {?>
                        <option value="<?php echo $bank_account['value']?>" <?php if(($bank_account['value']==$item['bank_account_id'])){ echo "selected";}?>><?php echo $bank_account['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[price_open_other_currency]" id="price_open_other_currency" class="form-control float_type_positive price_open_other_currency" value="<?php echo $item['price_open_other_currency'];?>"/>
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_open]" id="remarks_open" class="form-control" ><?php echo $item['remarks_open'];?></textarea>
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
                        <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th style="min-width: 100px; text-align: right  ;"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th style="min-width: 100px; text-align: right;"><?php echo $CI->lang->line('LABEL_QUANTITY_OPEN_KG'); ?></th>
                        <th style="min-width: 100px; text-align: right;">Unit Price (Currency)</th>
                        <th style="min-width: 150px; text-align: right;">Total Price (Currency)</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="items_container">
                    <?php
                    $quantity_open_kg=0;
                    $quantity_open_total_kg=0;
                    $price_open_currency=0;
                    foreach($items as $index=>$value)
                    {
                        if($value['pack_size_id']==0)
                        {
                            $quantity_open_kg=$value['quantity_open'];
                        }
                        else
                        {
                            $quantity_open_kg=(($value['quantity_open']*$value['pack_size_name'])/1000);
                        }
                        $price_open_currency=($value['quantity_open']*$value['price_unit_currency']);

                        $quantity_open_total_kg+=$quantity_open_kg;
                        ?>
                        <tr>
                            <td>
                                <label><?php echo $value['variety_name']; ?> (<?php echo $value['variety_name_import']; ?>)</label>
                                <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $value['variety_id']; ?>">
                            </td>
                            <td>
                                <label><?php if($value['pack_size_id']==0){echo 'Bulk';}else{echo $value['pack_size_name'];} ?></label>
                                <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $value['pack_size_id']; ?>" class="pack_size_id" data-pack-size-name="<?php if($value['pack_size_id']==0){echo 0;}else{echo $value['pack_size_name'];} ?>">
                            </td>
                            <td>
                                <input type="text" value="<?php echo $value['quantity_open']; ?>" class="form-control float_type_positive quantity_open" id="quantity_open_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_open]">
                            </td>
                            <td class="text-right">
                                <label class="control-label quantity_open_kg" id="quantity_open_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <?php echo number_format($quantity_open_kg,3,'.',''); ?>
                                </label>
                            </td>
                            <td>
                                <input type="text" value="<?php echo $value['price_unit_currency']; ?>" class="form-control float_type_positive price_unit_currency" id="price_unit_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][price_unit_currency]">
                            </td>
                            <td class="text-right">
                                <label class="control-label price_open_currency" id="price_open_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <?php echo number_format($price_open_currency,2); ?>
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
                        <th class="text-right"><label class="control-label" id="lbl_quantity_open_total_kg"><?php echo number_format(($quantity_open_total_kg),3,'.','')?></label></th>
                        <th class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_variety_total_currency"><?php echo number_format($item['price_open_variety_currency'],2)?></label></th>
                        <th class="text-right"></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY')?></th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_open_other_currency"> <?php echo number_format(($item['price_open_other_currency']),2)?></label>
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                        <th class="text-right">
                            <label class="control-label" id="lbl_price_total_currency"> <?php echo number_format(($item['price_open_variety_currency']+$item['price_open_other_currency']),2)?></label>
                        </th>
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
                <select class="form-control variety_id" id="variety_id">
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
                <select style="display: none;" class="form-control pack_size_id" data-new-pack-size="0">
                    <option value="-1"><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="0" data-pack-size-name="0">Bulk</option>
                    <?php
                    foreach($pack_sizes as $pack_size)
                    {
                        ?>
                        <option value="<?php echo $pack_size['value']?>" data-pack-size-name="<?php echo $pack_size['text'];?>"><?php echo $pack_size['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive quantity_open" value="" style="display: none;"/>
            </td>
            <td class="text-right">
                <label class="control-label quantity_open_kg">0.000</label>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive price_unit_currency" value="" style="display: none;"/>
            </td>
            <td class="text-right">
                <label class="control-label price_open_currency">0.00</label>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>

    function calculate_total()
    {
        var quantity_open_total_kg=0;
        var price_variety_total_currency=0;
        $('#items_container .quantity_open_kg').each(function(index, element)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            quantity_open_total_kg+=parseFloat($('#quantity_open_kg_'+current_id).html().replace(/,/g,''));
            price_variety_total_currency+=parseFloat($('#price_open_currency_'+current_id).html().replace(/,/g,''));
        });
        $('#lbl_quantity_open_total_kg').html(number_format(quantity_open_total_kg,3,'.',''));
        $('#lbl_price_variety_total_currency').html(number_format(price_variety_total_currency,2));
        if(isNaN($('#price_open_other_currency').val()))
        {
            var price_total_currency=price_variety_total_currency;
        }
        else
        {
            var price_total_currency=(parseFloat($('#price_open_other_currency').val())+price_variety_total_currency);
        }

        $('#lbl_price_total_currency').html(number_format(price_total_currency,2));
    }
    $(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});

        $(document).off('change','#principal_id');
        $(document).on("change","#principal_id",function()
        {
            $('#items_container').html('');
            var principal_id = $('#principal_id').val();
            if(principal_id>0)
            {
                $.ajax({
                    url: '<?php echo site_url($CI->controller_url.'/get_dropdown_arm_varieties_by_principal_id'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{principal_id:principal_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#items_container').html('');
                $('.variety_id').html('');
            }
        });

        $(document).off("click", ".system_button_add_more");
        $(document).on("click", ".system_button_add_more", function(event)
        {
            if($("#principal_id").val()=="")
            {
                alert("Please select principal");
                return false;
            }
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
            $(content_id+' .variety_id').attr('data-current-id',current_id);
            $(content_id+' .variety_id').attr('name','items['+current_id+'][variety_id]');

            $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
            $(content_id+' .pack_size_id').attr('data-current-id',current_id);
            $(content_id+' .pack_size_id').attr('name','items['+current_id+'][pack_size_id]');

            $(content_id+' .quantity_open').attr('id','quantity_open_'+current_id);
            $(content_id+' .quantity_open').attr('data-current-id',current_id);
            $(content_id+' .quantity_open').attr('name','items['+current_id+'][quantity_open]');

            $(content_id+' .quantity_open_kg').attr('id','quantity_open_kg_'+current_id);
            $(content_id+' .quantity_open_kg').attr('data-current-id',current_id);

            $(content_id+' .price_unit_currency').attr('id','price_unit_currency_'+current_id);
            $(content_id+' .price_unit_currency').attr('data-current-id',current_id);
            $(content_id+' .price_unit_currency').attr('name','items['+current_id+'][price_unit_currency]');

            $(content_id+' .price_open_currency').attr('id','price_open_currency_'+current_id);
            $(content_id+' .price_open_currency').attr('data-current-id',current_id);
            $(content_id+' .price_open_currency').attr('name','items['+current_id+'][price_open_currency]');
            var html=$(content_id).html();
            $("#items_container").append(html);

        });

        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
            calculate_total();
        });

        $(document).off('change','#items_container .variety_id');
        $(document).on('change', '#items_container .variety_id', function(){
            var current_id=parseInt($(this).attr('data-current-id'));
            var variety_id=parseInt($(this).val());
            if(variety_id>0)
            {
                $('#pack_size_id_'+current_id).show();
            }
            else
            {
                $('#pack_size_id_'+current_id).hide();
            }
            $('#quantity_open_'+current_id).hide();
            $('#price_unit_currency_'+current_id).hide();

            $('#pack_size_id_'+current_id).val('-1');
            $('#quantity_open_'+current_id).val('');
            $('#price_unit_currency_'+current_id).val('');
            $('#quantity_open_kg_'+current_id).html('0.000');
            $('#price_open_currency_'+current_id).html('0.00');
            calculate_total();
        })

        $(document).off('change','#items_container .pack_size_id');
        $(document).on('change', '#items_container .pack_size_id', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var pack_size_id=parseInt($(this).val());
            if(pack_size_id!='-1')
            {
                $('#quantity_open_'+current_id).show();
                $('#price_unit_currency_'+current_id).show();
            }
            else
            {
                $('#quantity_open_'+current_id).hide();
                $('#price_unit_currency_'+current_id).hide();
            }
            $('#quantity_open_'+current_id).val('');
            $('#price_unit_currency_'+current_id).val('');
            $('#quantity_open_kg_'+current_id).html('0.000');
            $('#price_open_currency_'+current_id).html('0.00');
            calculate_total();
        })

        $(document).off('input','#items_container .quantity_open');
        $(document).on('input', '#items_container .quantity_open', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_open_kg=0;
            var quantity_open=parseFloat($(this).val());
            var price_unit_currency=parseFloat($("#price_unit_currency_"+current_id).val());
            if(isNaN(quantity_open))
            {
                quantity_open=0;
            }
            if(isNaN(price_unit_currency))
            {
                var price_unit_currency=0;
            }
            var pack_size=parseFloat($("#pack_size_id_"+current_id).attr('data-pack-size-name'));
            if($("#pack_size_id_"+current_id).attr('data-new-pack-size')==0)
            {
                var pack_size=parseFloat($('option:selected', $("#pack_size_id_"+current_id)).attr('data-pack-size-name'));
            }
            if(pack_size==0)
            {
                quantity_open_kg=quantity_open;
            }
            else
            {
                quantity_open_kg=parseFloat((pack_size*quantity_open)/1000);
            }
            $("#quantity_open_kg_"+current_id).html(number_format(quantity_open_kg,3,'.',''));
            var price_open_currency=(quantity_open*price_unit_currency);
            $("#price_open_currency_"+current_id).html(number_format(price_open_currency,2));
            calculate_total();
        })
        $(document).off('change','#items_container .price_unit_currency');
        $(document).on('input', '#items_container .price_unit_currency', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_open=parseFloat($("#quantity_open_"+current_id).val());
            var price_unit_currency=parseFloat($(this).val());
            if(isNaN(quantity_open))
            {
                quantity_open=0;
            }
            if(isNaN(price_unit_currency))
            {
                var price_unit_currency=0;
            }
            var price_open_currency=(quantity_open*price_unit_currency);
            $("#price_open_currency_"+current_id).html(number_format(price_open_currency,2));
            calculate_total();
        })
        $(document).off('input','#price_open_other_currency');
        $(document).on('input', '#price_open_other_currency', function()
        {
            calculate_total();
            if(isNaN($('#price_open_other_currency').val()))
            {
                var price_open_other_currency=0;
            }
            else
            {
                var price_open_other_currency=parseFloat($('#price_open_other_currency').val());
            }
            $('#lbl_price_open_other_currency').html(number_format(price_open_other_currency,2))
        })
    })
</script>