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
    <input type="hidden" id="id" name="id" value="0" />
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
                <select id="year_id" class="form-control" name="item[year_id]">
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
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
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
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_opening]" id="date_opening" class="form-control datepicker date_large" value="<?php echo System_helper::display_date(time());?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
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
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_expected]" id="date_expected" class="form-control datepicker date_large" value="<?php echo System_helper::display_date(time());?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[lc_number]" id="lc_number" class="form-control" value=""/>
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
                        <option value="<?php echo $currency['value']?>"><?php echo $currency['text'];?></option>
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
                <textarea name="item[consignment_name]" id="consignment_name" class="form-control" ></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[other_cost_currency]" id="other_cost_currency" class="form-control float_type_positive" value=""/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control" ></textarea>
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
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total">0.000</label></th>
                        <th class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                        <th class="text-right"><label class="control-label" id="lbl_price_grand_total">0.000</label></th>
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
    <div id="items_old"></div>
</div>

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
                $("#total_quantity_kg_"+id).html(number_format(total_quantity,3));

                var total_price=quantity*price;
                price_currency_grand_total+=total_price;
                $("#total_price_id_"+id).html(number_format(total_price,3,'.',','));
            }
            else
            {
                $(this).val('')
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
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});

        $(document).off('input','#items_container .order_quantity_total');
        $(document).off('change','#principal_id');
        $(document).on("change","#principal_id",function()
        {
            <?php
            if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
            {
                ?>
                alert('Access denied. You have no permission. Please try again. ')
                return;
                <?php
            }
            ?>
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
            $("#items_container").append(html);

        });

        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).off("input", ".price");
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
