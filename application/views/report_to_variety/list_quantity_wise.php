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
        'href'=>site_url($CI->controller_url.'/index/set_preference/list_quantity_wise')
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_quantity');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                 if((substr($key,-3)=='pkt') || (substr($key,-2)=='kg'))
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
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        /*var aggregates=function (total, column, element, record)
        {
            //console.log(record);
            //console.log(record['warehouse_5_pkt']);
            if(record.variety_name=="Grand Total")
            {
                return record[element];

            }
            return total;
        };*/
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            //console.log(record.transfer_wo_id);
            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': system_report_color_crop,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': system_report_color_grand,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            if(column.substr(-3)=='pkt')
            {
                if(value==0)
                {
                    element.html('');
                }
            }
            else if(column.substr(-2)=='kg')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(number_format(value,3,'.',''));
                }
            }
            else if(column.substr(0,6)=='amount')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(number_format(value,2));
                }
            }

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
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                enablebrowserselection: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',filtertype: 'list',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',filtertype: 'list',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',filtertype: 'list',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['variety_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',filtertype: 'list',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_REQUEST_PKT'); ?>', dataField: 'quantity_total_request_pkt',width:'100',cellsAlign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_request_pkt']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_REQUEST_KG'); ?>', dataField: 'quantity_total_request_kg',width:'100',cellsAlign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_request_kg']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_APPROVE_PKT'); ?>', dataField: 'quantity_total_approve_pkt',width:'100',cellsAlign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_approve_pkt']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_APPROVE_KG'); ?>', dataField: 'quantity_total_approve_kg',width:'100',cellsAlign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_approve_kg']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_RECEIVE_PKT'); ?>', dataField: 'quantity_total_receive_pkt',width:'100',cellsAlign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_receive_pkt']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_RECEIVE_KG'); ?>', dataField: 'quantity_total_receive_kg',width:'100',cellsAlign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_receive_kg']?0:1;?>}
                    ]
            });
    });
</script>