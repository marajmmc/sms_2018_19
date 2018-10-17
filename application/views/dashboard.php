<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$user=User_helper::get_user();
$CI = & get_instance();
?>
<div class="row widget">
    <?php
    if($user->user_group==0)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_NOT_ASSIGNED_GROUP');?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if($user->username_password_same)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_USERNAME_PASSWORD_SAME');?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if($CI->is_site_offline())
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_SITE_OFFLINE');?></h3>
        </div>
    <?php
    }
    ?>
    <div class="col-sm-12 text-center">
        <h1><?php echo $user->name;?></h1>
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_profile_picture').$user->image_location; ?>" alt="<?php echo $user->name; ?>">
    </div>
</div>
<div class="clearfix"></div>
