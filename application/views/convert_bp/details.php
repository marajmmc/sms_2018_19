<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action3']) && ($CI->permissions['action3']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DELETE"),
        'data-message-confirm'=>'Are you sure to Delete this data?',
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
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
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['user_created_full_name'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME');?></label></th>
                <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></th>
            </tr>
            <?php
            if($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['user_updated_full_name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?></label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ID');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo Barcode_helper::get_barcode_convert_bulk_to_packet($item['id']);?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['crop_name']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CONVERT');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo System_helper::display_date($item['date_convert']);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['crop_type_name']?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['variety_name']?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_SOURCE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['warehouse_name_source']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right">Convert <?php echo $CI->lang->line('LABEL_QUANTITY');?> (<?php echo $CI->lang->line('LABEL_KG');?>)</label></th>
                <th class="bg-danger header_value"><label class="control-label"><?php echo $item['quantity_convert']?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_DESTINATION');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['warehouse_name_destination']?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?></label></th>
                <th class="bg-danger header_value"><label class="control-label"><?php echo $item['pack_size']?></label></th>
            </tr>
            <?php
            if($item['remarks'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <?php
            if((isset($CI->permissions['action3']) && ($CI->permissions['action3']==1)))
            {
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Delete Reason</label></th>
                    <th class=" header_value" colspan="3">
                        <form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/delete');?>" method="post">
                            <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
                            <div class="row show-grid" id="remarks_id_container">
                                <textarea name="item[remarks_delete]" id="remarks_delete" class="form-control"></textarea>
                            </div>
                        </form>
                    </th>
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
                    <th class="widget-header text-center" colspan="30">Convert Details</th>
                </tr>
                <tr>
                    <th rowspan="2" class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th colspan="2" class="text-center">Quantity (Packet)</th>
                    <th colspan="2" class="text-center">Master Foil</th>
                    <th colspan="2" class="text-center">Common Foil</th>
                    <th colspan="2" class="text-center">Sticker</th>
                </tr>
                <tr>
                    <th class="text-right">Expected</th>
                    <th class="text-right">Actual</th>
                    <th class="text-right">Expected (KG)</th>
                    <th class="text-right">Actual (KG)</th>
                    <th class="text-right">Expected (KG)</th>
                    <th class="text-right">Actual (KG)</th>
                    <th class="text-right">Expected (pcs)</th>
                    <th class="text-right">Actual (pcs)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-right">1</td>
                    <td class="text-right"><?php echo $item['quantity_packet_expected']?></td>
                    <td class="text-right"><?php echo $item['quantity_packet_actual']?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_master_foil_expected'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_master_foil_actual'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_foil_expected'],3,'.','');?></td>
                    <td class="text-right"><?php echo number_format($item['quantity_foil_actual'],3,'.','');?></td>
                    <td class="text-right"><?php echo $item['quantity_sticker_expected']?></td>
                    <td class="text-right"><?php echo $item['quantity_sticker_actual']?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});

});
</script>