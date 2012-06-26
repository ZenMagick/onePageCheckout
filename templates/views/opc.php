<?php
$resources->cssFile('css/opc.css');
$resources->jsFile('js/libs/locksubmit.js', $resourceManager::FOOTER);
$resources->jsFile('js/libs/js_onepage_checkout.js', $resourceManager::FOOTER);
?>
<div id="onepage-checkout">
	<div class="back" id="checkout-steps">
<?php
$index = 1;$reached_latest = false;
foreach ($steps as $key => $step) {
    ?>

    <div id="<?php echo $key;?>" class="step<?php if($step["current"]) echo " active"; if(!$reached_latest) echo " loaded"; if($step["backable"]) echo " backable"; ?>" data-url="<?php echo $net->url("onepage_checkout", "step=".$key, true);?>">
    	<div class="step-title"><div class="step-count back"><?php echo $index;?></div><div class="step-heading back"><?php echo $step["title"] ?></div><div class="clearBoth"></div></div>
    	<div class="step-content"><div class="message"></div><div class="sub-content"><?php echo $step["content"]?></div></div>
    	<div class="clearBoth"></div>
    </div>

<?php if($step["current"]) $reached_latest = true;$index++;}?>
	</div>
	<div class="forward" id="checkout-progress">
        <?php echo $progress?>
    </div>
    <div class="clearBoth"></div>
</div>


<script type="text/javascript">
	validation = Array();
	validation["checkout_address"] = Array();
	validation["checkout_address"]["rules"] = {};
	validation["checkout_address"]["messages"] = {};
    <?php
  /*
    // starting some shipping/billing address
    $validation = $container->get('riValidator')->toJQuery('billing_address');
    ?>
    jQuery.extend(validation["checkout_address"]["rules"], {<?php echo $validation["rules"]?>});
    jQuery.extend(validation["checkout_address"]["messages"], {<?php echo $validation["messages"]?>});
    <?php
    $validation = $container->get('riValidator')->toJQuery('shipping_address');
    ?>
    jQuery.extend(validation["checkout_address"]["rules"], {<?php echo $validation["rules"]?>});
    jQuery.extend(validation["checkout_address"]["messages"], {<?php echo $validation["messages"]?>});
   */
?>

	// for zones
    var all_zones = new Array();
    <?php
        foreach ($container->get('countryService')->getCountries() as $country) {
            $zones = $container->get('countryService')->getZonesForCountryId($country->getId());
            if (0 < count($zones)) {
                echo 'all_zones['.$country->getId() . '] = new Array();';
                foreach ($zones as $zone) {
                    echo "all_zones[".$country->getId()."][".$zone->getZoneId()."] = '" . $zone->getName() ."';";
                }
            }
        }
    ?>
</script>
