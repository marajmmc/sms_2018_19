<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
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
                <th class=""><label class="control-label"><?php echo Barcode_helper::get_barcode_raw_foil_purchase($item['id']);?></label></th>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SUPPLIER_NAME');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['supplier_name'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_receive']);?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CHALLAN_NUMBER');?></label></th>
                <th class="bg-danger"><label class="control-label"><?php echo $item['challan_number'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CHALLAN');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_challan']);?></label></th>
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
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NUMBER_OF_REEL');?></label></th>
                <th class=" header_value"><label class="control-label"><?php echo $item['number_of_reel'];?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_SUPPLY');?></label></th>
                <th class=""><label class="control-label"><?php echo $item['quantity_supply'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Price Unit (Tk):</label></th>
                <th class=" header_value"><label class="control-label"><?php echo number_format($item['price_unit_tk'],2);?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_RECEIVE');?></label></th>
                <th class=""><label class="control-label"><?php echo $item['quantity_receive'];?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Total Taka</label></th>
                <th class=" header_value" colspan="3"><label class="control-label"><?php echo number_format(($item['quantity_receive']*$item['price_unit_tk']),2);?></label></th>
            </tr>

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
</div>

    <div class="clearfix"></div>