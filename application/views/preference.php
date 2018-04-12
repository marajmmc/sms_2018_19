<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI=& get_instance();

    foreach($system_preference_items as $key=>$value)
    {
    ?>
        <div class="col-xs-2 ">
            <div class="checkbox">
                <label><input type="checkbox" class="system_jqx_column" value="<?php echo $key;?>" <?php if($value){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_'.strtoupper($key)); ?></span></label>
            </div>
        </div>
        <?php
    }
?>