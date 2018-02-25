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
        <div class="col-md-12">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ID');?></label></th>
                    <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_stock_out($item['id']);?></label></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                <?php
                if($item['purpose']==$this->config->item('system_purpose_variety_demonstration') || $item['purpose']==$this->config->item('system_purpose_variety_sample'))
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DIVISION_NAME');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $item['division_name']; ?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ZONE_NAME');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $item['zone_name']; ?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_TERRITORY_NAME');?></label></th>
                        <th class="header_value"><label class="control-label"><?php echo $item['territory_name'];?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DISTRICT_NAME');?></label></th>
                        <th class="header_value"><label class="control-label"><?php echo $item['district_name'];?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OUTLET_NAME');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $item['outlet_name']; ?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_NAME');?></label></th>
                        <th class="header_value"><label class="control-label"><?php echo $item['customer_name'];?></label></th>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PURPOSE');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['purpose'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_stock_out']);?></label></th>
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
                    <th class="widget-header text-center" colspan="30">Product Details</th>
                </tr>
                <tr>
                    <th rowspan="2" class="text-right" style="width: 30px;"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th rowspan="2" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th rowspan="2" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                    <th rowspan="2" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th rowspan="2" class="text-right" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                    <th rowspan="2" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                    <th colspan="2" class="text-center" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                </tr>
                <tr>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                    <th class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $quantity_kg=0;
                $quantity_total_pkt=0;
                $quantity_total_kg=0;
                foreach($stock_out_varieties as $index=>$so_variety)
                {
                    if($so_variety['pack_size_id']!=0)
                    {
                        $quantity_total_pkt+=$so_variety['quantity'];
                    }
                    if($so_variety['pack_size_id']==0)
                    {
                        $quantity_kg = $so_variety['quantity'];
                    }
                    else
                    {
                        $quantity_kg = (($so_variety['pack_size']*$so_variety['quantity'])/1000);
                    }
                    $quantity_total_kg+=$quantity_kg;
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $index+1;?></td>
                        <td><?php echo $so_variety['crop_name']; ?></td>
                        <td><?php echo $so_variety['crop_type_name']; ?></td>
                        <td><?php echo $so_variety['variety_name']; ?></td>
                        <td class="text-right"><?php if($so_variety['pack_size_id']==0){echo 'Bulk';}else{echo $so_variety['pack_size'];} ?></td>
                        <td><?php echo $so_variety['ware_house_name']; ?></td>
                        <td class="text-right"><?php if($so_variety['pack_size_id']==0){echo '-';}else{echo $so_variety['quantity'];} ?></td>
                        <td class="text-right"><?php  echo number_format($quantity_kg,3,'.','');?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th class="text-right" colspan="6"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                    <th class="text-right"><?php echo $quantity_total_pkt;?></th>
                    <th class="text-right"><?php echo number_format($quantity_total_kg,3,'.','');?></th>
                </tr>
                </tfoot>
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