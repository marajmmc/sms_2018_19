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
                    ?>
                    { name: '<?php echo $key ?>', type: 'string' },
                    <?php
                 }
                ?>
            ],
            id: 'id',
            type: 'POST',
            url: url,
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
                    { text: '<?php echo $CI->lang->line('LABEL_OPENING_STOCK_PKT'); ?>', dataField: 'opening_stock_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['opening_stock_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OPENING_STOCK_KG'); ?>', dataField: 'opening_stock_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['opening_stock_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_STOCK_IN_PKT'); ?>', dataField: 'in_stock_in_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_stock_in_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_STOCK_IN_KG'); ?>', dataField: 'in_stock_in_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_stock_in_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_STOCK_EXCESS_PKT'); ?>', dataField: 'in_stock_excess_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_stock_excess_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_STOCK_EXCESS_KG'); ?>', dataField: 'in_stock_excess_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_stock_excess_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_STOCK_DELIVERY_SHORT_PKT'); ?>', dataField: 'in_stock_delivery_short_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_stock_delivery_short_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_STOCK_DELIVERY_SHORT_KG'); ?>', dataField: 'in_stock_delivery_short_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_stock_delivery_short_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_OW_PKT'); ?>', dataField: 'in_ow_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_ow_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_OW_KG'); ?>', dataField: 'in_ow_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_ow_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_CONVERT_BULK_PACK_PKT'); ?>', dataField: 'in_convert_bulk_pack_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_convert_bulk_pack_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_CONVERT_BULK_PACK_KG'); ?>', dataField: 'in_convert_bulk_pack_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_convert_bulk_pack_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_CONVERT_BULK_PACK_KG'); ?>', dataField: 'out_convert_bulk_pack_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_convert_bulk_pack_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_LC_PKT'); ?>', dataField: 'in_lc_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_lc_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IN_LC_KG'); ?>', dataField: 'in_lc_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['in_lc_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_SAMPLE_PKT'); ?>', dataField: 'out_stock_sample_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_sample_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_SAMPLE_KG'); ?>', dataField: 'out_stock_sample_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_sample_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_RND_PKT'); ?>', dataField: 'out_stock_rnd_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_rnd_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_RND_KG'); ?>', dataField: 'out_stock_rnd_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_rnd_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_DEMONSTRATION_PKT'); ?>', dataField: 'out_stock_demonstration_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_demonstration_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_DEMONSTRATION_KG'); ?>', dataField: 'out_stock_demonstration_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_demonstration_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_SHORT_INVENTORY_PKT'); ?>', dataField: 'out_stock_short_inventory_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_short_inventory_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_SHORT_INVENTORY_KG'); ?>', dataField: 'out_stock_short_inventory_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_short_inventory_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_DELIVERY_EXCESS_PKT'); ?>', dataField: 'out_stock_delivery_excess_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_delivery_excess_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_STOCK_DELIVERY_EXCESS_KG'); ?>', dataField: 'out_stock_delivery_excess_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_stock_delivery_excess_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_WO_PKT'); ?>', dataField: 'out_wo_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_wo_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OUT_WO_KG'); ?>', dataField: 'out_wo_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['out_wo_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_END_STOCK_PKT'); ?>', dataField: 'end_stock_pkt',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['end_stock_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_END_STOCK_KG'); ?>', dataField: 'end_stock_kg',cellsrenderer: cellsrenderer,width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['end_stock_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}
                ]
            });
    });
</script>
