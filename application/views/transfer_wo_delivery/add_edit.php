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
        <div class="col-md-12">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?></label></th>
                    <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_transfer_warehouse_to_outlet($item['id']);?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_REQUEST');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_request']);?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_APPROVE');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_approve']);?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DIVISION_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['division_name'];?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ZONE_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['zone_name'];?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_TERRITORY_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['territory_name'];?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DISTRICT_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['district_name'];?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OUTLET_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['outlet_name'];?></label></th>
                    <th colspan="2">&nbsp;</th>
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
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_UPDATED_BY');?> (TO Approve)</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $item['user_updated_approve_full_name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_UPDATED_TIME');?> (TO Approve)</label></th>
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
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
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
                                <select id="warehouse_id_<?php echo $index+1;?>" class="form-control warehouse_id" name="items[<?php echo $index+1;?>][warehouse_id]" data-current-id="<?php echo $index+1;?>">
                                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
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
                    </tfoot>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_CHALLAN');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks_challan]" id="remarks_challan" class="form-control"><?php echo $item['remarks_challan'];?></textarea>
                </div>
            </div>
        </div>
        <div class="widget-header">
            <div class="title">
                Delivery & Courier Information
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_DELIVERY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="courier[date_delivery]" id="date_delivery" class="form-control datepicker" value="<?php echo System_helper::display_date($courier['date_delivery']);?>" readonly="readonly" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CHALLAN');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="courier[date_challan]" id="date_challan" class="form-control datepicker" value="<?php echo $courier['date_challan']?System_helper::display_date($courier['date_challan']):'';?>" readonly="readonly" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Challan No</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="courier[challan_no]" id="challan_no" class="form-control" value="<?php echo $courier['challan_no'];?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Courier Name</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="courier_id" class="form-control" name="courier[courier_id]" >
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($couriers as $row)
                    {
                        ?>
                        <option value="<?php echo $row['id'];?>" <?php if($row['id']==$courier['courier_id']){echo "selected='selected'";}?>><?php echo $row['name'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Courier Tracing No</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="courier[courier_tracing_no]" id="courier_tracing_no" class="form-control" value="<?php echo $courier['courier_tracing_no'];?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Booking Branch (Place)</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="courier[place_booking_source]" id="place_booking_source" class="form-control"><?php echo $courier['place_booking_source'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Receive Branch (Place)</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="courier[place_destination]" id="place_destination" class="form-control"><?php echo $courier['place_destination'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Booking Date</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="courier[date_booking]" id="date_booking" class="form-control datepicker" value="<?php echo $courier['date_booking']?System_helper::display_date($courier['date_booking']):'';?>" readonly="readonly" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_COURIER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="courier[remarks]" id="remarks" class="form-control"><?php echo $courier['remarks'];?></textarea>
            </div>
        </div>
    </div>
</form>
<style>
    .quantity_exist_warning
    {
        background-color: red;
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
    $(document).ready(function()
    {
        console.log(hq_variety_stocks)
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".datepicker").datepicker({dateFormat : display_date_format});

        $(document).off("change",".warehouse_id");
        $(document).on("change",".warehouse_id",function()
        {
            var current_id=$(this).attr('data-current-id');
            $('#quantity_approve_kg_'+current_id).removeClass('quantity_exist_warning');
            $('#stock_current_'+current_id).removeClass('quantity_exist_warning');

            $("#stock_current_"+current_id).html("");
            var variety_id=$('#variety_id_'+current_id).val();
            var pack_size_id=$('#pack_size_id_'+current_id).val();
            var warehouse_id=$('#warehouse_id_'+current_id).val();
            var quantity_approve_kg=parseFloat($('#quantity_approve_kg_'+current_id).html().replace(/,/g,''));
            var current_stock=0;
            if(variety_id>0 && pack_size_id>0 && warehouse_id>0)
            {
                if(hq_variety_stocks[variety_id][pack_size_id][warehouse_id]!=undefined)
                {
                    current_stock=hq_variety_stocks[variety_id][pack_size_id][warehouse_id]['current_stock'];
                }
                current_stock=((current_stock*pack_size_id)/1000);
                $('#stock_current_'+current_id).html(number_format(current_stock,'3','.',''));
                if(quantity_approve_kg>current_stock)
                {
                    $('#quantity_approve_kg_'+current_id).addClass('quantity_exist_warning');
                    $('#stock_current_'+current_id).addClass('quantity_exist_warning');
                }
            }
        });
    });

</script>