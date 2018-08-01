<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://laccadive.io/
 * @since             1.0.0
 * @package           Wc_Guest_Checkout_Individual_Product
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Guest Checkout for Individual Product
 * Plugin URI:        https://github.com/laccadive-io/woocommerce-guest-checkout-for-individual-product
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Hussain Thajutheen
 * Author URI:        https://laccadive.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-guest-checkout-individual-product
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WC_GUEST_CHECKOUT_INDIVIDUAL', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-guest-checkout-individual-product-activator.php
 */
function activate_wc_guest_checkout_individual_product()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-guest-checkout-individual-product-activator.php';
    Wc_Guest_Checkout_Individual_Product_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-guest-checkout-individual-product-deactivator.php
 */
function deactivate_wc_guest_checkout_individual_product()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-guest-checkout-individual-product-deactivator.php';
    Wc_Guest_Checkout_Individual_Product_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wc_guest_checkout_individual_product');
register_deactivation_hook(__FILE__, 'deactivate_wc_guest_checkout_individual_product');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wc-guest-checkout-individual-product.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_guest_checkout_individual_product()
{

    $plugin = new Wc_Guest_Checkout_Individual_Product();
    $plugin->run();

}
run_wc_guest_checkout_individual_product();

/*----------------------------------------------------------
Woocommerce - Allow Guest Checkout for Individual Products
-----------------------------------------------------------*/

// Display Guest Checkout Field
add_action('woocommerce_product_options_general_product_data', 'wc_gcip_add_custom_general_fields');
function wc_gcip_add_custom_general_fields()
{
    global $woocommerce, $post;

    echo '<div class="options_group">';

    // Checkbox
    woocommerce_wp_checkbox(
        array(
            'id' => '_allow_guest_checkout',
            'wrapper_class' => array('show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external', 'show_if_booking'),
            'label' => __('Checkout', 'woocommerce'),
            'description' => __('Allow Guest Checkout', 'woocommerce'),
        )
    );

    echo '</div>';
}

// Save Guest Checkout Field
add_action('woocommerce_process_product_meta', 'wc_gcip_add_custom_general_fields_save');
function wc_gcip_add_custom_general_fields_save($post_id)
{
    $woocommerce_checkbox = isset($_POST['_allow_guest_checkout']) ? 'yes' : 'no';
    update_post_meta($post_id, '_allow_guest_checkout', $woocommerce_checkbox);
}

// Enable Guest Checkout on Certain products
add_filter('pre_option_woocommerce_enable_guest_checkout', 'wc_gcip_enable_guest_checkout_based_on_product');
function wc_gcip_enable_guest_checkout_based_on_product($value)
{

    if (WC()->cart) {
        $cart = WC()->cart->get_cart();
        foreach ($cart as $item) {
            if (get_post_meta($item['product_id'], '_allow_guest_checkout', true) == 'yes') {
                $value = "yes";
            } else {
                $value = "no";
                break;
            }
        }
    }

    return $value;
}
