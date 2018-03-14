<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_all')
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
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_REQUEST');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_request']);?></label></th>
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
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CREATED_BY');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['user_created_full_name'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CREATED_TIME');?></label></th>
                <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created_request']);?></label></th>
            </tr>
            <?php
            if($item['user_created_request'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_UPDATED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['user_updated_full_name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_UPDATED_TIME');?></label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_request']);?></label></th>
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
            </thead>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="row show-grid">
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="21" class="text-center text-danger danger">Order Items</th>
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
                    <?php
                    if(!($CI->locations['territory_id']>0))
                    {
                        ?>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_STOCK_AVAILABLE'); ?> (<?php echo $CI->lang->line('LABEL_KG');?>)</th>
                    <?php
                    }
                    ?>
                    <th colspan="2" class="text-center" style="width: 300px;"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER'); ?></th>
                </tr>
                <tr>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th style="width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                </tr>
                </thead>
                <tbody id="items_container">
                <?php
                $quantity_total_request=0;
                $quantity_total_request_kg=0;
                foreach($items as $index=>$value)
                {
                    if($value['pack_size_id']==0)
                    {
                        $quantity_request_kg=$value['quantity_request'];
                    }
                    else
                    {
                        $quantity_request_kg=(($value['quantity_request']*$value['pack_size'])/1000);
                    }
                    $quantity_total_request+=$value['quantity_request'];
                    $quantity_total_request_kg+=$quantity_request_kg;
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
                            <label class="control-label quantity_max_transferable" id="quantity_max_transferable_<?php echo $index+1;?>">
                                <?php
                                echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['quantity_max_transferable'],3,'.',''):'0.000';
                                ?>
                            </label>
                        </td>
                        <?php
                        if(!($CI->locations['territory_id']>0))
                        {
                            ?>
                            <td class="text-right">
                                <label class="control-label stock_available" id="stock_available_id_<?php echo $index+1;?>">
                                    <?php
                                    echo isset($two_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($two_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available'],3,'.',''):'0.000';
                                    ?>
                                </label>
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
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <?php
                    $quantity_total_colspan=8;
                    if(!($CI->locations['territory_id']>0))
                    {
                        $quantity_total_colspan+=1;
                    }
                    ?>
                    <th colspan="<?php echo $quantity_total_colspan?>" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_request;?></label></th>
                    <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_request_kg,3,'.','');?></label></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>