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
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
        'id'=>'button_action_save_new',
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_TO_REQUEST');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_request]" id="date_request" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_request']);?>" />
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
                    ?>
                    <select id="division_id" name="" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
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
                    ?>
                    <select id="zone_id" class="form-control" name="">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
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
                    ?>
                    <select id="territory_id" class="form-control" name="">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
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
                    ?>
                    <select id="district_id" class="form-control" name="">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($outlets)>0)){echo 'display:none';} ?>" class="row show-grid" id="outlet_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="outlet_id" name="item[outlet_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($outlets as $outlet)
                    {?>
                        <option value="<?php echo $outlet['value']?>"><?php echo $outlet['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_TO_REQUEST');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control" ><?php echo $item['remarks_request'];?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="widget-header">
                <div class="title">
                    Order Items
                </div>
                <div class="clearfix"></div>
            </div>
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width: 200px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                        <th style="width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                        <th style="width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                        <th class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_STOCK_MIN'); ?></th>
                        <th class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_STOCK_MAX'); ?></th>
                        <th class="text-right" style="width: 150px;"><?php echo $CI->lang->line('LABEL_STOCK_MAX'); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="items_container">

                    </tbody>
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
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
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
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                </select>
            </td>
            <td>
                <select class="form-control variety_id" style="display: none;">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                </select>
            </td>
            <td>
                <select class="form-control pack_size_id" style="display: none;">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($pack_sizes as $pack_size)
                    {?>
                        <option value="<?php echo $pack_size['value']?>"><?php echo $pack_size['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".datepicker").datepicker({dateFormat : display_date_format});

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

            $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
            $(content_id+' .variety_id').attr('data-current-id',current_id);
            $(content_id+' .variety_id').attr('name','items['+current_id+'][variety_id]');

            $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
            $(content_id+' .pack_size_id').attr('data-current-id',current_id);
            $(content_id+' .pack_size_id').attr('name','items['+current_id+'][pack_size_id]');

            var html=$(content_id).html();
            $("#items_container").append(html);
        });

        $(document).off("click", ".system_button_add_delete");
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
            //calculate_total();
        });

        $(document).off("change",".crop_id");
        $(document).on("change",".crop_id",function()
        {
            var current_id=$(this).attr('data-current-id');
            $("#crop_type_id_"+current_id).val("");
            $("#variety_id_"+current_id).val("");
            $("#pack_size_id_"+current_id).val("");
            var crop_id=$('#crop_id_'+current_id).val();
            $('#crop_type_id_'+current_id).hide();
            $('#variety_id_'+current_id).hide();
            $('#pack_size_id_'+current_id).hide();
            if(crop_id>0)
            {
                $('#crop_type_id_'+current_id).show();
                $('#crop_type_id_'+current_id).html(get_dropdown_with_select(system_types[crop_id]));
            }
            else
            {
                $('#crop_type_id_'+current_id).hide();
            }
        });

        $(document).off("change",".crop_type_id");
        $(document).on("change",".crop_type_id",function()
        {
            var current_id=$(this).attr('data-current-id');
            $("#variety_id_"+current_id).val("");
            $("#pack_size_id_"+current_id).val("");
            var crop_type_id=$('#crop_type_id_'+current_id).val();
            $('#pack_size_id_'+current_id).hide();
            if(crop_type_id>0)
            {
                $('#variety_id_'+current_id).show();
                $('#variety_id_'+current_id).html(get_dropdown_with_select(system_varieties[crop_type_id]));
            }
            else
            {
                $('#variety_id_'+current_id).hide();
            }
        });

        $(document).off("change",".variety_id");
        $(document).on("change",".variety_id",function()
        {
            var current_id=$(this).attr('data-current-id');

            $("#pack_size_id_"+current_id).val("");
            var variety_id=$('#variety_id_'+current_id).val();

            if(variety_id>0)
            {
                $('#pack_size_id_'+current_id).show();
            }
            else
            {
                $('#pack_size_id_'+current_id).hide();
            }
        });

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
    });
</script>