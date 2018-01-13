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
/*if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
{
    $disabled=' disabled';
}*/

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
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $fiscal_years['text']?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="year_id" class="form-control" name="item[year_id]">
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
                            <option value="<?php echo $i;?>" <?php if($i==$item['month_id']){echo 'selected';} ?>><?php echo date("F", mktime(0, 0, 0,  $i,1, 2000));?></option>
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
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label>
                <?php
                }
                else
                {
                    ?>
                    <input type="text" name="item[date_opening]" id="date_opening" class="form-control datepicker date_large" value="<?php echo System_helper::display_date($item['date_opening']);?>" />
                <?php
                }
                ?>
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
                    <label class="control-label"><?php echo $principals['text'];?></label>
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
                            <option value="<?php echo $principal['value']?>" <?php if(($principal['value']==$item['principal_id'])){ echo "selected";}?>><?php echo $principal['text'];?></option>
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
                            <th style="min-width: 100px;">KG</th>
                            <th style="min-width: 100px;">Unit Price (Currency)</th>
                            <th style="min-width: 150px;">Total Price (Currency)</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody id="items_old_container">
                    <?php
                        $total_kg='0.00';
                        $total_currency='0.00';
                        foreach($items as $index=>$value)
                        {
                            $item_per_kg='0.00';
                            $item_per_currency='0.00';
                            if($item['id']>0)
                            {
                                if($value['quantity_type_id']==0)
                                {
                                    $item_per_kg = number_format(($value['quantity_order']/1000),2);
                                }
                                else
                                {
                                    $item_per_kg = number_format((($packs[$value['quantity_type_id']]['text']*$value['quantity_order'])/1000),2);
                                }
                                $item_per_currency=number_format(($value['quantity_order']*$value['price_currency']),2);
                                $total_kg+=$item_per_kg;
                                $total_currency+=($value['quantity_order']*$value['price_currency']);
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    if($item['id']>0)
                                    {
                                        ?>
                                        <input type="hidden" name="varieties[<?php echo $index+1;?>][lc_detail_id]" value="<?php echo $value['id']; ?>" />
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
                                    if($item['id']>0)
                                    {
                                        ?>
                                        <label><?php if($value['quantity_type_id']==0){echo 'Bulk';}else{echo $packs[$value['quantity_type_id']]['text'];} ?></label>
                                        <input type="hidden" name="varieties[<?php echo $index+1;?>][quantity_type_id]" value="<?php echo $value['quantity_type_id']; ?>" class="quantity_type">
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
                                    <input type="text" value="<?php echo $value['quantity_order']; ?>" class="form-control float_type_positive quantity order_quantity_total" id="quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][quantity_order]">
                                    <input type="hidden" value="<?php echo $value['quantity_order']; ?>" class="form-control" id="old_quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][old_quantity_order]">
                                </td>
                                <td class="text-right">
                                    <label class="control-label total_price" id="total_quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php
                                            echo $item_per_kg;
                                        ?>
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
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('TOTAL_KG')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php echo $total_kg;?></label></th>
                        <th class="text-right"><?php echo $this->lang->line('TOTAL_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_grand_total"><?php echo number_format($total_currency,2);?></label></th>
                        <th class="text-right"></th>
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
                <label class="control-label total_quantity_kg">0.00</label>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive price" value=""/>
            </td>
            <td class="text-right">
                <label class="control-label total_price">0.00</label>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="items_old"></div>
</div>

<script type="text/javascript">
    function calculate_total()
    {
        $("#lbl_quantity_kg_grand_total").html('')
        $("#lbl_price_grand_total").html('')

        var quantity_kg_grand_total=0;
        var price_currency_grand_total=0;
        /*var lbl_quantity_kg_grand_total='0.00';
        var lbl_price_grand_total='0.00';*/
        //var get_current_id='';
        var id='';
        console.log($('.order_quantity_total').length)
        $('.order_quantity_total').each(function(index,element)
        {
            id = $(element).attr('data-current-id');
            var price=parseFloat($("#price_id_"+id).val());
            var quantity=parseFloat($("#quantity_id_"+id).val());
            console.log(element)
            //var currency_rate=$('#rate').val();

            if(isNaN(price))
            {
                price=0;
            }
            if(isNaN(quantity))
            {
                quantity=0;
            }

            var total_quantity='0.00';
            var pack_size=parseFloat($('option:selected', $("#quantity_type_id_"+id)).attr('data-pack-size-name'))
            if(pack_size==0)
            {
                total_quantity=parseFloat($("#quantity_id_"+id).val())
            }
            else
            {
                total_quantity=parseFloat((pack_size*$("#quantity_id_"+id).val())/1000)
            }
            quantity_kg_grand_total+=total_quantity;
            $("#total_quantity_kg_"+id).html(number_format(total_quantity,2));

            var total_price=quantity*price;
            price_currency_grand_total+=total_price;
            $("#total_price_id_"+id).html(number_format(total_price,2,'.',','));

        });
        $("#lbl_quantity_kg_grand_total").html(quantity_kg_grand_total)
        $("#lbl_price_grand_total").html(price_currency_grand_total)
    }
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var principal_id_old=<?php echo $item['principal_id']; ?>;
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});

        $(document).off('blur','.order_quantity_total');
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
            $("#items_old_container").append(html);

        });

        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).off("input", ".price");
        $(document).off("blur", ".order_quantity_total");
        $(document).off("input", ".quantity");
        $(document).off("input", ".quantity_type");


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

        //////// conversion with KG from GM without bulk.
        /*$(document).on("input",".quantity_order",function()
        {
            *//*var current_id = $(this).attr("data-current-id");
            var total_quantity=0;
            $("#total_quantity_kg_"+current_id).html('');
            $("#total_quantity_kg_"+current_id).html('0.00')
            $("#total_price_id_"+current_id).html('');
            $("#total_price_id_"+current_id).html('0.00')
            $("#price_id_"+current_id).val('')
            $("#price_id_"+current_id).val('0.00')
            if($("#quantity_type_id_"+current_id).val()=='-1')
            {
                alert('Please select pack size.');
                $(this).val('');
                return false;
            }*//*
            *//*var current_id = $(this).attr("data-current-id");
            calculate_total(current_id);*//*

        });*/

        //////// calculate total price currency.
        $(document).on("input",".price",function()
        {
            /*var current_id = $(this).attr("data-current-id");
            var quantity=parseFloat($("#quantity_id_"+current_id).val());
            var total_price=0;
            $("#total_price_id_"+current_id).html('')
            $("#total_price_id_"+current_id).html('0.00')
            if($("#quantity_type_id_"+current_id).val()=='-1' || $("#quantity_id_"+current_id).val()=='')
            {
                alert('Please select pack size and input quantity');
                $(this).val('');
                return false;
            }
            total_price=parseFloat(quantity*$(this).val())
            $("#total_price_id_"+current_id).html(total_price)*/
            calculate_total();

        });

        $('#items_old').html($('#items_old_container').html());
    });
    $(document).ready(function()
    {
        $(document).off('input','.order_quantity_total');
        $(document).on("blur",".order_quantity_total",function()
        {
            /*var current_id = $(this).attr("data-current-id");
             var total_quantity=0;
             $("#total_quantity_kg_"+current_id).html('');
             $("#total_quantity_kg_"+current_id).html('0.00')
             $("#total_price_id_"+current_id).html('');
             $("#total_price_id_"+current_id).html('0.00')
             $("#price_id_"+current_id).val('')
             $("#price_id_"+current_id).val('0.00')
             if($("#quantity_type_id_"+current_id).val()=='-1')
             {
             alert('Please select pack size.');
             $(this).val('');
             return false;
             }*/
            /*var current_id = $(this).attr("data-current-id");*/
             calculate_total();

        });
    })
</script>
