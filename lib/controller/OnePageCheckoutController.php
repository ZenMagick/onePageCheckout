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
<?php
namespace zenmagick\plugins\onePageCheckout\controller;

use zenmagick\base\Beans;
use zenmagick\base\Runtime;
use zenmagick\base\Toolbox;
use zenmagick\base\events\Event;
use zenmagick\http\forms\FormData;
use zenmagick\http\sacs\SacsManager;


/**
 * One Page Checkout controller.
 *
 * @author DerManoMann
 */
class OnePageCheckoutController extends \ZMController {
    const KEY_REDIRECT = 'loginRedirect';

    // Experimental: we put whatever final result of the step logic here, including possible redirections
    // simply use the name a_ since I cannot think of a better name now
    protected $a_ = array();
    protected $redirect_ = array();
    protected $steps_ = array();
    protected $step_;

    /**
     * Create new instance.
     */
    public function __construct($requestId=null) {
        parent::__construct();
    	  Runtime::getEventDispatcher()->listen($this);
    }


    private function processStep($step, $request, $method = "GET"){
        $this->steps_[$this->step_]["current"] = false;
        $this->step_ = $step;
        $this->steps_[$this->step_]["current"] = true;
        $class_method = $this->camelize("step_{$step}_".$method);
        if(method_exists($this, $class_method)){
            return $this->$class_method($request);
        }

        return false;
    }

    private function getNextStep(){
        $keys = array_keys($this->steps_);
        $position = array_search($this->step_, $keys);
        if (isset($keys[$position + 1])) {
            return $keys[$position + 1];
        }
        return false;
    }

	  private function getPreviousStep(){
        $keys = array_keys($this->steps_);
        $position = array_search($this->step_, $keys);
        if (isset($keys[$position - 1])) {
            return $keys[$position - 1];
        }
        return false;
    }

    /**
     *
     * Experimental, we will set actions here
     * @param string $key
     * @param string $value
     */
    private function setA($key, $value, $overwrite = false){
        if(!isset($this->a_[$key])){
            $this->a_[$key] = $value;
            return true;
        }

        if($overwrite){
            $this->a_[$key] = $value;
            return true;
        }

        if(is_array($this->a_[$key])){
            if(!is_array($value)) $value = (array)$value;
            // note: merge may still overwrite, it works for our current needs though
            $this->a_[$key] = array_merge($this->a_[$key], $value);
            return true;
        }

        return false;
    }

    private function getA($key, $default = null){
        return isset($this->a_[$key]) ? $this->a_[$key] : $default;
    }

    private function generateView($request, $view_name = null, $params = array()){
        $view = $this->findView($view_name, $params);
        $this->initViewVars($view, $request);
        $view->setTemplate("views/opc/_".$view_name.'.php');
        $view->setLayout('');
        $view->setContentType('text/plain');
        return $view->generate($request);
    }

    private function camelize($string, $pascalCase = false)
    {
        $string = str_replace(array('-', '_'), ' ', $string);
        $string = ucwords(strtolower($string));
        $string = str_replace(' ', '', $string);

        if (!$pascalCase) {
        return lcfirst($string);
        }
        return $string;
    }

