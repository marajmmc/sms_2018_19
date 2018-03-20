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
    <div class="col-md-12">
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?></label></th>
                <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_transfer_warehouse_to_outlet($item['id']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DIVISION_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['division_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_REQUEST');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_request']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ZONE_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['zone_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_APPROVE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_approve']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_TERRITORY_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['territory_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_DELIVERY');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['date_delivery']?System_helper::display_date($item['date_delivery']):System_helper::display_date(time());?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DISTRICT_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['district_name'];?></label></th>
            </tr>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OUTLET_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['outlet_name'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CREATED_BY');?> (TO Request)</label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['user_created_full_name'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CREATED_TIME');?> (TO Request)</label></th>
                <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created_request']);?></label></th>
            </tr>
            <?php
            if($item['user_updated_request'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_UPDATED_BY');?> (TO Request)</label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['user_updated_full_name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_UPDATED_TIME');?> (TO Request)</label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_request']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <?php
            if($item['user_updated_approve'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_APPROVED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['user_updated_approve_full_name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_APPROVED_TIME');?> </label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_approve']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <?php
            if($item['remarks_request'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_REQUEST');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_request']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <?php
            if($item['remarks_approve_edit'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_APPROVE');?> (Edit)</label></th>
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
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_APPROVE');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_approve']);?></label></th>
                </tr>
            <?php
            }
            ?>
            </thead>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="21" class="text-center text-danger danger">Order Items (Delivery Status: <?php echo $item['status_delivery'];?>)</th>
                </tr>
                <tr>
                    <th rowspan="2" style="width: 200px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                    <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
                    <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG'); ?></th>
                </tr>
                <tr>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                </tr>
                </thead>
                <tbody id="items_container">
                <?php
                $quantity_approve=0;
                $quantity_total_approve=0;
                $quantity_total_approve_kg=0;

                foreach($items as $index=>$value)
                {
                    $quantity_approve=$value['quantity_approve'];
                    $quantity_approve_kg=(($quantity_approve*$value['pack_size'])/1000);

                    $quantity_total_approve+=$quantity_approve;
                    $quantity_total_approve_kg+=$quantity_approve_kg;
                    if(isset($stocks[$value['variety_id']][$value['pack_size_id']][$value['warehouse_id']]))
                    {
                        $stock_current=$stocks[$value['variety_id']][$value['pack_size_id']][$value['warehouse_id']]['current_stock'];
                    }
                    else
                    {
                        $stock_current=0;
                    }
                    $stock_current=(($stock_current*$value['pack_size'])/1000);
                    ?>
                    <tr>
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
                            <label><?php echo $value['warehouse_name']; ?></label>
                        </td>
                        <td class="text-right">
                            <label class=" "><?php echo $quantity_approve; ?></label>
                        </td>
                        <td class="text-right">
                            <label class=" " id="quantity_approve_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_approve_kg,3,'.','');?> </label>
                        </td>
                        <td class="text-right">
                            <label class="control-label stock_current " id="stock_current_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                <?php
                                echo number_format($stock_current,3,'.','');
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
                    <th colspan="5" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_approve"> <?php echo $quantity_total_approve;?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_approve_kg"> <?php echo number_format($quantity_total_approve_kg,3,'.','');?></label></th>
                    <th colspan="5">&nbsp;</th>
                </tr>
                <?php
                if($item['remarks_challan'])
                {
                    ?>
                    <tr>
                        <td colspan="21">
                            <strong><?php echo $CI->lang->line('LABEL_REMARKS_CHALLAN');?>: </strong>
                            <?php echo nl2br($item['remarks_challan']);?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="widget-header">
        <div class="title">
            Delivery & Courier Information
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row show-grid">
        <div class="col-md-12">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_DELIVERY');?></label></th>
                    <th class="alert-warning header_value"><label class="control-label"><?php echo System_helper::display_date($item['courier_date_delivery']);?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Challan No</label></th>
                    <th class=""><label class="control-label"><?php echo $item['challan_no'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CHALLAN');?></label></th>
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
</div>