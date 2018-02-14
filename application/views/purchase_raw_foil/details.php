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

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['date_receive']; ?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SUPPLIER_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['date_receive']; ?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="current_stock_id" class="control-label pull-right">Current Stock</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['current_stock'];?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHALLAN_NUMBER');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['challan_number'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CHALLAN');?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_challan']);?>
            </div>
        </div>
    </div>

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Created Time :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date_time($item['date_created']);?>
            </div>
        </div>
    </div>

    <div class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Created By :</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo $item['created_by'];?>
            </div>
        </div>
    </div>

    <?php if($item['date_updated']){?>
        <div class="row show-grid">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Updated Time :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo System_helper::display_date_time($item['date_updated']);?>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Updated By :</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php echo $item['updated_by'];?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="number_of_reel" class="control-label pull-right"><?php echo $this->lang->line('LABEL_NUMBER_OF_REEL');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['number_of_reel'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="quantity_supply" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_SUPPLY');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['quantity_supply'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="quantity_receive" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_RECEIVE');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['quantity_receive'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="price_unit_tk" class="control-label pull-right">Price Unit (Tk):</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo number_format($item['price_unit_tk'],2);?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Total (Tk) :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo number_format(($item['quantity_receive']*$item['price_unit_tk']),2);?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['remarks'] ?>
        </div>
    </div>
</div>

    <div class="clearfix"></div>