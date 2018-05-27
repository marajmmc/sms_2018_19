<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array
(
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
                <!-- All Status -->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_REQUEST');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_request" name="report[status_request]" class="form-control">
                            <option value="<?php echo $this->config->item('system_status_forwarded')?>"><?php echo $this->config->item('system_status_forwarded')?></option>
                            <option value="<?php echo $this->config->item('system_status_pending')?>"><?php echo $this->config->item('system_status_pending')?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_APPROVE');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_approve" name="report[status_approve]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_approved')?>"><?php echo $this->config->item('system_status_approved')?></option>
                            <option value="<?php echo $this->config->item('system_status_pending')?>"><?php echo $this->config->item('system_status_pending')?></option>
                            <option value="<?php echo $this->config->item('system_status_rejected')?>"><?php echo $this->config->item('system_status_rejected')?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_DELIVERY');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_delivery" name="report[status_delivery]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_delivered')?>"><?php echo $this->config->item('system_status_delivered')?></option>
                            <option value="<?php echo $this->config->item('system_status_pending')?>"><?php echo $this->config->item('system_status_pending')?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_RECEIVE');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_receive" name="report[status_receive]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_received')?>"><?php echo $this->config->item('system_status_received')?></option>
                            <option value="<?php echo $this->config->item('system_status_pending')?>"><?php echo $this->config->item('system_status_pending')?></option>
                            <option value="<?php echo $this->config->item('system_status_forwarded')?>"><?php echo $this->config->item('system_status_forwarded')?></option>
                        </select>
                    </div>
                </div>
                <!--<div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php /*echo $CI->lang->line('LABEL_STATUS_RECEIVE_FORWARD');*/?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_receive_forward" name="report[status_receive_forward]" class="form-control">
                            <option value=""><?php /*echo $CI->lang->line('LABEL_SELECT_ALL');*/?></option>
                            <option value="<?php /*echo $this->config->item('system_status_forwarded')*/?>"><?php /*echo $this->config->item('system_status_forwarded')*/?></option>
                            <option value="<?php /*echo $this->config->item('system_status_pending')*/?>"><?php /*echo $this->config->item('system_status_pending')*/?></option>
                        </select>
                    </div>
                </div>-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_APPROVE');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_receive_approve" name="report[status_receive_approve]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_approved')?>"><?php echo $this->config->item('system_status_approved')?></option>
                            <option value="<?php echo $this->config->item('system_status_pending')?>"><?php echo $this->config->item('system_status_pending')?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right">Auto Receive<span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_system_delivery_receive" name="report[status_system_delivery_receive]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('LABEL_SELECT_ALL');?></option>
                            <option value="<?php echo $this->config->item('system_status_yes')?>"><?php echo $this->config->item('system_status_yes')?></option>
                            <option value="<?php echo $this->config->item('system_status_no')?>"><?php echo $this->config->item('system_status_no')?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <!-- Location Section-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['division_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                            <input type="hidden" id="division_id" name="report[division_id]" value="<?php echo $this->locations['division_id']?>" class="form-control" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="division_id" name="report[division_id]" class="form-control">
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
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['zone_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                            <input type="hidden" id="zone_id" name="report[zone_id]" value="<?php echo $this->locations['zone_id']?>" class="form-control" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="zone_id" name="report[zone_id]" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($zones as $zone)
                                {?>
                                    <option value="<?php echo $zone['value']?>"><?php echo $zone['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['territory_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                            <input type="hidden" id="territory_id" name="report[territory_id]" value="<?php echo $this->locations['territory_id']?>" class="form-control" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="territory_id" name="report[territory_id]" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($territories as $territory)
                                {?>
                                    <option value="<?php echo $territory['value']?>"><?php echo $territory['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['district_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                            <input type="hidden" id="district_id" name="report[district_id]" value="<?php echo $this->locations['district_id']?>" class="form-control" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="district_id" name="report[district_id]" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($districts as $district)
                                {?>
                                    <option value="<?php echo $district['value']?>" ><?php echo $district['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($outlets)>0)){echo 'display:none';} ?>" class="row show-grid" id="outlet_id_container">
                    <div class="col-xs-6">
                        <select id="outlet_id" name="report[outlet_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($outlets as $outlet)
                            {?>
                                <option value="<?php echo $outlet['value']?>"><?php echo $outlet['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <!-- Fiscal Year & Date Range Section-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <select id="fiscal_year_id" name="report[fiscal_year_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($fiscal_years as $year)
                            {?>
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
                        <label class="control-label "><?php echo $CI->lang->line('LABEL_DATE_END');?> <span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <select id="date_type" name="report[date_type]" class="form-control">
                            <option value="date_request">TO Date</option>
                            <option value="date_updated_forward">TO Forward Time</option>
                            <option value="date_approve">TO Approve Date</option>
                            <option value="date_delivery">TO Delivery Date</option>
                            <option value="date_updated_delivery_forward">TO Delivery Time</option>
                            <option value="date_receive">TO Receive Date</option>
                            <option value="date_updated_receive_approve">TO Receive Approval Time</option>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label">Type of Date<span style="color:#FF0000">*</span></label>
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

        $(document).off('change','#warehouse_id');
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

        /* Location Section */
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
