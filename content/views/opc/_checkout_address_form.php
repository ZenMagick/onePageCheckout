<?php
/**
 * Module Template
 *
 * Allows entry of new addresses during checkout stages
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_checkout_new_address.php 4683 2006-10-07 06:11:53Z drbyte $
 */

?>
<?php
    $address_var_name = $address_type.'_address';
	$geo = $session->getValue("geo", "ri");
	
    if ($addresses_count > 0) {
        $current_address_class = "show";
        $new_address_class = "hide";
		// BOF Rubikintegration
		$countryId = $container->get('pluginService')->getPluginForId('riLocation')->getCountryId($session); 
		// EOF Rubikintegration
        //$countryId = (is_object($$address_var_name) && 0 != $$address_var_name->getCountryId()) ? $$address_var_name->getCountryId() : ZMSettings::get('storeCountry');
        $zoneId = (is_object($$address_var_name) && 0 != $$address_var_name->getCountryId()) ? $$address_var_name->getZoneId() : ZMSettings::get('storeZone');
    }
    else {
        $current_address_class = "hide";
        $new_address_class = "show";
		// BOF Rubikintegration
		$countryId = ZMPlugins::instance()->getPluginForId('riLocation')->getCountryId($session); 
		// EOF Rubikintegration
        //$countryId = ZMSettings::get('storeCountry');
        $zoneId = ZMSettings::get('storeZone');
    }

?>
	<div style="margin: 10px 0;">
    <button class="awesome pie blue small <?php echo $address_type?>-address-selection toggle <?php echo $current_address_class?>" data-target=".<?php echo $address_type?>-address-selection"><?php _vzm("Add new address?") ?></button>
    <button class="awesome pie blue small <?php echo $address_type?>-address-selection toggle <?php echo $new_address_class?>" data-target=".<?php echo $address_type?>-address-selection"><?php _vzm("Choose From Address Book") ?></button>
    </div>
    <div class="<?php echo $address_type?>-address-selection <?php echo $current_address_class?>" id="shipping_address_book">
        <fieldset>
        	<?php if ($addresses_count > 0) { ?>
            	<legend><?php _vzm("Choose From Your Address Book Entries"); ?></legend>

                <select name="<?php echo $address_type?>_address_id">
                <?php
                    foreach ($addressList as $address){ ?>
                        <option value="<?php echo $address->getId()?>">
                        	<?php echo (($address->getFirstName() != "") ? $address->getFirstName().', ' : "") . (($address->getLastName() != "") ? $address->getLastName().', ' : "") .
                        	//$address->getAddressLine1() .' ,'. $address->getCity() .
                        	$address->getAddressLine1() .
                        	((0 != $address->getCountryId()) ? ' ,'.$address->getCountry()->getName() : '');?>
                        </option>
                    <?php }
                ?>
                </select>
			<?php } else { ?>
				<legend><?php _vzm("You currently do not have any entry in your address book"); ?></legend>
			<?php } ?>
        </fieldset>
    </div>

<?php

?>

<div class="<?php echo $address_type?>-address-selection <?php echo $new_address_class?>">
    <div class="centerColumnModule" id="<?php echo $address_type;?>checkoutNewAddress">

        <div class="alert forward"><?php echo FORM_REQUIRED_INFORMATION; ?></div>
        <br class="clearBoth" />
        <table>
            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>FirstName"><?php _vzm("Firstname"); ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'FirstName', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_firstname', '40') . ' id="'.$address_type.'FirstName"') . (zen_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="alert">' . ENTRY_FIRST_NAME_TEXT . '</span>': ''); ?>
            	</td>
            </tr>

            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>LastName"><?php echo ENTRY_LAST_NAME; ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'LastName', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_lastname', '40') . ' id="'.$address_type.'LastName"') . (zen_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="alert">' . ENTRY_LAST_NAME_TEXT . '</span>': ''); ?>
            	</td>
            </tr>

            <?php if (ZMSettings::get('isAccountCompany')) { ?>
            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>CompanyName"><?php echo ENTRY_COMPANY; ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'CompanyName', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_company', '40') . ' id="'.$address_type.'CompanyName"') . (zen_not_null(ENTRY_COMPANY_TEXT) ? '<span class="alert">' . ENTRY_COMPANY_TEXT . '</span>': ''); ?>
            	</td>
            </tr>
            <?php } ?>

            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>AddressLine1"><?php echo ENTRY_STREET_ADDRESS; ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'AddressLine1', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_street_address', '40') . ' id="'.$address_type.'AddressLine1"') . (zen_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_STREET_ADDRESS_TEXT . '</span>': ''); ?>
            	</td>
            </tr>

            <?php
              if (ACCOUNT_SUBURB == 'true') {
            ?>
            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>Suburb"><?php echo ENTRY_SUBURB; ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'Suburb', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_suburb', '40') . ' id="'.$address_type.'Suburb"') . (zen_not_null(ENTRY_SUBURB_TEXT) ? '<span class="alert">' . ENTRY_SUBURB_TEXT . '</span>': ''); ?>
            	</td>
            </tr>
            <?php
              }
            ?>

            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>City"><?php echo ENTRY_CITY; ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'City', $geo['city'], zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_city', '40') . ' id="'.$address_type.'City"') . (zen_not_null(ENTRY_CITY_TEXT) ? '<span class="alert">' . ENTRY_CITY_TEXT . '</span>': ''); ?>
            	</td>
            </tr>

            <tr>
                <td class="label">
                    <label class="input-label" for="<?php echo $address_type;?>CountryId"><?php _vzm("Country") ?></label>
                </td>
                <td>
                    <?php echo $form->idpSelect($address_type.'CountryId', array_merge(array(new ZMIdNamePair("", _zm("Select Country"))), $container->get('countryService')->getCountries()), $countryId) ?><span class="alert">*</span>
                </td>
            </tr>
            <?php if (ZMSettings::get('isAccountState')) { ?>
                <?php $zones = $container->get('countryService')->getZonesForCountryId($countryId); ?>
                <?php
                //var_dump($countryId);
                //var_dump($zones);
                ?>
                <tr>
                    <td class="label">
                        <label class="input-label"><?php _vzm("State/Province") ?></label>
                    </td>
                    <td>
                        <?php if (0 < count($zones)) { ?>
                            <?php echo $form->idpSelect($address_type.'ZoneId', $zones, $zoneId) ?>
                        <?php } else { ?>
                            <input type="text" id="<?php echo $address_type;?>State" name="<?php echo $address_type;?>State" value="<?php echo $geo['region']; //echo ((!empty($$address_var_name)) ? $html->encode($$address_var_name->getState()) : ''); ?>" />
                        <?php } ?>
                        <span class="alert">*</span>
                    </td>
                </tr>
            <?php } ?>


            <tr>
            	<td class="label">
            		<label class="input-label" for="<?php echo $address_type;?>Postcode"><?php echo ENTRY_POST_CODE; ?></label>
            	</td>
            	<td>
                    <?php echo zen_draw_input_field($address_type.'Postcode', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_postcode', '40') . ' id="'.$address_type.'Postcode"') . (zen_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="alert">' . ENTRY_POST_CODE_TEXT . '</span>': ''); ?>
            	</td>
            </tr>
        </table>
    </div>
</div>
