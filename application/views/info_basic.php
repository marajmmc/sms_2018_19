 <?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$accordion_header=isset($accordion['header'])?$accordion['header']:'+ Basic Information';
$accordion_id=isset($accordion['div_id'])?$accordion['div_id']:'accordion_basic';
$accordion_collapse=isset($accordion['collapse'])?$accordion['collapse']:'out';
$accordion_data=isset($accordion['data'])?$accordion['data']:$info_basic;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#<?php echo $accordion_id;?>" href="#"><?php echo $accordion_header; ?></a></label>
        </h4>
    </div>
    <div id="<?php echo $accordion_id;?>" class="panel-collapse collapse <?php echo $accordion_collapse; ?>">

        <table class="table table-bordered table-responsive system_table_details_view">
            <tbody>
                <?php
                foreach($accordion_data as $info)
                {
                    if(isset($info['label_1']))
                    {
                        if(isset($info['value_1']))
                        {
                            if(isset($info['label_2']))
                            {
                                if(isset($info['value_2']))
                                {
                                    ?>
                                    <tr>
                                        <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $info['label_1'];?></label></td>
                                        <td class="warning header_value"><label class="control-label"><?php echo $info['value_1'];?></label></td>
                                        <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $info['label_2'];?></label></td>
                                        <td class="warning header_value"><label class="control-label"><?php echo $info['value_2'];?></label></td>
                                    </tr>
                                <?php
                                }
                                else
                                {
                                    ?>
                                    <tr>
                                        <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $info['label_1'];?></label></td>
                                        <td class="warning header_value"><label class="control-label"><?php echo $info['value_1'];?></label></td>
                                        <td class="widget-header header_caption" colspan="2"><label class="control-label"><?php echo $info['label_2'];?></label></td>
                                    </tr>
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <tr>
                                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $info['label_1'];?></label></td>
                                    <td class="warning header_value" colspan="3"><label class="control-label"><?php echo $info['value_1'];?></label></td>
                                </tr>
                            <?php
                            }
                        }
                        else
                        {
                            ?>
                            <tr><td colspan="4" class="bg-info"><?php echo $info['label_1'];?></td></tr>
                            <?php
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>