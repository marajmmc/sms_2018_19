<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-8">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_WAREHOUSE');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="warehouse_id" name="report[warehouse_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($warehouses as $warehouse)
                            {?>
                                <option value="<?php echo $warehouse['value']?>"><?php echo $warehouse['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="" class="row show-grid" id="crop_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_id" name="report[crop_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($crops as $crop)
                            {?>
                                <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="variety_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="variety_id" name="report[variety_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
                <div style="" id="pack_size_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="pack_size_id" name="report[pack_size_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
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
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_WAREHOUSE');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="warehouse_id" name="report[warehouse_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($warehouses as $warehouse)
                            {
                            ?>
                                <option value="<?php echo $warehouse['value']?>"><?php echo $warehouse['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-12">
                <div class="action_button pull-right">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
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
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(document).on("change","#fiscal_year_id",function()
        {
            var fiscal_year_ranges=$('#fiscal_year_id').val();
            if(fiscal_year_ranges!='')
            {
                var dates = fiscal_year_ranges.split("/");
                $("#date_start").val(dates[0]);
                $("#date_end").val(dates[1]);

            }
        });

        $(document).on("change","#warehouse_id",function()
        {
            $("#crop_id").val("");
            $("#crop_type_id").val("");
            $("#variety_id").val("");
            var warehouse_id=$('#warehouse_id').val();
            if(warehouse_id>0)
            {
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();

                $.ajax({
                    url: base_url+"common_controller/get_dropdown_crops_by_warehouseid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{warehouse_id:warehouse_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();

                $.ajax({
                    url: base_url+"common_controller/get_dropdown_allcrops",
                    type: 'POST',
                    datatype: "JSON",
                    data:{warehouse_id:warehouse_id},
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
        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            $("#variety_id").val("");

            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                $('#variety_id_container').hide();

                $.ajax({
                    url: base_url+"common_controller/get_dropdown_croptypes_by_cropid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_id:crop_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();

            }
        });
        $(document).on("change","#crop_type_id",function()
        {

            $("#variety_id").val("");

            var crop_type_id=$('#crop_type_id').val();
            if(crop_type_id>0)
            {
                $('#variety_id_container').show();

                $.ajax({
                    url: base_url+"common_controller/get_dropdown_armvarieties_by_croptypeid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_type_id:crop_type_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#variety_id_container').hide();

            }
        });
        $(document).on("change","#variety_id",function()
        {
            $("#pack_size_id").val("");
            var variety_id=$('#variety_id').val();
            var warehouse_id=$('#warehouse_id').val();
            if(variety_id>0)
            {

                $.ajax({
                    url: base_url+"common_controller/get_dropdown_packsizes_by_variety_warehouse/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{variety_id:variety_id,warehouse_id:warehouse_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_allpack_sizes/",
                    type: 'POST',
                    datatype: "JSON",
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

    });
</script>
