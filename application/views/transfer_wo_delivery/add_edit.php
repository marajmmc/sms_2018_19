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
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_MIN'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_MAX'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_STOCK_OUTLET'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_TRANSFER_MAXIMUM'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_STOCK_AVAILABLE'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
                    </tr>
                    <tr>
                        <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    </tr>
                    </thead>
                    <tbody id="items_container">
                    <?php
                    $quantity_approve=0;
                    $quantity_total_request=0;
                    $quantity_total_request_kg=0;
                    $quantity_total_approve=0;
                    $quantity_total_approve_kg=0;
                    $class_quantity_exist_warning='';
                    foreach($items as $index=>$value)
                    {
                        /*if($item['user_updated_approve'])
                        {
                            $quantity_approve=$value['quantity_approve'];
                        }
                        else
                        {
                            $quantity_approve=0;
                        }*/
                        $quantity_approve=$value['quantity_approve'];
                        if($value['pack_size_id']==0)
                        {
                            $quantity_request_kg=$value['quantity_request'];
                            $quantity_approve_kg=$quantity_approve;
                        }
                        else
                        {
                            $quantity_request_kg=(($value['quantity_request']*$value['pack_size'])/1000);
                            $quantity_approve_kg=(($quantity_approve*$value['pack_size'])/1000);
                        }
                        $quantity_total_request+=$value['quantity_request'];
                        $quantity_total_request_kg+=$quantity_request_kg;


                        $quantity_total_approve+=$quantity_approve;
                        $quantity_total_approve_kg+=$quantity_approve_kg;
                        if($quantity_request_kg>$two_variety_info[$value['variety_id']][$value['pack_size_id']]['quantity_max_transferable'] || $quantity_request_kg>$two_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available'])
                        {
                            $class_quantity_exist_warning='quantity_exist_warning';
                        }
                        else
                        {
                            $class_quantity_exist_warning='';
                        }
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
                            </td>
                            <td class="text-right">
                                <label><?php if($value['pack_size_id']==0){echo 'Bulk';}else{echo $value['pack_size'];} ?></label>
                            </td>
                            <td class="text-right">
                                <label id="quantity_min_<?php echo $index+1;?>">
                                    <?php
                                    echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['quantity_min'],3,'.',''):'0.000';
                                    ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label id="quantity_max_<?php echo $index+1;?>">
                                    <?php
                                    echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['quantity_max'],3,'.',''):'0.000';
                                    ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="control-label stock_outlet" id="stock_outlet_<?php echo $index+1;?>">
                                    <?php
                                    echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_outlet'],3,'.',''):'0.000';
                                    ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="control-label quantity_max_transferable <?php echo $class_quantity_exist_warning;?>" id="quantity_max_transferable_<?php echo $index+1;?>">
                                    <?php
                                    echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['quantity_max_transferable'],3,'.',''):'0.000';
                                    ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="control-label stock_available <?php echo $class_quantity_exist_warning;?>" id="stock_available_id_<?php echo $index+1;?>">
                                    <?php
                                    echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available'],3,'.',''):'0.000';
                                    ?>
                                </label>
                            </td>
                            <td class="text-right">
                                <label ><?php echo $value['quantity_request']; ?></label>
                            </td>
                            <td class="text-right">
                                <label id="quantity_request_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_request_kg,3,'.','');?> </label>
                            </td>
                            <td class="text-right">
                                <label class=" <?php echo $class_quantity_exist_warning;?>"><?php echo $quantity_approve; ?></label>
                            </td>
                            <td class="text-right">
                                <label class=" <?php echo $class_quantity_exist_warning;?>" id="quantity_approve_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_approve_kg,3,'.','');?> </label>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="9" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_request;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_request_kg,3,'.','');?></label></th>
                        <th class="text-right"><label class="control-label <?php if($quantity_total_approve_kg>$quantity_to_maximum_kg){echo 'quantity_exist_warning';}?>" id="quantity_total_approve"> <?php echo $quantity_total_approve;?></label></th>
                        <th class="text-right"><label class="control-label <?php if($quantity_total_approve_kg>$quantity_to_maximum_kg){echo 'quantity_exist_warning';}?>" id="quantity_total_approve_kg"> <?php echo number_format($quantity_total_approve_kg,3,'.','');?></label></th>
                    </tr>
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
                <label class="control-label pull-right">Booking Date</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="courier[date_booking]" id="date_booking" class="form-control datepicker" value="<?php echo $courier['date_booking']?System_helper::display_date($courier['date_booking']):'';?>" readonly="readonly" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
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

    $(document).ready(function()
    {
        //console.log(two_variety_info)
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".datepicker").datepicker({dateFormat : display_date_format});
    });

</script>