<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$method=isset($preference_method_name)?$preference_method_name:'list';
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/'.$method)
);
$action_buttons[]=array
(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_preference');?>" method="post">
    <input type="hidden" id="id" name="id" value="" />
    <input type="hidden" id="method_name" name="preference_method_name" value="<?php echo $method; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Preference Settings
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-12 text-center">
                <div class="checkbox  btn btn-danger">
                    <label>
                        <input type="checkbox" class="allSelectCheckbox" name="" checked>
                        Select All
                    </label>
                </div>
            </div>
            <?php
            foreach($system_preference_items as $key=>$value)
            {
                ?>
                <div class="col-xs-4">
                    <div class="checkbox">
                        <label><input type="checkbox" name="system_preference_items[<?php echo $key;?>]" value="1" <?php if($value){echo 'checked';}?>><span class="label label-success"><?php echo $CI->lang->line('LABEL_'.strtoupper($key)); ?></span></label>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

    </div>

    <div class="clearfix"></div>
</form>


<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).on("click",'.allSelectCheckbox',function()
        {
            if($(this).is(':checked'))
            {
                $('input:checkbox').prop('checked', true);
            }
            else
            {
                $('input:checkbox').prop('checked', false);
            }
        });
    });

</script>
