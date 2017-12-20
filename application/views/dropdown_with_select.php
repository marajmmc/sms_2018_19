<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!isset($selected_value))
{
    $selected_value='';
}
?>

<option value="">
    <?php
    if(isset($select_label))
    {
        echo $select_label;
    }
    else
    {
        echo $this->lang->line('SELECT');
    }
    ?>
</option>
<?php



    foreach($items as $item)
    {
    ?>
        <option value="<?php echo $item['value'];?>" <?php if($item['value']==$selected_value){echo 'selected';} ?>><?php echo $item['text'];?></option>
    <?php

}
?>

