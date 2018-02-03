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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_receive_complete');?>" method="post">
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
            <div class="col-xs-4">
                <label class="control-label pull-right">LC Receive<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_receive" class="form-control" name="item[status_receive]">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_complete')?>"><?php echo $this->config->item('system_status_complete')?></option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered table-responsive ">
                <thead>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right">Release Completed By</label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['user_full_name']?></label></th>
                    <th class="widget-header"><label class="control-label pull-right">Release Completed Time</label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo System_helper::display_date_time($item['date_release_completed']);?></label></th>
                </tr>
                </thead>
            </table>
            <table class="table table-bordered table-responsive ">
                <thead>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['fiscal_year_name']?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $item['month_id'],1, 2000));?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_opening']);?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_EXPECTED');?></label></th>
                    <th><label class="control-label"><?php echo System_helper::display_date($item['date_expected']);?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PRINCIPAL_NAME');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['principal_name'];?></label></th>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_LC_NUMBER');?></label></th>
                    <th class="bg-danger"><label class="control-label"><?php echo $item['lc_number'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CONSIGNMENT_NAME');?></label></th>
                    <th colspan="3"><label class="control-label"><?php echo $item['consignment_name'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_OPEN');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks_open'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RELEASE');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks_release'];?></label></th>
                </tr>
                <tr>
                    <th class="widget-header"><label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS_LC_RECEIVE');?></label></th>
                    <th class="bg-danger" colspan="3"><label class="control-label"><?php echo $item['remarks_receive'];?></label></th>
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
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_VARIETY'); ?></th>
                        <th class="label-info text-center" rowspan="2"><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></th>
                        <th class="label-info" rowspan="2"><?php echo $CI->lang->line('LABEL_WAREHOUSE'); ?></th>
                        <th class="label-primary text-center" colspan="2">Release Information</th>
                        <th class="label-warning text-center" colspan="2">Receive Information</th>
                    </tr>
                    <tr>
                        <th class="label-primary text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-primary text-center">KG</th>
                        <th class="label-warning text-center"><?php echo $CI->lang->line('LABEL_QUANTITY_KG_PACK'); ?></th>
                        <th class="label-warning text-center">KG</th>
                    </tr>
                    </thead>
                    <?php
                    if(!empty($items))
                    {
                        ?>
                        <tbody>
                        <?php
                        $quantity_total_release_kg=0;
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
                                $quantity_release_kg=(($data['pack_size_name']*$data['quantity_release'])/1000);
                                $quantity_receive_kg=(($data['pack_size_name']*$data['quantity_receive'])/1000);
                            }
                            $quantity_total_release_kg+=$quantity_release_kg;
                            $quantity_total_receive_kg+=$quantity_receive_kg;
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-success"><?php echo $data['variety_name']?> (<?php echo $data['variety_name_import']?>)</strong>
                                </td>
                                <td class="text-center"> <?php if($data['pack_size_name']==0){echo "Bulk";}else{echo $data['pack_size_name'];}?></td>
                                <td><?php echo $data['warehouse_name']?> </td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($data['quantity_release'],3,'.','')?></label></td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($quantity_release_kg,3,'.','')?></label></td>
                                <td class="text-right"><label class="control-label" for=""><?php echo number_format($data['quantity_receive'],3,'.',''); ?></label></td>
                                <td class="text-right" ><label class="control-label "><?php echo number_format($quantity_receive_kg,3,'.',''); ?></label></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4" class="text-right"><?php echo $this->lang->line('LABEL_TOTAL_KG')?></th>
                            <th class="text-right"><label class="control-label"><?php echo number_format($quantity_total_release_kg,3,'.','');?></label></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><label class="control-label" id="lbl_quantity_total_receive_kg"><?php echo number_format($quantity_total_receive_kg,3,'.','');?></label></th>
                        </tr>
                        </tfoot>
                    <?php
                    }
                    else
                    {
                        ?>
                        <tfoot>
                        <tr>
                            <td class="widget-header text-center" colspan="21"><strong>Data Not Found</strong></td>
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