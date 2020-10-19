<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stripe Library for CodeIgniter 3.x
 *
 * Library for Stripe payment gateway. It helps to integrate Stripe payment gateway
 * in CodeIgniter application.
 *
 * This library requires the Stripe PHP bindings and it should be placed in the third_party folder.
 * It also requires Stripe API configuration file and it should be placed in the config directory.
 *
 * @package     CodeIgniter
 * @category    Libraries
 * @author      CodexWorld
 * @license     http://www.codexworld.com/license/
 * @link        http://www.codexworld.com
 * @version     3.0
 */

class Stripe_lib{
    var $CI;
	var $api_error;
    
    function __construct(){
		$this->api_error = '';
        $this->CI =& get_instance();
        $this->CI->load->config('stripe');
		
		// Include the Stripe PHP bindings library
		require APPPATH .'third_party/stripe-php/init.php';
		
		// Set API key
		\Stripe\Stripe::setApiKey($this->CI->config->item('stripe_api_key'));
    }

    function addCustomer($name, $email, $token){
		try {
			// Add customer to stripe
			$customer = \Stripe\Customer::create(array(
				'name' => $name,
				'email' => $email,
				'source'  => $token
			));
			return $customer;
		}catch(Exception $e) {
			$this->api_error = $e->getMessage();
			return false;
		}
    }
	
	function createPlan($planName, $planPrice, $planInterval){
		// Convert price to cents
		$priceCents = ($planPrice*100);
		$currency = $this->CI->config->item('stripe_currency');
		
		try {
			// Create a plan
			$plan = \Stripe\Plan::create(array(
				"product" => [
					"name" => $planName
				],
				"amount" => $priceCents,
				"currency" => $currency,
				"interval" => $planInterval,
				"interval_count" => 1
			));
			return $plan;
		}catch(Exception $e) {
			$this->api_error = $e->getMessage();
			return false;
		}
    }
	
	function createSubscription($customerID, $planID){
		try {
			// Creates a new subscription
			$subscription = \Stripe\Subscription::create(array(
				"customer" => $customerID,
				"items" => array(
					array(
						"plan" => $planID
					),
				),
			));
			
			// Retrieve charge details
			$subsData = $subscription->jsonSerialize();
			return $subsData;
		}catch(Exception $e) {
			$this->api_error = $e->getMessage();
			return false;
		}
    }
}