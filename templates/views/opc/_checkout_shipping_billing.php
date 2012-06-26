<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
 *
 * Portions Copyright (c) 2003 The zen-cart developers
 * Portions Copyright (c) 2003 osCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?php echo $form->open("onepage_checkout", 'step=checkout_confirmation', true, array('id'=>'checkout_shipping_billing_form', 'onsubmit' => null, 'class' => 'ajax'));?>
<table>
<tr>
<td class="onepage-checkout-left">
<?php //echo $form->open(FILENAME_CHECKOUT_SHIPPING) ?>
    <?php if ($shoppingCart->getShippingProviders()) { ?>
    	<h2><?php _vzm("Shipping Methods") ?></h2>
        <fieldset>

            <p class="inst"><?php _vzm("Please select the preferred shipping method to use on this order.") ?></p>
            <table cellpadding="0" cellspacing="0" id="smethods">

                <tbody>
                <?php $providers = $shoppingCart->getShippingProviders(); ?>
                <?php foreach ($providers as $provider) { ?>
                  <?php $methods = $shoppingCart->getMethodsForProvider($provider); ?>
                  <?php if ($utils->isFreeShipping($shoppingCart)) { $id = 'free_free'; ?>
                      <?php $selected = (0 == count($providers) && 0 == count($methods)); ?>
                      <tr class="smethod" onclick="document.getElementById('<?php echo $id ?>').checked = true;">
						  <td class="smbutt"><input type="radio" id="<?php echo $id ?>" name="shipping" value="<?php echo $id ?>"<?php $form->checked(true, $selected) ?> /></td>
                          <td><?php echo _vzm('Free Shipping') ?></td>
                          <td class="smcost"><?php echo $utils->formatMoney(0) ?></td>
                      </tr>
                  <?php } ?>
                  <?php $errors = $provider->getErrors(); ?>
                  <?php if (0 < count($methods) || $provider->hasErrors()) { ?>
                    <!--<tr><td colspan="3">
                      <strong><?php echo $html->encode($provider->getName()) ?></strong>
                      <?php if ($provider->hasIcon()) { ?>
                        <img src="<?php echo $provider->getIcon() ?>" alt="<?php echo $html->encode($provider->getName()) ?>" title="<?php echo $html->encode($provider->getName()) ?>">
                      <?php } ?>
                      <?php if ($provider->hasErrors()) { echo '<br>'; _vzm("(%s)", $errors[0]); } ?>
                    </td></tr>-->
                  <?php } ?>
                  <?php foreach ($methods as $method) { ?>
                      <?php $id = 'ship_'.$method->getId();?>
                      <?php $selected = (1 == count($methods) && 1 == count($providers)) || ($method->getShippingId() == $shoppingCart->getSelectedShippingMethodId()); ?>
                      <tr class="smethod" onclick="document.getElementById('<?php echo $id ?>').checked = true;">
                          <td class="smbutt"><input type="radio" id="<?php echo $id ?>" name="shipping" value="<?php echo $method->getShippingId() ?>"<?php $form->checked(true, $selected) ?> /></td>
                          <td><?php echo $html->encode($method->getName()) ?> / <span class="smPrice"><?php echo $utils->formatMoney($method->getCost()) ?></span></td>
<!--                          <td class="smcost"><?php echo $utils->formatMoney($method->getCost()) ?></td>                          -->
                      </tr>
                  <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </fieldset>
    <?php } ?>

</td>

<td class="onepage-checkout-right">

<script type="text/javascript">var submitter = 0;</script>
<?php echo $shoppingCart->getPaymentFormValidationJS($request) ?>

<h2><?php _vzm("Please choose your payment method") ?></h2>

<?php //echo $form->open(FILENAME_CHECKOUT_CONFIRMATION, '', true, array('id'=>'checkout_payment', 'onsubmit' => 'return check_form();')) ?>
  <?php if (ZMSettings::get('isConditionsMessage')) { ?>
      <fieldset>
          <legend><?php _vzm("Terms and Conditions") ?></legend>
          <p>
              <?php _vzm("Please acknowledge the terms and conditions bound to this order by ticking the following box.") ?></br>
              <?php $href = '<a href="' . $net->staticPage('conditions') . '">' . _zm("here") . '</a>'; ?>
              <?php _vzm("The terms and conditions can be read %s.", $href) ?></p>
          <p><input type="checkbox" id="conditions" name="conditions" value="1" /><label for="conditions"><?php _vzm("I have read and agreed to the terms and conditions bound to this order.") ?></label></p>
      </fieldset>
  <?php } ?>

  <fieldset id="paytypes">
<!--      <legend><?php _vzm("Payment Options") ?></legend>-->
  <?php
      $paymentTypes = $shoppingCart->getPaymentTypes();
      $single = 1 == count($paymentTypes);
      foreach ($paymentTypes as $type) {
        $sptid = 'pt_'.$type->getId();
        if ($single) {
          ?><p><input type="hidden" id="<?php echo $sptid ?>" name="payment" value="<?php echo $type->getId() ?>" /><?php
        } else {
          ?><p class="paytype" onclick="document.getElementById('<?php echo $sptid ?>').checked = true;"><input type="radio" id="<?php echo $sptid ?>" name="payment" value="<?php echo $type->getId() ?>"<?php $form->checked($shoppingCart->getPaymentTypeId(), $type->getId()) ?> /><?php
        }
        ?><label for="<?php echo $sptid ?>"><?php echo $type->getName() ?></label></p><?php
        $fields = $type->getFields();
        if (0 < count($fields)) { $ii=0;
            ?><div id="pm_<?php echo $sptid;?>"><table class="pt" cellpadding="0" cellspacing="0"><tbody><?php
            foreach ($fields as $field) {
              ?><tr><td><label><?php echo $field->getLabel() ?></label></td><td><?php echo $field->getHTML() ?></td></tr><?php
            }
            ?></tbody></table></div><?php
          }
      }
  ?>
  </fieldset>
  <fieldset><div class="fieldRequire forward"><span class="alert">*</span> <?php _vzm('required fields');?></div></fieldset>
  <!-- <fieldset>
      <legend><?php _vzm("Comments") ?></legend>
      <p class="inst"><?php _vzm("Special instructions or comments about your order.") ?></p>
      <?php /* Fix for IE bug regarding textarea... */ ?>
      <table><tr><td><textarea name="comments" rows="3" cols="45"><?php echo $html->encode($shoppingCart->getComments()) ?></textarea></td></tr></table>
  </fieldset> -->

  <?php $creditTypes = $shoppingCart->getCreditTypes(); ?>
  <?php if (0 < count($creditTypes)) { ?>

	    <fieldset id="credittypes">
	    <?php foreach ($creditTypes as $type) { ?>
	      <p class="credittype"><?php echo $type->getName() ?></p>
	      <div class="instr"><?php echo $type->getInstructions() ?></div>
	      <table class="pt" cellpadding="0" cellspacing="0"><tbody>
                  <?php foreach ($type->getFields() as $field) { ?>
                     <tr>
                     <td class="creditInput" align="right"><?php echo $field->getHTML() ?></td>
                     </tr>
                  <?php } ?>
	       <?php } ?>
	       </tbody></table>
	    </fieldset>
      <!--origin html<fieldset>
          <legend><?php _vzm("Credit Options") ?></legend>
          <?php foreach ($creditTypes as $type) { ?>
              <p class="credittype"><?php echo $type->getName() ?></p>
              <div class="instr"><?php echo $type->getInstructions() ?></div>
              <table class="pt" cellpadding="0" cellspacing="0"><tbody>
                  <?php foreach ($type->getFields() as $field) { ?>
                     <tr><td><label><?php echo $field->getLabel() ?></label></td><td><?php echo $field->getHTML() ?></td></tr>
                  <?php } ?>
              </tbody></table>
          <?php } ?>
      </fieldset>origin html-->

  <?php } ?>
  </td>
  </tr>
  </table>
  <button type="submit" class="awesome forward green large"><?php _vzm("Continue to review your order &#8250") ?></button>
  <div class="clearBoth"></div>
</form>
