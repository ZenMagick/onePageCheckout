<?php
/*
 * Shopping bag Service inlcude with Trustpilot RSS integration
 *
 * cord@mailbeez.com
 *
 *
 */
?>
<div class="shoppingcart_servicebox_headintro">why shop at Always Riding?</div>
<div class="shoppingcart_servicebox">
  <div class="shoppingcart_servicebox_inner trustpilot">
    <div class="shoppingcart_servicebox_headline">a whole lotta trust.</div>
    <div class="shoppingcart_servicebox_content trustpilot">
      <div class="shoppingcart_servicebox_rating tp_logo"></div>
      <?php
      //include class (one line w/o blanks)
      require_once(DIR_FS_CATALOG . 'mailhive/configbeez/config_trustpilot_rss_importer/classes/trustpilot_import_rss.php');

      // create new instance
      $rss = new trustpilot_import_rss();

      // show 7 reviews using the 'rss.tpl' template
      // with 4 or 5 stars, shuffled:
      // template is located in
      // mailhive\configbeez\config_trustpilot_rss_importer\templates\rss_shoppingbag.tpl
      echo $rss->output_rss('rss_shoppingbag.tpl', 1, 4, 'True');
      ?>
      <div class="poweredby">powered by <a href="http://www.mailbeez.com/documentation/configbeez/config_trustpilot_rss_importer/" target="_blank">MailBeez Trustpilot Integration Suite</a></div>
    </div>
  </div>
  <div class="shoppingcart_servicebox_inner">
    <div class="shoppingcart_servicebox_headline">oh baby.</div>
    <div class="shoppingcart_servicebox_content">
      <ul>
        <li>Same day despatch if you order before 14:00 G.M.T on most items.</li>
        <li>One shipping price no matter how much you order, and free shipping available</li>
        <li>Trackable shipping</li>
        <li>Email confirmation of order and despatch</li>
        <li>All orders shipped from London, England</li>
      </ul>
    </div>
  </div>
  <div class="shoppingcart_servicebox_inner">
    <div class="shoppingcart_servicebox_headline">pinch me.</div>
    <div class="shoppingcart_servicebox_content">
      <ul>
        <li>30 day returns policy</li>
        <li>Returns form inside every shipment to make life easy</li>
        <li>Pre printed returns label included in every order</li>
        <li>No additional shipping costs for exchanges</li>
        <li>All exchanges and returns processed the day we receive them</li>
      </ul>
    </div>
  </div>
  <div class="shoppingcart_servicebox_inner more">
    <div class="shoppingcart_servicebox_content more">
      want to know more? <a href="<?php echo zen_href_link(FILENAME_SHIPPING)?>" >visit the customer service page here &gt;</a>
    </div>
  </div>
  <br>
</div>
<br class="clearBoth" />