    private function getStep($request){
    	$step = null;
    	$request_id = $request->getRequestId();
    	switch($request_id){
    		case 'onepage_checkout':
    			$step = $request->getParameter('step', null);
    			break;
    		default:
    			$map = array("checkout_shipping" => "checkout_shipping_billing", "checkout_payment" => "checkout_shipping_billing", "checkout_confirmation" => "checkout_confirmation");
    			$step = isset($map[$request_id])? $map[$request_id] : null;
    			break;
    	};
    	return $step;
    }
    /**
     * {@inheritDoc}
     * TODO: we will need to redirect pages from the classic checkout here
     */
    public function process($request) {try{

    	// ensure a usable id is set
        $this->requestId_ = null != $this->requestId_ ? $this->requestId_ : $request->getRequestId();

        $view = parent::process($request);
        $this->shoppingCart = $request->getShoppingCart();
        $this->checkoutHelper = $this->shoppingCart->getCheckoutHelper();
        $session = $request->getSession();

        Runtime::getEventDispatcher()->listen($this);

        // TODO: move this section to settings, perhaps yaml?
        // note: we still use the classic checkout success page for several reasons:
        // 1. it is easier to integrate tracking/conversion code that way
        // 2. if we want to use checkout success here, we will need to have more controls over the redirections and links generated by the payment modules, which we don't have right now
        $this->steps_ = array("checkout_login_register" => array("title" => _zm("login or register"), "content" => "", "current" => false, "level" => "guest", "backable" => false),
        					   "checkout_address" => array("title" => _zm("delivery & billing address"), "content" => "", "current" => false, "level" => "registered", "backable" => true),
        			  		   "checkout_shipping_billing" => array("title" => _zm("delivery & billing method"), "content" => "", "current" => false, "level" => "registered", "backable" => true),
        					   "checkout_confirmation" => array("title" => _zm("check and confirm your order"), "content" => "", "current" => false, "level" => "registered", "backable" => true),
        					   //"checkout_success" => array("title" => _zm("checkout success"), "content" => "", "current" => false)
        					   );

        $this->step_ = (($step = $this->getStep($request)) != null) ? $step : current(array_keys($this->steps_));

        // we remove the first register step if necessary
        if ($request->isRegistered() || $request->isGuest()) {
            // we need to redirect the customers to empty cart
            if($this->shoppingCart->isEmpty())
                return $this->findView('empty_cart');

            if($this->step_ == "checkout_login_register"){
            	$this->step_ = $this->getNextStep();

	            // if the addresses are set, we can even skip the address step
	            if($this->step_ == "checkout_address" && ($this->shoppingCart->getShippingAddress() != null && $this->shoppingCart->getBillingAddress() != null)){
	               $this->step_ = "checkout_shipping_billing";
	            }
            }

            // we cannot really skip further because the users will have to enter payment info anyway
            // TODO: if payment is 0 (free product) or payment info is not needed (check/money order)
            // and only 1 shipping method then we should also skip that step
        }
        else{
            // a bit hacky here, but guest cannot get past the login step
            if($request->getMethod() != 'POST' && $this->step_ != "checkout_address"){
                $this->step_ = "checkout_login_register";
            }
        }

        if($this->processStep($this->step_, $request, $request->getMethod())){
        	$this->steps_[$this->step_]["current"] = true;
        	if($this->getA("next", false) !== false){//die("here");
				$this->processStep($this->getA("next"), $request);
        	}
			elseif($request->getMethod() == "POST"){
				$this->processStep($this->step_, $request, 'GET');
			}
        }
        else {
        	// if this is a post request, then we assume there is a previous step we could go back to?
        	if($request->getMethod() == "POST"){
	        	$this->steps_[$this->step_]["current"] = false;
	        	$this->step_ = $this->getPreviousStep();
	        	$this->steps_[$this->step_]["current"] = true;
        	}
        }

        foreach ($this->getA("view", array()) as $step => $sub_view)
            $this->steps_[$step]["content"] = $sub_view;


        $data["steps"] = $this->steps_;

        $data["progress"] = $this->generateView($request, "checkout_progress", array("shoppingCart" => $this->shoppingCart, "step" => $this->step_));

        // if this is an ajax request, we need to return data in json format
        if($this->isAjax($request)){
            // redirect?
            if($this->getA("redirect") != null)
                $data["redirect"] = $this->getA("redirect");

            foreach($this->messageService->getMessages() as $message){
                $data["messages"][$message->getType()][$message->getRef()][] = $message->getText();
            }
            //$data["actions"] = $this->actions_;
            $json = json_encode($data);
            //$this->setJSONHeader($json);
            $this->setContentType('text/plain');
            $request->getSession()->clearMessages();
            //$request->getSession()->close();
			echo $json;
            exit();
        }
        else {
            // redirect?
            if($this->getA("redirect") != null)
                $request->redirect($this->getA("redirect"));

            $view->setLayout('opc_layout.php');
            $view->setVariables(array("steps" => $data["steps"], "progress" => $data["progress"]));
            return $view;
        }}catch(Exception $e){echo $e->getMessage();}
    }

    public function stepCheckoutLoginRegisterGet($request){
        $this->setA("view", array("checkout_login_register" => $this->generateView($request, "checkout_login_register")));
        return true;
    }

    public function stepCheckoutAddressPost($request){
        $result = false;
        switch($request->getParameter("login_register", "login")){
            case "login":
                $result = $this->login($request);
                break;
            case "register":
                 $result = $this->createNormalAccount($request);
                break;
            case "guest":
                $result = $this->createAnonymousAccount($request);
                break;
        }
        //if($result) $this->setA("next", $this->getNextStep());
        return $result;
    }

