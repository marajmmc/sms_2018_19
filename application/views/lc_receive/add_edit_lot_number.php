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
}
$action_buttons[]=array
(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_add_edit_lot_number');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="col-md-12">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Release Completed By</label></th>
                    <th class="bg-danger header_value"><label class="control-label"><?php echo $item['user_full_name']?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Release Completed Time</label></th>
                    <th class="bg-danger header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_release_completed']);?></label></th>
                </tr>
                </thead>
            </table>
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item['fiscal_year']?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MONTH');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LC_NUMBER');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_OPENING');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>

                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_open']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_LC_RELEASE');?></label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item['remarks_release']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUMBER_LOT');?></label></th>
                    <th class="bg-danger header_value" colspan="3"><label class="control-label"><?php echo $item['lot_number'];?></label></th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="row show-grid">
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
                        <!--<th class="label-danger text-center" colspan="2">Deference Information</th>-->
                    </tr>
                    <tr>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('KG');?></th>
                        <th class="label-success text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-success text-center"><?php echo $CI->lang->line('KG');?></th>
                        <!--<th class="label-danger text-center"><?php /*echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); */?></th>
                        <th class="label-danger text-center"><?php /*echo $CI->lang->line('KG');*/?></th>-->
                    </tr>
                    </thead>
                    <?php
                    $lot_numbers_encode=json_decode($item['lot_numbers_encode'], true);
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
                                <!--<td class="text-right"><label class="control-label"><?php /*echo ($data['quantity_release']-$data['quantity_receive'])*/?></label></td>
                                <td class="text-right"><label class="control-label"><?php /*echo number_format(($quantity_release_kg-$quantity_receive_kg),3,'.','')*/?></label></td>-->
                            </tr>
                            <tr>
                                <th colspan="21">
                                    <div style="overflow-x: auto;" class="row show-grid">
                                        <table class="table table-bordered" style="width: 50%; float: right">
                                            <thead>
                                            <tr>
                                                <th style="text-align: right;">Box Number (Start)</th>
                                                <th style="text-align: right;">Box Number (End)</th>
                                                <th style="text-align: left;">Lot Number <span style="color:#FF0000">*</span></th>
                                                <th style="text-align: right;">Quantity <span style="color:#FF0000">*</span></th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody id="items_container_<?php echo $data['id']?>" class="items_container">
                                            <?php
                                            if(isset($lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['number_lot']) && sizeof($lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['number_lot'])>0)
                                            {
                                                for($i=0; $i<sizeof($lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['number_lot']); $i++)
                                                {
                                                    ?>
                                                    <tr>
                                                        <td class="text-right">
                                                            <input type="text" class="form-control float_type_positive number_box_start" name="items[<?php echo $data['id']?>][number_box_start][]" value="<?php echo $lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['number_box_start'][$i]?>" />
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" class="form-control float_type_positive number_box_end" name="items[<?php echo $data['id']?>][number_box_end][]" value="<?php echo $lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['number_box_end'][$i]?>" />
                                                        </td>
                                                        <td class="text-right">
                                                            <select class="form-control number_lot" name="items[<?php echo $data['id']?>][number_lot][]">
                                                                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                                                <?php
                                                                if($item['lot_number'])
                                                                {
                                                                    $lot_numbers=explode(',',$item['lot_number']);
                                                                    foreach($lot_numbers as $lot_number)
                                                                    {
                                                                        if($lot_number)
                                                                        {
                                                                            if($lot_number==$lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['number_lot'][$i])
                                                                            {
                                                                                ?>
                                                                                <option value="<?php echo $lot_number?>" selected="selected"><?php echo $lot_number?></option>
                                                                            <?php
                                                                            }
                                                                            else
                                                                            {
                                                                                ?>
                                                                                <option value="<?php echo $lot_number?>"><?php echo $lot_number?></option>
                                                                            <?php
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" class="form-control float_type_positive quantity_lot" name="items[<?php echo $data['id']?>][quantity_lot][]" value="<?php echo $lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]['quantity_lot'][$i]?>" />
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row show-grid">
                                        <div class="col-xs-4">

                                        </div>
                                        <div class="col-xs-4">

                                        </div>
                                        <div class="col-xs-4">
                                            <button type="button" class="btn btn-warning system_button_add_more pull-right" data-current-id="<?php //echo isset($lot_numbers_encode[$data['variety_id']][$data['pack_size_id']])?sizeof($lot_numbers_encode[$data['variety_id']][$data['pack_size_id']]):0;?>" data-current-container-id="<?php echo $data['id']?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
                                        </div>
                                    </div>
                                </th>
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
                            <!--<th class="text-right"><label class="control-label"><?php /*echo ($quantity_total_release-$quantity_total_receive);*/?></label></th>
                            <th class="text-right"><label class="control-label"><?php /*echo number_format(($quantity_total_release_kg-$quantity_total_receive_kg),3,'.','');*/?></label></th>-->
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
    </div>
    <div class="clearfix"></div>
</form>
<div id="system_content_add_more" style="display: none">
    <table>
        <tbody>
        <tr>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive number_box_start " />
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive number_box_end" />
            </td>
            <td class="text-right">
                <select class="form-control number_lot">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if($item['lot_number'])
                    {
                        $lot_numbers=explode(',',$item['lot_number']);
                        foreach($lot_numbers as $lot_number)
                        {
                            ?>
                            <option value="<?php echo $lot_number?>"><?php echo $lot_number?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive quantity_lot" value="" />
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function()
    {
        $(document).off("click", ".system_button_add_more");
        $(document).on("click", ".system_button_add_more", function(event)
        {
            /*var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);*/
            var current_container_id=parseInt($(this).attr('data-current-container-id'));
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .number_box_start').attr('id','number_box_start_'+current_container_id);
            $(content_id+' .number_box_start').attr('data-current-id',current_container_id);
            $(content_id+' .number_box_start').attr('name','items['+current_container_id+'][number_box_start][]');

            $(content_id+' .number_box_end').attr('id','number_box_end_'+current_container_id);
            $(content_id+' .number_box_end').attr('data-current-id',current_container_id);
            $(content_id+' .number_box_end').attr('name','items['+current_container_id+'][number_box_end][]');

            $(content_id+' .number_lot').attr('id','number_lot_'+current_container_id);
            $(content_id+' .number_lot').attr('data-current-id',current_container_id);
            $(content_id+' .number_lot').attr('name','items['+current_container_id+'][number_lot][]');

            $(content_id+' .quantity_lot').attr('id','quantity_lot_'+current_container_id);
            $(content_id+' .quantity_lot').attr('data-current-id',current_container_id);
            $(content_id+' .quantity_lot').attr('name','items['+current_container_id+'][quantity_lot][]');

            var html=$(content_id).html();
            $("#items_container_"+current_container_id).append(html);
        });
        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
            //calculate_total();
        });
    })
</script>
