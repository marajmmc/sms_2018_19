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
        'href'=>site_url($CI->controller_url.'/index/set_preference_lc')
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
                { name: 'id', type: 'int' },
                { name: 'barcode', type: 'string' },
                { name: 'fiscal_year', type: 'string' },
                { name: 'month', type: 'string' },
                { name: 'date_opening', type: 'string' },
                { name: 'date_expected', type: 'string' },
                { name: 'date_forwarded_time', type: 'string' },
                { name: 'date_released_time', type: 'string' },
                { name: 'date_receive', type: 'string' },
                { name: 'date_received_time', type: 'string' },
                { name: 'date_completed_time', type: 'string' },
                { name: 'principal_name', type: 'string' },
                { name: 'currency_name', type: 'string' },
                { name: 'lc_number', type: 'string' },
                { name: 'quantity_open_kg', type: 'string' },
                { name: 'status_open_forward', type: 'string' },
                { name: 'status_release', type: 'string' },
                { name: 'status_received', type: 'string' },
                { name: 'status_open', type: 'string' },
                { name: 'details_button', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            console.log(record.transfer_wo_id);
            if(column=='details_button')
            {
                if(record.id)
                {
                    element.html('<div><button class="btn btn-primary pop_up" data-action-link="<?php echo site_url($CI->controller_url.'/index/details'); ?>/'+record.id+'">View Details</button></div>');
                }
                else
                {
                    element.html('');
                }
            }

            return element[0].outerHTML;

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
                sortable: true,
                filterable: false,
                showfilterrow: false,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                enablebrowserselection: true,
                rowsheight: 45,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode',pinned:true, width:80, hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year',pinned:true, width:65, hidden: <?php echo $system_preference_items['fiscal_year']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>', dataField: 'month',pinned:true, width:80, hidden: <?php echo $system_preference_items['month']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_OPENING'); ?>', dataField: 'date_opening',pinned:true, width:100, hidden: <?php echo $system_preference_items['date_opening']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_LC_NUMBER'); ?>', dataField: 'lc_number', width:110, hidden: <?php echo $system_preference_items['lc_number']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>', dataField: 'date_expected',width:100, hidden: <?php echo $system_preference_items['date_expected']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME'); ?>', dataField: 'date_forwarded_time',width:180, hidden: <?php echo $system_preference_items['date_forwarded_time']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_RELEASED_TIME'); ?>', dataField: 'date_released_time',width:180, hidden: <?php echo $system_preference_items['date_released_time']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_RECEIVE'); ?>', dataField: 'date_receive',width:100, hidden: <?php echo $system_preference_items['date_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_RECEIVED_TIME'); ?>', dataField: 'date_received_time',width:180, hidden: <?php echo $system_preference_items['date_receive']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_COMPLETED_TIME'); ?>', dataField: 'date_completed_time',width:180, hidden: <?php echo $system_preference_items['date_completed_time']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME'); ?>', dataField: 'principal_name',pinned:true, width:180, hidden: <?php echo $system_preference_items['date_received_time']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CURRENCY_NAME'); ?>', dataField: 'currency_name', width:80, hidden: <?php echo $system_preference_items['currency_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_OPEN_KG'); ?>', dataField: 'quantity_open_kg', width:100, cellsalign: 'right', hidden: <?php echo $system_preference_items['quantity_open_kg']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_OPEN_FORWARD');?>', dataField: 'status_open_forward', width:30,cellsalign: 'center', hidden: <?php echo $system_preference_items['status_open_forward']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RELEASE');?>', dataField: 'status_release', width:70,cellsalign: 'center', hidden: <?php echo $system_preference_items['status_release']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_RECEIVED');?>', dataField: 'status_received', width:70,cellsalign: 'center', hidden: <?php echo $system_preference_items['status_received']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_OPEN');?>', dataField: 'status_open', width:70,cellsalign: 'center', hidden: <?php echo $system_preference_items['status_open']?0:1;?>},
                        { text: 'Details', dataField: 'details_button',width: '120',cellsrenderer: cellsrenderer}
                    ]
            });
    });
</script>