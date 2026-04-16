<?php
/**
 * Class PTWooPlugins_FSHO file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Our main class
 */
final class PTWooPlugins_FSHO {

	/**
	 * Single instance.
	 *
	 * @var $instance The classs single instances
	 */
	protected static $instance = null;

	/**
	 * Methods to hide.
	 *
	 * @var array
	 */
	public $methods_to_hide = array();

	/**
	 * Our methods IDs.
	 *
	 * @var array
	 */
	private $our_methods = array(
		'free_shipping_hide_others',
		'flat_rate_hide_others',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Ensures only one instance of our plugin is loaded or can be loaded
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize Hooks
	 */
	private function init_hooks() {
		add_filter( 'woocommerce_shipping_methods', array( $this, 'add_shipping_methods' ) );
	}

	/**
	 * Add our shipping methods
	 *
	 * @param array $methods The shipping methods.
	 * @return array
	 */
	public function add_shipping_methods( $methods ) {
		require_once __DIR__ . '/class-wc-shipping-free-shipping-hide-others.php';
		require_once __DIR__ . '/class-wc-shipping-flat-rate-hide-others.php';
		$methods['free_shipping_hide_others'] = 'WC_Shipping_Free_Shipping_Hide_Others';
		$methods['flat_rate_hide_others']     = 'WC_Shipping_Flat_Rate_Hide_Others';
		return $methods;
	}

	/**
	 * Get methods to hide
	 *
	 * @param int $zone_id The zone id.
	 * @return array
	 */
	public function get_methods_to_hide( $zone_id ) {
		if ( ! isset( $this->methods_to_hide[ $zone_id ] ) ) {
			$this->methods_to_hide[ $zone_id ] = array();
			global $wpdb;
			$methods = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE zone_id = %d ORDER BY method_order ASC",
					$zone_id
				)
			);
			if ( is_array( $methods ) && count( $methods ) > 0 ) {
				$wc_shipping     = WC_Shipping::instance();
				$allowed_classes = $wc_shipping->get_shipping_method_class_names();
				$methods_to_hide = array();
				foreach ( $methods as $method ) {
					if ( isset( $allowed_classes[ $method->method_id ] ) ) { // Because we may have entries on the database for non-active shipping plugins
						if ( ! in_array( $method->method_id, $this->our_methods, true ) ) {
							$temp_instance     = new $allowed_classes[ $method->method_id ]( $method->instance_id );
							$method->title     = $temp_instance->get_title();
							$methods_to_hide[] = $method;
						} elseif ( $temp_settings = get_option( 'woocommerce_' . $method->method_id . '_' . $method->instance_id . '_settings' ) ) { // phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
								$temp_method              = new stdClass();
								$temp_method->instance_id = $method->instance_id;
								$temp_method->method_id   = $method->method_id;
								$temp_method->title       = $temp_settings['title'];
								$methods_to_hide[]        = $temp_method;
						}
					}
				}
				if ( count( $methods_to_hide ) > 0 ) {
					foreach ( $methods_to_hide as $method ) {
						$this->methods_to_hide[ $zone_id ][ 'hide_others_' . $method->instance_id ] = array(
							'title'        => $method->title,
							'label'        => $method->method_id . ':' . $method->instance_id,
							'type'         => 'checkbox',
							'default'      => '',
							'_instance_id' => intval( $method->instance_id ),
						);
					}
				}
			}
		}
		return $this->methods_to_hide[ $zone_id ];
	}

	/**
	 * Get shipping classes from package
	 *
	 * @param array $package The shipping package.
	 * @return array
	 */
	public function find_shipping_classes_on_cart( $package ) {
		$found_shipping_classes = array();
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['data']->needs_shipping() ) {
				$found_shipping_classes[] = $values['data']->get_shipping_class();
			}
		}
		return array_unique( $found_shipping_classes );
	}

	/**
	 * Get the WC form field definition for the "Restrict to user roles" multiselect.
	 * Shared by both shipping method classes.
	 *
	 * @return array
	 */
	public function get_user_roles_field_definition() {
		$roles   = wp_roles()->get_names();
		$options = array(
			'fsho_guest' => __( 'Guest (non-logged-in)', 'free-shipping-hide-other-methods-woo' ),
		);
		foreach ( $roles as $role_value => $role_name ) {
			$options[ $role_value ] = translate_user_role( $role_name );
		}
		return array(
			'title'       => __( 'Restrict to user roles', 'free-shipping-hide-other-methods-woo' ),
			'type'        => 'multiselect',
			'class'       => 'wc-enhanced-select',
			'options'     => $options,
			'default'     => array(),
			'description' => __( 'Only show this method to users with the selected roles. Leave blank to allow all roles.', 'free-shipping-hide-other-methods-woo' ),
			'desc_tip'    => true,
		);
	}

	/**
	 * Check whether this shipping method should be available for the current user based on allowed roles.
	 *
	 * @param array $allowed_roles The roles allowed to see this method. Empty array means all roles are allowed.
	 * @return bool
	 */
	public function is_available_for_user_role( $allowed_roles ) {
		if ( empty( $allowed_roles ) ) {
			return true;
		}
		if ( ! is_user_logged_in() ) {
			return in_array( 'fsho_guest', $allowed_roles, true );
		}
		$real_roles = array_diff( $allowed_roles, array( 'fsho_guest' ) );
		return count( array_intersect( $real_roles, wp_get_current_user()->roles ) ) > 0;
	}
}
