<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line('ACTION_NEW'),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS_PRINT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details_print')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
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
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
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
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>

    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {

        //$(document).off("click", "#button_jqx_load_more");

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'barcode', type: 'string' },
                { name: 'date_convert', type: 'string' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size', type: 'string' },
                { name: 'quantity_convert_total_kg', type: 'string' },
                { name: 'quantity_packet_actual', type: 'string' },
                { name: 'warehouse_name_source', type: 'string' },
                { name: 'warehouse_name_destination', type: 'string' },
                { name: 'remarks', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                enablebrowserselection:true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',width:'80',hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_CONVERT'); ?>', dataField: 'date_convert',width:'100',hidden: <?php echo $system_preference_items['date_convert']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width:'100',filtertype: 'list',hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',width:'100',hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width:'100',hidden: <?php echo $system_preference_items['variety_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',width:'100',cellsalign: 'right',filtertype: 'list',hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_CONVERT_TOTAL_KG'); ?>',dataField: 'quantity_convert_total_kg',width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['quantity_convert_total_kg']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_PACKET_ACTUAL'); ?>',dataField: 'quantity_packet_actual',width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['quantity_convert_total_kg']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_SOURCE'); ?>',dataField: 'warehouse_name_source',width:'100',filtertype: 'list',hidden: <?php echo $system_preference_items['warehouse_name_source']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME_DESTINATION'); ?>',dataField: 'warehouse_name_destination',width:'100',filtertype: 'list',hidden: <?php echo $system_preference_items['warehouse_name_destination']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks',hidden: <?php echo $system_preference_items['remarks']?0:1;?>}
                ]
            });
    });
</script>