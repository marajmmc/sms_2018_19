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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPENSE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
                {
                    ?>
                    <input type="text" name="item[date_expense]" id="name" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_expense']);?>" readonly />
                <?php
                }
                else
                {
                    ?>
                    <label class="control-label"><?php echo System_helper::display_date($item['date_expense']);?></label>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EXPENSE_ITEM');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="expense_id" name="item[expense_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($expense_items as $expense)
                    {?>
                        <option value="<?php echo $expense['id']?>" <?php if($expense['id']==$item['expense_id']){ echo "selected";}?>><?php echo $expense['name'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_EXPENSE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[amount]" id="ordering" class="form-control float_type_positive " value="<?php echo $item['amount'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control" ><?php echo $item['remarks'];?></textarea>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        //$(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+2"});
        $(".datepicker").datepicker({dateFormat : display_date_format});
    });
</script>