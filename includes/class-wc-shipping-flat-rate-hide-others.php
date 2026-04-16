<?php
/**
 * Class WC_Shipping_Flat_Rate_Hide_Others file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flat Rate Shipping Method with extended possibility to hide other methods
 *
 * @class   WC_Shipping_Flat_Rate_Hide_Others
 * @version 1.1
 */
class WC_Shipping_Flat_Rate_Hide_Others extends WC_Shipping_Flat_Rate {

	/**
	 * Methods to hide.
	 *
	 * @var array
	 */
	private $methods_to_hide = false;

	/**
	 * Internal zone id.
	 *
	 * @var int
	 */
	private $zone_id = false;

	/**
	 * Requires option.
	 *
	 * @var string
	 */
	public $requires = '';

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Shipping method instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'flat_rate_hide_others';
		$this->instance_id        = absint( $instance_id );
		$data_store               = WC_Data_Store::load( 'shipping-zone' );
		$this->zone_id            = $data_store->get_zone_id_by_instance_id( $this->instance_id );
		$this->method_title       = __( 'Flat rate (hide other methods)', 'free-shipping-hide-other-methods-woo' );
		$this->method_description = __( 'Lets you charge a fixed rate for shipping and has the possibility to hide other shipping methods when active.', 'free-shipping-hide-other-methods-woo' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->init();
		// Our filter to hide other methods - This is running three times, why? - https://github.com/woocommerce/woocommerce/issues/27849
		add_filter( 'woocommerce_package_rates', array( $this, 'hide_if_flat_active' ) );
	}

	/**
	 * Initialize free shipping.
	 */
	public function init() {
		// The same as the parent class
		parent::init();
		$this->requires = $this->get_option( 'requires' );
		// Actions - Our javascript adapted from the original class, because they hardcoded the class name - We use the class name so that it's called only once
		add_action( 'admin_footer', array( 'WC_Shipping_Flat_Rate_Hide_Others', 'enqueue_admin_js' ), 10 ); // Priority needs to be higher than wc_print_js (25).
	}

	/**
	 * Get methods to hide.
	 *
	 * @return array
	 */
	public function get_instance_form_fields_methods_to_hide() {
		if ( ! $this->methods_to_hide ) {
			$this->methods_to_hide = array();
			if ( $this->zone_id !== false && is_numeric( $this->zone_id ) ) {
				$this->methods_to_hide = PTWooPlugins_FSHO()->get_methods_to_hide( $this->zone_id );
				foreach ( $this->methods_to_hide as $key => $method ) {
					if ( $method['_instance_id'] === $this->instance_id ) {
						unset( $this->methods_to_hide[ $key ] );
					}
				}
			}
		}
		return $this->methods_to_hide;
	}