    public function stepCheckoutAddressGet($request){

    	$addressService = $this->container->get('addressService');
        // set shipping and billing address
        if(($shipping_address = $this->shoppingCart->getShippingAddress()) == null){
            $shipping_address = $addressService->getAddressForId($request->getAccount()->getDefaultAddressId());

        }

        if(($billing_address = $this->shoppingCart->getBillingAddress()) == null){
            $billing_address = $addressService->getAddressForId($request->getAccount()->getDefaultAddressId());
        }

        // get the address list
        $addressList = $addressService->getAddressesForAccountId($request->getAccountId());
        $this->setA("view", array("checkout_address" => $this->generateView($request, "checkout_address", array('shipping_address' => $shipping_address, 'billing_address' => $billing_address, 'addresses_count'=> count($addressList), 'addressList' => $addressList))));

        return true;
    }

    public function stepCheckoutShippingBillingPost($request){

        $addresses = array("shipping" => array("id" => null, "data" => null), "billing" => array("id" => null, "data" => null));

        // ignore all info regarding biling in this case
        if($request->getParameter("same_shipping_billing_address") == 1)
            unset($addresses["billing"]);

        // check the preset id
        $error = false;
        foreach($addresses as $address_type => $address){
            if(($addresses[$address_type]["id"] = $request->getParameter($address_type."_address_id")) == null){
                if(!$this->validate($request, $address_type."_address", $request->getParameterMap())){
                    // wrong address? We go no where and simply report back to the user
                    $error = true;
                }
            }
        }
        if($error) return false;

        // for the one with no preset id, we need to validate first
        foreach ($request->getParameterMap() as $key => $value){
            foreach($addresses as $address_type => $address){
                if($address["id"] == null && (strpos($key, $address_type) !== false)){
                    $addresses[$address_type]["data"][lcfirst(substr($key, strlen($address_type)))] = $value;
                    continue;
                }
            }
        }

        // create addresses
        $has_new_address = false;
        $addressService = $this->container->get('addressService');
        foreach($addresses as $address_type => $address){
            if($address["id"] == null && $address["data"] != null){
                $address["data"]["accountId"] = $request->getAccountId();

                if(empty($address["data"]["gender"])) $address["data"]["gender"] = "m";

                $new_address = new \ZMAddress();

                Beans::setAll($new_address, $address["data"], null);

                $new_address = $addressService->createAddress($new_address);

                $addresses[$address_type]["id"] = $new_address->getId();

                $has_new_address = true;
            }
        }

        $this->shoppingCart->setShippingAddressId($addresses["shipping"]["id"]);
        // do we use the same shipping address for billing?
        if($request->getParameter("same_shipping_billing_address") == 1)
            $this->shoppingCart->setBillingAddressId($addresses["shipping"]["id"]);
        else
            $this->shoppingCart->setBillingAddressId($addresses["billing"]["id"]);
        //$this->setA("next", $this->getNextStep());

        // hacky. We could move all the code inside stepCheckoutAddressGet to a sub function then have both
        // stepCheckoutAddressGet and stepCheckoutAddressPost call it but this will also do the job
        // here we want to pass the view of checkout_address back as well because we want to make sure that the
        // dropdowns are updated with new addresses
        if($has_new_address){
            $this->stepCheckoutAddressGet($request);

            // update account address
            $session = $request->getSession();
            if($session->getValue('set_default_address') == true){
	            $account = $request->getAccount();

				$new_address->setPrimary(true);
				$account->setFirstName($new_address->getFirstName());
				$account->setLastName($new_address->getLastName());

	        	$account->setDefaultAddressId($new_address->getId());
		        $this->container->get('accountService')->updateAccount($account);
		        $addressService = $this->container->get('addressService');
				$addressService->updateAddress($new_address);
		        $session->setValue('set_default_address', false);

				if($session->getValue('send_welcome_email') == true){
				    // account email
				    $settingsService = Runtime::getSettings();
			        $context = array('currentAccount' => $account, 'office_only_html' => '', 'office_only_text' => '');
			        zm_mail(sprintf(_zm("Welcome to %s"), $settingsService->get('storeName')), 'welcome', $context, $account->getEmail(), $account->getFullName());
			        if ($settingsService->get('isEmailAdminCreateAccount')) {
			            // store copy
			            $context = $request->getToolbox()->macro->officeOnlyEmailFooter($account->getFullName(), $account->getEmail(), $session);
			            $context['currentAccount'] = $account;
			            zm_mail(sprintf(_zm("[CREATE ACCOUNT] Welcome to %s"), $settingsService->get('storeName')), 'welcome', $context, $settingsService->get('emailAdminCreateAccount'));
			        }
					$session->setValue('send_welcome_email', false);
				}
            }
        }
        return true;
    }

