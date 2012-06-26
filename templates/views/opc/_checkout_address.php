<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_payment_address.<br />
 * Allows customer to change the billing address.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_checkout_payment_address_default.php 4852 2006-10-28 06:47:45Z drbyte $
 */
?>
    
<?php echo $form->open('onepage_checkout', 'step=checkout_shipping_billing', true, array('id' => 'checkout_address_form', 'data-validation' => 'checkout_address', 'class' => 'ajax', 'onsubmit'=>null)) ?>
    <?php // begin the shipping address section?>
    <table>
    <tr>
    <td id="onepage-checkout-shipping-address-container" class="onepage-checkout-left">
    
        <h2><?php _vzm("Shipping address");?></h2>        
     
        <?php                           
        /**
         * require template to display new address form
         */
            echo $this->fetch('views/onepage/_checkout_address_form.php', array("address_type" => "shipping"));
        ?>
		<!-- 
		<div class="input-container">
            <?php //echo zen_draw_checkbox_field('shipping_set_xpress', 1, false, 'id="shipping_set_xpress"') ?>
            <label class="input-label2" for="shipping_set_xpress"><?php _vzm("Use this as your express shipping address"); ?> (<a class="tooltip" title="<?php _vzm("Choosing an Express Shipping Address allows you to skip the address selection step in your future order");?>" href="#">?</a>)</label>
		</div>
		-->
		<div class="input-container">
            <?php echo zen_draw_checkbox_field('same_shipping_billing_address', 1, false, 'id="same_shipping_billing_address"') ?>
            <label class="input-label2" for="same_shipping_payment_address"><?php echo _vzm("Use this as the billing address as well?"); ?></label>
        </div>
        
    </td>
    <td id="onepage-checkout-payment-address-container" class="onepage-checkout-right">
    	<h2><?php _vzm("Billing address");?></h2>
     
        <?php                           
        /**
         * require template to display new address form
         */
            echo $this->fetch('views/onepage/_checkout_address_form.php', array("address_type" => "billing"));
        ?>
		<!-- 
		<div class="input-container">
            <?php //echo zen_draw_checkbox_field('billing_set_xpress', 1, false, 'id="billing_set_xpress"') ?>
            <label class="input-label2" for="billing_set_xpress"><?php _vzm("Use this as your express billing address"); ?> (<a class="tooltip" title="<?php _vzm("Choosing an Express Billing Address allows you to skip the address selection step in your future order");?>" href="#">?</a>)</label>
		</div>
		 -->
    </td>
    </tr>
    </table>
    <button type="submit" class="forward awesome pie green large"><?php _vzm("Next step &#8250") ?></button>
    <div class="clearBoth"></div>
</form>

