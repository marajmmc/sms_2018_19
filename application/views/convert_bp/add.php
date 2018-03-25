<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
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
                    <label class="control-label pull-right">Date Convert<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_convert]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_convert']);?>"/>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="crop_id" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <?php
                        foreach($crops as $crop)
                        {
                            ?>
                            <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="crop_type_id" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="variety_id_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="variety_id" name="item[variety_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="warehouse_id_source_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Source Warehouse<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="warehouse_id_source" name="item[warehouse_id_source]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="current_stock_bulk_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Current Bulk Stock (KG)</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="current_stock_bulk"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_convert_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Convert Quantity (KG)<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[quantity_convert]" id="quantity_convert" class="form-control float_type_positive" value=""/>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="warehouse_id_destination_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Destination Warehouse<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="warehouse_id_destination" name="item[warehouse_id_destination]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <?php
                        foreach($destination_warehouses as $destination_warehouse)
                        {
                            ?>
                            <option value="<?php echo $destination_warehouse['value']?>"><?php echo $destination_warehouse['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="pack_size_id_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="pack_size_id" name="item[pack_size_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_packet_expected_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Expected Packet Quantity</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="quantity_packet_expected" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_packet_actual_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Actual Packet Quantity<span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input id="quantity_packet_actual" type="text" data-master-foil-per-pack="0" data-common-foil-per-pack="0" data-sticker-per-pack="0" name="item[quantity_packet_actual]" id="quantity_pack_actual_id" class="form-control float_type_positive" value=""/>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="current_stock_master_foil_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Current Stock Master Foil (KG):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="current_stock_master_foil" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_master_foil_expected_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Expected Master Foil (KG):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="quantity_master_foil_expected" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_master_foil_actual_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Actual Master Foil (KG):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[quantity_master_foil_actual]" id="quantity_master_foil_actual" class="form-control float_type_positive" value=""/>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="current_stock_foil_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Current Stock Common Foil (KG):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="current_stock_foil" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_foil_expected_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Expected Common Foil (KG):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="quantity_foil_expected" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_foil_actual_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Actual Common Foil (KG):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[quantity_foil_actual]" id="quantity_foil_actual" class="form-control float_type_positive" value=""/>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="current_stock_sticker_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Current Stock Sticker(pcs):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="current_stock_sticker" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_sticker_expected_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Expected Sticker(pcs):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label id="quantity_sticker_expected" class="control-label"></label>
                </div>
            </div>
            <div style="display: none;" class="row show-grid" id="quantity_sticker_actual_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Actual Sticker(pcs):</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[quantity_sticker_actual]" id="quantity_sticker_actual" class="form-control float_type_positive" value=""/>
                </div>
            </div>
            <div class="row show-grid" id="remarks_id_container">
                <div class="col-xs-4">
                    <label for="remarks" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <textarea name="item[remarks]" id="remarks_id" class="form-control"><?php echo $item['remarks'] ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    function calculate_raw(quantity_packet_actual,master=0,foil=0,sticker=0)
    {
        $('#current_stock_master_foil_container').hide();
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_actual_container').hide();


        $('#current_stock_foil_container').hide();
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_actual_container').hide();


        $('#current_stock_sticker_container').hide();
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_actual_container').hide();
        if(master>0)
        {
            $('#current_stock_master_foil_container').show();
            $('#quantity_master_foil_expected_container').show();
            $('#quantity_master_foil_actual_container').show();
            var total_master=number_format(quantity_packet_actual*master/1000,3,'.','');
            $('#quantity_master_foil_expected').html(total_master);
            $('#quantity_master_foil_actual').val(total_master);
        }
        else
        {
            $('#current_stock_foil_container').show();
            $('#quantity_foil_expected_container').show();
            $('#quantity_foil_actual_container').show();
            var total_foil=number_format(quantity_packet_actual*foil/1000,3,'.','');
            $('#quantity_foil_expected').html(total_foil);
            $('#quantity_foil_actual').val(total_foil);

            $('#current_stock_sticker_container').show();
            $('#quantity_sticker_expected_container').show();
            $('#quantity_sticker_actual_container').show();
            var total_sticker=quantity_packet_actual*sticker;
            $('#quantity_sticker_expected').html(total_sticker);
            $('#quantity_sticker_actual').val(total_sticker);

        }



    }
jQuery(document).ready(function()
{
    system_preset({controller:'<?php echo $CI->router->class; ?>'});

    $(".datepicker").datepicker({dateFormat : display_date_format});

    $(document).off('change','#crop_id');
    $(document).on("change","#crop_id",function()
    {
        $("#crop_type_id").val('');
        $('#crop_type_id_container').hide();
        $("#variety_id").val('');
        $('#variety_id_container').hide();
        $('#warehouse_id_source_container').hide();
        $("#current_stock_bulk").html('');
        $("#current_stock_bulk_container").hide();
        $("#quantity_convert").val('');
        $("#quantity_convert_container").hide();
        $("#warehouse_id_destination").val('');
        $("#warehouse_id_destination_container").hide();
        $("#pack_size_id").val('');
        $('#pack_size_id_container').hide();
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();

        $('#current_stock_master_foil_container').hide();
        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');


        var crop_id=$('#crop_id').val();
        $("#warehouse_id_source").val('');
        if(crop_id>0)
        {
            $('#crop_type_id_container').show();
            $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
        }
    });

    $(document).off('change','#crop_type_id');
    $(document).on("change","#crop_type_id",function()
    {
        $("#variety_id").val('');
        $('#variety_id_container').hide();
        $("#warehouse_id_source").val('');
        $('#warehouse_id_source_container').hide();
        $("#current_stock_bulk").html('');
        $("#current_stock_bulk_container").hide();
        $("#quantity_convert").val('');
        $("#quantity_convert_container").hide();
        $("#warehouse_id_destination").val('');
        $("#warehouse_id_destination_container").hide();
        $("#pack_size_id").val('');
        $('#pack_size_id_container').hide();
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();
        $('#current_stock_master_foil_container').hide();
        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');
        var crop_type_id=$('#crop_type_id').val();
        if(crop_type_id>0)
        {
            $('#variety_id_container').show();
            $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
        }
    });

    $(document).off('change','#variety_id');
    $(document).on("change","#variety_id",function()
    {
        $("#warehouse_id_source").val('');
        $('#warehouse_id_source_container').hide();
        $("#current_stock_bulk").html('');
        $("#current_stock_bulk_container").hide();
        $("#quantity_convert").val('');
        $("#quantity_convert_container").hide();
        $("#warehouse_id_destination").val('');
        $("#warehouse_id_destination_container").hide();
        $("#pack_size_id").val('');
        $('#pack_size_id_container').hide();
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();

        $('#current_stock_master_foil_container').hide();
        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');
        var variety_id=$('#variety_id').val();
        if(variety_id>0)
        {
            $('#warehouse_id_source_container').show();
            $.ajax({
                url:"<?php echo site_url($CI->controller_url.'/get_warehouse_source_and_packsize/');?>",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

    $(document).off('change','#warehouse_id_source');
    $(document).on("change","#warehouse_id_source",function()
    {
        $("#current_stock_bulk").html('');
        $("#current_stock_bulk_container").hide();
        $("#quantity_convert").val('');
        $("#quantity_convert_container").hide();
        $("#warehouse_id_destination").val('');
        $("#warehouse_id_destination_container").hide();
        $("#pack_size_id").val('');
        $('#pack_size_id_container').hide();
        var variety_id=$('#variety_id').val();
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();

        $('#current_stock_master_foil_container').hide();
        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');
        var pack_size_id=0;
        var warehouse_id_source=$('#warehouse_id_source').val();
        if(warehouse_id_source>0)
        {
            $("#current_stock_bulk_container").show();
            $("#quantity_convert_container").show();

            $.ajax({
                url:"<?php echo site_url('/common_controller/get_current_stock/');?>",
                type: 'POST',
                datatype: "JSON",
                data:{
                    warehouse_id:warehouse_id_source,
                    pack_size_id:pack_size_id,
                    variety_id:variety_id,
                    html_container_id:'#current_stock_bulk'
                },
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

    $(document).off('input','#quantity_convert');
    $(document).on("input","#quantity_convert",function()
    {
        $("#warehouse_id_destination").val('');
        $("#warehouse_id_destination_container").hide();
        $("#pack_size_id").val('');
        $('#pack_size_id_container').hide();
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();
        $('#current_stock_master_foil_container').hide();

        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');
        var convert_quantity=parseFloat($('#quantity_convert').val());

        if(convert_quantity>0)
        {
            $('#warehouse_id_destination_container').show();

        }


    });

    $(document).off('change','#warehouse_id_destination');
    $(document).on("change","#warehouse_id_destination",function()
    {
        $("#pack_size_id").val('');
        $('#pack_size_id_container').hide();
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();

        $('#current_stock_master_foil_container').hide();
        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');
        var warehouse_id_destination=$('#warehouse_id_destination').val();
        if(warehouse_id_destination>0)
        {
            $('#pack_size_id_container').show();
        }
    });

    $(document).off('change','#pack_size_id');
    $(document).on("change","#pack_size_id",function()
    {
        $('#quantity_packet_expected').html('');
        $('#quantity_packet_expected_container').hide();
        $('#quantity_packet_actual').val('');
        $('#quantity_packet_actual_container').hide();

        $('#current_stock_master_foil_container').hide();
        $('#current_stock_master_foil').html('');
        $('#quantity_master_foil_expected_container').hide();
        $('#quantity_master_foil_expected').html('');
        $('#quantity_master_foil_actual_container').hide();
        $('#quantity_master_foil_actual').val('');

        $('#current_stock_foil_container').hide();
        $('#current_stock_foil').html('');
        $('#quantity_foil_expected_container').hide();
        $('#quantity_foil_expected').html('');
        $('#quantity_foil_actual_container').hide();
        $('#quantity_foil_actual').val('');

        $('#current_stock_sticker_container').hide();
        $('#current_stock_sticker').html('');
        $('#quantity_sticker_expected_container').hide();
        $('#quantity_sticker_expected').html('');
        $('#quantity_sticker_actual_container').hide();
        $('#quantity_sticker_actual').val('');
        var variety_id=$('#variety_id').val();
        var pack_size_id=$('#pack_size_id').val();
        var convert_quantity=$('#quantity_convert').val();
        var pack_size=parseFloat($('option:selected', $("#pack_size_id")).html());

        if(pack_size_id>0)
        {
            $.ajax({
                url:"<?php echo site_url($CI->controller_url.'/check_variety_raw_config/');?>",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id,pack_size_id:pack_size_id},
                success: function (data, status)
                {
                    if(data['masterfoil']!==undefined)
                    {
                        if((data['masterfoil']>0)||((data['foil']>0)&&(data['sticker']>0)))
                        {
                            $('#quantity_packet_expected_container').show();
                            $('#quantity_packet_actual_container').show();

                            $('#quantity_packet_actual').attr('data-master-foil-per-pack',data['masterfoil']);
                            $('#quantity_packet_actual').attr('data-common-foil-per-pack',data['foil']);
                            $('#quantity_packet_actual').attr('data-sticker-per-pack',data['sticker']);
                            var quantity_packet_expected=Math.floor(parseFloat(convert_quantity*1000/pack_size).toFixed(3));
                            $('#quantity_packet_expected').html(quantity_packet_expected);
                            $('#quantity_packet_actual').val(quantity_packet_expected);
                            $('#current_stock_master_foil').html((data['current_stock_master']));
                            $('#current_stock_foil').html((data['current_stock_foil']));
                            $('#current_stock_sticker').html((data['current_stock_sticker']));
                            calculate_raw(quantity_packet_expected,data['masterfoil'],data['foil'],data['sticker']);

                        }
                    }
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });


    $(document).off('input','#quantity_packet_actual');
    $(document).on("input","#quantity_packet_actual",function()
    {
        var master=$('#quantity_packet_actual').attr('data-master-foil-per-pack');
        var foil=$('#quantity_packet_actual').attr('data-common-foil-per-pack');
        var sticker=$('#quantity_packet_actual').attr('data-sticker-per-pack');
        calculate_raw($(this).val(),master,foil,sticker);
    });
});
</script>