<div class="progress-step<?php if($step == "checkout_address") echo " active"?>">
<div class="progress-title"><?php _vzm("Shipping & Billing Address")?></div>
<div class="progress-content">
    <?php if($shoppingCart->hasShippingAddress()) { ?>
    	<div class="sub-content">
    	<div class="step-edit-link-wrapper back padding-right">
    		<span class="step-edit-link" data-step="checkout_address"><?php _vzm("edit")?></span>
		</div>
		<div class="address back">    		
            <h3><?php _vzm("shipping address:") ?></h3>
            <?php echo $macro->formatAddress($shoppingCart->getShippingAddress()) ?>
        </div>
        <div class="clearBoth"></div>
        </div>
    <?php } ?>    
    
    <?php if($shoppingCart->hasShippingAddress()) { ?>
    	<?php if($shoppingCart->hasShippingAddress()) { ?>
    		<hr />
    	<?php } ?>
    	<div class="sub-content">
        	<div class="step-edit-link-wrapper back padding-right">
        		<span class="step-edit-link" data-step="checkout_address"><?php _vzm("edit")?></span>
    		</div>
    		<div class="address back">
                <h3><?php _vzm("billing address:") ?></h3>
                <?php echo $macro->formatAddress($shoppingCart->getBillingAddress()) ?>
    		</div>    
    		<div class="clearBoth"></div>     
		</div>   
	<?php } ?>        
</div>
</div>

<div class="progress-step<?php if($step == "checkout_shipping_billing") echo " active"?>">
<div class="progress-title"><?php _vzm("Shipping & Billing Method")?></div>
<div class="progress-content">
    <?php if (!$shoppingCart->isVirtual()) { ?>                            
        <?php if (null != ($shippingMethod = $shoppingCart->getSelectedShippingMethod())) { ?>
        <div class="sub-content">		
        	<div class="step-edit-link-wrapper back padding-right">
    			<span class="step-edit-link" data-step="checkout_shipping_billing"><?php _vzm("edit")?></span>
			</div>
			<div class="method back">              	
                <?php echo $html->encode($shippingMethod->getProvider()->getName()) . ': ' . $html->encode($shippingMethod->getName()) ?>
			</div> 
			<div class="clearBoth"></div>			     		               
		</div>            
        <?php } ?>    
    <?php } ?>
    
    <?php if (null != ($paymentType = $shoppingCart->getSelectedPaymentType())) { ?>
    	<?php if($shippingMethod != null) {?>
    		<hr />
    	<?php } ?>   
    	<div class="sub-content">		
        	<div class="step-edit-link-wrapper back padding-right">
    			<span class="step-edit-link" data-step="checkout_shipping_billing"><?php _vzm("edit")?></span>
			</div>
			<div class="method back">          
        		<h4><?php echo $paymentType->getName() ?></h4>
			</div>   
			<div class="clearBoth"></div>			     		
        </div>
    <?php } ?>
</div>
</div>

<div class="progress-step<?php if($step == "checkout_confirmation") echo " active"?>">
<div class="progress-title"><?php _vzm("Total")?></div>
<div class="progress-content">
	
	<?php 
	      //if($request->getSession()->isRegistered()){ ?>	     
	      	  <div class="sub-content">		
	      	  <table>
	      	  <?php 
			  $rows = array();
              $totals = $shoppingCart->getTotals();
              foreach ($totals as $total) {
                  $tot = 'sub';
                  if ('total' == $total->getType()) {
                      $tot = 'tot';
                  }        
                  if($total->getName() != '' && $total->getName() != ':') {
				  	$rows[$html->encode($total->getName())] = array('value' => $total->getValue(), 'tot' => $tot);
				  }
			  }
				
			  foreach($rows as $key => $value){ ?>
				
					<tr class="total <?php echo $value['tot'] ?>"><td class="name"><?php echo $key ?></td><td class="price"><?php echo $value['value'] ?></td></tr>			             
              <?php }?>                 

              </table>
              </div>
	      <?php //} ?>
      
</div>
</div>