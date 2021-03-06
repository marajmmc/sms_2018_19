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
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                 if($key=='id')
                 {
                 ?>
                { name: '<?php echo $key ?>', type: 'number' },
                <?php
                 }
                 else
                 {
                 ?>
                { name: '<?php echo $key ?>', type: 'string' },
                <?php
                 }
             }
            ?>
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
                { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME_SOURCE'); ?>', dataField: 'outlet_name_source',pinned:true,filtertype: 'list', width:'250',hidden: <?php echo $system_preference_items['outlet_name_source']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME_DESTINATION'); ?>', dataField: 'outlet_name_destination',pinned:true,filtertype: 'list', width:'250',hidden: <?php echo $system_preference_items['outlet_name_destination']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DATE_REQUEST'); ?>', dataField: 'date_request', width:'100',hidden: <?php echo $system_preference_items['date_request']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_REQUEST'); ?>', dataField: 'quantity_total_request', width:'100', cellsAlign:'right', hidden: <?php echo $system_preference_items['quantity_total_request']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_APPROVE'); ?>', dataField: 'quantity_total_approve', width:'100', cellsAlign:'right', hidden: <?php echo $system_preference_items['quantity_total_approve']?0:1;?>,cellsrenderer: cellsrenderer},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_RECEIVE'); ?>', dataField: 'quantity_total_receive', width:'100', cellsAlign:'right', hidden: <?php echo $system_preference_items['quantity_total_receive']?0:1;?>,cellsrenderer: cellsrenderer},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_REQUEST'); ?>', dataField: 'status_request',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_request']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_APPROVE'); ?>', dataField: 'status_approve',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_approve']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_DELIVERY'); ?>', dataField: 'status_delivery',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_delivery']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE'); ?>', dataField: 'status_receive',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_receive']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_FORWARD'); ?>', dataField: 'status_receive_forward',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_receive_forward']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVE_APPROVE'); ?>', dataField: 'status_receive_approve',filtertype: 'list', width:'70', hidden: <?php echo $system_preference_items['status_receive_approve']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_SYSTEM_DELIVERY_RECEIVE'); ?>', dataField: 'status_system_delivery_receive',filtertype: 'list', width:'30', hidden: <?php echo $system_preference_items['status_system_delivery_receive']?0:1;?>}
            ]
        });
    });
</script>
