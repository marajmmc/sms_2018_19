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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BARCODE');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo Barcode_helper::get_barcode_stock_out($item['id']);?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_stock_out']);?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_PURPOSE'); ?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['purpose']; ?>
            </div>
        </div>
        <?php if($item['customer_name']){?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DIVISION_NAME'); ?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['division_name']; ?>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_ZONE_NAME'); ?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['zone_name']; ?>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_TERRITORY_NAME'); ?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['territory_name']; ?>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DISTRICT_NAME'); ?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['district_name']; ?>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_OUTLET_NAME'); ?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['outlet_name']; ?>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label for="purpose" class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_NAME'); ?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['customer_name']; ?>
                </div>
            </div>
        <?php } ?>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $this->lang->line('LABEL_CREATED_BY');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['created_by'];?>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CREATED_TIME');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date_time($item['date_created']);?>
            </div>
        </div>
        <?php if($item['updated_by']){?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label for="remarks" class="control-label pull-right"><?php echo $this->lang->line('LABEL_UPDATED_BY');?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['updated_by'];?>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label for="remarks" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_UPDATED_TIME');?> :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo System_helper::display_date_time($item['date_updated']);?>
                </div>
            </div>
        <?php } ?>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?> :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['remarks']; ?>
            </div>
        </div>

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
                foreach($stock_out_varieties as $index=>$so_variety)
                {
                    ?>
                    <tr>
                        <td><?php echo $index+1;?></td>
                        <td>
                            <label><?php echo $so_variety['crop_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $so_variety['crop_type_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $so_variety['variety_name']; ?></label>
                        </td>
                        <td>
                            <label><?php if($so_variety['pack_size_id']==0){echo 'Bulk';}else{echo $so_variety['pack_size'];} ?></label>
                        </td>
                        <td>
                            <label><?php echo $so_variety['ware_house_name']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $so_variety['quantity']; ?></label>
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
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>