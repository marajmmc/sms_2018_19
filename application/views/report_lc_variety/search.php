<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-6">
                <div id="container_product">
                    <div style="" class="row show-grid" id="crop_id_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="crop_id" name="report[crop_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            </select>
                        </div>
                    </div>
                    <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
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
                    <div class="row show-grid" id="pack_size_id_container">
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
                </div>
                <!--<div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php /*echo $CI->lang->line('LABEL_LC_FORWARD');*/?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_open_forward" name="report[status_open_forward]" class="form-control">
                            <option value="<?php /*echo $this->config->item('system_status_yes')*/?>"><?php /*echo $this->config->item('system_status_yes')*/?></option>
                            <option value="<?php /*echo $this->config->item('system_status_no')*/?>"><?php /*echo $this->config->item('system_status_no')*/?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php /*echo $CI->lang->line('LABEL_STATUS_RELEASE');*/?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_release" name="report[status_release]" class="form-control">
                            <option value=""><?php /*echo $CI->lang->line('LABEL_SELECT_ALL');*/?></option>
                            <option value="<?php /*echo $this->config->item('system_status_complete')*/?>"><?php /*echo $this->config->item('system_status_complete')*/?></option>
                            <option value="<?php /*echo $this->config->item('system_status_pending')*/?>"><?php /*echo $this->config->item('system_status_pending')*/?></option>
                        </select>
                    </div>
                </div>-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_RECEIVED');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_receive" name="report[status_receive]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_complete')?>"><?php echo $this->config->item('system_status_complete')?></option>
                            <option value="<?php echo $this->config->item('system_status_pending')?>"><?php echo $this->config->item('system_status_pending')?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_OPEN');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_open" name="report[status_open]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_complete')?>"><?php echo $this->config->item('system_status_complete')?></option>
                            <option value="<?php echo $this->config->item('system_status_active')?>"><?php echo $this->config->item('system_status_active')?></option>
                            <!--<option value="<?php /*echo $this->config->item('system_status_delete')*/?>"><?php /*echo $this->config->item('system_status_delete')*/?></option>-->
                        </select>
                    </div>
                </div>
                <div style="" class="row show-grid" id="pack_size_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select name="report[principal_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($principals as $principal)
                            {
                                ?>
                                <option value="<?php echo $principal['value']?>"><?php echo $principal['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <!-- Fiscal Year & Date Range Section-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <select id="fiscal_year_id" name="report[fiscal_year_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($fiscal_years as $year)
                            {
                                ?>
                                <option value="<?php echo $year['value']?>"><?php echo $year['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR');?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DATE_START');?> <span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DATE_END');?> <span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <select id="date_type" name="report[date_type]" class="form-control">
                            <option value="date_opening">LC Opening Date</option>
                            <option value="date_expected"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED');?></option>
                            <option value="date_awb">LC AWB Date</option>
                            <option value="date_open_forward">LC Forward Time</option>
                            <option value="date_release">LC Release Date</option>
                            <option value="date_release_completed">LC Released Time</option>
                            <option value="date_receive">LC Receive Date</option>
                            <option value="date_receive_completed">LC Received Time</option>
                            <option value="date_expense_completed">LC Completed Time</option>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label">Type of Date<span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <select id="date_type" name="report[report_type]" class="form-control">
                            <option value="lc">LC Wise</option>
                            <option value="quantity">Quantity Wise</option>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label">Report Type<span style="color:#FF0000">*</span></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-8">

            </div>
            <div class="col-xs-4">
                <div class="action_button">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT_VIEW"); ?></button>
                </div>

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
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "c-2:c+2"});

        $(document).off('change','#crop_id');
        $(document).off('change','#crop_type_id');
        $(document).off('change','#variety_id');
        $(document).off('change','#pack_size_id');
        $(document).off("change","#fiscal_year_id");

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
                $('#crop_type_id_container').show();
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
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
                $('#variety_id_container').show();
                if(system_varieties[crop_type_id]!==undefined)
                {
                    $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
                }
            }
        });

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

        $(document).off("click", ".pop_up");
        $(document).on("click", ".pop_up", function(event)
        {
            $('#popup_content').html('');
            var left=((($(window).width() - 550) / 2) +$(window).scrollLeft());
            var top=((($(window).height() - 550) / 2) +$(window).scrollTop());
            $("#popup_window").jqxWindow({position: { x: left, y: top  }});
            $.ajax(
                {
                    url: $(this).attr('data-action-link'),
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
            $("#popup_window").jqxWindow('open');
        });

    });
</script>
