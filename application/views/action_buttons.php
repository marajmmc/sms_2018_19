<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
//type ==link|button
//label == Button text
//rest as attribute
    $CI = & get_instance();
?>
<div class="row widget hidden-print" style="padding-bottom: 0px;">
    <?php
    foreach($action_buttons as $button)
    {
        $type='link';
        $label='LABEL';
        $classes='btn';
        $attributes='';
        foreach($button as $key=>$value)
        {
            if($key=='type')
            {
                $type=$value;
            }
            elseif($key=='label')
            {
                $label=$value;
            }
            elseif($key=='class')
            {
                $classes.=' '.$value;
            }
            else
            {
                $attributes.=' '.$key.'="'.$value.'"';
            }
        }

        $attributes='class="'.$classes.'"'.$attributes;
        ?>
        <div class="action_button">
            <?php
            if($type=='link')
            {
                ?>
                <a <?php echo $attributes; ?>><?php echo $label; ?></a>
                <?php
            }
            elseif($type=='button')
            {
                ?>
                <button <?php echo $attributes; ?>><?php echo $label; ?></button>
                <?php
            }
            ?>

        </div>
        <?php

    }
    ?>
</div>
<div class="clearfix"></div>
