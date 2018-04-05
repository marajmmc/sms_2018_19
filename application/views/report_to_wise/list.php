<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'district_name', type: 'string' },
                { name: 'outlet_name', type: 'string' },
                { name: 'transfer_wo_id', type: 'string' },
                { name: 'date_request', type: 'string' },
                { name: 'quantity_total_request', type: 'string' },
                { name: 'status_request', type: 'string' },
                { name: 'date_approve', type: 'string' },
                { name: 'quantity_total_approve', type: 'string' },
                { name: 'status_approve', type: 'string' },
                { name: 'date_delivery', type: 'string' },
                { name: 'status_delivery', type: 'string' },
                { name: 'date_receive', type: 'string' },
                { name: 'quantity_total_receive', type: 'string' },
                { name: 'status_receive', type: 'string' },
                { name: 'status_receive_forward', type: 'string' },
                { name: 'status_receive_approve', type: 'string' },
                { name: 'status_system_delivery_receive', type: 'string' },
                { name: 'status', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                rowsheight: 35,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',pinned:true,width:'100',hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name',pinned:true,width:'100',hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name',pinned:true,width:'100',hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name',pinned:true,width:'100',hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name',pinned:true,width:'200',hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',width:'100',hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_REQUEST'); ?>', dataField: 'date_request',width:'100',hidden: <?php echo $system_preference_items['date_request']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_REQUEST'); ?>', dataField: 'quantity_total_request',width:'80',cellsAlign: 'right',hidden: <?php echo $system_preference_items['quantity_total_request']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_REQUEST'); ?>', dataField: 'status_request',width:'100',hidden: <?php echo $system_preference_items['status_request']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_APPROVE'); ?>', dataField: 'date_approve',width:'100',hidden: <?php echo $system_preference_items['date_approve']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_APPROVE'); ?>', dataField: 'quantity_total_approve',width:'80',cellsAlign: 'right',hidden: <?php echo $system_preference_items['quantity_total_approve']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_APPROVE'); ?>', dataField: 'status_approve',width:'100',hidden: <?php echo $system_preference_items['status_approve']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_DELIVERY'); ?>', dataField: 'date_delivery',width:'100',hidden: <?php echo $system_preference_items['date_delivery']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_DELIVERY'); ?>', dataField: 'status_delivery',width:'100',hidden: <?php echo $system_preference_items['status_delivery']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_RECEIVE'); ?>', dataField: 'date_receive',width:'100',hidden: <?php echo $system_preference_items['date_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_RECEIVE_KG'); ?>', dataField: 'quantity_total_receive',width:'80',cellsAlign: 'right',hidden: <?php echo $system_preference_items['quantity_total_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE'); ?>', dataField: 'status_receive',width:'100',hidden: <?php echo $system_preference_items['status_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_FORWARD'); ?>', dataField: 'status_receive_forward',width:'100',hidden: <?php echo $system_preference_items['status_receive_forward']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_APPROVE'); ?>', dataField: 'status_receive_approve',width:'100',hidden: <?php echo $system_preference_items['status_receive_approve']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_SYSTEM_DELIVERY_RECEIVE'); ?>', dataField: 'status_system_delivery_receive',width:'100',hidden: <?php echo $system_preference_items['status_system_delivery_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>', dataField: 'status',width:'100',hidden: <?php echo $system_preference_items['status']?0:1;?>}
                    ]
            });
    });
</script>