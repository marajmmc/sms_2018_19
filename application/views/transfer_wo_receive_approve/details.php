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
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#">+ Basic Information</a></label>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ID');?></label></th>
                    <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_transfer_warehouse_to_outlet($item['id']);?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['division_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_REQUEST');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_request']);?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['zone_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['territory_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['district_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['outlet_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY');?> (Request)</label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_created_request']]['name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME');?> (Request)</label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created_request']);?></label></th>
                </tr>
                <?php
                if($item['user_updated_request'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?> (Request)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_request']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?> (Request)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_request']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['date_updated_forward'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY');?> (Request)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_forward']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME');?> (Request)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_forward']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['remarks_request'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_REQUEST');?></label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_request']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <!-- Approval Information-->
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Approve Status</label></th>
                    <th class="warning header_value"><label class="control-label"><?php echo $item['status_approve'];?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVE');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_approve']);?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <?php
                if($item['date_updated_approve'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?> (Approve Edit)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_approve']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?> (Approve Edit)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_approve']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['date_updated_approve_forward'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY');?> (Approve)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_approve_forward']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME');?> (Approve)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_approve_forward']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['remarks_approve_edit'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_APPROVE');?> (Edit)</label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_approve_edit']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['remarks_approve'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_APPROVE');?></label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_approve']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <!-- Delivery Information-->
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Delivery Status</label></th>
                    <th class="warning header_value"><label class="control-label"><?php echo $item['status_delivery'];?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <?php
                if($item['date_delivery'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_DELIVERY');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_delivery']);?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['date_updated_delivery'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?> (Delivery Edit)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_delivery']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?> (Delivery Edit)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_delivery']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['date_updated_delivery_forward'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY');?> (Delivery)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_delivery_forward']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME');?> (Delivery)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_delivery_forward']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['remarks_delivery'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_DELIVERY');?></label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_delivery']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['full_name_receive_forward'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY');?> (Receive)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $item['full_name_receive_forward'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME');?> (Receive)</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_receive_forward']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item['remarks_receive_forward'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_RECEIVE');?></label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_receive_forward']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                </thead>
            </table>
            <div class="clearfix"></div>
            <div class="widget-header">
                <div class="title">
                    Courier Information
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Challan No:</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['challan_no'];?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CHALLAN');?>:</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date($item['date_challan']);?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Courier Name:</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['courier_name'];?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Receive Branch (Place):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['place_destination'];?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Remarks for Courier:</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['remarks_couriers'];?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="21" class="text-center success"><?php echo $CI->lang->line('LABEL_ORDER_ITEMS');?></th>
                </tr>
                <tr>
                    <th rowspan="2" style="width: 200px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                    <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
                    <th colspan="2" class="text-center" style="width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?></th>
                    <th colspan="2" class="text-center" style="width: 150px;">New Stock<?php //echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                </tr>
                <tr>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                </tr>
                </thead>
                <tbody id="items_container">
                <?php
                $stock_current_kg=0;

                $quantity_approve=0;
                $quantity_total_approve=0;
                $quantity_total_approve_kg=0;

                $quantity_receive=0;
                $quantity_total_receive=0;
                $quantity_total_receive_kg=0;

                $stock_current_quantity_total=0;
                $stock_current_quantity_total_kg=0;

                $stock_quantity_new=0;
                $stock_quantity_new_kg=0;
                $stock_quantity_total_new=0;
                $stock_quantity_total_new_kg=0;

                foreach($items as $index=>$value)
                {
                    $quantity_approve=$value['quantity_approve'];
                    $quantity_approve_kg=(($quantity_approve*$value['pack_size'])/1000);
                    $quantity_total_approve+=$quantity_approve;
                    $quantity_total_approve_kg+=$quantity_approve_kg;

                    $quantity_receive=$value['quantity_receive'];
                    $quantity_receive_kg=(($quantity_receive*$value['pack_size'])/1000);
                    $quantity_total_receive+=$quantity_receive;
                    $quantity_total_receive_kg+=$quantity_receive_kg;

                    if(isset($stocks[$value['variety_id']][$value['pack_size_id']]))
                    {
                        $stock_current=$stocks[$value['variety_id']][$value['pack_size_id']]['current_stock'];
                    }
                    else
                    {
                        $stock_current=0;
                    }
                    $stock_current_kg=(($stock_current*$value['pack_size'])/1000);

                    $stock_current_quantity_total+=$stock_current;
                    $stock_current_quantity_total_kg+=$stock_current_kg;

                    $stock_quantity_new=($stock_current+$quantity_receive);
                    $stock_quantity_new_kg=($stock_current_kg+$quantity_receive_kg);
                    $stock_quantity_total_new+=$stock_quantity_new;
                    $stock_quantity_total_new_kg+=$stock_quantity_new_kg;

                    ?>
                    <tr class='<?php if($quantity_approve!=$quantity_receive){echo 'quantity_exist_warning';}?>'>
                        <td>
                            <label><?php echo $value['crop_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $value['crop_type_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $value['variety_name']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $value['pack_size']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $stock_current; ?> </label>
                        </td>
                        <td class="text-right">
                            <label><?php echo number_format($stock_current_kg,3,'.',''); ?></label>
                        </td>
                        <td class="text-right"><label><?php echo $quantity_approve; ?></label></td>
                        <td class="text-right"><label> <?php echo number_format($quantity_approve_kg,3,'.','');?> </label></td>
                        <td class="text-right"><label><?php echo $quantity_receive; ?></label></td>
                        <td class="text-right"><label> <?php echo number_format($quantity_receive_kg,3,'.','');?> </label></td>
                        <td class="text-right">
                            <label class="control-label stock_quantity_new " id="stock_quantity_new_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                <?php
                                echo $stock_quantity_new;
                                ?>
                            </label>
                        </td>
                        <td class="text-right">
                            <label class="control-label stock_quantity_new_kg " id="stock_quantity_new_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                <?php
                                echo number_format($stock_quantity_new_kg,3,'.','');
                                ?>
                            </label>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                    <th class="text-right"><label class="control-label" id="stock_current_quantity_total"> <?php echo $stock_current_quantity_total;?></label></th>
                    <th class="text-right"><label class="control-label" id="stock_current_quantity_total_kg"> <?php echo number_format($stock_current_quantity_total_kg,3,'.','');?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_approve"> <?php echo $quantity_total_approve;?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_approve_kg"> <?php echo number_format($quantity_total_approve_kg,3,'.','');?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_receive"> <?php echo $quantity_total_receive;?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_receive_kg"> <?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                    <th class="text-right"><label class="control-label" id="stock_quantity_total_new"> <?php echo $stock_quantity_total_new;?></label></th>
                    <th class="text-right"><label class="control-label" id="stock_quantity_total_new_kg"> <?php echo number_format($stock_quantity_total_new_kg,3,'.','');?></label></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<style>
    .quantity_exist_warning
    {
        background-color: red !important;
        color: #FFFFFF;
    }
</style>