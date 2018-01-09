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

$disabled='';
if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
{
    $disabled=' disabled';
}
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
                <select id="year_id" class="form-control" name="item[year_id]"<?php echo $disabled; ?>>
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    if(isset($item['year_id']))
                    {
                        foreach($fiscal_years as $year)
                        {
                            ?>
                            <option value="<?php echo $year['value']?>"<?php if($item['year_id']==$year['value']) {echo 'selected';} ?>><?php echo $year['text'];?></option>
                            <?php
                        }
                    }
                    else
                    {
                        $time=time();
                        $selected='';
                        foreach($fiscal_years as $year)
                        {
                            if($time>=$year['date_start'] && $time<=$year['date_end'])
                            {
                                $selected=' selected';
                            }
                            ?>
                            <option value="<?php echo $year['value']?>"<?php echo $selected; ?>><?php echo $year['text'];?></option>
                            <?php
                            if($selected)
                            {
                                $selected='';
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="month_id" class="form-control" name="item[month_id]"<?php echo $disabled; ?>>
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    for($i=1;$i<13;$i++)
                    {
                        ?>
                        <option value="<?php echo $i;?>" <?php if($i==$item['month_id']){echo 'selected';} ?>><?php echo date("F", mktime(0, 0, 0,  $i,1, 2000));?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_opening]" id="date_opening" class="form-control datepicker date_large" value="<?php echo System_helper::display_date($item['date_opening']);?>"<?php echo $disabled; ?>/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_expected]" id="date_expected" class="form-control datepicker date_large" value="<?php echo System_helper::display_date($item['date_expected']);?>"<?php echo $disabled; ?>/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="principal_id" name="item[principal_id]" class="form-control"<?php echo $disabled; ?>>
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($principals as $principal)
                    {
                        ?>
                        <option value="<?php echo $principal['value']?>" <?php if(($principal['value']==$item['principal_id'])){ echo "selected";}?>><?php echo $principal['text'];?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[lc_number]" id="lc_number" class="form-control" value="<?php echo $item['lc_number'];?>"<?php echo $disabled; ?>/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="currency_id" name="item[currency_id]" class="form-control"<?php echo $disabled; ?>>
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
                <textarea name="item[consignment_name]" id="consignment_name" class="form-control" <?php echo $disabled; ?>><?php echo $item['consignment_name'];?></textarea>
            </div>
        </div>
        <!--<div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php /*echo $this->lang->line('LABEL_TOTAL_CURRENCY');*/?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[price_total_currency]" id="price_total_currency" class="form-control" value="<?php /*echo $item['price_total_currency'];*/?>"<?php /*echo $disabled; */?>/>
            </div>
        </div>-->
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[other_cost_currency]" id="other_cost_currency" class="form-control float_type_positive" value="<?php echo $item['other_cost_currency'];?>"<?php echo $disabled; ?>/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control" <?php echo $disabled; ?>><?php echo $item['remarks'];?></textarea>
            </div>
        </div>
        <!--<div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php /*echo $this->lang->line('LABEL_CURRENCY_RATE');*/?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[amount_currency_rate]" id="rate" class="form-control float_type_positive" style="text-align: left;" value="<?php /*echo $item['amount_currency_rate'];*/?>"<?php /*echo $disabled; */?>/>
            </div>
        </div>-->
        <!--<div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php /*echo $CI->lang->line('STATUS');*/?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="item[status]" class="form-control"<?php /*echo $disabled; */?>>
                    <option value="<?php /*echo $CI->config->item('system_status_active'); */?>"
                        <?php
/*                        if ($item['status'] == $CI->config->item('system_status_active')) {
                            echo "selected='selected'";
                        }
                        */?> ><?php /*echo $CI->lang->line('ACTIVE') */?>
                    </option>
                    <option value="<?php /*echo $CI->config->item('system_status_inactive'); */?>"
                        <?php
/*                        if ($item['status'] == $CI->config->item('system_status_inactive')) {
                            echo "selected='selected'";
                        }
                        */?> ><?php /*echo $CI->lang->line('INACTIVE') */?></option>
                </select>
            </div>
        </div>-->
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
                            <th style="min-width: 100px;">Unit Price (Currency)</th>
                            <!--<th style="min-width: 150px;">Total Price (Tk.)</th>-->
                        </tr>
                    </thead>
                    <tbody id="items_old_container">
                    <?php
                        foreach($items as $index=>$value)
                        {
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
                                    {
                                        ?>
                                        <label><?php echo $varieties[$value['variety_id']]['text']; ?></label>
                                        <input type="hidden" name="varieties[<?php echo $index+1;?>][variety_id]" value="<?php echo $value['variety_id']; ?>">
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <select name="varieties[<?php echo $index+1;?>][variety_id]" class="form-control variety">
                                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                            <?php
                                                foreach($varieties as $variety)
                                                {
                                                    ?>
                                                    <option value="<?php echo $variety['value']; ?>"<?php if($variety['value']==$value['variety_id']){echo ' selected';} ?>><?php echo $variety['text']; ?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
                                    {
                                        ?>
                                        <label><?php if($value['quantity_type_id']==0){echo 'Bulk';}else{echo $packs[$value['quantity_type_id']]['text'];} ?></label>
                                        <input type="hidden" name="varieties[<?php echo $index+1;?>][quantity_type_id]" value="<?php echo $value['quantity_type_id']; ?>">
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <select name="varieties[<?php echo $index+1;?>][quantity_type_id]" class="form-control quantity_type">
                                            <option value="-1"><?php echo $this->lang->line('SELECT'); ?></option>
                                            <option value="0"<?php if($value['quantity_type_id']==0){echo 'selected';} ?>>Bulk</option>
                                            <?php
                                                foreach($packs as $pack)
                                                {
                                                    ?>
                                                    <option value="<?php echo $pack['value']?>"<?php if($value['quantity_type_id']==$pack['value']){echo 'selected';} ?>><?php echo $pack['text'];?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
                                    {
                                        ?>
                                        <label><?php if($value['quantity_type_id']==0){echo number_format($value['quantity_order'],3);}else{echo $value['quantity_order'];}  ?></label>
                                        <input type="hidden" value="<?php echo $value['quantity_order']; ?>" name="varieties[<?php echo $index+1;?>][quantity_order]">
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <input type="text" value="<?php echo $value['quantity_order']; ?>" class="form-control float_type_positive quantity" id="quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][quantity_order]">
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
                                    {
                                        ?>
                                        <label><?php echo $value['price_currency']; ?></label>
                                        <input type="hidden" value="<?php echo $value['price_currency']; ?>" name="varieties[<?php echo $index+1;?>][price_currency]">
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <input type="text" value="<?php echo $value['price_currency']; ?>" class="form-control float_type_positive price" id="price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][price_currency]">
                                        <?php
                                    }
                                    ?>
                                </td>
                                <!--<td class="text-right">
                                    <label class="control-label total_price" id="total_price_id_<?php /*echo $index+1;*/?>" data-current-id="<?php /*echo $index+1;*/?>"><?php /*echo number_format($value['amount_price_total_order'],2); */?></label>
                                </td>-->
                                <td>
                                    <?php
                                        if(isset($CI->permissions['action3']) && ($CI->permissions['action3']==1))
                                        {
                                            ?>
                                            <button class="btn btn-danger system_button_add_delete" type="button"><?php echo $CI->lang->line('DELETE'); ?></button>
                                            <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
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
                    <option value="0">Bulk</option>
                    <?php
                        foreach($packs as $pack)
                        {
                            ?>
                            <option value="<?php echo $pack['value']?>"><?php echo $pack['text'];?></option>
                            <?php
                        }
                    ?>
                </select>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive quantity" value=""/>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive price" value=""/>
            </td>
            <!--<td class="text-right">
                <label class="control-label total_price">0.00</label>
            </td>-->
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="items_old">
    </div>
</div>

<script type="text/javascript">
    function calculate_total(id)
    {
        var price=parseFloat($("#price_id_"+id).val());
        var quantity=parseFloat($("#quantity_id_"+id).val());
        var currency_rate=$('#rate').val();

        if(isNaN(price))
        {
            price=0;
        }
        if(isNaN(quantity))
        {
            quantity=0;
        }
        if(isNaN(currency_rate))
        {
            currency_rate=0;
        }
        total_price=quantity*price*currency_rate;
        $("#total_price_id_"+id).html(number_format(total_price,2,'.',','));
    }

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var principal_id_old=<?php echo $item['principal_id']; ?>;
        var currencies=JSON.parse('<?php echo json_encode($currency_rates);?>');
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        
        $(document).off("change", "#currency_id");
        $(document).on("change","#currency_id",function()
        {
            <?php
            if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
            {
                ?>
                return;
                <?php
            }
            ?>
            var currency_id = $('#currency_id').val();
            if(currency_id>0)
            {
                $("#rate").val(currencies[currency_id]);
            }
            else
            {
                $("#rate").val('');
            }

            $('.total_price').each(function(index,element)
            {
                if($(element).attr('id'))
                {
                    var data_current_id=$(element).attr('data-current-id');
                    calculate_total(data_current_id);
                }
            });
        });

        $(document).off("input","#rate");
        $(document).on("input","#rate",function()
        {
            <?php
            if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
            {
                ?>
                return;
                <?php
            }
            ?>
            $('.total_price').each(function(index,element)
            {
                if($(element).attr('id'))
                {
                    var data_current_id=$(element).attr('data-current-id');
                    calculate_total(data_current_id);
                }
            });
        });

        $(document).off('change','#principal_id');
        $(document).on("change","#principal_id",function()
        {
            <?php
            if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
            {
                ?>
                return;
                <?php
            }
            ?>
            $('#items_old_container').html('');
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
                        if(principal_id==principal_id_old && <?php if($item['id']>0){echo 'true';}else{echo 'false';} ?>)
                        {
                            $('#items_old_container').html($('#items_old').html());
                            $('.total_price').each(function(index,element)
                            {
                                if($(element).attr('id'))
                                {
                                    var data_current_id=$(element).attr('data-current-id');
                                    calculate_total(data_current_id);
                                }
                            });
                        }
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#items_old_container').html('');
                $('#varieties_container').html('');
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

            $(content_id+' .variety').attr('name','varieties['+current_id+'][variety_id]');

            $(content_id+' .quantity_type').attr('id','quantity_type_id_'+current_id);
            $(content_id+' .quantity_type').attr('data-current-id',current_id);
            $(content_id+' .quantity_type').attr('name','varieties['+current_id+'][quantity_type_id]');

            $(content_id+' .quantity').attr('id','quantity_id_'+current_id);
            $(content_id+' .quantity').attr('data-current-id',current_id);
            $(content_id+' .quantity').attr('name','varieties['+current_id+'][quantity_order]');

            $(content_id+' .price').attr('id','price_id_'+current_id);
            $(content_id+' .price').attr('data-current-id',current_id);
            $(content_id+' .price').attr('name','varieties['+current_id+'][price_currency]');

            $(content_id+' .total_price').attr('id','total_price_id_'+current_id);
            $(content_id+' .total_price').attr('data-current-id',current_id);
            $(content_id+' .total_price').attr('name','varieties['+current_id+'][amount_price_total_order]');
            var html=$(content_id).html();
            $("#items_old_container").append(html);
            $(content_id+' .quantity_type').removeAttr('id');
            $(content_id+' .quantity').removeAttr('id');
            $(content_id+' .price').removeAttr('id');
            $(content_id+' .total_price').removeAttr('id');
        });

        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).off("input", ".price");
        $(document).off("input", ".quantity");
        $(document).on("input",".price",function()
        {
            var current_id = $(this).attr("data-current-id");
            calculate_total(current_id);
        });
        $(document).on("input",".quantity",function()
        {
            var current_id = $(this).attr("data-current-id");
            calculate_total(current_id);
        });
        $('#items_old').html($('#items_old_container').html());
    });
</script>
