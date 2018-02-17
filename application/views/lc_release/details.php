<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Forward By</label></th>
                <th class="header_value"><label class="control-label"><?php echo $item['user_full_name']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right">Forwarded Time</label></th>
                <th class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_open_forward']);?></label></th>
            </tr>
            </thead>
        </table>
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['fiscal_year']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>
                <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['currency_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_ACCOUNT_NUMBER');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['bank_account_number'];?></label></th>
                <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_OPEN_OTHER_CURRENCY');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo number_format($item['price_open_other_currency'],2);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_open']);?></label></th>
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
                    <th class="label-primary text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER');?></th>
                    <th class="label-warning text-center" colspan="3"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?></th>
                </tr>
                <tr>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY');?></th>

                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_TOTAL_CURRENCY');?></th>
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
                        $quantity_total_release+=$data['quantity_release'];
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
                        <th colspan="3" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?> & <?php echo $this->lang->line('LABEL_TOTAL_CURRENCY')?></th>
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