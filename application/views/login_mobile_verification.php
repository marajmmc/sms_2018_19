<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            SMS Login
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-sm-3">&nbsp;</div>
    <div class="col-sm-6">
        <div class="login-wrapper">
            <form action="<?php echo site_url('home/login');?>" class="form-horizontal" method="post">
                <div class="login_header">
                    <h3>
                        AR Malik
                        <img alt="Logo" height="40" class="pull-right" src="<?php echo str_replace('sms_2018_19','login_2018_19',base_url('images/logo.png'));?>">
                    </h3>
                    <p class="alert alert-warning"><?php echo $CI->lang->line('WARNING_LOGIN_FAIL_1101'); ?></p>
                </div>
                <div class="login_content">
                    <input class="form-control margin_bottom" type="text" name="code_verification" placeholder="Verification Code" value="" required>
                </div>
                <div class="login_action">
                    <input type="submit"  value="Verify" name="Login" class="btn btn-danger pull-right">
                    <a class="btn btn-primary" href="<?php echo site_url('home/login');?>">Back</a>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm-3">&nbsp;</div>

</div>
<div class="clearfix"></div>