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

<fieldset>
    <legend><?php _vzm("Your selections") ?></legend>
    <div id="OPEditCartText" class="forward"><a href="<?php echo $net->url('shopping_cart');?>"><?php _vzm('edit your cart');?></a></div>
    <table cellpadding="0" cellspacing="0" id="cart">
        <thead>
        	<tr>
        		<th><?php _vzm("Quantity")?></th>
        		<th><?php _vzm("Product")?></th>
        		<th><?php _vzm("Options")?></th>
        		<th><?php _vzm("Sub total")?></th>
        	</tr>
        </thead>
        <tbody>
        <?php foreach ($shoppingCart->getItems() as $item) { ?>
            <tr>
                <td class="quantity">
                    <?php echo $item->getQuantity() ?>
                </td>
                <td class="name">
                    <?php echo $html->encode($item->getProduct()->getName()) ?>
				</td>
				<td class="attributes">
                    <?php if ($item->hasAttributes()) { ?>
                        <?php foreach ($item->getAttributes() as $attribute) { ?>
                            <p><span class="attr"><?php echo $html->encode($attribute->getName()) ?>:</span>
                            <?php $first = true; foreach ($attribute->getValues() as $attributeValue) { ?>
                                <?php if (!$first) { ?>, <?php } ?>
                                <span class="atval"><?php echo $html->encode($attributeValue->getName()) ?></span>
                            <?php $first = false; } ?>
                            </p>
                        <?php } ?>
                    <?php } ?>
                </td>

                <td class="price">
                    <?php echo $utils->formatMoney($item->getItemTotal()) ?>
                </td>
            </tr>
        <?php } ?>
          <?php

              foreach ($totals as $total) {
                  $tot = 'sub';
                  if ('total' == $total->getType()) {
                      $tot = 'tot';
                  }

                  ?><tr class="total <?php echo $tot ?>"><td colspan="2"><?php if($tot == "tot") echo _zmsprintf("forgotten and item? <a href='%s'>edit your bag here</a>", $net->url("shopping_cart")) ?></td><td class="name"><?php echo $html->encode($total->getName()) ?></td><td class="price"><?php echo $total->getValue() ?></td></tr><?php
              }
          ?>

        </tbody>
    </table>
</fieldset>

<?php if(!ZMLangUtils::isEmpty($shoppingCart->getComments())) {?>
<fieldset>
    <legend><?php _vzm("Special instructions or comments") ?> - <span class="step-edit-link"><?php _vzm("Change") ?></span></legend>

    <div><?php echo $html->encode($shoppingCart->getComments()) ?></div>
</fieldset>

<?php }?>

<?php echo $form->open($orderFormUrl, '', true) ?>
    <?php echo $orderFormContent ?>
    <button type="submit" class="lockable forward awesome large pie magenta"><?php _vzm("Place my order now &#8250") ?></button>
    <div class="clearBoth"></div>
</form>
<script type="text/javascript">
	// lock submit
	jQuery(".lockable").lockSubmit();
</script>
