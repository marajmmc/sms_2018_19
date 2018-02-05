<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
/*if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}*/
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        //$CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>
    <!--<div class="col-xs-12" id="system_jqx_container">

    </div>-->
    <div class="col-xs-12">
        <table class="table table-responsive table-bordered">
            <thead>
            <tr>
                <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_NAME')?></th>
                <th rowspan="2"><?php echo $this->lang->line('LABEL_CROP_TYPE')?></th>
                <th rowspan="2"><?php echo $this->lang->line('LABEL_VARIETY')?></th>
                <th rowspan="2"><?php echo $this->lang->line('LABEL_PACK_NAME')?></th>
                <th colspan="<?php echo sizeof($warehouse)?>" class="text-center"><?php echo $this->lang->line('LABEL_WAREHOUSE')?></th>
            </tr>
            <tr>
                <?php
                foreach($warehouse as $warehouse_id=>$warehouse_name)
                {
                    ?>
                    <th class="text-center"><?php echo $warehouse_name?></th>
                <?php
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($items as $crop)
            {
                foreach($crop['crop_type'] as $crop_type)
                {
                    foreach($crop_type['variety'] as $variety)
                    {
                        foreach($variety['pack_size'] as $pack)
                        {
                            ?>
                            <tr>
                                <td><?php echo $crop['crop_name']?></td>
                                <td><?php echo $crop_type['crop_type_name']?></td>
                                <td><?php echo $variety['variety_name']?></td>
                                <td><?php echo $pack['pack_size_name']?></td>
                                <?php
                                foreach($warehouse as $warehouse_id=>$warehouse_name)
                                {
                                    ?>
                                    <td class="text-center">
                                        <?php
                                        echo isset($pack['warehouse'][$warehouse_id]['current_stock'])?$pack['warehouse'][$warehouse_id]['current_stock']:'--';
                                        ?>
                                    </td>
                                <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>

