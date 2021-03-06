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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_forward');?>" method="post">
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
                        <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_transfer_outlet_to_outlet($item['id']);?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME_SOURCE');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $CI->outlets[$item['outlet_id_source']]['name'];?></label></th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_REQUEST');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_request']);?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME_DESTINATION');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $CI->outlets[$item['outlet_id_destination']]['name'];?></label></th>
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

                    <!-- Approval Information-->
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Approve Status</label></th>
                        <th class="warning header_value"><label class="control-label"><?php echo $item['status_approve'];?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <?php
                    if($item['date_approve'])
                    {
                        ?>
                        <tr>
                            <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVE');?></label></th>
                            <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_approve']);?></label></th>
                            <th colspan="2">&nbsp;</th>
                        </tr>
                    <?php
                    }
                    ?>
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
                        <th colspan="21" class="text-center bg-success"><?php echo $CI->lang->line('LABEL_ORDER_ITEMS');?></th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width: 200px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th rowspan="2" style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th colspan="2" class="text-center" style="width: 12.5%;"><?php echo $CI->lang->line('LABEL_STOCK_AVAILABLE'); ?> </th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER'); ?></th>
                        <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_APPROVE'); ?></th>
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
                    $quantity_total_request=0;
                    $quantity_total_request_kg=0;
                    $quantity_total_approve=0;
                    $quantity_total_approve_kg=0;
                    $class_bg_warning='';
                    foreach($items as $index=>$value)
                    {
                        $quantity_approve=$value['quantity_approve'];

                        $quantity_request_kg=(($value['quantity_request']*$value['pack_size'])/1000);
                        $quantity_approve_kg=(($quantity_approve*$value['pack_size'])/1000);

                        $quantity_total_request+=$value['quantity_request'];
                        $quantity_total_request_kg+=$quantity_request_kg;


                        $quantity_total_approve+=$quantity_approve;
                        $quantity_total_approve_kg+=$quantity_approve_kg;
                        if($quantity_approve>$too_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available_pkt'])
                        {
                            $class_bg_warning='bg-danger';
                        }
                        else
                        {
                            $class_bg_warning='';
                        }
                        ?>
                        <tr class="<?php echo $class_bg_warning;?>">
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
                                <label class="control-label"> <?php echo System_helper::get_string_quantity($too_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available_pkt']);  ?></label>
                            </td>
                            <td class="text-right">
                                <label class="control-label "> <?php echo System_helper::get_string_kg($too_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available_kg']); ?> </label>
                            </td>
                            <td class="text-right">
                                <label ><?php echo System_helper::get_string_quantity($value['quantity_request']); ?></label>
                            </td>
                            <td class="text-right">
                                <label id="quantity_request_kg_<?php echo $index+1;?>"> <?php echo System_helper::get_string_kg($quantity_request_kg);?> </label>
                            </td>
                            <td class="text-right">
                                <label class=" "><?php echo System_helper::get_string_quantity($quantity_approve); ?></label>
                            </td>
                            <td class="text-right">
                                <label class=" " id="quantity_approve_kg_<?php echo $index+1;?>"> <?php echo System_helper::get_string_kg($quantity_approve_kg);?> </label>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="6" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo System_helper::get_string_quantity($quantity_total_request);?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo System_helper::get_string_kg($quantity_total_request_kg);?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_approve"> <?php echo System_helper::get_string_quantity($quantity_total_approve);?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_approve_kg"> <?php echo System_helper::get_string_kg($quantity_total_approve_kg);?></label></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED');?>/<?php echo $CI->lang->line('LABEL_REJECTED');?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_approve" class="form-control" name="item[status_approve]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_approved')?>"><?php echo $this->config->item('system_status_approved')?></option>
                    <option value="<?php echo $this->config->item('system_status_rejected')?>"><?php echo $this->config->item('system_status_rejected')?></option>
                </select>
            </div>
        </div>
        <div style="font-size: 12px;margin-top: -10px;font-style: italic; color: red;" class="row show-grid">
            <div class="col-xs-4"></div>
            <div class="col-sm-4 col-xs-8">
                Must be fill up reject remarks when this TR reject.
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_APPROVE');?>/Reject <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_approve]" id="remarks_approve" class="form-control" ><?php echo $item['remarks_approve'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Want to Showroom to Showroom Transfer Approved or Rejected?">Save</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

            </div>
        </div>
    </div>
</form>