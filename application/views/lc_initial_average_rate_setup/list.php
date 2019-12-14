<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Average Rate Set',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
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
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                    if(($key=='id') || ($key=='number_of_lc')|| ($key=='number_of_lc_rate_receive'))
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

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            //element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if(record.number_of_lc!=record.number_of_lc_rate_receive)
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px',background:'#F99797'});
            }

            return element[0].outerHTML;

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
                height: '350px',
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', width:50, hidden: <?php echo $system_preference_items['id']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',filtertype: 'list', width:150, hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name', width:150, hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name', width:150, hidden: <?php echo $system_preference_items['variety_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_LC'); ?>', dataField: 'number_of_lc', cellsalign: 'right', width:100,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_lc']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_LC_RATE_RECEIVE'); ?>', dataField: 'number_of_lc_rate_receive', cellsalign: 'right', width:100,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['number_of_lc_rate_receive']?0:1;?>}
                    ]
            });
    });
</script>
