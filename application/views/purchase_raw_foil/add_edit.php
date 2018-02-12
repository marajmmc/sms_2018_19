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
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_receive]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_receive']);?>"/>
                </div>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SUPPLIER_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="supplier_id" class="form-control" name="item[supplier_id]">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($suppliers as $supplier)
                    {?>
                        <option value="<?php echo $supplier['value']?>" <?php if($supplier['value']==$item['supplier_id']){ echo "selected";}?>><?php echo $supplier['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="current_stock_container">
            <div class="col-xs-4">
                <label for="current_stock_id" class="control-label pull-right">Current Stock</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label id="current_stock_id"><?php echo $item['current_stock'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHALLAN_NUMBER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[challan_number]" id="challan_number" class="form-control" value="<?php echo $item['challan_number'];?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CHALLAN');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <input type="text" name="item[date_challan]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_challan']);?>"/>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="quantity_supply" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_SUPPLY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[quantity_supply]" class="form-control float_type_positive" value="<?php echo $item['quantity_supply'];?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="quantity_receive" class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_RECEIVE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[quantity_receive]" id="quantity_receive" class="form-control quantity_receive float_type_positive" value="<?php echo $item['quantity_receive'];?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="price_unit_tk" class="control-label pull-right">Price Unit (Tk)<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[price_unit_tk]" id="price_unit_tk" class="form-control price_unit_tk float_type_positive" value="<?php echo $item['price_unit_tk'];?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total (Tk)</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label id="lbl_price_total_tk" class="control-label"><?php echo ($item['quantity_receive']*$item['price_unit_tk'])?></label>
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

        $(document).off('change','#supplier_id');
        $(document).on("change","#supplier_id",function()
        {
            $("#current_stock_id").text("");
            var supplier_id=$('#supplier_id').val();
            var variety_id=0;
            var pack_size_id=0;
            var packing_item='<?php echo $CI->config->item('system_common_foil')?>';
            if(supplier_id>0)
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

        $(document).off('input','#quantity_receive');
        $(document).on('input', '#quantity_receive', function()
        {
            $("#lbl_price_total_tk").html('');
            var quantity_receive=parseFloat($(this).val());
            if(isNaN(quantity_receive))
            {
                quantity_receive=0;
            }
            var price_unit_tk=parseFloat($("#price_unit_tk").val());
            if(isNaN(price_unit_tk))
            {
                price_unit_tk=0;
            }
            $("#lbl_price_total_tk").html(price_unit_tk*quantity_receive);
        });

        $(document).off('input','#price_unit_tk');
        $(document).on('input', '#price_unit_tk', function()
        {
            $("#lbl_price_total_tk").html('');
            var quantity_receive=parseFloat($("#quantity_receive").val());
            if(isNaN(quantity_receive))
            {
                quantity_receive=0;
            }
            var price_unit_tk=parseFloat($(this).val());
            if(isNaN(price_unit_tk))
            {
                price_unit_tk=0;
            }
            $("#lbl_price_total_tk").html(price_unit_tk*quantity_receive);
        });

    });
</script>