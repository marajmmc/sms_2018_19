<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );

    $action_buttons[]=array
    (
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
                    <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_other_cost_total_currency'],2);?></label></th>
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
                        <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc_open($item['id']);?>) Product & Price Details </th>
                    </tr>
                    <tr>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
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

                        foreach($items as $index=>$data)
                        {
                            if(!$item['user_release_created'])
                            {
                                $quantity_release=$data['quantity_lc'];
                                $price_total_release_currency=number_format($data['price_total_lc_currency'],2);
                            }
                            else
                            {
                                $quantity_release=$data['quantity_release'];
                                $price_total_release_currency=number_format($data['price_total_release_currency'],3);
                            }
                            if($data['pack_size_id']==0)
                            {
                                $quantity_lc_kg=number_format($data['quantity_lc'],3);
                                $quantity_release_kg=number_format($quantity_release,3);
                            }
                            else
                            {
                                $quantity_lc_kg=number_format(($data['pack_size_name']*$data['quantity_lc'])/1000,3);
                                $quantity_release_kg=number_format(($data['pack_size_name']*$quantity_release)/1000,3);
                            }
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
                                    <?php echo $data['price_unit_lc_currency']?>
                                    <input type="hidden" value="<?php echo $data['price_unit_lc_currency']; ?>" class="form-control float_type_positive price_unit_lc_currency" id="price_unit_lc_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][price_unit_lc_currency]">
                                </td>
                                <td class="text-right"><?php echo number_format($data['quantity_lc'],3)?></td>
                                <td class="text-right"><?php echo $quantity_lc_kg?></td>
                                <td class="text-right"><?php echo number_format($data['price_total_lc_currency'],2)?></td>
                                <td>
                                    <input type="text" value="<?php echo $quantity_release; ?>" class="form-control float_type_positive quantity_release" id="quantity_release_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_release]">
                                </td>
                                <td class="text-right" >
                                    <label class="control-label quantity_release_kg" id="quantity_release_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $quantity_release_kg; ?>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <label class="control-label price_total_release_currency" id="price_total_release_currency_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                        <?php echo $price_total_release_currency; ?>
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
                            <th class="text-right"><label class="control-label"><?php //echo number_format($total_kg,3);?></label></th>
                            <th class="text-right"><label class="control-label"><?php //echo number_format($total_currency,2);?></label></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_kg_grand_total"><?php //echo number_format($release_total_kg,3);?></label></th>
                            <th class="text-right"><label class="control-label" id="lbl_price_grand_total"><?php //echo number_format($release_total_currency,2);?></label></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY')?></th>
                            <th class="text-right"><label class="control-label"><?php //echo number_format($item['other_cost_currency'],2)?></label></th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right"><label class="control-label"><?php //echo number_format($item['other_cost_currency'],2)?></label></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><?php echo $this->lang->line('LABEL_GRAND_TOTAL_CURRENCY')?></th>
                            <th class="text-right">
                                <label class="control-label">
                                    <?php
                                    //$grand_total_currency=($total_currency+$item['other_cost_currency']);
                                    //echo number_format($grand_total_currency,2);?>
                                </label>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right">
                                <label class="control-label" id="lbl_price_grand_total_currency">
                                    <?php
                                    //$release_grand_total_currency=($release_total_currency+$item['other_cost_currency']);
                                    //echo number_format($release_grand_total_currency,2);?>
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

