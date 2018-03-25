<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
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
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_STOCK_OUT');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[date_stock_out]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_stock_out']);?>"/>
        </div>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label for="purpose" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE'); ?></label>
    </div>
    <?php if($item['purpose']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['purpose']; ?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="purpose" name="item[purpose]" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                <option value="<?php echo $CI->config->item('system_purpose_variety_short_inventory');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_short_inventory')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_OUT_SHORT_INVENTORY');?></option>
                <option value="<?php echo $CI->config->item('system_purpose_variety_rnd');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_rnd')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_OUT_RND');?></option>
                <option value="<?php echo $CI->config->item('system_purpose_variety_sample');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_sample')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_OUT_SAMPLE_PURPOSE');?></option>
                <option value="<?php echo $CI->config->item('system_purpose_variety_demonstration');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_demonstration')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_OUT_DEMONSTRATION');?></option>
                <option value="<?php echo $CI->config->item('system_purpose_variety_delivery_excess');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_delivery_excess')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_OUT_DELIVERY_EXCESS');?></option>
            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['division_id'])){echo 'display:none';} ?>" class="row show-grid" id="division_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="division_id" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($divisions as $division)
            {?>
                <option value="<?php echo $division['value']?>" <?php if($division['value']==$item['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!($item['zone_id'])){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="zone_id" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($zones as $zone)
            {?>
                <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$item['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!($item['territory_id'])){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="territory_id" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($territories as $territory)
            {?>
                <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$item['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!($item['district_id'])){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="district_id" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($districts as $district)
            {?>
                <option value="<?php echo $district['value']?>" <?php if($district['value']==$item['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!($item['customer_id'])){echo 'display:none';} ?>" class="row show-grid" id="customer_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="customer_id" name="item[customer_id]" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($customers as $customer)
            {?>
                <option value="<?php echo $customer['value']?>" <?php if($customer['value']==$item['customer_id']){ echo "selected";}?>><?php echo $customer['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div class="row show-grid" style="<?php if(!($item['customer_name'])){echo 'display:none';} ?>" id="customer_name_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item[customer_name]" id="customer_name" class="form-control" value="<?php echo $item['customer_name']?>"/>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea name="item[remarks]" id="remarks" class="form-control"><?php echo $item['remarks'] ?></textarea>
    </div>
</div>

<div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
            <th style="min-width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
            <th style="min-width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
            <th style="min-width: 150px;" class="text-right"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
            <th style="min-width: 150px;"><?php echo $CI->lang->line('ACTION'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($stock_out_varieties as $index=>$so_variety)
        {
            ?>
            <tr>
                <td>
                    <label><?php echo $so_variety['crop_name']; ?></label>
                </td>
                <td>
                    <label><?php echo $so_variety['crop_type_name']; ?></label>
                </td>
                <td>
                    <label><?php echo $so_variety['variety_name']; ?></label>
                    <input type="hidden"  id="variety_id<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $so_variety['variety_id']; ?>" />
                </td>
                <td class="text-right">
                    <label><?php if($so_variety['pack_size_id']==0){echo 'Bulk';}else{echo $so_variety['pack_size'];} ?></label>
                    <input type="hidden" id="pack_size_id<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][pack_size_id]" value="<?php echo $so_variety['pack_size_id']; ?>" />

                </td>
                <td>
                    <label><?php echo $so_variety['ware_house_name']; ?></label>
                    <input type="hidden" id="warehouse_id<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][warehouse_id]" value="<?php echo $so_variety['warehouse_id']; ?>" />
                </td>
                <td class="text-right">
                    <label><?php $current_stock=Stock_helper::get_variety_stock(array($so_variety['variety_id'])); if(isset($current_stock)){echo $current_stock[$so_variety['variety_id']][$so_variety['pack_size_id']][$so_variety['warehouse_id']]['current_stock'];}else{echo 0;}?></label>
                </td>
                <td class="text-right">
                    <input type="text" id="quantity<?php echo $index+1;?>" value="<?php echo $so_variety['quantity']; ?>" class="form-control text-right float_type_positive quantity" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity]">
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>
<div class="row show-grid">
    <div class="col-xs-4">

    </div>
    <div class="col-xs-4">
        <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($stock_out_varieties);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
    </div>
    <div class="col-xs-4">

    </div>
</div>

</div>

<div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <select class="form-control crop_id">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td>
                <div style="display: none;" class="crop_type_id_container">
                    <select class="form-control crop_type_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="variety_id_container">
                    <select class="form-control variety_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="pack_size_id_container">
                    <select class="form-control pack_size_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <option value="0">Bulk</option>
                        <?php
                        foreach($packs as $pack)
                        {?>
                            <option value="<?php echo $pack['value']?>"><?php echo $pack['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="warehouse_id_container">
                    <select class="form-control warehouse_id">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <?php
                        foreach($warehouses as $warehouse)
                        {?>
                            <option value="<?php echo $warehouse['value']?>"><?php echo $warehouse['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td class="text-right">
                <label class="stock_current">&nbsp;</label>
            </td>
            <td class="text-right">
                <input type="text" class="form-control text-right quantity float_type_positive" value=""/>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">

jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});

    $(".datepicker").datepicker({dateFormat : display_date_format});

    $(document).off('change', '#purpose');
    $(document).on('change','#purpose',function()
    {
        $('#division_id').val('');
        $('#zone_id').val('');
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#customer_name').val('');
        var purpose=$('#purpose').val();
        $('#division_id_container').hide();
        $('#zone_id_container').hide();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#customer_name_container').hide();
        if(purpose=='<?php echo $CI->config->item('system_purpose_variety_sample'); ?>')
        {
            $('#division_id_container').show();
        }
        if(purpose=='<?php echo $CI->config->item('system_purpose_variety_demonstration'); ?>')
        {
            $('#division_id_container').show();
        }
    });

    $(document).off('change', '#division_id');
    $(document).on('change','#division_id',function()
    {
        $('#zone_id').val('');
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#customer_name').val('');
        var division_id=$('#division_id').val();
        $('#zone_id_container').hide();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#customer_name_container').hide();
        if(division_id>0)
        {
            if(system_zones[division_id]!==undefined)
            {
                $('#zone_id_container').show();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
            }
        }
    });

    $(document).off('change', '#zone_id');
    $(document).on('change','#zone_id',function()
    {
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#customer_name').val('');
        var zone_id=$('#zone_id').val();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#customer_name_container').hide();
        if(zone_id>0)
        {
            if(system_territories[zone_id]!==undefined)
            {
                $('#territory_id_container').show();
                $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
            }
        }
    });

    $(document).off('change', '#territory_id');
    $(document).on('change','#territory_id',function()
    {
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#customer_name').val('');
        var territory_id=$('#territory_id').val();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#customer_name_container').hide();
        if(territory_id>0)
        {
            if(system_districts[territory_id]!==undefined)
            {
                $('#district_id_container').show();
                $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
            }

        }
    });

    $(document).off('change', '#district_id');
    $(document).on('change','#district_id',function()
    {
        $('#customer_id').val('');
        $('#customer_name').val('');
        var district_id=$('#district_id').val();
        $('#customer_id_container').hide();
        $('#customer_name_container').hide();
        if(district_id>0)
        {
            if(system_outlets[district_id]!==undefined)
            {
                $('#customer_id_container').show();
                $('#customer_id').html(get_dropdown_with_select(system_outlets[district_id]));
                $('#customer_name_container').show();
            }
        }
    });

    $(document).off('change', '#customer_id');

    $(document).off("click", ".system_button_add_more");
    $(document).on("click", ".system_button_add_more", function(event)
    {
        var current_id=parseInt($(this).attr('data-current-id'));
        current_id=current_id+1;
        $(this).attr('data-current-id',current_id);
        var content_id='#system_content_add_more table tbody';

        $(content_id+' .crop_id').attr('id','crop_id_'+current_id);
        $(content_id+' .crop_id').attr('data-current-id',current_id);

        $(content_id+' .crop_type_id').attr('id','crop_type_id_'+current_id);
        $(content_id+' .crop_type_id').attr('data-current-id',current_id);
        $(content_id+' .crop_type_id_container').attr('id','crop_type_id_container_'+current_id);

        $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
        $(content_id+' .variety_id').attr('data-current-id',current_id);
        $(content_id+' .variety_id').attr('name','items['+current_id+'][variety_id]');
        $(content_id+' .variety_id_container').attr('id','variety_id_container_'+current_id);

        $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
        $(content_id+' .pack_size_id').attr('data-current-id',current_id);
        $(content_id+' .pack_size_id').attr('name','items['+current_id+'][pack_size_id]');
        $(content_id+' .pack_size_id_container').attr('id','pack_size_id_container_'+current_id);

        $(content_id+' .warehouse_id').attr('id','warehouse_id_'+current_id);
        $(content_id+' .warehouse_id').attr('data-current-id',current_id);
        $(content_id+' .warehouse_id').attr('name','items['+current_id+'][warehouse_id]');
        $(content_id+' .warehouse_id_container').attr('id','warehouse_id_container_'+current_id);

        $(content_id+' .stock_current').attr('id','stock_current_'+current_id);
        $(content_id+' .stock_current').attr('data-current-id',current_id);

        $(content_id+' .quantity').attr('id','quantity_'+current_id);
        $(content_id+' .quantity').attr('data-current-id',current_id);
        $(content_id+' .quantity').attr('name','items['+current_id+'][quantity]');

        var html=$(content_id).html();
        $("#order_items_container tbody").append(html);
        $(content_id+' .crop_id').removeAttr('id');
        $(content_id+' .crop_type_id').removeAttr('id');
        $(content_id+' .crop_type_id_container').removeAttr('id');
        $(content_id+' .variety_id').removeAttr('id');
        $(content_id+' .variety_id_container').removeAttr('id');
        $(content_id+' .pack_size_id').removeAttr('id');
        $(content_id+' .pack_size_id_container').removeAttr('id');
        $(content_id+' .warehouse_id').removeAttr('id');
        $(content_id+' .warehouse_id_container').removeAttr('id');
        $(content_id+' .stock_current').removeAttr('id');
        $(content_id+' .quantity').removeAttr('id');

    });

    $(document).off("click", ".system_button_add_delete");
    $(document).on("click", ".system_button_add_delete", function(event)
    {
        $(this).closest('tr').remove();
    });

    $(document).off("change",".crop_id");
    $(document).on("change",".crop_id",function()
    {
        var active_id=$(this).attr('data-current-id');
        $("#crop_type_id_"+active_id).val("");
        $("#variety_id_"+active_id).val("");
        $("#pack_size_id_"+active_id).val("");
        $("#warehouse_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var crop_id=$('#crop_id_'+active_id).val();
        $('#variety_id_container_'+active_id).hide();
        $('#pack_size_id_container_'+active_id).hide();
        $('#warehouse_id_container_'+active_id).hide();
        if(crop_id>0)
        {
            $('#crop_type_id_container_'+active_id).show();
            $('#crop_type_id_'+active_id).html(get_dropdown_with_select(system_types[crop_id]));
        }
        else
        {
            $('#crop_type_id_container_'+active_id).hide();
        }
    });

    $(document).off("change",".crop_type_id");
    $(document).on("change",".crop_type_id",function()
    {
        var active_id=$(this).attr('data-current-id');

        $("#variety_id_"+active_id).val("");
        $("#pack_size_id_"+active_id).val("");
        $("#warehouse_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var crop_type_id=$('#crop_type_id_'+active_id).val();
        $('#pack_size_id_container_'+active_id).hide();
        $('#warehouse_id_container_'+active_id).hide();
        if(crop_type_id>0)
        {
            $('#variety_id_container_'+active_id).show();
            $('#variety_id_'+active_id).html(get_dropdown_with_select(system_varieties[crop_type_id]));
        }
        else
        {
            $('#variety_id_container_'+active_id).hide();
        }
    });

    $(document).off("change",".variety_id");
    $(document).on("change",".variety_id",function()
    {
        var active_id=$(this).attr('data-current-id');

        $("#pack_size_id_"+active_id).val("");
        $("#warehouse_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var variety_id=$('#variety_id_'+active_id).val();
        $('#warehouse_id_container_'+active_id).hide();

        if(variety_id>0)
        {
            $('#pack_size_id_container_'+active_id).show();
        }
        else
        {
            $('#pack_size_id_container_'+active_id).hide();
        }
    });

    $(document).off("change",".pack_size_id");
    $(document).on("change",".pack_size_id",function()
    {
        var active_id=$(this).attr('data-current-id');

        $("#warehouse_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var pack_size_id=$('#pack_size_id_'+active_id).val();
        if(pack_size_id!="")
        {
            $('#warehouse_id_container_'+active_id).show();
        }
        else
        {
            $('#warehouse_id_container_'+active_id).hide();
        }
    });

    $(document).off("change",".warehouse_id");
    $(document).on("change",".warehouse_id",function()
    {
        var active_id=$(this).attr('data-current-id');

        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var variety_id=$('#variety_id_'+active_id).val();
        var pack_size_id=$('#pack_size_id_'+active_id).val();
        var warehouse_id=$('#warehouse_id_'+active_id).val();
        if(variety_id>0 && pack_size_id!="" && warehouse_id>0)
        {
            $.ajax({
                url: "<?php echo site_url('common_controller/get_current_stock'); ?>",
                type: 'POST',
                datatype: "JSON",
                data:{
                    warehouse_id:warehouse_id,
                    pack_size_id:pack_size_id,
                    variety_id:variety_id,
                    html_container_id:'#stock_current_'+active_id
                },
                success: function (data, status)
                {
                    $("#stock_current_"+active_id).text(data);
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");
                }
            });
        }
    });
});
</script>
