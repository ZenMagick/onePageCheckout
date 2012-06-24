<?php echo $form->open('onepage_checkout', 'step=checkout_address', true, array('id' => 'checkout_login_register_form', 'class' => 'ajax', 'onsubmit'=>null)) ?>
    <fieldset class="padded">
        <legend><?php _vzm("What is your email address?")?></legend>
        <div class="fieldset-inner padded">
            <label class="email-password" for="email"><?php _vzm("My email address is:")?></label>
            <input type="text" name="email" id="email" />
        </div>
        <div class="input-container padded" style="display:none">
            <label class="email-password" for="confirm_email"><?php _vzm("Confirm email:")?></label>
            <input type="text" name="confirm_email" id="confirm_email">
        </div>
    </fieldset>

    <fieldset class="middle padded">
        <legend><?php echo _zmsprintf("Do you have an %s password?", STORE_NAME);?></legend>
        <div class="fieldset-inner padded">
        	<div class="input-container">
        		<label class="email-password" for="password"><span id="login_password_label"><?php _vzm("Password:")?></span><span style="display:none" id="register_password_label"><?php _vzm("Select a password:")?></span></label>
        		<input type="password" name="password" id="password"/><span><a class="onepageForgotPassword" href="<?php echo $net->url("password_forgotten", '', ($_SERVER['HTTPS'] == 'on') ? true : false) ?>"><?php _vzm('Need a new password?');?></a></span>
        	</div>
        	<div class="input-container">
                <input type="radio" name="login_register" checked="checked" value="login" id="onepage_login" />
                <label for="onepage_login"><?php _vzm("Yes, I have a password")?></label>
                <div class="clearBoth"></div>
            </div>
            <div class="input-container">
                <input type="radio" name="login_register" value="register" id="onepage_register" />
                <label for="onepage_register"><?php _vzm("No, I'm a new customer and I want to register")?></label>
                <div class="clearBoth"></div>
            </div>
            <div class="input-container">
                <input type="radio" name="login_register" value="guest" id="onepage_no_register" />
                <label for="onepage_no_register"><?php _vzm("No, I'm a new customer and I do NOT want to register")?></label>
                <div class="clearBoth"></div>
            </div>
        </div>
    </fieldset>

    <fieldset class="middle padded" style="display:none">
        <legend><?php _vzm("Account information");?></legend>
        <div class="fieldset-inner padded">
        </div>
    </fieldset>

    <button type="submit" class="lockable awesome large green pie forward"><?php _vzm("Sign in using our secure server &#8250")?></button>
    <div class="clearBoth"></div>
</form>

<br class="clearBoth" /><br class="clearBoth" />

<?php echo $this->fetch('views/shopping_cart_serviceblock_include.php');?>
