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
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?><span> :</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label><?php echo $item['consignment_name'];?></label>
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
                            <th rowspan="2" style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                            <th rowspan="2" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                            <th rowspan="2" style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE'); ?></th>
                            <th colspan="2" style="min-width: 100px; text-align: center">Actual</th>
                            <th colspan="2" style="min-width: 100px; text-align: center">Receive</th>
                        </tr>
                        <tr>
                            <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                            <th style="min-width: 100px;">KG</th>

                            <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?></th>
                            <th style="min-width: 100px;">KG</th>

                        </tr>
                        </thead>
                        <tbody id="items_container">
                        <?php
                        foreach($items as $index=>$item)
                        {
                            ?>
                            <tr>
                                <td><label><?php echo $item['variety_name'];?></label></td>
                                <td>
                                    <label><?php echo $item['pack_size_name'];?></label>
                                    <input type="hidden" id="quantity_type_id_<?php echo $index+1?>" data-current-id="<?php echo $index+1;?>" value="<?php echo $item['pack_size_name']; ?>" class="quantity_type_id">
                                </td>
                                <td>
                                    <select class="form-control warehouse_id">
                                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($warehouses as $warehouse)
                                        {?>
                                            <option value="<?php echo $warehouse['value']?>"><?php echo $warehouse['text'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>

                                </td>
                                <td><label><?php echo $item['quantity_order'];?></label></td>
                                <td><label><?php echo $item['total_quantity_in_kg'];?></label></td>
                                <td>
                                    <input type="text" value="" class="form-control float_type_positive quantity" id="quantity_id_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="varieties[<?php echo $index+1;?>][quantity_order]">
                                </td>
                                <td><span id="total_quantity_kg_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>"><label></label></span></td>

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
        <input type="hidden" id="id" name="lc_id" value="<?php echo $lc_id; ?>" />

</div>
<div class="clearfix"></div>
</form>

<script type="text/javascript">
jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});

    $(document).off("input", ".quantity");
    $(document).off("input", ".quantity_type");

    //////// conversion with KG from GM without bulk.
    $(document).on("input",".quantity",function()
    {
        var current_id = $(this).attr("data-current-id");
        var total_quantity=0;
        $("#total_quantity_kg_"+current_id).html('');
        $("#total_quantity_kg_"+current_id).html('0.00');
        var pack_size=$("#quantity_type_id_"+current_id).val();
        if(pack_size=='Bulk')
        {
            total_quantity=parseFloat($(this).val())
        }
        else
        {
            total_quantity=parseFloat((pack_size*$(this).val())/1000)
        }
        $("#total_quantity_kg_"+current_id).html(number_format(total_quantity,3));
    });
});
</script>

