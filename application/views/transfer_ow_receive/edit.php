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
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
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
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                        <th colspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;">New Stock</th>
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
                        <tr id="item_rows_<?php echo $index+1;?>" class='<?php if($quantity_approve!=$quantity_receive){echo 'quantity_exist_warning';}?>'>
                            <td>
                                <label><?php echo $value['crop_name']; ?></label>
                            </td>
                            <td>
                                <label><?php echo $value['crop_type_name']; ?></label>
                            </td>
                            <td>
                                <label><?php echo $value['variety_name']; ?></label>
                                <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" id="variety_id_<?php echo $index+1;?>" value="<?php echo $value['variety_id']; ?>" data-current-id="<?php echo $index+1;?>" />
                            </td>
                            <td class="text-right">
                                <label><?php echo $value['pack_size']; ?></label>
                                <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $value['pack_size_id']; ?>" class="pack_size_id" data-current-id="<?php echo $index+1;?>" data-pack-size-name="<?php echo $value['pack_size']; ?>">
                            </td>
                            <td>
                                <select id="warehouse_id_<?php echo $index+1;?>" class="form-control warehouse_id" name="items[<?php echo $index+1;?>][warehouse_id]" data-current-id="<?php echo $index+1;?>">
                                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                    <?php
                                    foreach($warehouses as $warehouse)
                                    {
                                        ?>
                                        <option value="<?php echo $warehouse['value'];?>" <?php if($warehouse['value']==$value['warehouse_id']){echo "selected='selected'";}?>><?php echo $warehouse['text'];?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="text-right">
                                <label class="control-label stock_current_pkt " id="stock_current_pkt_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <?php echo $stock_current; ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="control-label stock_current " id="stock_current_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <?php echo number_format($stock_current_kg,3,'.',''); ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label class=" " id="quantity_approve_<?php echo $index+1;?>"><?php echo $quantity_approve; ?></label>
                            </td>
                            <td class="text-right">
                                <label class=" " id="quantity_approve_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_approve_kg,3,'.','');?> </label>
                            </td>
                            <td>
                                <input type="text" value="<?php echo $quantity_receive; ?>" id="quantity_receive_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_receive]" class="form-control float_type_positive quantity_receive" />
                            </td>
                            <td class="text-right">
                                <label class="" id="quantity_receive_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_receive_kg,3,'.','');?> </label>
                            </td>
                            <td class="text-right">
                                <label class="control-label stock_quantity_new" id="stock_quantity_new_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>"><?php echo $stock_quantity_new; ?></label>
                            </td>
                            <td class="text-right">
                                <label class="control-label stock_quantity_new_kg" id="stock_quantity_new_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>"><?php echo number_format($stock_quantity_new_kg,3,'.',''); ?></label>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="7" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_approve"> <?php echo $quantity_total_approve;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_approve_kg"> <?php echo number_format($quantity_total_approve_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_receive"> <?php echo $quantity_total_receive;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_receive_kg"> <?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label" id="stock_quantity_total_new"></label></th>
                        <th class="text-right"><label class="control-label" id="stock_quantity_total_new_kg"></label></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_RECEIVE');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks_receive_forward]" id="remarks_receive_forward" class="form-control"><?php echo $item['remarks_receive_forward'];?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
<style>
    .quantity_exist_warning
    {
        background-color: red !important;
        color: #FFFFFF;
    }
</style>
<script>
    <?php
        if(sizeof($stocks)>0)
        {
        ?>
        var hq_variety_stocks=JSON.parse('<?php echo json_encode($stocks);?>');
        <?php
        }
        else
        {
            ?>
            var hq_variety_stocks={};
            <?php
        }
    ?>
    function calculate_total()
    {
        var quantity_total_receive=0;
        var quantity_total_receive_kg=0;
        $('#items_container .quantity_receive').each(function(index, element)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_receive=parseFloat($(this).val());
            if(isNaN(quantity_receive))
            {
                quantity_receive=0;
            }
            quantity_total_receive+=quantity_receive;
            var quantity_receive_kg=parseFloat($('#quantity_receive_kg_'+current_id).html().replace(/,/g,''));
            if(isNaN(quantity_receive_kg))
            {
                quantity_receive_kg=0;
            }
            quantity_total_receive_kg+=quantity_receive_kg;
        });
        $('#quantity_total_receive').html(quantity_total_receive);
        $('#quantity_total_receive_kg').html(number_format((quantity_total_receive_kg),3,'.',''));
    }

    $(document).ready(function()
    {
        //console.log(hq_variety_stocks)
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".datepicker").datepicker({dateFormat : display_date_format});

        $(document).off("change",".warehouse_id");
        $(document).on("change",".warehouse_id",function()
        {
            var current_id=$(this).attr('data-current-id');

            $('#item_rows_'+current_id).removeClass('quantity_exist_warning');
            $('#stock_current_pkt_'+current_id).html('');
            $('#stock_current_'+current_id).html('');
            $('#stock_quantity_new_'+current_id).html('');
            $('#stock_quantity_new_kg_'+current_id).html('');

            var variety_id=$('#variety_id_'+current_id).val();
            var pack_size_id=$('#pack_size_id_'+current_id).val();
            var pack_size=$('#pack_size_id_'+current_id).attr('data-pack-size-name');
            var warehouse_id=$('#warehouse_id_'+current_id).val();
            var quantity_approve=parseFloat($('#quantity_approve_'+current_id).html().replace(/,/g,''));
            var quantity_receive=parseFloat($('#quantity_receive_'+current_id).val());
            if(quantity_receive==undefined)
            {
                quantity_receive=0;
            }

            var quantity_receive_kg=parseFloat($('#quantity_receive_kg_'+current_id).html().replace(/,/g,''));
            if(quantity_receive_kg==undefined)
            {
                quantity_receive_kg=0;
            }
            var current_stock=0;
            var current_stock_kg=0;
            var stock_quantity_new=0;
            var stock_quantity_new_kg=0;
            if(variety_id>0 && pack_size_id>0 && warehouse_id>0)
            {
                if(hq_variety_stocks[variety_id][pack_size_id][warehouse_id]!=undefined)
                {
                    current_stock=hq_variety_stocks[variety_id][pack_size_id][warehouse_id]['current_stock'];
                    current_stock_kg=((current_stock*pack_size)/1000);
                }
                stock_quantity_new=(current_stock+quantity_receive);
                stock_quantity_new_kg=(current_stock_kg+quantity_receive_kg);
            }
            $('#stock_current_pkt_'+current_id).html(current_stock);
            $('#stock_current_'+current_id).html(number_format(current_stock_kg,'3','.',''));
            $('#stock_quantity_new_'+current_id).html(stock_quantity_new);
            $('#stock_quantity_new_kg_'+current_id).html(number_format(stock_quantity_new_kg,'3','.',''));
            if(quantity_approve!=quantity_receive)
            {
                $('#item_rows_'+current_id).addClass('quantity_exist_warning');
            }
        });

        $(document).off("input",".quantity_receive");
        $(document).on("input",".quantity_receive",function()
        {
            var current_id=$(this).attr('data-current-id');
            $('#item_rows_'+current_id).removeClass('quantity_exist_warning');
            $('#stock_quantity_new_'+current_id).html('');
            $('#stock_quantity_new_kg_'+current_id).html('');
            var variety_id=$('#variety_id_'+current_id).val();
            var pack_size_id=$('#pack_size_id_'+current_id).val();
            var pack_size=$('#pack_size_id_'+current_id).attr('data-pack-size-name');
            var warehouse_id=$('#warehouse_id_'+current_id).val();
            var current_stock=0;
            var current_stock_kg=0;
            var stock_quantity_new=0;
            var stock_quantity_new_kg=0;
            var quantity_approve=parseFloat($('#quantity_approve_'+current_id).html().replace(/,/g,''));
            var quantity_receive=parseFloat($('#quantity_receive_'+current_id).val());
            if(quantity_receive==undefined)
            {
                quantity_receive=0;
            }
            var quantity_receive_kg=((quantity_receive*pack_size)/1000);
            $('#quantity_receive_kg_'+current_id).html(number_format(quantity_receive_kg,'3','.',''));

            if(variety_id>0 && pack_size_id>0 && warehouse_id>0)
            {
                if(hq_variety_stocks[variety_id][pack_size_id][warehouse_id]!=undefined)
                {
                    current_stock=hq_variety_stocks[variety_id][pack_size_id][warehouse_id]['current_stock'];
                    current_stock_kg=((current_stock*pack_size)/1000);
                }
                stock_quantity_new=(current_stock+quantity_receive);
                stock_quantity_new_kg=(current_stock_kg+quantity_receive_kg);
            }
            $('#stock_quantity_new_'+current_id).html(stock_quantity_new);
            $('#stock_quantity_new_kg_'+current_id).html(number_format(stock_quantity_new_kg,'3','.',''));
            if(quantity_approve!=quantity_receive)
            {
                $('#item_rows_'+current_id).addClass('quantity_exist_warning');
            }
            calculate_total();
        });
    });

</script>