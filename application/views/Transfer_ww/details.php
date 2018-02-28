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
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
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
                <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_transfer_warehouse_to_warehouse($item['id']);?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_TRANSFER');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_transfer']);?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CREATED_BY');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['created_by'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CREATED_TIME');?></label></th>
                <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></th>
            </tr>
            <?php
            if($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_UPDATED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['updated_by'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_UPDATED_TIME');?></label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']);?></label></th>
                </tr>
            <?php
            }
            ?>
            <?php
            if($item['remarks'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks']);?></label></th>
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
                    <th class="widget-header text-center" colspan="30">Product Details</th>
                </tr>
                <tr>
                    <th rowspan="2"style="width: 5px"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2" class="text-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                    <th colspan="2" class="text-center"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                </tr>
                <tr>
                    <th class="text-left" >Source</th>
                    <th class="text-left" >Destination</th>
                    <th class="text-right" ><?php echo $CI->lang->line('LABEL_PACK'); ?></th>
                    <th class="text-right" ><?php echo $CI->lang->line('LABEL_KG'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td><?php echo $item['crop_name']; ?></td>
                    <td><?php echo $item['crop_type_name']; ?></td>
                    <td><?php echo $item['variety_name']; ?></td>
                    <td class="text-right"><?php if($item['pack_size_id']==0){echo 'Bulk';}else{echo $item['pack_size'];} ?></td>
                    <td><?php echo $item['warehouse_name_source']; ?></td>
                    <td><?php echo $item['warehouse_name_destination']; ?></td>
                    <td class="text-right"><label class="control-label"><?php if($item['pack_size_id']==0){echo '-';}else{echo $item['quantity_transfer'];} ?></label></td>
                    <td class="text-right">
                        <?php
                        if($item['pack_size_id']==0)
                        {
                            echo number_format($item['quantity_transfer'],3,'.','');
                        }
                        else
                        {
                            echo number_format((($item['quantity_transfer']*$item['pack_size'])/1000),3,'.','');
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><label class="control-label"><?php echo $this->lang->line('LABEL_TOTAL')?></label></td>
                    <td class="text-right"><label class="control-label"><?php if($item['pack_size_id']==0){echo '-';}else{echo $item['quantity_transfer'];} ?></label></td>
                    <td class="text-right">
                        <label class="control-label">
                            <?php
                            if($item['pack_size_id']==0)
                            {
                                echo number_format($item['quantity_transfer'],3,'.','');
                            }
                            else
                            {
                                echo number_format((($item['quantity_transfer']*$item['pack_size'])/1000),3,'.','');
                            }
                            ?>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>

</div>
<div class="clearfix"></div>
</form>
<script type="text/javascript">

jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});
});
</script>