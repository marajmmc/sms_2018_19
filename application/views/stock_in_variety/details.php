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
    if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line("ACTION_PRINT"),
            'onClick'=>"window.print()"
        );
    }
}

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget ">
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
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_stock_in']);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PURPOSE');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['purpose'];?></label></th>
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

    <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                <th class="text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($stock_in_varieties as $index=>$si_variety)
            {
                ?>
                <tr>
                    <td><?php echo $index+1;?></td>
                    <td>
                        <label><?php echo $si_variety['crop_name']; ?></label>
                    </td>
                    <td>
                        <label><?php echo $si_variety['crop_type_name']; ?></label>
                    </td>
                    <td>
                        <label><?php echo $si_variety['variety_name']; ?></label>
                    </td>
                    <td>
                        <label><?php if($si_variety['pack_size_id']==0){echo 'Bulk';}else{echo $si_variety['pack_size'];} ?></label>
                    </td>
                    <td>
                        <label><?php echo $si_variety['ware_house_name']; ?></label>
                    </td>

                    <td class="text-right">
                        <label><?php echo $si_variety['quantity']; ?></label>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="clearfix"></div>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>