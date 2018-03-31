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
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_STOCK_IN');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_stock_in]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_stock_in']);?>" readonly/>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="purpose" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PURPOSE'); ?></label>

            </div>
            <?php
            if($item['purpose'])
            {
                ?>
                <label for="purpose" class="control-label"><?php echo $item['purpose']; ?></label>
            <?php
            }
            else
            {
                ?>
                <div class="col-sm-4 col-xs-8">
                    <select id="purpose" name="item[purpose]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <option value="<?php echo $CI->config->item('system_purpose_variety_stock_in');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_stock_in')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_STOCK_IN');?></option>
                        <option value="<?php echo $CI->config->item('system_purpose_variety_excess');?>" <?php if(isset($item['purpose'])){if($item['purpose']==$CI->config->item('system_purpose_variety_excess')){echo "selected";}}?>><?php echo $CI->lang->line('LABEL_EXCESS');?></option>
                    </select>
                </div>
            <?php } ?>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="current_stock_container">
            <div class="col-xs-4">
                <label for="current_stock_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label id="current_stock_id"><?php echo number_format($item['current_stock'],3,'.','');?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="quantity" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_KG');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[quantity]" class="form-control float_type_positive" value="<?php echo $item['quantity'];?>"/>
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
    </div>

    <div class="clearfix"></div>
</form>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".datepicker").datepicker({dateFormat : display_date_format});

        $(document).off('change','#purpose');
        $(document).on("change","#purpose",function()
        {
            $("#current_stock_id").text("");
            var purpose=$('#purpose').val();
            var variety_id=0;
            var pack_size_id=0;
            var packing_item='<?php echo $CI->config->item('system_common_foil')?>';
            if(purpose)
            {
                $('#current_stock_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_raw_current_stock/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        pack_size_id:variety_id,
                        variety_id:pack_size_id,
                        packing_item:packing_item
                    },
                    success: function (data, status)
                    {
                        $("#current_stock_id").text(data);
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#current_stock_container').hide();
            }
        });
    });
</script>