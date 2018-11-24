<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            ..:: Stock Management System ::..
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
                    <p>Fill out the form below to login.</p>
                </div>
                <div class="login_content">
                    <div class="alert-danger"><?php echo $message_warning;?></div>
                    <input class="form-control margin_bottom" type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
                    <input class="form-control" type="password" name="password" value="" placeholder="Password" required>
                </div>
                <div class="login_action">
                    <input type="submit"  value="Login" name="Login" class="btn btn-danger pull-right">
                    <div class="clearfix"></div>
                </div>
            </form>

        </div>
    </div>
    <div class="col-sm-3">&nbsp;</div>

</div>
<div class="clearfix"></div>