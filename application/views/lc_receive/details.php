<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK").' to Pending List',
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK").' to All list',
    'href'=>site_url($CI->controller_url.'/index/list_all')
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'onClick'=>"window.print()"
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/details/'.$item['id'])
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
    echo $CI->load->view("info_basic",'',true);
    echo $CI->load->view("info_basic",array('accordion'=>array('header'=>'+LC Info','div_id'=>'accordion_lc_info','data'=>$info_lc)),true);
    ?>
    <div class="clearfix"></div>
    <div style="overflow-x: auto;" class="row show-grid">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="widget-header text-center" colspan="21">LC (<?php echo Barcode_helper::get_barcode_lc($item['id']);?>) Product & Price Details  :: ( Receive Status: <?php echo $item['status_receive']?> )</th>
            </tr>
            <tr>
                <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_NUMBER')?></th>
                <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_CARTON_SIZE')?></th>
                <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></th>
                <th class="label-primary text-center" colspan="2">Release Information</th>
                <th class="label-success text-center" colspan="2">Receive Information</th>
                <th class="label-danger text-center" colspan="2">Deference Information</th>
            </tr>
            <tr>
                <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                <th class="label-success text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                <th class="label-success text-center"><?php echo $CI->lang->line('KG');?></th>
                <th class="label-danger text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                <th class="label-danger text-center"><?php echo $CI->lang->line('KG');?></th>
            </tr>
            </thead>
            <?php
            if(!empty($items))
            {
                ?>
                <tbody>
                <?php
                $quantity_total_release=0;
                $quantity_total_release_kg=0;
                $quantity_total_receive=0;
                $quantity_total_receive_kg=0;
                foreach($items as $index=>$data)
                {
                    if($data['pack_size_id']==0)
                    {
                        $quantity_release_kg=$data['quantity_release'];
                        $quantity_receive_kg=$data['quantity_receive'];
                    }
                    else
                    {
                        $quantity_release_kg=(($data['pack_size']*$data['quantity_release'])/1000);
                        $quantity_receive_kg=(($data['pack_size']*$data['quantity_receive'])/1000);
                    }
                    $quantity_total_release+=$data['quantity_release'];
                    $quantity_total_release_kg+=$quantity_release_kg;
                    $quantity_total_receive+=$data['quantity_receive'];;
                    $quantity_total_receive_kg+=$quantity_receive_kg;
                    ?>
                    <tr>
                        <td>
                            <strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong>
                        </td>
                        <td class="text-center"> <?php if($data['pack_size']==0){echo "Bulk";}else{echo $data['pack_size'];}?></td>
                        <td class="text-right"><?php echo $data['carton_number_receive']; ?></td>
                        <td class="text-right"><?php echo $data['carton_size_receive']; ?></td>
                        <td><?php echo $data['warehouse_name']?> </td>
                        <td class="text-right"><label class="control-label" for=""><?php echo $data['quantity_release'];?></label></td>
                        <td class="text-right"><label class="control-label" for=""><?php echo number_format($quantity_release_kg,3,'.','')?></label></td>
                        <td class="text-right"><label class="control-label" for=""><?php echo $data['quantity_receive']; ?></label></td>
                        <td class="text-right" ><label class="control-label "><?php echo number_format($quantity_receive_kg,3,'.',''); ?></label></td>
                        <td class="text-right"><label class="control-label"><?php echo ($data['quantity_release']-$data['quantity_receive'])?></label></td>
                        <td class="text-right"><label class="control-label"><?php echo number_format(($quantity_release_kg-$quantity_receive_kg),3,'.','')?></label></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL_KG')?></th>
                    <th class="text-right"><label class="control-label"><?php echo $quantity_total_release;?></label></th>
                    <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                    <th class="text-right"><label class="control-label"><?php echo $quantity_total_receive;?></label></th>
                    <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                    <th class="text-right"><label class="control-label"><?php echo ($quantity_total_release-$quantity_total_receive);?></label></th>
                    <th class="text-right"><label class="control-label"><?php echo number_format(($quantity_total_release_kg-$quantity_total_receive_kg),3,'.','');?></label></th>
                </tr>
                </tfoot>
            <?php
            }
            else
            {
                ?>
                <tfoot>
                <tr>
                    <td class="widget-header text-center" colspan="21"><strong><?php echo $CI->lang->line('NO_DATA_FOUND');?></strong></td>
                </tr>
                </tfoot>
            <?php
            }
            ?>
        </table>
    </div>
</div>
<div class="clearfix"></div>