<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'Pending List',
        'href'=>site_url($CI->controller_url.'/index/list')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
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
        'label'=>'Challan '.$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/challan_print')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Courier '.$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/print_courier')
    );
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
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference_all')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list_all')
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
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_all');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'barcode', type: 'string' },
                { name: 'outlet_name', type: 'string'},
                { name: 'date_request', type: 'string'},
                { name: 'date_approve', type: 'string'},
                { name: 'outlet_code', type: 'string'},
                { name: 'division_name', type: 'string'},
                { name: 'zone_name', type: 'string'},
                { name: 'territory_name', type: 'string'},
                { name: 'district_name', type: 'string'},
                { name: 'quantity_total_request', type: 'string'},
                { name: 'quantity_total_approve', type: 'string'},
                { name: 'quantity_total_receive', type: 'string'},
                { name: 'status_delivery', type: 'string'},
                { name: 'status_receive', type: 'string'},
                { name: 'status_receive_forward', type: 'string'},
                { name: 'status_receive_approve', type: 'string'},
                { name: 'status_system_delivery_receive', type: 'string'},
                { name: 'status', type: 'string'}
            ],
            id: 'id',
            type: 'POST',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column=='quantity_total_receive')
            {
                if(record.quantity_total_approve!=record.quantity_total_receive)
                {
                    element.html('<div class="bg-danger">'+record.quantity_total_receive+'</div>');
                }
                else
                {
                    element.html(record.quantity_total_receive);
                }
            }
            if(column=='quantity_total_approve')
            {
                if(record.quantity_total_request!=record.quantity_total_approve)
                {
                    element.html('<div class="bg-warning">'+record.quantity_total_approve+'</div>');
                }
                else
                {
                    element.html(record.quantity_total_approve);
                }
            }
            return element[0].outerHTML;

        };

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
            pagesizeoptions: ['50', '100', '200','300','500','1000','5000'],
            selectionmode: 'singlerow',
            altrows: true,
            height: '350px',
            columnsreorder: true,
            enablebrowserselection: true,
            columns:
            [
                { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',pinned:true, width:'80',hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name', width:'150',pinned:true,filtertype: 'list',hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DATE_REQUEST'); ?>', dataField: 'date_request', width:'100',hidden: <?php echo $system_preference_items['date_request']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?>', dataField: 'date_approve', width:'200',hidden: <?php echo $system_preference_items['date_request']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_OUTLET_CODE'); ?>', dataField: 'outlet_code', width:'90',hidden: <?php echo $system_preference_items['outlet_code']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name', width:'100',filtertype: 'list',hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name', width:'100',hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name', width:'100',hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', width:'100',hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_REQUEST'); ?>', dataField: 'quantity_total_request', width:'100', cellsAlign:'right', hidden: <?php echo $system_preference_items['quantity_total_request']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_APPROVE'); ?>', dataField: 'quantity_total_approve', width:'100', cellsAlign:'right', hidden: <?php echo $system_preference_items['quantity_total_approve']?0:1;?>,cellsrenderer: cellsrenderer},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_RECEIVE'); ?>', dataField: 'quantity_total_receive', width:'100', cellsAlign:'right', hidden: <?php echo $system_preference_items['quantity_total_receive']?0:1;?>,cellsrenderer: cellsrenderer},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_DELIVERY'); ?>', dataField: 'status_delivery',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_delivery']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE'); ?>', dataField: 'status_receive',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_receive']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_FORWARD'); ?>', dataField: 'status_receive_forward',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_receive_forward']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_APPROVE'); ?>', dataField: 'status_receive_approve',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_receive_approve']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_SYSTEM_DELIVERY_RECEIVE'); ?>', dataField: 'status_system_delivery_receive',filtertype: 'list', width:'30', hidden: <?php echo $system_preference_items['status_system_delivery_receive']?0:1;?>}
            ]
        });
    });
</script>
