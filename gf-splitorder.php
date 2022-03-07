<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ghostfiregaming.com/
 * @since             1.0.0
 * @package           Gf_Splitorder
 *
 * @wordpress-plugin
 * Plugin Name:       gf-splitorder
 * Plugin URI:        https://morningstardigital.com.au/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.5
 * Author:            Morningstar Digital
 * Author URI:        https://morningstardigital.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gf-splitorder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define WCBS_PLUGIN_FILE.
if ( ! defined( 'GFSO_PLUGIN_FILE' ) ) {
	define( 'GFSO_PLUGIN_FILE', __FILE__ );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GF_SPLITORDER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gf-splitorder-activator.php
 */
function activate_gf_splitorder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gf-splitorder-activator.php';
	Gf_Splitorder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gf-splitorder-deactivator.php
 */
function deactivate_gf_splitorder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gf-splitorder-deactivator.php';
	Gf_Splitorder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gf_splitorder' );
register_deactivation_hook( __FILE__, 'deactivate_gf_splitorder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gf-splitorder.php';
// require plugin_dir_path( __FILE__ ) . 'includes/class-gf-splitorder-frontend.php';

// wp-content\plugins\woocommerce\includes\class-wc-checkout.php
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gf_splitorder() {

	$plugin = new Gf_Splitorder();
	$plugin->run();

}

// add_action( 'woocommerce_review_order_before_submit', 'gf_splitorder_footer', 1 );
add_action( 'woocommerce_review_order_after_order_total', 'gf_splitorder_checkbox', 1 ); // add checkbox and button on checkout
// add_action( 'woocommerce_checkout_update_order_review', 'gf_splitorder_footer', 1 );
// add_action( 'woocommerce_checkout_create_order', 'gf_splitorder_payment_complete', 1, 1);
add_action( 'woocommerce_payment_complete', 'gf_splitorder_payment_complete', 1, 1); // splites order after payment is completed
add_filter( 'woocommerce_checkout_posted_data', 'gf_splitorder_parse_splitorder_var' );  // add splitorder session to posteddata
add_action( 'wp_footer', 'gf_splitorder_footer'); // add modal on footer
// add_action( 'woocommerce_order_item_add_action_buttons', 'gf_splitorder_admin_order_item_add_action_buttons'); // add button to admin

add_filter( 'woocommerce_package_rates', 'gf_custom_shipping_costs', 20, 2 ); // doubles shipping


add_action("wp_ajax_set_split_order_backend", "set_split_order_backend"); //  set cookie when checking checkbox
add_action("wp_ajax_nopriv_set_split_order_backend", "set_split_order_backend"); //  set cookie when checking checkbox

add_action('woocommerce_checkout_update_order_review', 'gf_update_order_review', 10, 2); // to refresh orderreivew in front

// add_action( 'wp_ajax_admin_split_order', 'admin_split_order' );

add_action( "wp_ajax_gf_reload_footer", "gf_reload_footer"); // reload gf footer
add_action( "wp_ajax_nopriv_gf_reload_footer", "gf_reload_footer"); // reload gf footer


add_filter( 'woocommerce_update_cart_action_cart_updated', 'unset_split_order_backend', 20, 1 ); // removes

add_action( "woocommerce_after_shipping_rate", "gf_after_shipping_rate");

add_action( 'woocommerce_before_checkout_form', 'gf_checkout_add_cart_notice' );
add_action( 'woocommerce_before_cart', 'gf_cart_notice' );
add_action( 'woocommerce_cart_totals_before_shipping_even_noshipping', 'gf_cart_totals_before_shipping' );

function gf_cart_totals_before_shipping()
{
	if ( MD_check_cart_has_backorder_product() AND MD_check_cart_has_instock_product() ) {
		include plugin_dir_path( __FILE__ ) . 'public/partials/gf-splitorder-public-carttotalsbeforeshipping.php';
	}
}


function gf_cart_notice() {
    $message = "Important: We noticed that you have ordered a Pre-Order items in your cart.
	Ghostfire allows you to split your order up between products that are available and products that are on pre-order.
	You may see the option to split your order on checkout page under your order summary.";

    if ( MD_check_cart_has_backorder_product() AND MD_check_cart_has_instock_product() )
        echo '<div id="woocommerce-cart-notice-backorder" class="woocommerce-cart-notice woocommerce-error" style="display: block;">' . $message.  '</div>';

}

function gf_checkout_add_cart_notice() {
    $message = "Important: We noticed that you have ordered a Pre-Order items in your cart.
	Ghostfire allows you to split your order up between products that are available and products that are on pre-order.
	See the breakup of your order to have your available products delivered ASAP and your pre-order products when they become available.";

    if ( MD_check_cart_has_backorder_product() AND MD_check_cart_has_instock_product() )
        echo '<div id="woocommerce-cart-notice-backorder" class="woocommerce-cart-notice woocommerce-error" style="display: block;">' . $message.  '</div>';

}

function gf_after_shipping_rate()
{
	global $woocommerce;
	global $wp;
	if($wp->request != 'checkout') {
		return false;
	}
	if (isset( $woocommerce->cart )) {
		WC()->session->__unset( 'gf_splitorder_originalShippingCost' );
		$splitOrderVal = WC()->session->get( 'gf_splitorder_splitorderval' );
		$gfSessionVal = ( isset($splitOrderVal ) && $splitOrderVal == 1 ) ? 1: 0;

		if (  $splitOrderVal != 1 ) {
			$cart_shipping_total = $woocommerce->cart->get_shipping_total();
			WC()->session->set(
				'gf_splitorder_originalShippingCost', $cart_shipping_total
			);

		}

	    $items = $woocommerce->cart->get_cart();
		$hasOnbackorder = 0;
		$hasInstock = 0;
		foreach ($items as $item) {
			if ( $item['data']->get_stock_status() == 'onbackorder') {
				$hasOnbackorder++;
			}

			if ( $item['data']->get_stock_status() == 'instock') {
				$hasInstock++;
			}
		}

		if ($hasOnbackorder > 0 && $hasInstock > 0) {
			include plugin_dir_path( __FILE__ ) . 'public/partials/gf-splitorder-public-aftershippingrate.php';
		}
	}
}

function gf_reload_footer()
{
	global $woocommerce;
	WC()->session->__unset( 'gf_splitorder_originalShippingCost' );

	$cart_shipping_total = $woocommerce->cart->get_shipping_total();
	WC()->session->set(
		'gf_splitorder_originalShippingCost', $cart_shipping_total
	);

	$calcfooter = calcfooter();
	if ($calcfooter['load'] == true) {
		foreach ($calcfooter as $key => $val) {
			$$key = $val;
		}
		include plugin_dir_path( __FILE__ ) . 'public/partials/gf-splitorder-public-footer.php';
	}


	wp_die();
}

function calcfooter()
{
	global $woocommerce;
	if (isset( $woocommerce->cart )) {

	    $items = $woocommerce->cart->get_cart();
	    $cart_shipping_total = WC()->session->get( 'gf_splitorder_originalShippingCost' );
	    $cart_shipping_total = $woocommerce->cart->get_shipping_total();
		$hasOnbackorder = 0;
		$hasInstock = 0;
		$backorderItems = [];
		$backorderItemsSubtotal = 0;
		$instockItems = [];
		$instockItemsSubtotal = 0;

		$backorderWeight = 0;
		$backorderShipping = 0;
		foreach ($items as $item) {

			if ( $item['data']->get_stock_status() == 'onbackorder') {
				$hasOnbackorder++;
				$backorderItems[] = $item;
				$backorderItemsSubtotal += $item['line_subtotal'];

				$qty = is_numeric($item['quantity']) ? $item['quantity'] : 0;
				$weight = is_numeric($item['data']->get_weight()) ? $item['data']->get_weight() : 0;
				$backorderWeight += $qty * $weight;

			}

			if ( $item['data']->get_stock_status() == 'instock') {
				$hasInstock++;
				$instockItems[] = $item;
				$instockItemsSubtotal += $item['line_subtotal'];
			}
		}

		$backorderShipping = getBackorderShipping($backorderWeight);


		if ($hasOnbackorder > 0 && $hasInstock > 0) {
			return [
				'load' => true,
				'instockItems' => $instockItems,
				'instockItemsSubtotal' => $instockItemsSubtotal,
				'backorderItems' => $backorderItems,
				'backorderItemsSubtotal' => $backorderItemsSubtotal,
				'cart_shipping_total' => $cart_shipping_total,
				'backorderShipping' => $backorderShipping,
			]; // show
		}

		return [
			'load' => false,
		]; // show;
	}
}


function getBackorderShipping($backorderWeight, $selectedRate = null)
{

	if ($selectedRate == null) {
		$chosen_shipping_methods = isset(WC()->session->get( 'chosen_shipping_methods' )[0]) ? WC()->session->get( 'chosen_shipping_methods' )[0] : null;
	} else {
		$chosen_shipping_methods = $selectedRate;
	}

	if($chosen_shipping_methods == null) {
		return 0;
	}

	$wpOptionName = '';
	$csm1 = explode(':',$chosen_shipping_methods);
	$wpOptionName = 'woocommerce_'.$csm1[0].'_'.$csm1[1].'_settings';
	$shippingRate = get_option($wpOptionName);
	$selectedCost = 0;
	if(isset($shippingRate['cost'])) {
		$selectedCost = $shippingRate['cost'];
	} else {

		$csm2 = explode('-',$csm1[1]);
		$wpOptionName = $csm1[0] . '_options-' . $csm2[0];

		$shippingRate = get_option($wpOptionName);

		$csmRows = [];
		if (isset($shippingRate['settings'][$csm2[1]])) {
			$csmRows = $shippingRate['settings'][$csm2[1]]['rows'];
		}

		foreach ($csmRows as $csmR) {
			$costs = $csmR['costs'];
			$conditions = $csmR['conditions'];

			$selected = true;
			foreach ($conditions as $cond) {
				if($cond['cond_type'] == 'weight') {

					switch ($cond['cond_secondary']) {
						case 'less_than':
							if ( $backorderWeight > $cond['cond_tertiary']) {
								$selected = false;
							}
						break;

						case 'greater_than':
							if ( $backorderWeight < $cond['cond_tertiary']) {
								$selected = false;
							}
						break;

						case 'equal_to':
							if ( $backorderWeight != $cond['cond_tertiary']) {
								$selected = false;
							}
						break;

						// default: $selected = true; break;
					}

				}

			}

			if ( $selected == true ) {
				$selectedCost = $costs[0]['cost_value'];
				break;
			}

		}

	}

	return $selectedCost;

}

function admin_split_order()
{
	global $wpdb; // this is how you get access to the database

	$order_id = intval( $_POST['order_id'] );
	splitOrderBackorderByOrderId($order_id, false);
}

function gf_update_order_review($post_data)
{
    $packages = WC()->cart->get_shipping_packages();
    foreach ($packages as $package_key => $package ) {
         WC()->session->set( 'shipping_for_package_' . $package_key, false ); // Or true
    }
}

function set_split_order_backend()
{
	$splitOrderVal = $_POST['splitOrderVal'];

	WC()->session->set(
		'gf_splitorder_splitorderval', $splitOrderVal
	);

}

function unset_split_order_backend()
{
	$splitOrderVal = $_POST['splitOrderVal'];
	WC()->session->__unset( 'gf_splitorder_splitorderval' );

}

function gf_custom_shipping_costs( $rates, $package ) {
    // New shipping cost (can be calculated)

	$splitOrderVal = WC()->session->get( 'gf_splitorder_splitorderval' );

	// if( 1 ) {
	if( isset($splitOrderVal ) && $splitOrderVal == 1) {

		global $woocommerce;
		if (isset( $woocommerce->cart )) {
		    $items = $woocommerce->cart->get_cart();
			$backorderItems = [];
			$backorderItemsSubtotal = 0;

			$backorderWeight = 0;
			$backorderShipping = 0;
			foreach ($items as $item) {

				if ( $item['data']->get_stock_status() == 'onbackorder') {
					$backorderItems[] = $item;
					$backorderItemsSubtotal += $item['line_subtotal'];
					$wei = is_numeric($item['data']->get_weight()) ? $item['data']->get_weight() : 0;
					$backorderWeight += $item['quantity'] * $wei;

				}

			}

			$taxRate = array_values( WC_Tax::get_rates() );

			if (isset($taxRate[0])) {
				$taxRate = $taxRate[0]['rate'];
			} else {
				$taxRate = 0;
			}

		    foreach( $rates as $rate_key => $rate ){
				$selectedRate = $rate->get_id();
				$backorderShipping = getBackorderShipping($backorderWeight, $selectedRate);
		        // Excluding free shipping methods
		        if( $rate->method_id != 'free_shipping'){
					// echo '<pre>';print_r($rates);echo '</pre>';die();
					$oldCost = $rates[$rate_key]->cost;
					$new_cost = $oldCost + $backorderShipping;

					// $taxPercent = WC_Tax::get_rate_percent_value($rate);
					// $taxPercent = WC_Tax::get_rate_percent_value($rates[$rate_key]->get_instance_id());

		            // Set rate cost
		            $rates[$rate_key]->cost = $new_cost;

		            // Set taxes rate cost (if enabled)
		            $taxes = array();
		            foreach ($rates[$rate_key]->taxes as $key => $tax){
		                if( $rates[$rate_key]->taxes[$key] > 0 ){
							 $taxes[$key] = $new_cost * ($taxRate/100);
						}

		            }
		            $rates[$rate_key]->taxes = $taxes;

		        }
		    }
	    } else {

		}
	}

	// die();
    return $rates;
}

function gf_splitorder_admin_order_item_add_action_buttons($order)
{
	$inStock = 0;
	$backOrder = 0;
	foreach($order->get_items() as $item_key => $item){
		$product = $item->get_product();
		if ( $product && $product->is_on_backorder() == true ) {
			$backOrder++;
		} else {
			$inStock++;
		}
	}

	if( $backOrder > 0 && $inStock > 0 ) {
		$plugin_dir = '/wp-content/plugins/gf-splitorder/';
		$order_id = $order->get_id();
		wp_enqueue_script( 'gf_splitorder_admin_js', $plugin_dir . 'public/js/gf-splitorder-public-admin.js');
		include plugin_dir_path( __FILE__ ) . 'public/partials/gf-splitorder-public-adminorderbtns.php';
	}
}

function gf_splitorder_payment_complete($order_id)
{
	if (isset(WC()->session)) {
		$splitOrderVal = WC()->session->get( 'gf_splitorder_splitorderval' );
		if( isset($splitOrderVal ) && $splitOrderVal == 1) {

			splitOrderBackorderByOrderId($order_id);
			WC()->session->__unset( 'gf_splitorder_splitorderval' );
			WC()->session->__unset( 'gf_splitorder_originalShippingCost' );
		} else {
			splitByVirtual($order_id);
		}
	}
}


function splitByVirtual($order_id)
{
	$completed_order = new WC_Order($order_id);

	$hasVirtual = false;
	$hasNonVirtual = false;
	foreach($completed_order->get_items() as $item_key => $item){
		$product = $item->get_product();
		if ( $product && ($product->get_virtual() == true || $product->get_type() == 'variable-subscription' ) ) {
			$hasVirtual = true;
		} else {
			$hasNonVirtual = true;
		}
	}

	if ($hasVirtual == true && $hasNonVirtual == true) {
		$address_billing = array(
			'first_name' => $completed_order->get_billing_first_name(),
			'last_name'  => $completed_order->get_billing_last_name(),
			'company'    => '',
			'email'      => $completed_order->get_billing_email(),
			'phone'      => $completed_order->get_billing_phone(),
			'address_1'  => $completed_order->get_billing_address_1(),
			'address_2'  => $completed_order->get_billing_address_2(),
			'city'       => $completed_order->get_billing_city(),
			'state'      => $completed_order->get_billing_state(),
			'postcode'   => $completed_order->get_billing_postcode(),
			'country'    => $completed_order->get_billing_country()
		);

		$address_shipping = array(
			'first_name' => $completed_order->get_shipping_first_name(),
			'last_name'  => $completed_order->get_shipping_last_name(),
			'company'    => '',
			'email'      => $completed_order->get_billing_email(),
			'phone'      => $completed_order->get_billing_phone(),
			'address_1'  => $completed_order->get_shipping_address_1(),
			'address_2'  => $completed_order->get_shipping_address_2(),
			'city'       => $completed_order->get_shipping_city(),
			'state'      => $completed_order->get_shipping_state(),
			'postcode'   => $completed_order->get_shipping_postcode(),
			'country'    => $completed_order->get_shipping_country(),
		);

		//create new order
		$new_order_args = array(
			'customer_id' => $completed_order->get_customer_id(),
			'parent' => $order_id,
			'customer_note' => 'This is a backorder for order_id #' . $order_id,
		);

		$new_order = wc_create_order($new_order_args);
		$new_order_id = $new_order->get_id();

		$new_order->set_address($address_billing, 'billing');
		$new_order->set_address($address_shipping, 'shipping');
		wp_update_post(['ID' => $new_order->get_id(), 'post_status' => 'wc-completed']);

		foreach($completed_order->get_items() as $item_key => $item){

			$product = $item->get_product();

			if ( $product && ($product->get_virtual() == true || $product->get_type() == 'variable-subscription' ) ) {
			   $args = [
					'product_id' => $item->get_product_id(),
					'variation_id' => $item->get_variation_id(),
					'variation' => [],
				];
				$item['quantity'] = $item->get_quantity();
				$item_id = $new_order->add_product( $product, $item['quantity']);
				$newItem = $new_order->get_item( $item_id, false );
				$newItem->calculate_taxes($address_shipping);
				$newItem->save();

				wc_delete_order_item($item_key);
		   } else {

		   }
		}

		$completed_order = new WC_Order($order_id);
		$completed_order->calculate_taxes();
		$completed_order->calculate_totals();
		$completed_order->save();

		$new_order->calculate_shipping();
		$new_order->calculate_totals();
		$new_order->save();
	}
}

function splitOrderBackorderByOrderId($order_id, $setShippingFee=true)
{
	$completed_order = new WC_Order($order_id);
	$item_splitted = false;

	$address_billing = array(
		'first_name' => $completed_order->get_billing_first_name(),
		'last_name'  => $completed_order->get_billing_last_name(),
		'company'    => '',
		'email'      => $completed_order->get_billing_email(),
		'phone'      => $completed_order->get_billing_phone(),
		'address_1'  => $completed_order->get_billing_address_1(),
		'address_2'  => $completed_order->get_billing_address_2(),
		'city'       => $completed_order->get_billing_city(),
		'state'      => $completed_order->get_billing_state(),
		'postcode'   => $completed_order->get_billing_postcode(),
		'country'    => $completed_order->get_billing_country()
	);

	$address_shipping = array(
		'first_name' => $completed_order->get_shipping_first_name(),
		'last_name'  => $completed_order->get_shipping_last_name(),
		'company'    => '',
		'email'      => $completed_order->get_billing_email(),
		'phone'      => $completed_order->get_billing_phone(),
		'address_1'  => $completed_order->get_shipping_address_1(),
		'address_2'  => $completed_order->get_shipping_address_2(),
		'city'       => $completed_order->get_shipping_city(),
		'state'      => $completed_order->get_shipping_state(),
		'postcode'   => $completed_order->get_shipping_postcode(),
		'country'    => $completed_order->get_shipping_country(),
	);


	//create new order
	$new_order_args = array(
		'customer_id' => $completed_order->get_customer_id(),
		// 'status' => 'wc-pending',
		'parent' => $order_id,
		'customer_note' => 'This is a backorder for order_id #' . $order_id,
	);

	$new_order = wc_create_order($new_order_args);
	$new_order_id = $new_order->get_id();

	$new_order->set_address($address_billing, 'billing');
	$new_order->set_address($address_shipping, 'shipping');
	wp_update_post(['ID' => $new_order->get_id(), 'post_status' => 'wc-processing']);
	$backorderWeight = 0;
	foreach($completed_order->get_items() as $item_key => $item){

		$product = $item->get_product();
		if ( $product && $product->is_on_backorder( $item->get_quantity() ) == true ) {
		   $args = [
				'product_id' => $item->get_product_id(),
				'variation_id' => $item->get_variation_id(),
				'variation' => [],
			];
			$item['quantity'] = $item->get_quantity();
			$item_id = $new_order->add_product( $product, $item['quantity']);
			$newItem = $new_order->get_item( $item_id, false );
			$newItem->calculate_taxes($address_shipping);
			$newItem->save();
			$wei = is_numeric($product->get_weight()) ? $product->get_weight() : 0;
			$backorderWeight += $item['quantity'] * $wei;

			wc_delete_order_item($item_key);
	   } else {

	   }
	}

	$get_backorder_shipping_total = getBackorderShipping($backorderWeight);
	$completed_order1 = wc_get_order( $order_id );
	$get_shipping_total = $completed_order1->get_shipping_total() - $get_backorder_shipping_total;

	$hasShipp = 0;
	$shippingMethods = $completed_order->get_shipping_methods();
	foreach ( $shippingMethods as $shipping_method ) {
		$get_method_id = $shipping_method->get_method_id();
		$get_instance_id = $shipping_method->get_instance_id();
		$get_method_title = $shipping_method->get_method_title();

		$hasShipp = 1;
	}

	if ($hasShipp == 1 && $setShippingFee == true) {
		$item = new WC_Order_Item_Shipping();
		$item->set_method_title( $get_method_title );
		$item->set_method_id( $get_method_id.":".$get_instance_id ); // set an existing Shipping method rate ID
		$item->set_total( $get_backorder_shipping_total ); // (optional)
		$item->calculate_taxes($address);
		$new_order->add_item( $item );
		$new_order->calculate_totals();
		$new_order->save();
	}
	if ($setShippingFee == true) {
		gf_updateMainOrderShipping($order_id, $get_shipping_total);
	}
	// foreach ($completed_order1->get_shipping_methods() as $item_id => $item_obj) {
	// 	wc_update_order_item_meta($item_id, 'cost', $get_shipping_total);
	// }
	// $completed_order1->set_shipping_total($get_shipping_total); // update shipping total to be saved in db.
	// $completed_order1->calculate_totals();
	// $completed_order1->calculate_shipping();
	// $completed_order1->save();



	$new_order->calculate_shipping();
	$new_order->calculate_totals();
	$new_order->save();

	splitByVirtual($order_id);
	splitByVirtual($new_order_id);

}

function gf_updateMainOrderShipping($order_id, $newShipping) {
	$completed_order1 = wc_get_order( $order_id );
	$get_shipping_total = $newShipping; // divide 2 because split order
	// $get_shipping_total = $completed_order1->get_shipping_total() / 2;
	foreach ($completed_order1->get_shipping_methods() as $item_id => $item_obj) {
		wc_update_order_item_meta($item_id, 'cost', $get_shipping_total);
	}

	$completed_order1->set_shipping_total($get_shipping_total); // update shipping total to be saved in db.
	// $completed_order1->set_status('wc-on-hold');
	$completed_order1->calculate_taxes();
	$completed_order1->calculate_shipping();
	$completed_order1->calculate_totals();
	$completed_order1->save();

	$completed_order = new WC_Order($order_id);
	$completed_order->set_shipping_total($get_shipping_total); // update shipping total to be saved in db.
	// $completed_order->set_status('wc-on-hold');
	$completed_order->calculate_taxes();
	$completed_order->calculate_shipping();
	$completed_order->calculate_totals();
	$completed_order->save();
}

function gf_splitorder_parse_splitorder_var($data)
{
	$data1 = [
		'splitbackorder' => (int) isset( $_POST['splitorder'] )
	];

	return array_merge($data, $data1);
}

function gf_splitorder_checkbox()
{

	global $woocommerce;
	if (isset( $woocommerce->cart )) {
		WC()->session->__unset( 'gf_splitorder_originalShippingCost' );
		$splitOrderVal = WC()->session->get( 'gf_splitorder_splitorderval' );
		$gfSessionVal = ( isset($splitOrderVal ) && $splitOrderVal == 1 ) ? 1: 0;

		if (  $splitOrderVal != 1 ) {
			$cart_shipping_total = $woocommerce->cart->get_shipping_total();
			WC()->session->set(
				'gf_splitorder_originalShippingCost', $cart_shipping_total
			);

		}

	    $items = $woocommerce->cart->get_cart();
		$hasOnbackorder = 0;
		$hasInstock = 0;
		foreach ($items as $item) {
			if ( $item['data']->get_stock_status() == 'onbackorder') {
				$hasOnbackorder++;
			}

			if ( $item['data']->get_stock_status() == 'instock') {
				$hasInstock++;
			}
		}

		if ($hasOnbackorder > 0 && $hasInstock > 0) {
			include plugin_dir_path( __FILE__ ) . 'public/partials/gf-splitorder-public-checkbox.php';
		}
	}

	// return get_template( 'public/partials/gf-splitorder-public-checkbox.php' );
}

function gf_splitorder_footer()
{
	$calcfooter = calcfooter();
	if ($calcfooter['load'] == true) {
		// $plugin_dir = '/wp-content/plugins/gf-splitorder/';
		// wp_enqueue_style( 'gf_splitorder_css', $plugin_dir . 'public/css/gf-splitorder-public.css');
		// wp_enqueue_script( 'gf_splitorder_js', $plugin_dir . 'public/js/gf-splitorder-public.js');
		// $instockItems = $calcfooter['instockItems'];
		// $instockItemsSubtotal = $calcfooter['instockItemsSubtotal'];
		// $backorderItems = $calcfooter['backorderItems'];
		// $backorderItemsSubtotal = $calcfooter['backorderItemsSubtotal'];
		foreach ($calcfooter as $key => $val) {
			$$key = $val;
		}

		include plugin_dir_path( __FILE__ ) . 'public/partials/gf-splitorder-public-footer-wrapper.php';
	}

}

function randomKey($length) {
	$key = '';
    $pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
    for($i=0; $i < $length; $i++) {
        $key .= $pool[mt_rand(0, count($pool) - 1)];
    }
    return $key;
}


run_gf_splitorder();
