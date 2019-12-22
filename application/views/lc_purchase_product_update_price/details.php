<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'onClick'=>"window.print()"
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/details/'.$item['id'])
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

$total_currency=$item['price_release_other_currency']+$item['price_release_variety_currency'];
$currency_bdt_rate_release=($item['price_release_other_variety_taka']/$total_currency);
$price_airfare_tk=($item['price_release_other_currency']*$currency_bdt_rate_release);
$quantity_total_kg_open=$item['quantity_open_kg'];
$price_total_tk_open=($item['price_open_other_currency']+$item['price_open_variety_currency']);
$currency_bdt_rate_complete=$item['rate_currency'];
$price_complete_total_taka=$item['price_complete_total_taka'];


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
    <div class="row widget">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="widget-header text-center" colspan="21">LC Release Price Details</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Air Freight & Docs (Currency)</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo number_format($item['price_release_other_currency'],2);?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right">LC Received Time</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_receive_completed']);?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Total Variety (Currency)</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo number_format($item['price_release_variety_currency'],2);?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right">Current Rate (BDT) [Release]</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo $currency_bdt_rate_release;//echo number_format($currency_bdt_rate,2)?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Total Currency</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo number_format($total_currency,2)?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRICE_RELEASE_OTHER_VARIETY_TAKA');?></label></td>
                <td class="warning header_value"><label class="control-label"><?php echo number_format($item['price_release_other_variety_taka'],2);?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption" colspan="3"><label class="control-label pull-right">Current Rate (BDT) [Complete]</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo $currency_bdt_rate_complete;//echo number_format($currency_bdt_rate,2)?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption" colspan="3"><label class="control-label pull-right">LC Complete Total (Taka)</label></td>
                <td class="warning header_value"><label class="control-label"><?php echo $price_complete_total_taka;//echo number_format($currency_bdt_rate,2)?></label></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
    <div style="overflow-x: auto;" class="row show-grid">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>crop</th>
                <th>type</th>
                <th>variety</th>
                <th>pack size</th>
                <th>Opening quantity</th>
                <th>Release quantity</th>
                <th>Receive quantity</th>
                <th>Unit price currency</th>
                <th>price variety taka</th>
                <th>other taka</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($items as $index=>$value)
            {
                if($value['pack_size_id']==0)
                {
                    $quantity_kg_open=$value['quantity_open'];
                    $quantity_kg_release=$value['quantity_release'];
                    $quantity_kg_receive=$value['quantity_receive'];

                    $price_variety_taka = ($value['quantity_release'] * $value['price_unit_currency']) * $currency_bdt_rate_release;
                }
                else
                {
                    $quantity_kg_open=(($value['quantity_open']*$value['pack_size'])/1000);
                    $quantity_kg_release=(($value['quantity_release']*$value['pack_size'])/1000);
                    $quantity_kg_receive=(($value['quantity_receive']*$value['pack_size'])/1000);

                    $price_variety_taka = ($value['quantity_release'] * $value['price_unit_currency']) * $currency_bdt_rate_release;
                }

                $price_other_taka = ($item['price_release_other_currency']/$item['price_release_variety_currency'])*$price_variety_taka; //(air fright)
                ?>
                <tr>
                    <td><label><?php echo $value['crop_name'];?></label></td>
                    <td><label><?php echo $value['crop_type_name'];?></label></td>
                    <td><label><?php echo $value['variety_name']; ?> (<?php echo $value['variety_name_import']; ?>)</label></td>
                    <td class="text-center"> <label><?php if($value['pack_size_id']==0){echo 'Bulk';}else{echo $value['pack_size'];} ?></label></td>
                    <td class="text-right"><?php echo System_helper::get_string_kg($quantity_kg_open);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_kg($quantity_kg_release);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_kg($quantity_kg_receive);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($value['price_unit_currency']);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($price_variety_taka);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($price_other_taka);?></td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
    <div style="overflow-x: auto;" class="row show-grid">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="widget-header text-center" colspan="29">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details</th>
            </tr>
            <tr>
                <th class="label-info" rowspan="4"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                <th class="label-info" rowspan="4"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                <th class="label-info" rowspan="4"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                <th class="label-info" rowspan="4"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                <th class="label-info text-center" colspan="22">Price & Stock</th>
            </tr>
            <tr>
                <th class="label-success text-center" colspan="9">LC Receive Info</th>
                <th class="label-warning text-center" colspan="9">LC Complete Info</th>
            </tr>
            <tr>
                <th class="label-success text-center" colspan="3" >Opening</th>
                <th class="label-success text-center" colspan="3">Receive</th>
                <th class="label-success text-center" colspan="3">Total</th>

                <th class="label-warning text-center" colspan="3">Opening</th>
                <th class="label-warning text-center" colspan="3">Complete</th>
                <th class="label-warning text-center" colspan="3">Total</th>
            </tr>
            <tr>
                <td class="label-success text-center">Opening Stock</td>
                <td class="label-success text-center">Previous Rate Ave.</td>
                <td class="label-success text-center">Total Amount</td>
                <td class="label-success text-center">Receive Qty</td>
                <td class="label-success text-center">Rate</td>
                <td class="label-success text-center">Total Amount</td>
                <td class="label-success text-center">Total Qty</td>
                <td class="label-success text-center">Ave. Rate</td>
                <td class="label-success text-center">Total Amount</td>

                <td class="label-warning text-center">Opening Stock</td>
                <td class="label-warning text-center">Previous Rate Ave.</td>
                <td class="label-warning text-center">Total Amount</td>
                <td class="label-warning text-center">Complete Qty</td>
                <td class="label-warning text-center">Rate</td>
                <td class="label-warning text-center">Total Amount</td>
                <td class="label-warning text-center">Total Qty</td>
                <td class="label-warning text-center">Ave. Rate</td>
                <td class="label-warning text-center">Total Amount</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($items as $index=>$value)
            {
                $rate_receive=isset($receive_rates[$value['variety_id']][$value['pack_size_id']])?$receive_rates[$value['variety_id']][$value['pack_size_id']]:0;
                if($value['pack_size_id']==0)
                {
                    $quantity_kg_receive=$value['quantity_receive'];

                    $total_amount_receive=($value['quantity_receive']*$rate_receive);
                    // rate complete
                    $rate_complete=($value['price_total_taka']/$value['quantity_receive']);
                    $total_amount_complete=($value['quantity_receive']*$rate_complete);
                }
                else
                {
                    $quantity_kg_receive=(($value['quantity_receive']*$value['pack_size'])/1000);

                    $total_amount_receive=($value['quantity_receive']*$rate_receive);
                    // rate complete
                    $rate_complete=($value['price_total_taka']/$value['quantity_receive']);
                    $total_amount_complete=($value['quantity_receive']*$rate_complete);
                }




                $stock_total_opening=0;
                if(isset($stock_opening[$value['variety_id']][$value['pack_size_id']]))
                {
                    $stock=$stock_opening[$value['variety_id']][$value['pack_size_id']];
                    $stock_total_opening=$stock['stock_hq_kg']+$stock['stock_outlet_kg']+$stock['stock_to_kg']+$stock['stock_tr_kg']+$stock['stock_ts_kg'];
                }
                $rate_previous_receive=0;
                $rate_previous_complete=0;
                if(isset($previous_rates[$value['variety_id']][$value['pack_size_id']]))
                {
                    $rate_previous=$previous_rates[$value['variety_id']][$value['pack_size_id']];
                    $rate_previous_receive=$rate_previous['rate_weighted_receive'];
                    $rate_previous_complete=$rate_previous['rate_weighted_complete'];
                }
                $total_amount_opening_receive=($stock_total_opening*$rate_previous_receive);
                $total_amount_opening_complete=($stock_total_opening*$rate_previous_complete);

                $quantity_kg_total_receive_total=($stock_total_opening+$quantity_kg_receive);
                //$rate_total_receive_total=($rate_previous_receive+$rate_receive);
                $rate_total_receive_total=($rate_previous_receive+$rate_receive);
                $amount_total_receive_total=($quantity_kg_total_receive_total*$rate_total_receive_total);
                //$rate_complete=($value['price_total_taka']/$quantity_kg_receive);
                //$total_amount_complete=($quantity_kg_receive*$rate_complete);
                ?>
                <tr>
                    <td><label><?php echo $value['crop_name'];?></label></td>
                    <td><label><?php echo $value['crop_type_name'];?></label></td>
                    <td><label><?php echo $value['variety_name']; ?> (<?php echo $value['variety_name_import']; ?>)</label></td>
                    <td class="text-center"> <label><?php if($value['pack_size_id']==0){echo 'Bulk';}else{echo $value['pack_size'];} ?></label></td>

                    <td class="text-right"><?php echo System_helper::get_string_kg($stock_total_opening);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($rate_previous_receive);?></td>
                    <td class="text-right <?php if(!$total_amount_opening_receive){echo 'bg-danger';}?>"><?php echo System_helper::get_string_amount($total_amount_opening_receive);?></td>

                    <td class="text-right"><?php echo System_helper::get_string_kg($quantity_kg_receive);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($rate_receive);?></td>
                    <td class="text-right <?php if(!$total_amount_receive){echo 'bg-danger';}?>"><?php echo System_helper::get_string_amount($total_amount_receive);?></td>

                    <td class="text-right"><?php echo System_helper::get_string_kg($quantity_kg_total_receive_total);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($rate_total_receive_total);?></td>
                    <td class="text-right <?php if(!$amount_total_receive_total){echo 'bg-danger';}?>"><?php echo System_helper::get_string_amount($amount_total_receive_total);?></td>

                    <td class="text-right"><?php echo System_helper::get_string_kg($stock_total_opening);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($rate_previous_complete);?></td>
                    <td class="text-right <?php if(!$total_amount_opening_complete){echo 'bg-danger';}?>"><?php echo System_helper::get_string_amount($total_amount_opening_complete);?></td>

                    <td class="text-right"><?php echo System_helper::get_string_kg($quantity_kg_receive);?></td>
                    <td class="text-right"><?php echo System_helper::get_string_amount($rate_complete);?></td>
                    <td class="text-right <?php if(!$total_amount_complete){echo 'bg-danger';}?>"><?php echo System_helper::get_string_amount($total_amount_complete);?></td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>