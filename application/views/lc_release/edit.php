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
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['currency_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['other_cost_currency'],2);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['remarks'];?></label></th>
                </tr>
                </thead>

            </table>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="widget-header text-center" colspan="21">LC (<?php echo $item['lc_number'];?>) Product & Price Details </th>
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
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $total_kg='0.000';
                        $total_currency='0.00';
                        $grand_total_currency='0.00';

                        $release_total_kg='0.000';
                        $release_total_currency='0.00';
                        $release_grand_total_currency='0.00';

                        foreach($items as $index=>$data)
                        {
                            $item_per_kg='0.000';
                            $item_per_currency='0.000';
                            $quantity_release_per_kg='0.000';
                            $quantity_release_per_currency='0.000';
                            if($data['quantity_type_id']==0)
                            {
                                $item_per_kg = number_format(($data['quantity_order']),3);
                            }
                            else
                            {
                                $item_per_kg = number_format((($data['pack_size_name']*$data['quantity_order'])/1000),3);
                            }
                            $item_per_currency=number_format(($data['quantity_order']*$data['price_currency']),2);
                            $total_kg+=$item_per_kg;
                            $total_currency+=($data['quantity_order']*$data['price_currency']);

                            $quantity_release='0.000';
                            if($item['date_release_updated'])
                            {
                                $quantity_release=$data['quantity_release'];
                            }
                            else
                            {
                                $quantity_release=$data['quantity_order'];
                            }
                            if($data['quantity_type_id']==0)
                            {
                                $quantity_release_per_kg = number_format(($quantity_release),3);
                            }
                            else
                            {
                                $quantity_release_per_kg = number_format((($data['pack_size_name']*$quantity_release)/1000),3);
                            }
                            $quantity_release_per_currency=number_format(($quantity_release*$data['price_currency']),2);
                            $release_total_kg+=$quantity_release_per_kg;
                            $release_total_currency+=($quantity_release*$data['price_currency']);
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-success"><?php echo $data['variety_name_import']?></strong>
                                    <input type="hidden" name="varieties[<?php echo $index+1;?>][variety_id]" value="<?php echo $data['variety_id']; ?>">
                                </td>
                                <td class="text-center">
                                    <?php if($data['pack_size_name']==0){echo "Bulk";}else{echo $data['pack_size_name'];}?>
                                    <input type="hidden" name="varieties[<?php echo $index+1;?>][quantity_type_id]" id="quantity_type_id_<?php echo $index+1;?>" value="<?php echo $data['quantity_type_id']; ?>" class="quantity_type" data-pack-size-name="<?php if($data['pack_size_name']==0){echo 0;}else{echo $data['pack_size_name'];}?>">
                                </td>
                                <td class="text-right">
                                    <?php echo $data['price_currency']?>
                                    <input type="hidden" value="<?php echo $data['price_currency']; ?>" class="form-control float_type_positive price" id="price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][price_currency]">
                                </td>
                                <td class="text-right"><?php echo $data['quantity_order']?></td>
                                <td class="text-right"><?php echo $item_per_kg?></td>
                                <td class="text-right"><?php echo $item_per_currency?></td>
                                <td>
                                    <input type="text" value="<?php echo $quantity_release; ?>" class="form-control float_type_positive release_quantity_total" id="quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][quantity_release]">
                                </td>
                                <td class="text-right" >
                                    <label class="control-label total_price" id="total_quantity_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $quantity_release_per_kg; ?>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <label class="control-label total_price" id="total_price_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $quantity_release_per_currency; ?>
                                    </label>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?> & <?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($total_kg,3);?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($total_currency,2);?></label></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php echo number_format($release_total_kg,3);?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_price_grand_total"><?php echo number_format($release_total_currency,2);?></label></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['other_cost_currency'],2)?></label></th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['other_cost_currency'],2)?></label></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                            <th class="text-right">
                                <label class="control-label">
                                    <?php
                                    $grand_total_currency=($total_currency+$item['other_cost_currency']);
                                    echo number_format($grand_total_currency,2);?>
                                </label>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right">
                                <label class="control-label" id="lbl_price_grand_total_currency">
                                    <?php
                                    $release_grand_total_currency=($release_total_currency+$item['other_cost_currency']);
                                    echo number_format($release_grand_total_currency,2);?>
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_TAKA')?></th>
                            <th>
                                <input type="text" name="item[price_total_taka]" id="price_total_taka" class="form-control float_type_positive" value="<?php echo number_format($item['price_total_taka'],2);?>"/>
                            </th>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    else
                    {
                        ?>
                        <tfoot>
                        <tr>
                            <td class="widget-header text-center" colspan="21"><strong>Data Not Found</strong></td>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    ?>
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
        $("#lbl_price_grand_total").html('0.00')
        $("#lbl_price_grand_total_currency").html('0.00')

        var quantity_kg_grand_total=0;
        var price_currency_grand_total=0;
        var other_cost_currency=parseFloat(<?php echo $item['other_cost_currency']?>);
        if(isNaN(other_cost_currency))
        {
            other_cost_currency=0;
        }
        var id='';
        //console.log($('.release_quantity_total').length)
        $('.release_quantity_total').each(function(index,element)
        {
            id = $(element).attr('data-current-id');
            if($('#quantity_type_id_'+id).val()!='-1')
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
        $("#lbl_price_grand_total").html(number_format(price_currency_grand_total,2))
        $("#lbl_price_grand_total_currency").html(number_format(price_other_cost_currency_grand_total,2))
    }
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off("input", "'.release_quantity_total'");
        //////// calculate total quantity.
        $(document).on("input",".release_quantity_total",function()
        {
            calculate_total();
        });
        //////// calculate total price currency.
    });

</script>
