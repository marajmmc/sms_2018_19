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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_LC_RELEASE');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_release]" id="remarks_release" class="form-control" ><?php echo $item['remarks_release'];?></textarea>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details  :: ( Release Status: <?php echo $item['status_release']?> )</th>
                    </tr>
                    <tr>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_UNIT');?></th>
                        <th class="label-primary text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER');?></th>
                        <th class="label-warning text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?></th>
                    </tr>
                    <tr>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>

                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL');?></th>
                    </tr>
                    </thead>
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $quantity_total_open=0;
                        $quantity_total_open_kg=0;
                        $quantity_total_release=0;
                        $quantity_total_release_kg=0;
                        foreach($items as $index=>$data)
                        {
                            if($item['revision_release_count']==0)
                            {
                                $quantity_release=$data['quantity_open'];
                                $price_release_currency=($data['quantity_open']*$data['price_unit_currency']);
                            }
                            else
                            {
                                $quantity_release=$data['quantity_release'];
                                $price_release_currency=($data['quantity_release']*$data['price_unit_currency']);
                            }
                            if($data['pack_size_id']==0)
                            {
                                $quantity_open_kg=$data['quantity_open'];
                                $quantity_release_kg=$quantity_release;
                            }
                            else
                            {
                                $quantity_open_kg=(($data['pack_size']*$data['quantity_open'])/1000);
                                $quantity_release_kg=(($data['pack_size']*$quantity_release)/1000);
                            }
                            $quantity_total_open+=$data['quantity_open'];
                            $quantity_total_open_kg+=$quantity_open_kg;
                            $quantity_total_release+=$quantity_release;
                            $quantity_total_release_kg+=$quantity_release_kg;
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $data['variety_id']; ?>">
                                </td>
                                <td class="text-center">
                                    <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $data['pack_size_id']; ?>" class="pack_size_id" data-pack-size-name="<?php if($data['pack_size']==0){echo 0;}else{echo $data['pack_size'];}?>">
                                </td>
                                <td class="text-center">
                                    <?php echo $data['price_unit_currency']?>
                                    <input type="hidden" value="<?php echo $data['price_unit_currency']; ?>" class="form-control float_type_positive price_unit_currency" id="price_unit_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][price_unit_currency]">
                                </td>
                                <td class="text-right"><label class="control-label" for=""><?php echo $data['quantity_open']?></label></td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($quantity_open_kg,3,'.','')?></label></td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format(($data['quantity_open']*$data['price_unit_currency']),2)?></label></td>
                                <td>
                                    <input type="text" value="<?php echo $quantity_release; ?>" class="form-control float_type_positive quantity_release" id="quantity_release_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_release]">
                                </td>
                                <td class="text-right" >
                                    <label class="control-label quantity_release_kg" id="quantity_release_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo number_format($quantity_release_kg,3,'.',''); ?>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <label class="control-label price_release_currency" id="price_release_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo number_format($price_release_currency,2); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_KG')?> & <?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL')?></th>
                            <th class="text-right"><label class="control-label"><?php echo $quantity_total_open;?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_open_kg,3,'.','');?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_variety_currency'],2);?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_release_total"><?php echo $quantity_total_release;?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_release_total_kg"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                            <th class="text-right">
                                <label class="control-label" id="lbl_price_variety_total_release_currency">
                                    <?php
                                    if($item['revision_release_count']==0)
                                    {
                                        echo number_format($item['price_open_variety_currency'],2);
                                    }
                                    else
                                    {
                                        echo number_format($item['price_release_variety_currency'],2);
                                    }
                                    ?>
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $CI->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2)?></label></th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right">
                                <?php
                                if($item['revision_release_count']==0)
                                {
                                    $price_release_other_currency= $item['price_open_other_currency'];
                                }
                                else
                                {
                                    $price_release_other_currency=$item['price_release_other_currency'];
                                }
                                ?>
                                <input type="text" class="form-control float_type_positive" name="item[price_release_other_currency]" id="price_release_other_currency" value="<?php echo $price_release_other_currency?>"/>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $CI->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                            <th class="text-right">
                                <label class="control-label"><?php echo number_format(($item['price_open_other_currency']+$item['price_open_variety_currency']),2);?></label>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right">
                                <label class="control-label" id="lbl_price_release_other_variety_currency">
                                    <?php
                                    if($item['revision_release_count']==0)
                                    {
                                        echo number_format(($item['price_open_other_currency']+$item['price_open_variety_currency']),2);
                                    }
                                    else
                                    {
                                        echo number_format(($item['price_release_other_currency']+$item['price_release_variety_currency']),2);
                                    }
                                    ?>
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA')?></th>
                            <th>
                                <input type="text" name="item[price_release_other_variety_taka]" id="price_release_other_variety_taka" class="form-control float_type_positive" value="<?php echo $item['price_release_other_variety_taka'];?>" />
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
                            <td class="widget-header text-center" colspan="21"><strong><?php echo $CI->lang->line('NO_DATA_FOUND');?></strong></td>
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
<script>
    $(document).ready(function()
    {
        system_off_events();
        $(document).off('input','.quantity_release');
        $(document).on('input', '.quantity_release', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_release_kg=0;
            var quantity_release=parseFloat($(this).val());
            if(isNaN(quantity_release))
            {
                quantity_release=0;
            }
            var price_unit_currency=parseFloat($("#price_unit_currency_"+current_id).val());
            if(isNaN(price_unit_currency))
            {
                var price_unit_currency=0;
            }
            var pack_size=parseFloat($("#pack_size_id_"+current_id).attr('data-pack-size-name'));
            if(pack_size==0)
            {
                quantity_release_kg=quantity_release;
            }
            else
            {
                quantity_release_kg=parseFloat((pack_size*quantity_release)/1000);
            }
            $("#quantity_release_kg_"+current_id).html(number_format(quantity_release_kg,3,'.',''));
            var price_release_currency=(quantity_release*price_unit_currency);
            $("#price_release_currency_"+current_id).html(number_format(price_release_currency,2));
            calculate_total()
        })

        $(document).off('input','#price_release_other_currency');
        $(document).on('input', '#price_release_other_currency', function()
        {
            var price_release_other_currency=parseFloat($('#price_release_other_currency').val());
            if(isNaN(price_release_other_currency))
            {
                price_release_other_currency=0;
            }
            var price_variety_total_release_currency=parseFloat($('#lbl_price_variety_total_release_currency').html().replace(/,/g,''));
            if(isNaN(price_variety_total_release_currency))
            {
                price_variety_total_release_currency=0;
            }
            var price_release_other_variety_currency=(price_release_other_currency+price_variety_total_release_currency);
            $('#lbl_price_release_other_variety_currency').html(number_format(price_release_other_variety_currency,2));
        })
        function calculate_total()
        {
            var quantity_release_total=0;
            var quantity_release_total_kg=0;
            var price_release_total_currency=0;
            $('.quantity_release').each(function(index, element)
            {
                var current_id=parseInt($(this).attr('data-current-id'));
                var quantity_release=parseFloat($(this).val());
                if(isNaN(quantity_release))
                {
                    quantity_release=0;
                }
                quantity_release_total+=quantity_release;
                var quantity_release_kg=parseFloat($('#quantity_release_kg_'+current_id).html().replace(/,/g,''));
                if(isNaN(quantity_release_kg))
                {
                    quantity_release_kg=0;
                }
                quantity_release_total_kg+=quantity_release_kg;
                var price_release_currency=parseFloat($('#price_release_currency_'+current_id).html().replace(/,/g,''));
                if(isNaN(price_release_currency))
                {
                    price_release_currency=0;
                }
                price_release_total_currency+=price_release_currency;
            });
            $('#lbl_quantity_release_total').html(quantity_release_total);
            $('#lbl_quantity_release_total_kg').html(number_format(quantity_release_total_kg,3,'.',''));
            $('#lbl_price_variety_total_release_currency').html(number_format(price_release_total_currency,2));
            var price_variety_total_release_currency=parseFloat($('#lbl_price_variety_total_release_currency').html().replace(/,/g,''));
            if(isNaN(price_variety_total_release_currency))
            {
                price_variety_total_release_currency=0;
            }
            var price_release_other_currency=parseFloat($('#price_release_other_currency').val());
            if(isNaN(price_release_other_currency))
            {
                price_release_other_currency=0;
            }
            $('#lbl_price_release_other_variety_currency').html(number_format((price_variety_total_release_currency+price_release_other_currency),2));
        }
    })
</script>