    public function stepCheckoutShippingBillingGet($request){

        if(!$this->integrityCheck($request)) {
            return false;
        }

        if ($this->checkoutHelper->isVirtual()) {
            $this->checkoutHelper->markCartFreeShipping();
            $this->setA("view", array("checkout_shipping_billing" => $this->generateView($request, "checkout_shipping_billing", array("skip_shipping" => true, "shoppingCart" => $this->shoppingCart))));
            return true;
            //return $this->findView('skip_shipping');
        }

        //TODO: preselect shipping
        // a) something to preselect free shipping as per ot_freeshipper
        // b) is a preferred option configured via setting??
        // c) cheapest except storepickup

        $this->setA("view", array("checkout_shipping_billing" => $this->generateView($request, "checkout_shipping_billing", array("shoppingCart" => $this->shoppingCart))));
        return true;
    }

    public function stepCheckoutConfirmationPost($request){

        // ZM code modifies $order among other things, which break the $order object
        if(!$this->integrityCheck($request))
            return false;

        if ($this->checkoutHelper->isVirtual()) {
            $this->checkoutHelper->markCartFreeShipping();
            //return $this->findView('skip_shipping');
        }
        else{
            // process selected shipping method
            $shipping = $request->getParameter('shipping');
            list($providerName, $methodName) = explode('_', $shipping);
            if (null != ($shippingProvider = $this->container->get('shippingProviderService')->getShippingProviderForId($providerName))) {
                $shippingMethod = $shippingProvider->getShippingMethodForId($methodName, $this->shoppingCart, $this->shoppingCart->getShippingAddress());
            }

            if (null == $shippingProvider || null == $shippingMethod) {
                $this->messageService->error(_zm('Please select a shipping method.'));
                return false;
            }

            $this->shoppingCart->setSelectedShippingMethod($shippingMethod);

            if (Runtime::getSettings()->get('isConditionsMessage') && !Toolbox::asBoolean($request->getParameter('conditions'))) {
                $this->messageService->error(_zm('Please confirm the terms and conditions bound to this order by ticking the box below.'));
                return false;
            }

            // TODO: check if credit/gv covers total (currently in order_total::pre_confirmation_check)

            if (null == ($paymentTypeId = $request->getParameter('payment'))) {
                $this->messageService->error(_zm('Please select a payment type.'));
                return false;
            }

            if (null == ($paymentType = $this->container->get('paymentTypeService')->getPaymentTypeForId($paymentTypeId))) {
                $this->messageService->error(_zm('Please select a valid payment type.'));
                return false;
            }

            $this->shoppingCart->setSelectedPaymentType($paymentType);
        }

        if (null != ($comments = $request->getParameter('comments'))) {
            $this->shoppingCart->setComments($comments);
        }

        // very very hacky here, but we need to catch if a coupon is not applied correctly etc...
        // can only be fixed once ZM implement its own payment/order total module
        // note that we will also have to edit the zencart payment module that is used, and comment out the redirection
        // hint: replace "zen_redirect" with return false;//zen_redirect
        $this->messageService->loadMessages($request->getSession());
        foreach($this->messageService->getMessages() as $message){
            if($message->getType() == "error" || $message->getType() == "warn")
                return false;
        }
        //$this->setA("next", $this->getNextStep());
        return true;
    }


    public function stepCheckoutConfirmationGet($request){
        // some defaults
        $orderFormContent =  '';
        $orderFormUrl = $request->url('checkout_process', '', true);

        if (null != ($paymentType = $this->shoppingCart->getSelectedPaymentType())) {
            $orderFormContent = $paymentType->getOrderFormContent($request);
            $orderFormUrl = $paymentType->getOrderFormUrl($request);
        }
        $totals = $this->shoppingCart->getTotals();

        $this->setA("view", array("checkout_confirmation" => $this->generateView($request, "checkout_confirmation", array('shoppingCart' => $this->shoppingCart, 'orderFormContent' => $orderFormContent, 'orderFormUrl' => $orderFormUrl))));
        return true;
    }

