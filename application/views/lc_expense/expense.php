<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_CLEAR"),
        'id'=>'button_action_clear',
        'data-form'=>'#save_form'
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

$disabled='';
if(!(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)) && $item['id']>0)
{
    $disabled=' disabled';
}
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
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['year_name']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['month_name']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo System_helper::display_date($item['date_opening']);?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo System_helper::display_date($item['date_expected']);?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['principal_name'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['lc_number'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CURRENCY_NAME');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['currency_name'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['consignment_name'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_OTHER_COST_CURRENCY');?><span> :</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['other_cost_currency'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?><span> :</span> </label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['remarks'];?></label>
    </div>
</div>
<div class="panel-group" id="accordion">
        <div class="panel panel-default" id="principal_container">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse" href="#">
                        Details Of LC <?php echo $item['lc_number']?>
                    </a>
                </h4>
            </div>
            <div id="collapse" class="panel-collapse collapse in">

                <div style="overflow-x: auto;" class="row show-grid">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                            <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                            <th style="min-width: 100px;">KG</th>
                            <th style="min-width: 100px;">Unit Price (Currency)</th>
                            <th style="min-width: 150px;">Total Price (Currency)</th>

                        </tr>
                        </thead>
                        <tbody id="items_container">
                        <?php
                        foreach($items as $item)
                        {
                            ?>
                            <tr>
                                <td><label><?php echo $item['variety_name'];?></label></td>
                                <td><label><?php echo $item['pack_size_name'];?></label></td>
                                <td><label><?php echo $item['quantity_order'];?></label></td>
                                <td><label><?php echo $item['total_quantity_in_kg'];?></label></td>
                                <td><label><?php echo $item['price_currency'];?></label></td>
                                <td><label><?php echo $item['total_price_in_currency'];?></label></td>

                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="lc_id" value="<?php echo $item['id']; ?>" />
    <div class="row show-grid">
        <div class="col-xs-6">
            <label class="control-label pull-right"><span style="font-size: 16px">Expense Item</span></label>
        </div>
    </div>
    <?php foreach($items_cost as $item_cost){?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $item_cost['name'];?><span> :</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[<?php echo $item_cost['id']?>]" id="cost_item" class="form-control" value="<?php echo $item_cost['amount']?>"/>
            </div>
        </div>
    <?php } ?>
</div>
<div class="clearfix"></div>
</form>

