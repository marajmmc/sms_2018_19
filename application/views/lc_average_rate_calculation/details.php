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
//echo '<pre>';
//print_r($item);
//echo '</pre>';
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#receive_info" href="#">Receive Calculation</a></label>
            </h4>
        </div>
        <div id="receive_info" class="panel-collapse collapse in">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-success text-center" colspan="4">Total</th>
                        <th class="label-success text-center" colspan="8">Receive</th>
                        <th class="label-success text-center" colspan="3" >Opening</th>
                    </tr>
                    <tr>
                        <th class="label-success text-center">Quantity(kg)</th>
                        <th class="label-success text-center">Amount</th>
                        <th class="label-success text-center">Rate/kg(calculated)</th>
                        <th class="label-success text-center">Rate/kg(saved)</th>

                        <th class="label-success text-center">Quantity(Kg) (received)</th>

                        <th class="label-success text-center">Quantity(released)</th>
                        <th class="label-success text-center">Unit price(currency)</th>
                        <th class="label-success text-center">price(currency)</th>
                        <th class="label-success text-center">price(taka)</th>

                        <th class="label-success text-center">air(currency)</th>
                        <th class="label-success text-center">air(taka)</th>
                        <th class="label-success text-center">Total(taka)</th>

                        <th class="label-success text-center">Stock(kg)</th>
                        <th class="label-success text-center">rate/kg</th>
                        <th class="label-success text-center">Total amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_receive_price_variety_currency=0;
                    $total_receive_price_variety_taka=0;
                    $total_receive_price_other_currency=0;
                    $total_receive_price_other_taka=0;
                    $total_receive_price_total_taka=0;
                    foreach($items as $variety_ifo)
                    {
                        $total_receive_price_variety_currency+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_variety_currency'];
                        $total_receive_price_variety_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_variety_taka'];
                        $total_receive_price_other_currency+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_other_currency'];
                        $total_receive_price_other_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_other_taka'];
                        $total_receive_price_total_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_total_taka'];

                        ?>
                        <tr>
                            <td><label><?php echo $variety_ifo['crop_name'];?></label></td>
                            <td><label><?php echo $variety_ifo['crop_type_name'];?></label></td>
                            <td><label><?php echo $variety_ifo['variety_name']; ?> (<?php echo $variety_ifo['variety_name_import']; ?>)</label></td>
                            <td class="text-center"> <label><?php if($variety_ifo['pack_size_id']==0){echo 'Bulk';}else{echo $variety_ifo['pack_size'];} ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_total_quantity_kg']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_total_total_amount']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_total_rate_weighted_receive_calculated'];?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_total_rate_weighted_receive']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['quantity_receive_kg']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['quantity_release']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_rate_variety_currency']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_variety_currency']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_variety_taka']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_other_currency']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_other_taka']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_price_total_taka']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_opening_stock_kg']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_opening_rate_weighted_receive']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['receive_opening_total_amount']; ?></label></td>

                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"><?php echo $total_receive_price_variety_currency; ?></td>
                        <td class="text-center"><?php echo $total_receive_price_variety_taka; ?></td>
                        <td class="text-center"><?php echo $total_receive_price_other_currency; ?></td>
                        <td class="text-center"><?php echo $total_receive_price_other_taka; ?></td>
                        <td class="text-center"><?php echo $total_receive_price_total_taka; ?></td>

                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#complete_info" href="#">Complete Calculation</a></label>
            </h4>
        </div>
        <div id="complete_info" class="panel-collapse collapse in">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-success text-center" colspan="4">Total</th>
                        <th class="label-success text-center" colspan="8">Complete</th>
                        <th class="label-success text-center" colspan="3" >Opening</th>
                    </tr>
                    <tr>
                        <th class="label-success text-center">Quantity(kg)</th>
                        <th class="label-success text-center">Amount</th>
                        <th class="label-success text-center">Rate/kg(calculated)</th>
                        <th class="label-success text-center">Rate/kg(saved)</th>

                        <th class="label-success text-center">Quantity(Kg) (received)</th>

                        <th class="label-success text-center">Unit price(currency)</th>
                        <th class="label-success text-center">price(currency)</th>
                        <th class="label-success text-center">price(taka)</th>

                        <th class="label-success text-center">air(currency)</th>
                        <th class="label-success text-center">air(taka)</th>
                        <th class="label-success text-center">dc expense(taka)</th>
                        <th class="label-success text-center">Total(taka)</th>

                        <th class="label-success text-center">Stock(kg)</th>
                        <th class="label-success text-center">rate/kg</th>
                        <th class="label-success text-center">Total amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_complete_price_variety_currency=0;
                    $total_complete_price_variety_taka=0;
                    $total_complete_price_other_currency=0;
                    $total_complete_price_other_taka=0;
                    $total_complete_price_dc_expense_taka=0;
                    $total_complete_price_total_taka=0;
                    foreach($items as $variety_ifo)
                    {
                        $total_complete_price_variety_currency+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_variety_currency'];
                        $total_complete_price_variety_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_variety_taka'];
                        $total_complete_price_other_currency+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_other_currency'];
                        $total_complete_price_other_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_other_taka'];
                        $total_complete_price_dc_expense_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_dc_expense_taka'];
                        $total_complete_price_total_taka+=$rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_total_taka'];

                        ?>
                        <tr>
                            <td><label><?php echo $variety_ifo['crop_name'];?></label></td>
                            <td><label><?php echo $variety_ifo['crop_type_name'];?></label></td>
                            <td><label><?php echo $variety_ifo['variety_name']; ?> (<?php echo $variety_ifo['variety_name_import']; ?>)</label></td>
                            <td class="text-center"> <label><?php if($variety_ifo['pack_size_id']==0){echo 'Bulk';}else{echo $variety_ifo['pack_size'];} ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_total_quantity_kg']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_total_total_amount']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_total_rate_weighted_complete_calculated'];?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_total_rate_weighted_complete']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['quantity_complete_kg']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_rate_variety_currency']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_variety_currency']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_variety_taka']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_other_currency']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_other_taka']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_dc_expense_taka']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_price_total_taka']; ?></label></td>

                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_opening_stock_kg']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_opening_rate_weighted_complete']; ?></label></td>
                            <td class="text-center"> <label><?php echo $rates[$variety_ifo['variety_id']][$variety_ifo['pack_size_id']]['complete_opening_total_amount']; ?></label></td>

                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"><?php echo $total_complete_price_variety_currency; ?></td>
                        <td class="text-center"><?php echo $total_complete_price_variety_taka; ?></td>
                        <td class="text-center"><?php echo $total_complete_price_other_currency; ?></td>
                        <td class="text-center"><?php echo $total_complete_price_other_taka; ?></td>
                        <td class="text-center"><?php echo $total_complete_price_dc_expense_taka; ?></td>
                        <td class="text-center"><?php echo $total_complete_price_total_taka; ?></td>

                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>
    </div>


</div>
<div class="clearfix"></div>