    private function integrityCheck($request){
    // do we really need to verifyHash? What for? If we already recheckout stock at the last step
//        if (!$this->checkoutHelper->verifyHash($request)) {
//            return $this->findView('check_cart');
//        }

        // some redirection here
        if (null !== ($viewId = $this->checkoutHelper->validateCheckout($request, false)) && 'require_shipping' != $viewId) {
            switch($viewId){
                case "require_shipping":
                    return false;
                    break;
                case "login":
                    $this->setA("next", "checkout_login_register");
                    return false;
                    break;
                default:
                    // very very hacky here, should handle the redirection view better
                    $this->setA("redirect", $request->url("shopping_cart"));
                    return false;
                    break;
            }
        }

        if (null !== ($viewId = $this->checkoutHelper->validateAddresses($request, true))) {
            // if false here we will be required to go back to address step
            $this->setA("next", "checkout_address");
            return false;
        }

        return true;
    }

    private function login($request) {
        $session = $request->getSession();

        // get before doing anything with the session!
        $lastUrl = $request->getLastUrl();

        if (!$session->isStarted()) {
            $session->removeValue(self::KEY_REDIRECT);
            $this->setA("redirect", "cookie_usage");
            return false;
        }

        if ($session->isRegistered()) {
            // already logged in
            $session->removeValue(self::KEY_REDIRECT);
            return true;
        }

        if (!$this->validate($request, 'onepage_login')) {
            return false;
        }

        $emailAddress = $request->getParameter('email');
        $account = $this->container->get('accountService')->getAccountForEmailAddress($emailAddress);
        if (null === $account) {
            $this->messageService->error(_zm('Sorry, there is no match for that email address and/or password.'));
            return false;
        }

        $password = $request->getParameter('password');
        if (!$this->container->get('authenticationManager')->validatePassword($password, $account->getPassword())) {
            $this->messageService->error(_zm('Sorry, there is no match for that email address and/or password.'));
            return false;
        }

        if (!$session->registerAccount($account, $request, $this)) {
            return false;
        }

        return true;
        //return $this->findView('success', array(), array('url' => $stickyUrl));
    }

    private function createAnonymousAccount($request){

        if (!$this->validate($request, 'onepage_checkout_guest')) {
            return false;
        }

        $session = $request->getSession();
        // create anonymous account
        $account = Beans::getBean("ZMAccount");
        $account->setEmail($request->getParameter('email'));
        $account->setEmailFormat('HTML');
        $account->setPassword('');
        $account->setDob(\ZMDatabase::NULL_DATETIME);
        $account->setType(\ZMAccount::GUEST);
        $account = $this->container->get('accountService')->createAccount($account);

        // update session with valid account
        $session->regenerate();
        $session->setAccount($account);
        $session->setValue('set_default_address', true);
        return true;
    }

    private function createNormalAccount($request){

		if (!$this->validate($request, 'onepage_register')) {
            return false;
        }

    	$registration = $this->customGetFormData($request, 'ZMRegistrationForm', 'registration');

        $clearPassword = $registration->getPassword();
        $account = $registration->getAccount();
        $account->setEmailFormat('HTML');
        $account->setPassword($this->container->get('authenticationManager')->encryptPassword($clearPassword));
        $account = $this->container->get('accountService')->createAccount($account);


        // here we have a proper account, so time to let other know about it
        $args = array('request' => $request, 'controller' => $this, 'account' => $account, 'address' => $address, 'clearPassword' => $clearPassword);
        Runtime::getEventDispatcher()->dispatch('create_account', new Event($this, $args));

        // in case it got changed
        $this->container->get('accountService')->updateAccount($account);

        $session = $request->getSession();
        $session->regenerate();
        $session->setAccount($account);
        $session->restoreCart();

        $this->messageService->success(_zm("Thank you for signing up"));

        $session->setValue('set_default_address', true);
        $session->setValue('send_welcome_email', true);

        return true;
    }

	public function customGetFormData($request, $form = null, $formId = null) {
        if (null == $this->formData_ && null !== ($mapping = \ZMUrlManager::instance()->findMapping($this->requestId_))) {
            if (null != $form) {
                $this->formData_ =  Beans::getBean($form.(false === strpos($mapping['view'], '#') ? '#' : '&').'formId='.$formId);
                if ($this->formData_ instanceof FormData) {
                    $this->formData_->populate($request);
                } else {
                    $this->formData_ = Beans::setAll($this->formData_, $request->getParameterMap());
                }
            }
        }

        return $this->formData_;
    }
}
