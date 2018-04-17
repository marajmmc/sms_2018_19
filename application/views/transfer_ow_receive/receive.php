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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_receive');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
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
                        <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_transfer_outlet_to_warehouse($item['id']);?></label></th>
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
                        <th colspan="21" class="bg-info"> Approval Information</th>
                    </tr>
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
                        <th colspan="21" class="bg-info"> Delivery Information</th>
                    </tr>
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
                            <th class=" header_value"><label class="control-label"><?php echo $item['full_name_delivery_edit'];?></label></th>
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
                            <th class=" header_value"><label class="control-label"><?php echo $item['full_name_delivery_forward'];?></label></th>
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
                    if($item['remarks_challan'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_CHALLAN');?></label></th>
                            <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_challan']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <th colspan="21" class="bg-info"> Courier Information</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Challan No</label></th>
                        <th class=""><label class="control-label"><?php echo $item['challan_no'];?></label></th>
                        <th colspan="21">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CHALLAN');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_challan']);?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Courier Name</label></th>
                        <th class="alert-warning"><label class="control-label"><?php echo $item['courier_name'];?></label></th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Booking Date</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_booking']);?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Courier Tracing No</label></th>
                        <th class=""><label class="control-label"><?php echo $item['courier_tracing_no'];?></label></th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Booking Branch (Place)</label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['place_booking_source']);?></label></th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Receive Branch (Place)</label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['place_destination']);?></label></th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Remarks for Courier</label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_couriers']);?></label></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="21" class="text-center text-danger danger"><?php echo $CI->lang->line('LABEL_RETURN_ITEMS');?></th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width: 200px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;">New Quantity</th>
                    </tr>
                    <tr>
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
                    $quantity_approve=0;
                    $quantity_total_approve=0;
                    $quantity_total_approve_kg=0;

                    $quantity_receive=0;
                    $quantity_total_receive=0;
                    $quantity_total_receive_kg=0;

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

                        if(isset($stocks[$value['variety_id']][$value['pack_size_id']][$value['warehouse_id']]))
                        {
                            $stock_current=$stocks[$value['variety_id']][$value['pack_size_id']][$value['warehouse_id']]['current_stock'];
                        }
                        else
                        {
                            $stock_current=0;
                        }
                        $stock_current_kg=(($stock_current*$value['pack_size'])/1000);

                        $stock_quantity_new=($stock_current+$quantity_receive);
                        $stock_quantity_new_kg=($stock_current_kg+$quantity_receive_kg);
                        $stock_quantity_total_new+=$stock_quantity_new;
                        $stock_quantity_total_new_kg+=$stock_quantity_new_kg;
                        ?>
                        <tr style="<?php if($quantity_approve!=$quantity_receive){echo 'background-color: red; color: #fff';}?>">
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
                            <td>
                                <label><?php echo $value['warehouse_name']; ?></label>
                            </td>
                            <td class="text-right">
                                <label> <?php echo number_format($stock_current_kg,3,'.',''); ?> </label>
                            </td>
                            <td class="text-right">
                                <label class=" "><?php echo $quantity_approve; ?></label>
                            </td>
                            <td class="text-right">
                                <label> <?php echo number_format($quantity_approve_kg,3,'.','');?> </label>
                            </td>
                            <td class="text-right">
                                <label><?php echo $quantity_receive; ?></label>
                            </td>
                            <td class="text-right">
                                <label> <?php echo number_format($quantity_receive_kg,3,'.','');?> </label>
                            </td>
                            <td class="text-right">
                                <label><?php echo $stock_quantity_new; ?></label>
                            </td>
                            <td class="text-right">
                                <label> <?php echo number_format($stock_quantity_new_kg,3,'.','');?> </label>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="6" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                        <th class="text-right"><label class="control-label"><?php echo $quantity_total_approve;?></label></th>
                        <th class="text-right"><label class="control-label"> <?php echo number_format($quantity_total_approve_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label"> <?php echo $quantity_total_receive;?></label></th>
                        <th class="text-right"><label class="control-label"> <?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label"></label></th>
                        <th class="text-right"><label class="control-label"></label></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="widget-header">
            <div class="title">
                Receive Confirmation
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_RECEIVED');?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_receive" class="form-control" name="item[status_receive]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_received')?>"><?php echo $this->config->item('system_status_received')?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure Outlet to HQ transfer receive done?">Outlet to HQ `TR` Receive</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

            </div>
        </div>
    </div>
</form>
