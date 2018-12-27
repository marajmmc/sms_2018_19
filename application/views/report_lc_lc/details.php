<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$status_completed=false;
$status_open_forward=false;
$status_release=false;
$status_receive=false;

$permission_delete=false;
if((isset($CI->permissions['action3']) && ($CI->permissions['action3']==1)))
{
    $permission_delete=true;
}

$number_footer_colspan=6;
$number_th_td_colspan=3;
if(!$permission_delete)
{
    $number_footer_colspan=5;
    $number_th_td_colspan=2;
}
if($item['status_open']==$this->config->item('system_status_complete'))
{
    $status_completed=true;
}
if($item['status_open_forward']==$this->config->item('system_status_yes'))
{
    $status_open_forward=true;
}
if($item['status_release']==$this->config->item('system_status_complete'))
{
    $status_release=true;
}
if($item['status_receive']==$this->config->item('system_status_complete'))
{
    $status_receive=true;
    $number_footer_colspan+=3;
}

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    echo $CI->load->view("info_basic",'',true);
    echo $CI->load->view("info_basic",array('accordion'=>array('header'=>'+LC Info','div_id'=>'accordion_lc_info','data'=>$info_lc)),true);
    ?>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="widget-header text-center" colspan="30">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details</th>
                </tr>
                <tr>
                    <th class="label-info text-right" rowspan="2" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <?php
                    if($permission_delete)
                    {
                        ?>
                        <th class="label-info text-right" rowspan="2"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_UNIT'); ?></th>
                    <?php
                    }
                    ?>

                    <?php
                    if($status_receive)
                    {
                        ?>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_NUMBER')?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_SIZE')?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                    <?php
                    }
                    ?>
                    <th class="label-primary text-center" colspan="<?php echo $number_th_td_colspan;?>"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER');?></th>
                    <?php
                    if($status_release)
                    {
                        ?>
                        <th class="label-warning text-center" colspan="<?php echo $number_th_td_colspan;?>"><?php echo $CI->lang->line('LABEL_QUANTITY_SUPPLY');?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($status_receive)
                    {
                        ?>
                        <th class="label-success text-center" colspan="2"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE');?></th>
                        <th class="label-danger text-center" colspan="2"><?php echo $CI->lang->line('LABEL_QUANTITY_DIFFERENCE');?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($permission_delete)
                    {
                        if($status_completed)
                        {
                            ?>
                            <th class=" text-center" colspan="5"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA');?></th>
                            <th class="label-default text-center" colspan="2">Variety Rate</th>
                        <?php
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                    <?php
                    if($permission_delete)
                    {
                        ?>
                        <th class="label-primary text-right"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL'); ?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($status_release)
                    {
                        ?>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('KG');?></th>
                        <?php
                        if($permission_delete)
                        {
                            ?>
                            <th class="label-warning text-right"><?php echo $CI->lang->line('LABEL_PRICE_CURRENCY_TOTAL'); ?></th>
                        <?php
                        }
                        ?>
                    <?php
                    }
                    ?>
                    <?php
                    if($status_receive)
                    {
                        ?>
                        <th class="label-success text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-success text-center"><?php echo $CI->lang->line('KG');?></th>

                        <th class="label-danger text-center"><?php echo $CI->lang->line('LABEL_PACK'); ?>/<?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th class="label-danger text-center"><?php echo $CI->lang->line('KG');?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($permission_delete)
                    {
                        if($status_completed)
                        {
                            ?>
                            <th class=" text-center">Unit Price (Taka)</th>
                            <th class=" text-center">Variety Price (Taka)</th>
                            <th class=" text-center">Other Cost (Taka)</th>
                            <th class=" text-center">Expense (Taka)</th>
                            <th class=" text-center"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA');?></th>
                            <th class="label-default text-center">Pkt (Taka)</th>
                            <th class="label-default text-center">Kg (Taka)</th>
                        <?php
                        }
                    }
                    ?>
                </tr>
                </thead>
                <?php
                if(!empty($items))
                {
                    ?>
                    <tbody>
                    <?php
                    $serial=0;
                    $quantity_total_open=0;
                    $quantity_total_open_kg=0;
                    $quantity_total_release=0;
                    $quantity_total_release_kg=0;
                    $quantity_total_receive=0;
                    $quantity_total_receive_kg=0;

                    $price_unit_currency=0;

                    $price_open_currency=0;
                    $price_total_open_currency=0;
                    $price_release_currency=0;
                    $price_total_release_currency=0;
                    $price_receive_currency=0;
                    $price_total_receive_currency=0;

                    $price_total_unit_complete_taka=0;
                    $price_total_complete_variety_taka=0;
                    $price_total_complete_other_taka=0;
                    $price_total_dc_expense_taka=0;
                    $price_total_total_taka=0;

                    $rate_variety_pkt=0;
                    $rate_variety_kg=0;
                    foreach($items as $data)
                    {
                        ++$serial;
                        if($data['pack_size_id']==0)
                        {
                            $quantity_open_kg=$data['quantity_open'];
                            $quantity_release_kg=$data['quantity_release'];
                            $quantity_receive_kg=$data['quantity_receive'];
                        }
                        else
                        {
                            $quantity_open_kg=(($data['pack_size']*$data['quantity_open'])/1000);
                            $quantity_release_kg=(($data['pack_size']*$data['quantity_release'])/1000);
                            $quantity_receive_kg=(($data['pack_size']*$data['quantity_receive'])/1000);
                        }
                        $price_unit_currency=$data['price_unit_currency'];

                        $quantity_total_open+=$data['quantity_open'];
                        $quantity_total_open_kg+=$quantity_open_kg;
                        $quantity_total_release+=$data['quantity_release'];
                        $quantity_total_release_kg+=$quantity_release_kg;
                        $quantity_total_receive+=$data['quantity_receive'];;
                        $quantity_total_receive_kg+=$quantity_receive_kg;

                        $price_open_currency=($data['quantity_open']*$price_unit_currency);
                        $price_total_open_currency+=$price_open_currency;
                        $price_release_currency=($data['quantity_release']*$price_unit_currency);
                        $price_total_release_currency+=$price_release_currency;
                        $price_receive_currency=($data['quantity_receive']*$price_unit_currency);
                        $price_total_receive_currency+=$price_receive_currency;

                        $price_total_unit_complete_taka+=$data['price_unit_complete_taka'];
                        $price_total_complete_variety_taka+=$data['price_complete_variety_taka'];
                        $price_total_complete_other_taka+=$data['price_complete_other_taka'];
                        $price_total_dc_expense_taka+=$data['price_dc_expense_taka'];
                        $price_total_total_taka+=$data['price_total_taka'];

                        ?>
                        <tr>
                            <td class="text-right"><?php echo $serial;?></td>
                            <td><?php echo $data['crop_name'];?></td>
                            <td><?php echo $data['crop_type_name'];?></td>
                            <td><?php echo $data['variety_name'];?> ( <?php echo $data['variety_name_import'];?> )</td>
                            <td class="text-center"> <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?></td>
                            <?php
                            if($permission_delete)
                            {
                                ?>
                                <td class="text-right"><?php echo $price_unit_currency;?></td>
                            <?php
                            }
                            ?>
                            <?php
                            if($status_receive)
                            {
                                ?>
                                <td class="text-right"><?php echo $data['carton_number_receive']; ?></td>
                                <td class="text-right"><?php echo $data['carton_size_receive']; ?></td>
                                <td><?php echo $data['warehouse_name']?> </td>
                            <?php
                            }
                            ?>
                            <td class="text-right"><label class="control-label" for=""><?php echo $data['quantity_open'];?></label></td>
                            <td class="text-right"><label class="control-label" for=""><?php echo number_format($quantity_open_kg,3,'.','')?></label></td>
                            <?php
                            if($permission_delete)
                            {
                                ?>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($price_open_currency,2)?></label></td>
                            <?php
                            }
                            ?>
                            <?php
                            if($status_release)
                            {
                                ?>
                                <td class="text-right"><label class="control-label" for=""><?php echo $data['quantity_release'];?></label></td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($quantity_release_kg,3,'.','')?></label></td>
                                <?php
                                if($permission_delete)
                                {
                                    ?>
                                    <td class="text-right"><label class="control-label" for=""><?php echo number_format($price_release_currency,2)?></label></td>
                                <?php
                                }
                                ?>
                            <?php
                            }
                            ?>
                            <?php
                            if($status_receive)
                            {
                                ?>
                                <td class="text-right"><label class="control-label" for=""><?php echo $data['quantity_receive']; ?></label></td>
                                <td class="text-right" ><label class="control-label "><?php echo number_format($quantity_receive_kg,3,'.',''); ?></label></td>

                                <td class="text-right"><label class="control-label"><?php echo ($data['quantity_receive']-$data['quantity_release'])?></label></td>
                                <td class="text-right"><label class="control-label"><?php echo number_format(($quantity_receive_kg-$quantity_release_kg),3,'.','')?></label></td>
                            <?php
                            }
                            ?>
                            <?php
                            if($permission_delete)
                            {
                                if($status_completed)
                                {
                                    $price_variety_rate_pkt=($data['price_total_taka']/$data['quantity_receive']);
                                    $price_variety_rate_kg=($data['price_total_taka']/$quantity_receive_kg);
                                    ?>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($data['price_unit_complete_taka'],2); ?></label></td>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($data['price_complete_variety_taka'],2); ?></label></td>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($data['price_complete_other_taka'],2); ?></label></td>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($data['price_dc_expense_taka'],2); ?></label></td>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($data['price_total_taka'],2); ?></label></td>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($price_variety_rate_pkt,2); ?></label></td>
                                    <td class="text-right"><label class="control-label"><?php echo number_format($price_variety_rate_kg,2); ?></label></td>
                                <?php
                                }
                            }
                            ?>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="<?php echo $number_footer_colspan;?>" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_KG')?></th>
                        <th class="text-right"><label class="control-label"><?php echo $quantity_total_open;?></label></th>
                        <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_open_kg,3,'.','');?></label></th>
                        <?php
                        if($permission_delete)
                        {
                            ?>
                            <th class="text-right"><label class="control-label"><?php echo number_format($price_total_open_currency,2);?></label></th>
                        <?php
                        }
                        ?>
                        <?php
                        if($status_release)
                        {
                            ?>
                            <th class="text-right"><label class="control-label"><?php echo $quantity_total_release;?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                            <?php
                            if($permission_delete)
                            {
                                ?>
                                <th class="text-right"><label class="control-label"><?php echo number_format($price_total_release_currency,2);?></label></th>
                            <?php
                            }
                            ?>
                        <?php
                        }
                        ?>
                        <?php
                        if($status_receive)
                        {
                            ?>
                            <th class="text-right"><label class="control-label"><?php echo $quantity_total_receive;?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>

                            <th class="text-right"><label class="control-label"><?php echo ($quantity_total_receive-$quantity_total_release);?></label></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format(($quantity_total_receive_kg-$quantity_total_release_kg),3,'.','');?></label></th>
                        <?php
                        }
                        ?>
                        <?php
                        if($permission_delete)
                        {
                            if($status_completed)
                            {
                                ?>
                                <th class="text-right"><label class="control-label"><?php echo number_format($price_total_unit_complete_taka,2);?></label></th>
                                <th class="text-right"><label class="control-label"><?php echo number_format($price_total_complete_variety_taka,2);?></label></th>
                                <th class="text-right"><label class="control-label"><?php echo number_format($price_total_complete_other_taka,2);?></label></th>
                                <th class="text-right"><label class="control-label"><?php echo number_format($price_total_dc_expense_taka,2);?></label></th>
                                <th class="text-right"><label class="control-label"><?php echo number_format($price_total_total_taka,2);?></label></th>
                                <th colspan="2"> &nbsp;</th>
                            <?php
                            }
                        }
                        ?>
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
        <div class="clearfix"></div>
        <?php
        if($status_completed && $permission_delete)
        {
            ?>
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th colspan="21" class="text-center">Expense Details Information</th>
                    </tr>
                    <tr>
                        <th style="width: 5px;"><?php echo $CI->lang->line('LABEL_SL_NO');?></th>
                        <th>Expense Head</th>
                        <th class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA');?></th>
                        <?php
                        foreach($items as $data)
                        {
                            ?>
                            <th class="text-right"><?php echo $data['variety_name']?> ( <?php echo $data['pack_size']?$data['pack_size']:"Bulk"?> )</th>
                        <?php
                        }
                        ?>
                        <th class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $serial=0;
                    $amount_total_dc_amount=0;
                    $amount_total_variety=array();
                    foreach($dc_items as $dc_item)
                    {
                        ++$serial;
                        $amount_total_dc_amount+=$dc_item['amount'];
                        ?>
                        <tr>
                            <td><?php echo $serial;?></td>
                            <td><?php echo $dc_item['dc_name'];?></td>
                            <td class="text-right"><?php echo number_format($dc_item['amount'],2);?></td>
                            <?php
                            $amount_expense_variety=0;
                            $amount_total_expense_variety=0;
                            foreach($items as $data)
                            {
                                if(isset($dc_expense_varieties[$data['variety_id']][$data['pack_size_id']][$dc_item['dc_id']]))
                                {
                                    $amount_expense_variety = $dc_expense_varieties[$data['variety_id']][$data['pack_size_id']][$dc_item['dc_id']]['amount'];
                                }
                                else
                                {
                                    $amount_expense_variety = '0.00';
                                }
                                $amount_total_expense_variety+=$amount_expense_variety;
                                if(isset($amount_total_variety[$data['variety_id']][$data['pack_size_id']]))
                                {
                                    $amount_total_variety[$data['variety_id']][$data['pack_size_id']]+=$amount_expense_variety;
                                }
                                else
                                {
                                    $amount_total_variety[$data['variety_id']][$data['pack_size_id']]=$amount_expense_variety;
                                }
                                ?>
                                <td class="text-right">
                                    <?php
                                    echo number_format($amount_expense_variety,2);
                                    ?>
                                </td>
                            <?php
                            }
                            ?>
                            <td class="text-right">
                                <?php
                                echo number_format($amount_total_expense_variety,2);
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_TAKA');?></th>
                        <th class="text-right"><?php echo number_format($amount_total_dc_amount,2);?></th>
                        <?php
                        $amount_expense_variety=0;
                        $amount_total_expense_variety=0;
                        foreach($items as $data)
                        {
                            if(isset($amount_total_variety[$data['variety_id']][$data['pack_size_id']]))
                            {

                                $amount_expense_variety = $amount_total_variety[$data['variety_id']][$data['pack_size_id']];
                            }
                            else
                            {
                                $amount_expense_variety = 0;
                            }
                            $amount_total_expense_variety+=$amount_expense_variety;
                            ?>
                            <th class="text-right">
                                <?php
                                echo number_format($amount_expense_variety,2);
                                ?>
                            </th>
                        <?php
                        }
                        ?>
                        <th class="text-right"><?php echo number_format($amount_total_expense_variety,2);?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        <?php
        }
        ?>
    </div>
</div>