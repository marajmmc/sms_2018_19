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
                    <th class="widget-header"><label class="control-label pull-right">Forward By</label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['user_full_name']?></label></th>
                    <th class="widget-header"><label class="control-label pull-right">Forwarded Time</label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo System_helper::display_date_time($item['date_open_forward']);?></label></th>
                </tr>
                </thead>
            </table>
            <table class="table table-bordered table-responsive ">
                <thead>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['fiscal_year']?></label></th>
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
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks_open'];?></label></th>
                </tr>
                </thead>
            </table>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RELEASE');?> </label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks_release]" id="remarks_release" class="form-control" ><?php echo $item['remarks_release'];?></textarea>
                </div>
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
                        <th class="label-info text-center" rowspan="2">Unit Price (Currency)</th>
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
                        $quantity_open_total_kg=0;
                        $quantity_release_total_kg=0;
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
                                $quantity_open_kg=(($data['pack_size_name']*$data['quantity_open'])/1000);
                                $quantity_release_kg=(($data['pack_size_name']*$quantity_release)/1000);
                            }
                            $quantity_open_total_kg+=$quantity_open_kg;
                            $quantity_release_total_kg+=$quantity_release_kg;
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $data['variety_id']; ?>">
                                </td>
                                <td class="text-center">
                                    <?php if($data['pack_size_name']==0){echo "Bulk";}else{echo $data['pack_size_name'];}?>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $data['pack_size_id']; ?>" class="pack_size_id" data-pack-size-name="<?php if($data['pack_size_name']==0){echo 0;}else{echo $data['pack_size_name'];}?>">
                                </td>
                                <td class="text-center">
                                    <?php echo $data['price_unit_currency']?>
                                    <input type="hidden" value="<?php echo $data['price_unit_currency']; ?>" class="form-control float_type_positive price_unit_currency" id="price_unit_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][price_unit_currency]">
                                </td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($data['quantity_open'],3,'.','')?></label></td>
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
                            <th colspan="4" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?> & <?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_open_total_kg,3,'.','');?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($item['price_open_variety_currency'],2);?></label></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_release_total_kg"><?php echo number_format($quantity_release_total_kg,3,'.','');?></label></th>
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
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
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
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
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
                            <th colspan="8" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_TAKA')?></th>
                            <th>
                                <input type="text" name="item[price_release_other_variety_taka]" id="price_release_other_variety_taka" class="form-control float_type_positive" value="<?php echo $item['price_release_other_variety_taka'];?>"/>
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
<script>
    function calculate_total()
    {
        var quantity_release_total_kg=0;
        var price_release_currency=0;
        $('.quantity_release_kg').each(function(index, element)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            quantity_release_total_kg+=parseFloat($('#quantity_release_kg_'+current_id).html().replace(/,/g,''));
            price_release_currency+=parseFloat($('#price_release_currency_'+current_id).html().replace(/,/g,''));
        });
        $('#lbl_quantity_release_total_kg').html(number_format(quantity_release_total_kg,3,'.',''));
        $('#lbl_price_variety_total_release_currency').html(number_format(price_release_currency,2));
        if(isNaN($('#price_release_other_currency').val()))
        {
            var price_release_other_variety_currency=price_release_currency;
        }
        else
        {
            var price_release_other_variety_currency=(parseFloat($('#price_release_other_currency').val())+price_release_currency);
        }

        $('#lbl_price_release_other_variety_currency').html(number_format(price_release_other_variety_currency,2));
    }
    $(document).ready(function()
    {
        $(document).off('input','.quantity_release');
        $(document).on('input', '.quantity_release', function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_release_kg=0;
            var quantity_release=parseFloat($(this).val());
            var price_unit_currency=parseFloat($("#price_unit_currency_"+current_id).val());
            if(isNaN(quantity_release))
            {
                quantity_release=0;
            }
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
            calculate_total();
        })
    })
</script>
