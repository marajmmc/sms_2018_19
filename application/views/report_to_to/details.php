<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();

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
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_REQUEST');?></label></th>
                    <th class="warning header_value"><label class="control-label"><?php echo $item['status_request'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right">`TO` (Request) Number of Edit</label></th>
                    <th class="warning"><label class="control-label"><?php echo $item['revision_count_request'];?></label></th>
                </tr>
                <?php
                if($item['status_request']==$this->config->item('system_status_forwarded'))
                {
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
                }
                ?>
                <!-- Approval Information-->
                <?php
                if($item['status_approve']==$this->config->item('system_status_approved') || $item['status_approve']==$this->config->item('system_status_rejected'))
                {
                    $label_approve_reject='Approval ';
                    $status_approve_reject_color='warning';
                    if($item['status_approve']==$this->config->item('system_status_rejected'))
                    {
                        $label_approve_reject='Reject ';
                        $status_approve_reject_color='danger';
                    }
                    ?>
                    <tr><th colspan="21" class="bg-info"><?php echo $label_approve_reject?> Information</th></tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Approval Status</label></th>
                        <th class="<?php echo $status_approve_reject_color?> header_value"><label class="control-label"><?php echo $item['status_approve'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right">`TO` (<?php echo $label_approve_reject?>) Number of Edit</label></th>
                        <th class="warning"><label class="control-label"><?php echo $item['revision_count_approve'];?></label></th>
                    </tr>
                    <?php
                    if($item['date_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption"><label class="control-label pull-right">Date of <?php echo $label_approve_reject?></label></th>
                            <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_approve']);?></label></th>
                            <th colspan="2">&nbsp;</th>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['remarks_approve_edit'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Remarks for <?php echo $label_approve_reject?> (Edit)</label></th>
                            <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_approve_edit']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['date_updated_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?> (<?php echo $label_approve_reject?> Edit)</label></th>
                            <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_approve']]['name'];?></label></th>
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?> (<?php echo $label_approve_reject?> Edit)</label></th>
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
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $label_approve_reject?> By</label></th>
                            <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_approve_forward']]['name'];?></label></th>
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $label_approve_reject?> Time</label></th>
                            <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_approve_forward']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['remarks_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Remarks for <?php echo $label_approve_reject?></label></th>
                            <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_approve']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
                <!-- Deliver Information-->
                <?php
                if($item['status_delivery']==$this->config->item('system_status_delivered'))
                {
                ?>
                    <tr><th colspan="21" class="bg-info">Delivery & Courier Information</th></tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_DELIVERY');?></label></th>
                        <th class="warning header_value"><label class="control-label"><?php echo $item['status_delivery'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right">`TO` (Delivery) Number of Edit</label></th>
                        <th class="warning"><label class="control-label"><?php echo $item['revision_count_delivery'];?></label></th>
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
                <?php
                }
                ?>
                <!-- Receive Information-->
                <?php
                if($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes'))
                {
                ?>
                    <tr><th colspan="21" class="bg-info">Receive Information</th></tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Manually Product Receive</label></th>
                        <th class="warning header_value"><label class="control-label"><?php echo $item['status_system_delivery_receive'];?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_RECEIVE');?></label></th>
                        <th class="warning header_value"><label class="control-label"><?php echo $item['status_receive'];?></label></th>
                        <th colspan="21">&nbsp;</th>
                    </tr>
                    <?php
                    if($item['user_updated_receive_forward'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_RECEIVED_BY');?></label></th>
                            <th class=" header_value"><label class="control-label"><?php echo $item['full_name_receive_forward'];?></label></th>
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_RECEIVED_TIME');?></label></th>
                            <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_receive_forward']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['remarks_receive_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_APPROVE');?></label></th>
                            <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_receive_approve']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
                <?php
                if($item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                {
                ?>
                    <tr><th colspan="21" class="bg-info">Receive Information</th></tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Manually Product Receive</label></th>
                        <th class="warning header_value"><label class="control-label"><?php echo $item['status_system_delivery_receive'];?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <?php
                    if($item['user_updated_receive_forward'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption"><label class="control-label pull-right">Edit <?php echo $CI->lang->line('LABEL_RECEIVED_BY');?></label></th>
                            <th class=" header_value"><label class="control-label"><?php echo $item['full_name_receive_forward'];?></label></th>
                            <th class="widget-header header_caption"><label class="control-label pull-right">Edit <?php echo $CI->lang->line('LABEL_DATE_RECEIVED_TIME');?></label></th>
                            <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_receive_forward']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['user_updated_receive_forward'])
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
                    if($item['user_updated_receive_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption"><label class="control-label pull-right">Forward Approved By (Receive)</label></th>
                            <th class=" header_value"><label class="control-label"><?php echo $users[$item['user_updated_receive_approve']]['name'];?></label></th>
                            <th class="widget-header header_caption"><label class="control-label pull-right">Forward Approved Time (Receive)</label></th>
                            <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_receive_approve']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['remarks_receive_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_APPROVE');?></label></th>
                            <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_receive_approve']);?></label></th>
                        </tr>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
                </thead>
            </table>
        </div>
    </div>
    <div class="col-md-12">

    </div>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="21" class="text-center text-danger danger"><?php echo $CI->lang->line('LABEL_ORDER_ITEMS');?></th>
                </tr>
                <tr>
                    <th rowspan="2" style="width: 10px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th rowspan="2" style="width: 200px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <?php
                    if($item['status_delivery']==$this->config->item('system_status_delivered'))
                    {
                        ?>
                        <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                    <?php
                    }
                    ?>
                    <th colspan="2" class="text-center bg-info" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER'); ?></th>
                    <?php
                    if($item['status_approve']==$this->config->item('system_status_approved'))
                    {
                        ?>
                        <th colspan="2" class="text-center bg-warning" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if(($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes')) || $item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                    {
                        ?>
                        <th colspan="2" class="text-center bg-success" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_RECEIVE'); ?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                    {
                        ?>
                        <th colspan="2" class="text-center bg-danger" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_DIFFERENCE'); ?></th>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <th style="width: 150px;" class="text-right bg-info"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right bg-info"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <?php
                    if($item['status_approve']==$this->config->item('system_status_approved'))
                    {
                    ?>
                        <th style="width: 150px;" class="text-right bg-warning"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 150px;" class="text-right bg-warning"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if(($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes')) || $item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                    {
                    ?>
                        <th style="width: 150px;" class="text-right bg-success"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 150px;" class="text-right bg-success"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                    {
                        ?>
                        <th style="width: 150px;" class="text-right bg-danger"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 150px;" class="text-right bg-danger"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    <?php
                    }
                    ?>
                </tr>
                </thead>
                <tbody id="items_container">
                <?php

                $quantity_total_request=0;
                $quantity_total_request_kg=0;

                $quantity_approve=0;
                $quantity_total_approve=0;
                $quantity_total_approve_kg=0;

                $quantity_receive=0;
                $quantity_total_receive=0;
                $quantity_total_receive_kg=0;

                $quantity_difference=0;
                $quantity_difference_kg=0;
                $quantity_total_difference=0;
                $quantity_total_difference_kg=0;

                foreach($items as $index=>$value)
                {
                    $quantity_request_kg=(($value['quantity_request']*$value['pack_size'])/1000);
                    $quantity_total_request+=$value['quantity_request'];
                    $quantity_total_request_kg+=$quantity_request_kg;

                    $quantity_approve=$value['quantity_approve'];
                    $quantity_approve_kg=(($quantity_approve*$value['pack_size'])/1000);
                    $quantity_total_approve+=$quantity_approve;
                    $quantity_total_approve_kg+=$quantity_approve_kg;

                    $quantity_receive=$value['quantity_receive'];
                    $quantity_receive_kg=(($quantity_receive*$value['pack_size'])/1000);
                    $quantity_total_receive+=$quantity_receive;
                    $quantity_total_receive_kg+=$quantity_receive_kg;

                    $quantity_difference=($quantity_receive-$quantity_approve);
                    $quantity_difference_kg=($quantity_receive_kg-$quantity_approve_kg);
                    $quantity_total_difference+=$quantity_difference;
                    $quantity_total_difference_kg+=$quantity_difference_kg;

                    ?>
                    <tr>
                        <td class="text-right"><?php echo $index+1;?></td>
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
                        <?php
                        if($item['status_delivery']==$this->config->item('system_status_delivered'))
                        {
                            ?>
                            <td>
                                <label><?php echo $value['warehouse_name']; ?></label>
                            </td>
                        <?php
                        }
                        ?>
                        <td class="text-right">
                            <label ><?php echo $value['quantity_request']; ?></label>
                        </td>
                        <td class="text-right">
                            <label id="quantity_request_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_request_kg,3,'.','');?> </label>
                        </td>
                        <?php
                        if($item['status_approve']==$this->config->item('system_status_approved'))
                        {
                            ?>
                            <td class="text-right"><label><?php echo $quantity_approve; ?></label></td>
                            <td class="text-right"><label> <?php echo number_format($quantity_approve_kg,3,'.','');?> </label></td>
                        <?php
                        }
                        ?>
                        <?php
                        if(($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes')) || $item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                        {
                            ?>
                            <td class="text-right"><label><?php echo $quantity_receive; ?></label></td>
                            <td class="text-right"><label> <?php echo number_format($quantity_receive_kg,3,'.','');?> </label></td>
                        <?php
                        }
                        ?>
                        <?php
                        if($item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                        {
                            ?>
                            <td class="text-right"><label><?php echo $quantity_difference; ?></label></td>
                            <td class="text-right"><label> <?php echo number_format($quantity_difference_kg,3,'.','');?> </label></td>
                        <?php
                        }
                        ?>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <?php
                    $footer_colspan=5;
                    if($item['status_delivery']==$this->config->item('system_status_delivered'))
                    {
                        $footer_colspan+=1;
                    }
                    ?>
                    <th colspan="<?php echo $footer_colspan;?>" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_request;?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_request_kg,3,'.','');?></label></th>
                    <?php
                    if($item['status_approve']==$this->config->item('system_status_approved'))
                    {
                        ?>
                        <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_approve;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_approve_kg,3,'.','');?></label></th>
                    <?php
                    }
                    ?>
                    <?php
                    if(($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes')) || $item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                    {
                        ?>
                        <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_receive;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                    <?php
                    }
                    ?>
                    <?php
                    if($item['status_system_delivery_receive']==$this->config->item('system_status_no'))
                    {
                        ?>
                        <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_difference;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_difference_kg,3,'.','');?></label></th>
                    <?php
                    }
                    ?>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
