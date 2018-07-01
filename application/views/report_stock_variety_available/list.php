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
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                     if(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_id')||($key=='variety_name')||($key=='pack_size'))
                     {
                         ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                     }
                     else
                     {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
                 }
            ?>
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
             //console.log(defaultHtml);
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
                else
                {
                    element.html(get_string_quantity(value));
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
                    element.html(get_string_kg(value));
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
                    element.html(get_string_amount(value));
                }
            }

            return element[0].outerHTML;

        };

        var aggregates=function (total, column, element, record)
        {
            //console.log(record);
            //console.log(record['warehouse_5_pkt']);
            if(record.crop_name=="Grand Total")
            {
                return record[element];

            }
            return total;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log('here');
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['total']+'</div>';

        };
        var aggregatesrenderer_pkt=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0')||(aggregates['total']=='')))
            {
                text=get_string_quantity(aggregates['total']);
            }

            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';

        };
        var aggregatesrenderer_kg=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0.000')||(aggregates['total']=='')))
            {
                text=get_string_kg(aggregates['total']);
            }

            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';

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
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['variety_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['pack_size']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK_PKT'); ?>', dataField: 'current_stock_pkt',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['current_stock_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG'); ?>', dataField: 'current_stock_kg',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['current_stock_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_TO_ALL_PKT'); ?>', dataField: 'to_all_pkt',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['to_all_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_TO_ALL_KG'); ?>', dataField: 'to_all_kg',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['to_all_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_TO_SEARCH_PKT'); ?>', dataField: 'to_search_pkt',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['to_search_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_TO_SEARCH_KG'); ?>', dataField: 'to_search_kg',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['to_search_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_AVAILABLE_STOCK_PKT'); ?>', dataField: 'available_stock_pkt',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['available_stock_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_pkt},
                    { text: '<?php echo $CI->lang->line('LABEL_AVAILABLE_STOCK_KG'); ?>', dataField: 'available_stock_kg',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['available_stock_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg}

                ]
            });
    });
</script>