	/**
	 * Get setting form fields for instances of this shipping method within zones.
	 *
	 * @return array
	 */
	public function get_instance_form_fields() {
		// Get parent fields
		$fields = parent::get_instance_form_fields();
		// And then add ours
		if ( $this->instance_id > 0 ) {
			$new_fields = array();
			foreach ( $fields as $key => $field ) {
				$new_fields[ $key ] = $field;
				if ( $key === 'tax_status' ) {
					// Requires field, after tax status, similar to Free shipping but with just one option
					$new_fields['requires'] = array(
						'title'   => __( 'Flat rate requires...', 'free-shipping-hide-other-methods-woo' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'default' => '',
						'options' => array(
							''                    => __( 'N/A', 'free-shipping-hide-other-methods-woo' ),
							'fsho_shipping_class' => __( 'All products are in the same shipping class', 'free-shipping-hide-other-methods-woo' ),
							'fsho_min_amount'     => __( 'A minimum order amount', 'free-shipping-hide-other-methods-woo' ),
						),
					);
					$shipping_classes       = WC()->shipping()->get_shipping_classes();
					$options                = array(
						'' => __( 'N/A', 'free-shipping-hide-other-methods-woo' ),
					);
					foreach ( $shipping_classes as $shipping_class ) {
						$options[ $shipping_class->slug ] = $shipping_class->name;
					}
					$new_fields['fso_shipping_class'] = array(
						'title'   => __( 'Shipping class', 'free-shipping-hide-other-methods-woo' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => $options,
						'default' => '',
					);
					$new_fields['fso_min_amount']     = array(
						'title'             => __( 'Minimum order amount', 'free-shipping-hide-other-methods-woo' ),
						'type'              => 'text',
						'class'             => 'wc-shipping-modal-price',
						'placeholder'       => wc_format_localized_price( 0 ),
						'description'       => __( 'Customers will need to spend this amount to get free shipping.', 'free-shipping-hide-other-methods-woo' ),
						'default'           => '0',
						'desc_tip'          => true,
						'sanitize_callback' => array( $this, 'sanitize_cost' ),
					);
				}
			}
			// Hide methods
			$new_fields['fso_hide']    = array(
				'title'       => __( 'Hide other methods', 'free-shipping-hide-other-methods-woo' ),
				'type'        => 'title',
				'description' => __( 'Select which other methods to hide when this method is available', 'free-shipping-hide-other-methods-woo' ),
			);
			$new_fields                = array_merge( $new_fields, $this->get_instance_form_fields_methods_to_hide() );
			$new_fields['fso_reviews'] = array(
				'title'       => __( 'Is this plugin useful?', 'free-shipping-hide-other-methods-woo' ),
				'type'        => 'title',
				'description' => __( 'Consider <a href="https://wordpress.org/support/plugin/free-shipping-hide-other-methods-woo/reviews/" target="_blank">leaving us a review</a> if you find this plugin useful', 'free-shipping-hide-other-methods-woo' ),
			);
			// Return them
			return $new_fields;
		}
		// Return them
		return $fields;
	}

	/**
	 * Enqueue JS to handle free shipping options.
	 *
	 * Static so that's enqueued only once.
	 */
	public static function enqueue_admin_js() {
		$handle = 'flat-rate-hide-others-admin';
		wp_register_script( $handle, '', array( 'jquery' ), WC_VERSION, array( 'in_footer' => true ) );
		wp_enqueue_script( $handle );
		wp_add_inline_script(
			$handle,
			"jQuery( function( $ ) {
				function wcFlatRateHideOthersShowHideFields( el ) {
					var form = $( el ).closest( 'form' );
					var shippingClassField      = $( '#woocommerce_flat_rate_hide_others_fso_shipping_class', form ).closest( 'tr' );
					var shippingClassFieldLabel = null;
					if ( shippingClassField.length === 0 ) {
						var shippingClassField      = $( '#woocommerce_flat_rate_hide_others_fso_shipping_class', form ).closest( 'fieldset' );
						var shippingClassFieldLabel = $( 'label[for=woocommerce_flat_rate_hide_others_fso_shipping_class]' );
					}
					var minAmountField      = $( '#woocommerce_flat_rate_hide_others_fso_min_amount', form ).closest( 'tr' );
					var minAmountFieldLabel = null;
					if ( minAmountField.length === 0 ) {
						var minAmountField      = $( '#woocommerce_flat_rate_hide_others_fso_min_amount', form ).closest( 'fieldset' );
						var minAmountFieldLabel = $( 'label[for=woocommerce_flat_rate_hide_others_fso_min_amount]' );
					}
					switch( $( el ).val() ) {
						case '':
							shippingClassField.hide();
							shippingClassFieldLabel?.hide();
							minAmountField.hide();
							minAmountFieldLabel?.hide();
							break;
						case 'fsho_shipping_class':
							shippingClassField.show();
							shippingClassFieldLabel?.show();
							minAmountField.hide();
							minAmountFieldLabel?.hide();
							break;
						case 'fsho_min_amount':
							minAmountField.show();
							minAmountFieldLabel?.show();
							shippingClassField.hide();
							shippingClassFieldLabel?.hide();
							break;
					}
				}

				$( document.body ).on( 'change', '#woocommerce_flat_rate_hide_others_requires', function() {
					wcFlatRateHideOthersShowHideFields( this );
				});

				// Change while load.
				$( '#woocommerce_flat_rate_hide_others_requires' ).trigger( 'change' );
				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
						wcFlatRateHideOthersShowHideFields( $( '#wc-backbone-modal-dialog #woocommerce_flat_rate_hide_others_requires', evt.currentTarget ) );
					}
				} );
			} );"
		);
	}

	/**
	 * See if flat rate is available based on the package and cart.
	 * We can user is_available because the core WooCommerce Flat Rate method does not declare it.
	 *
	 * @param array $package Shipping package.
	 * @return bool
	 */
	public function is_available( $package ) {
		// By default, we are available, but we might need to check some conditions based on the "requires" setting
		$is_available = true;
		// Needs...
		switch ( $this->requires ) {
			case 'fsho_shipping_class':
				$shipping_classes_on_cart = PTWooPlugins_FSHO()->find_shipping_classes_on_cart( $package );
				if ( count( $shipping_classes_on_cart ) !== 1 || $shipping_classes_on_cart[0] !== $this->get_option( 'fso_shipping_class' ) ) {
					return false;
				}
				break;
			case 'fsho_min_amount':
				$total = WC()->cart->get_displayed_subtotal();
				if ( $total < $this->get_option( 'fso_min_amount' ) ) {
					return false;
				}
				break;
		}
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
	}

	/**
	 * Hide other shipping methods if our is active
	 *
	 * @param array $rates The shipping rates.
	 */
	public function hide_if_flat_active( $rates ) {
		if ( intval( $this->instance_id ) > 0 ) {
			// Our method is active?
			$do_it = false;
			foreach ( $rates as $rate_id => $rate ) {
				if ( $this->instance_id === $rate->instance_id ) {
					$do_it = true;
					break;
				}
			}
			// Let's remove the others then
			if ( $do_it ) {
				$new_rates = array();
				foreach ( $rates as $rate_id => $rate ) {
					// Not isset the setting and if its set its not "yes"
					if ( ! ( isset( $this->instance_settings[ 'hide_others_' . $rate->instance_id ] ) && $this->instance_settings[ 'hide_others_' . $rate->instance_id ] === 'yes' ) ) {
						$new_rates[ $rate_id ] = $rate;
					}
				}
				return $new_rates;
			}
		}
		return $rates;
	}
}
