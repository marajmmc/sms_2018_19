<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-8">
                <div style="" class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right">Packing Item</label>
                    </div>
                    <div class="col-xs-6">
                        <select id="packing_item" name="report[packing_item]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <option value="<?php echo $CI->config->item('system_master_foil');?>"><?php echo $CI->config->item('system_master_foil');?></option>
                            <option value="<?php echo $CI->config->item('system_common_foil');?>"><?php echo $CI->config->item('system_common_foil');?></option>
                            <option value="<?php echo $CI->config->item('system_sticker');?>"><?php echo $CI->config->item('system_sticker');?></option>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="crop_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_id" name="report[crop_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="variety_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="variety_id" name="report[variety_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="pack_size_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="pack_size_id" name="report[pack_size_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($pack_sizes as $pack_size)
                            {?>
                                <option value="<?php echo $pack_size['value']?>"><?php echo $pack_size['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        &nbsp;
                    </div>
                    <div class="col-xs-6">
                        <div class="action_button pull-right">
                            <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT_VIEW"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<div id="system_report_container">

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        $(document).off('change','#crop_id');
        $(document).off('change','#crop_type_id');
        $(document).off('change','#variety_id');
        $(document).off('change','#pack_size_id');
        $(document).off('change','#packing_item');

        $('#crop_id').html(get_dropdown_with_select(system_crops));

        $(document).on("change","#crop_id",function()
        {
            $('#system_report_container').html('');
            $('#crop_type_id').val("");
            $('#variety_id').val("");

            var crop_id=$('#crop_id').val();
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            if(crop_id>0)
            {
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id_container').show();
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
                }
                else
                {
                    $('#crop_type_id_container').hide();
                }
            }
        });
        $(document).on("change","#crop_type_id",function()
        {
            $('#system_report_container').html('');
            $('#variety_id').val("");
            var crop_type_id=$('#crop_type_id').val();
            $('#variety_id_container').hide();
            if(crop_type_id>0)
            {
                if(system_varieties[crop_type_id]!==undefined)
                {
                    $('#variety_id_container').show();
                    $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
                }
                else
                {
                    $('#variety_id_container').hide();
                }
            }
        });
        $(document).on("change","#packing_item",function()
        {
            $('#system_report_container').html('');
            $('#pack_size_id_container').hide();
            $('#crop_id_container').hide();
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            $('#crop_id').val("");
            $('#crop_type_id').val("");
            $('#variety_id').val("");
            $('#pack_size_id').val("");
            var packing_item=$('#packing_item').val();
            if(packing_item=='<?php echo $CI->config->item('system_master_foil')?>' || packing_item=='<?php echo $CI->config->item('system_sticker')?>')
            {
                $('#pack_size_id_container').show();
                $('#crop_id_container').show();
            }
        });

    });
</script>
