<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
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
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_REQUEST');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_request']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['division_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['division_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="division_id" name="division_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($divisions as $division)
                            {?>
                                <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['zone_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['zone_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="zone_id" name="zone_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($zones as $zone)
                            {?>
                                <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$item['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['territory_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['territory_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="territory_id" name="territory_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($territories as $territory)
                            {?>
                                <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$item['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['district_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                <?php
                }
                else
                {
                    if($item['id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $item['district_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="district_id" name="district_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($districts as $district)
                            {?>
                                <option value="<?php echo $district['value']?>" <?php if($district['value']==$item['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($outlets)>0)){echo 'display:none';} ?>" class="row show-grid" id="outlet_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($item['id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $item['outlet_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="outlet_id" name="item[outlet_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <?php
                        foreach($outlets as $outlet)
                        {?>
                            <option value="<?php echo $outlet['value']?>" <?php if($outlet['value']==$item['outlet_id']){ echo "selected";}?>><?php echo $outlet['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_REQUEST');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_request]" id="remarks" class="form-control" ><?php echo $item['remarks_request'];?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="21" class="text-center text-danger danger"><?php echo $CI->lang->line('LABEL_RETURN_ITEMS');?></th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width: 12.5%;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th rowspan="2" style="width: 12.5%;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th rowspan="2" style="width: 12.5%;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th rowspan="2" class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th colspan="2" class="text-center" style="width: 12.5%;"><?php echo $CI->lang->line('LABEL_STOCK_AVAILABLE'); ?> </th>
                        <th colspan="2" class="text-center" style="width: 12.5%;"><?php echo $CI->lang->line('LABEL_QUANTITY_ORDER'); ?></th>
                        <th rowspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <th style="width: 12.5%;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 12.5%;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                        <th style="width: 12.5%;" class="text-right"><?php echo $CI->lang->line('LABEL_PACK');?></th>
                        <th style="width: 12.5%;" class="text-right"><?php echo $CI->lang->line('LABEL_KG');?></th>
                    </tr>
                    </thead>
                    <tbody id="items_container">
                        <?php
                        $quantity_total_request=0;
                        $quantity_total_request_kg=0;
                        foreach($items as $index=>$value)
                        {
                            $quantity_request_kg=(($value['quantity_request']*$value['pack_size'])/1000);
                            $quantity_total_request+=$value['quantity_request'];
                            $quantity_total_request_kg+=$quantity_request_kg;
                            ?>
                            <tr>
                                <td>
                                    <label><?php echo $value['crop_name']; ?></label>
                                </td>
                                <td>
                                    <label><?php echo $value['crop_type_name']; ?></label>
                                </td>
                                <td>
                                    <label><?php echo $value['variety_name']; ?></label>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][variety_id]" value="<?php echo $value['variety_id']; ?>">
                                </td>
                                <td class="text-right">
                                    <label><?php echo $value['pack_size']; ?></label>
                                    <input type="hidden" name="items[<?php echo $index+1;?>][pack_size_id]" id="pack_size_id_<?php echo $index+1;?>" value="<?php echo $value['pack_size_id']; ?>" class="pack_size_id" data-pack-size-name="<?php echo $value['pack_size']; ?>">
                                </td>
                                <td class="text-right">
                                    <label class="control-label stock_available_pkt" id="stock_available_pkt_<?php echo $index+1;?>">
                                        <?php echo isset($tow_variety_info[$value['variety_id']][$value['pack_size_id']])?$tow_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available_pkt']:'0.000'; ?>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <label class="control-label stock_available" id="stock_available_<?php echo $index+1;?>">
                                        <?php echo isset($tow_variety_info[$value['variety_id']][$value['pack_size_id']])?number_format($tow_variety_info[$value['variety_id']][$value['pack_size_id']]['stock_available'],3,'.',''):'0.000'; ?>
                                    </label>
                                </td>
                                <td>
                                    <input type="text" value="<?php echo $value['quantity_request']; ?>" class="form-control integer_type_positive quantity_request" id="quantity_request_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="items[<?php echo $index+1;?>][quantity_request]">
                                </td>
                                <td class="text-right">
                                    <label id="quantity_request_kg_<?php echo $index+1;?>"> <?php echo number_format($quantity_request_kg,3,'.','');?> </label>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="6" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request"> <?php echo $quantity_total_request;?></label></th>
                        <th class="text-right"><label class="control-label" id="quantity_total_request_kg"> <?php echo number_format($quantity_total_request_kg,3,'.','');?></label></th>
                        <th>&nbsp;</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-xs-4">
                    <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($items);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
                </div>
                <div class="col-xs-4">

                </div>
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
                <select class="form-control crop_type_id" style="display: none;">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                </select>
            </td>
            <td>
                <select class="form-control variety_id" style="display: none;">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                </select>
            </td>
            <td>
                <select class="form-control pack_size_id" style="display: none;"  data-new-pack-size="0">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                </select>
            </td>
            <td class="text-right">
                <label class="control-label stock_available_pkt"> </label>
            </td>
            <td class="text-right">
                <label class="control-label stock_available"> </label>
            </td>
            <td class="text-right">
                <input type="text" class="form-control integer_type_positive quantity_request" value="" style="display: none;" />
            </td>
            <td class="text-right">
                <label class="control-label quantity_request_kg"> </label>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<style>
    .quantity_exist_warning
    {
        background-color: red;
        color: #FFFFFF;
    }
</style>
<script>
    <?php
    if(sizeof($tow_variety_info)>0)
    {
        ?>
        var tow_variety_info=JSON.parse('<?php echo json_encode($tow_variety_info);?>');
        <?php
    }
    else
    {
        ?>
        var tow_variety_info={};
        <?php
    }
    ?>
    function calculate_total()
    {
        var quantity_total_request=0;
        var quantity_total_request_kg=0;
        $('#items_container .quantity_request').each(function(index, element)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var quantity_request=parseFloat($(this).val());
            if(isNaN(quantity_request))
            {
                quantity_request=0;
            }
            quantity_total_request+=quantity_request;
            var quantity_request_kg=parseFloat($('#quantity_request_kg_'+current_id).html().replace(/,/g,''));
            if(isNaN(quantity_request_kg))
            {
                quantity_request_kg=0;
            }
            quantity_total_request_kg+=quantity_request_kg;
        });
        $('#quantity_total_request').html(quantity_total_request);
        $('#quantity_total_request_kg').html(number_format((quantity_total_request_kg),3,'.',''));
    }
    $(document).ready(function()
    {
        //console.log(tow_variety_info)
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".datepicker").datepicker({dateFormat : display_date_format});

        /*location*/
        $(document).off('change', '#division_id');
        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#items_container").html('');
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
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            var zone_id=$('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#items_container").html('');
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
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            $('#outlet_id_container').hide();
            $('#district_id_container').hide();
            $("#items_container").html('');
            var territory_id=$('#territory_id').val();
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
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            $("#items_container").html('');
            var district_id=$('#district_id').val();
            $('#outlet_id_container').hide();
            if(district_id>0)
            {
                if(system_outlets[district_id]!==undefined)
                {
                    $('#outlet_id_container').show();
                    $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
                }
            }
        });
        $(document).off('change', '#outlet_id');
        $(document).on('change','#outlet_id',function()
        {
            $("#items_container").html('');
            var outlet_id=$('#outlet_id').val();
            if(outlet_id>0)
            {
                $.ajax({
                    url: '<?php echo site_url($CI->controller_url.'/index/ajax_transfer_ow_variety_info'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{outlet_id:outlet_id},
                    success: function (data, status)
                    {
                        tow_variety_info=data;
                        calculate_total();
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            }
        });

        $(document).off("click", ".system_button_add_more");
        $(document).on("click", ".system_button_add_more", function(event)
        {
            if($("#outlet_id").val()=='')
            {
                alert('Outlet field is required.');
                return false;
            }
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .crop_id').attr('id','crop_id_'+current_id);
            $(content_id+' .crop_id').attr('data-current-id',current_id);

            $(content_id+' .crop_type_id').attr('id','crop_type_id_'+current_id);
            $(content_id+' .crop_type_id').attr('data-current-id',current_id);

            $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
            $(content_id+' .variety_id').attr('data-current-id',current_id);
            $(content_id+' .variety_id').attr('name','items['+current_id+'][variety_id]');

            $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
            $(content_id+' .pack_size_id').attr('data-current-id',current_id);
            $(content_id+' .pack_size_id').attr('name','items['+current_id+'][pack_size_id]');

            $(content_id+' .stock_available_pkt').attr('id','stock_available_pkt_'+current_id);
            $(content_id+' .stock_available_pkt').attr('data-current-id',current_id);

            $(content_id+' .stock_available').attr('id','stock_available_'+current_id);
            $(content_id+' .stock_available').attr('data-current-id',current_id);

            $(content_id+' .quantity_request').attr('id','quantity_request_'+current_id);
            $(content_id+' .quantity_request').attr('data-current-id',current_id);
            $(content_id+' .quantity_request').attr('name','items['+current_id+'][quantity_request]');

            $(content_id+' .quantity_request_kg').attr('id','quantity_request_kg_'+current_id);
            $(content_id+' .quantity_request_kg').attr('data-current-id',current_id);

            var html=$(content_id).html();
            $("#items_container").append(html);
        });

        $(document).off("click", ".system_button_add_delete");
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
            calculate_total();
        });

        $(document).off("change","#items_container .crop_id");
        $(document).on("change","#items_container .crop_id",function()
        {
            var current_id=$(this).attr('data-current-id');
            $("#crop_type_id_"+current_id).val('');
            $("#variety_id_"+current_id).val('');
            $("#pack_size_id_"+current_id).val('');
            $("#stock_available_pkt_"+current_id).html('');
            $("#stock_available_"+current_id).html('');
            $("#quantity_request_"+current_id).val('');
            $("#quantity_request_kg_"+current_id).html('');

            var crop_id=$('#crop_id_'+current_id).val();
            $('#crop_type_id_'+current_id).hide();
            $('#variety_id_'+current_id).hide();
            $('#pack_size_id_'+current_id).hide();
            $('#quantity_request_'+current_id).hide();

            if(crop_id>0 && system_types[crop_id]!=undefined)
            {
                $('#crop_type_id_'+current_id).show();
                $('#crop_type_id_'+current_id).html(get_dropdown_with_select(system_types[crop_id]));
            }
            calculate_total();
        });

        $(document).off("change","#items_container .crop_type_id");
        $(document).on("change","#items_container .crop_type_id",function()
        {
            var current_id=$(this).attr('data-current-id');
            $("#variety_id_"+current_id).val('');
            $("#pack_size_id_"+current_id).val('');
            $("#stock_available_pkt_"+current_id).html('');
            $("#stock_available_"+current_id).html('');
            $("#quantity_request_"+current_id).val('');
            $("#quantity_request_kg_"+current_id).html('');

            var crop_type_id=$('#crop_type_id_'+current_id).val();
            $('#variety_id_'+current_id).hide();
            $('#pack_size_id_'+current_id).hide();
            $('#quantity_request_'+current_id).hide();
            if(crop_type_id>0 && system_varieties[crop_type_id]!=undefined)
            {
                $('#variety_id_'+current_id).show();
                $('#variety_id_'+current_id).html(get_dropdown_with_select(system_varieties[crop_type_id]));
            }
            calculate_total();
        });

        $(document).off("change","#items_container .variety_id");
        $(document).on("change","#items_container .variety_id",function()
        {
            var current_id=$(this).attr('data-current-id');

            $("#stock_available_pkt_"+current_id).html('');
            $("#stock_available_"+current_id).html('');

            $("#pack_size_id_"+current_id).empty('');
            $("#quantity_request_"+current_id).val('');
            $("#quantity_request_"+current_id).hide();
            $("#quantity_request_kg_"+current_id).html('');
            var variety_id=$('#variety_id_'+current_id).val();
            var pack_size=0;
            if(variety_id>0)
            {
                $('#pack_size_id_'+current_id).show();
                $('#pack_size_id_'+current_id).append('<option value="">Select</option>');
                var pack_size_name=0;
                if(tow_variety_info[variety_id]!==undefined)
                {
                    $.each(tow_variety_info[variety_id], function(pack_size_id, pack_size)
                    {
                        if(pack_size_id!=0)
                        {
                            pack_size_name=pack_size['pack_size'];
                        }
                        $('#pack_size_id_'+current_id).append('<option value="'+pack_size_id+'" data-pack-size-name="'+pack_size_name+'" >'+pack_size['pack_size']+'</option>');
                    })
                }
            }
            else
            {
                $('#pack_size_id_'+current_id).hide();
            }
            calculate_total();
        });

        $(document).off("change","#items_container .pack_size_id");
        $(document).on("change","#items_container .pack_size_id",function()
        {
            var current_id=$(this).attr('data-current-id');

            $("#stock_available_pkt_"+current_id).html('');
            $("#stock_available_"+current_id).html('');
            $("#quantity_request_"+current_id).val('');
            $("#quantity_request_"+current_id).hide();
            $("#quantity_request_kg_"+current_id).html('');
            var variety_id=$('#variety_id_'+current_id).val();
            var pack_size_id=$('#pack_size_id_'+current_id).val();
            if(tow_variety_info[variety_id][pack_size_id]!==undefined)
            {
                //console.log(tow_variety_info[variety_id][pack_size_id])
                $("#stock_available_pkt_"+current_id).html(tow_variety_info[variety_id][pack_size_id]['stock_available_pkt']);
                $("#stock_available_"+current_id).html(number_format(tow_variety_info[variety_id][pack_size_id]['stock_available'],3,'.',''));
                $("#quantity_request_"+current_id).show();
            }
            calculate_total();
        });

        $(document).off('input', '#items_container .quantity_request');
        $(document).on('input','#items_container .quantity_request',function()
        {
            var current_id=$(this).attr('data-current-id');
            var quantity_request=parseFloat($(this).val());
            var quantity_request_kg=0;

            var pack_size=parseFloat($("#pack_size_id_"+current_id).attr('data-pack-size-name'));
            if($("#pack_size_id_"+current_id).attr('data-new-pack-size')==0)
            {
                var pack_size=parseFloat($('option:selected', $("#pack_size_id_"+current_id)).attr('data-pack-size-name'));
            }
            quantity_request_kg=parseFloat((pack_size*quantity_request)/1000);

            var stock_available_pkt=parseFloat($('#stock_available_pkt_'+current_id).html().replace(/,/g,''));
            if(isNaN(stock_available_pkt))
            {
                stock_available_pkt=0;
            }
            var stock_available=parseFloat($('#stock_available_'+current_id).html().replace(/,/g,''));
            if(isNaN(stock_available))
            {
                stock_available=0;
            }
            $(this).removeClass('quantity_exist_warning');
            $("#quantity_request_kg_"+current_id).removeClass('quantity_exist_warning');
            $("#stock_available_pkt_"+current_id).removeClass('quantity_exist_warning');
            $("#stock_available_"+current_id).removeClass('quantity_exist_warning');
            if(quantity_request>stock_available_pkt)
            {
                $(this).addClass('quantity_exist_warning');
                $("#quantity_request_kg_"+current_id).addClass('quantity_exist_warning');
                $("#stock_available_pkt_"+current_id).addClass('quantity_exist_warning');
                $("#stock_available_"+current_id).addClass('quantity_exist_warning');
            }

            $("#quantity_request_kg_"+current_id).html(number_format(quantity_request_kg,3,'.',''));
            calculate_total();
        });
    });

</script>