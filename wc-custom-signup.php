<?php
/**
 * Plugin Name: WooCommerce Custom Signup Fields
 * Description: Adds custom fields to the WooCommerce signup form, saves them to the database, assigns users a custom role, and creates a Sales Portal.
 * Version: 1.3.0
 * Author: Your Name
 * Text Domain: wc-custom-signup
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include the classes
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-custom-signup-fields.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-custom-signup-handler.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-custom-signup-columns.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-sales-portal.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/wc-plugin-debugger.php';


// Include the status report class
add_action( 'admin_notices', 'wc_custom_signup_status_report' );
function wc_custom_signup_status_report() {
    $debugger = new WC_Plugin_Debugger();
    $info = $debugger->gather_plugin_info();

    if ( ! empty( $info ) ) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>' . __( 'Plugin Debug Information', 'wc-custom-signup' ) . '</strong></p>';
        echo '<pre>';
        echo implode( "\n", $info );
        echo '</pre>';
        echo '</div>';
    }
}

// Initialize the custom fields and roles
add_action( 'plugins_loaded', 'wc_custom_signup_init' );
function wc_custom_signup_init() {
    new WC_Custom_Signup_Fields();
    new WC_Custom_Signup_Handler();
    new WC_Custom_Signup_Columns();
    new WC_Sales_Portal();
    new wc_plugin_debugger();
}

// Add custom user roles and pages on plugin activation
register_activation_hook( __FILE__, 'wc_custom_signup_activate' );
function wc_custom_signup_activate() {
    // Create roles
    add_role( 'pending_retailer', __( 'Pending Retailer', 'wc-custom-signup' ), [
        'read' => true,
    ] );

    add_role( 'sales', __( 'Sales', 'wc-custom-signup' ), [
        'read' => true,
        'edit_users' => true,
        'list_users' => true,
        'promote_users' => true,
        'edit_posts' => false,
        'delete_posts' => false,
        'sales_portal_access' => true,
    ] );

    // Create necessary pages
    wc_custom_signup_create_pages();
}

function wc_custom_signup_create_pages() {
    // Sales Portal Page
    $sales_portal_page = array(
        'post_title'     => __('Sales Portal', 'wc-custom-signup'),
        'post_content'   => '[sales_portal]', // Shortcode to display the portal
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_author'    => 1,
        'post_name'      => 'sales-portal'
    );

    // Check if the page already exists
    if ( null == get_page_by_title( $sales_portal_page['post_title'] ) ) {
        // Insert the post into the database
        wp_insert_post( $sales_portal_page );
    }
}

// Handle "Approve Retailer" action
add_action( 'admin_init', 'wc_handle_approve_retailer_action' );
function wc_handle_approve_retailer_action() {
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'approve_retailer' && isset( $_GET['user_id'] ) && current_user_can( 'edit_users' ) ) {
        $user_id = intval( $_GET['user_id'] );
        $user = get_user_by( 'ID', $user_id );

        if ( $user && in_array( 'pending_retailer', $user->roles ) ) {
            $user->set_role( 'retailer' );
            wp_redirect( admin_url( 'users.php?message=retailer_approved' ) );
            exit;
        }
    }
}

// Add custom fields to the "Add New User" page in the admin
add_action( 'user_new_form', 'wc_custom_signup_add_new_user_fields' );
function wc_custom_signup_add_new_user_fields() {
    $provinces = [
        'AB' => 'Alberta',
        'BC' => 'British Columbia',
        'MB' => 'Manitoba',
        'NB' => 'New Brunswick',
        'NL' => 'Newfoundland and Labrador',
        'NS' => 'Nova Scotia',
        'ON' => 'Ontario',
        'PE' => 'Prince Edward Island',
        'QC' => 'Quebec',
        'SK' => 'Saskatchewan',
        'NT' => 'Northwest Territories',
        'NU' => 'Nunavut',
        'YT' => 'Yukon',
    ];
    ?>
    <h3><?php _e('Custom User Information', 'wc-custom-signup'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="first_name"><?php _e('First Name', 'wc-custom-signup'); ?></label></th>
            <td><input type="text" name="first_name" id="first_name" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="last_name"><?php _e('Last Name', 'wc-custom-signup'); ?></label></th>
            <td><input type="text" name="last_name" id="last_name" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="phone"><?php _e('Phone', 'wc-custom-signup'); ?></label></th>
            <td><input type="text" name="phone" id="phone" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="company_name"><?php _e('Company Name', 'wc-custom-signup'); ?></label></th>
            <td><input type="text" name="company_name" id="company_name" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="legal_entity_type"><?php _e('Legal Entity Type', 'wc-custom-signup'); ?></label></th>
            <td>
                <select name="legal_entity_type" id="legal_entity_type" class="regular-text">
                    <option value=""><?php _e('Select an option', 'wc-custom-signup'); ?></option>
                    <option value="Partnership"><?php _e('Partnership', 'wc-custom-signup'); ?></option>
                    <option value="Individual"><?php _e('Individual', 'wc-custom-signup'); ?></option>
                    <option value="Corporation"><?php _e('Corporation', 'wc-custom-signup'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="gst_number"><?php _e('GST Number (if applicable)', 'wc-custom-signup'); ?></label></th>
            <td><input type="text" name="gst_number" id="gst_number" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="territory"><?php _e('Territory', 'wc-custom-signup'); ?></label></th>
            <td>
                <select name="territory" id="territory" class="regular-text">
                    <option value=""><?php _e('Select a province', 'wc-custom-signup'); ?></option>
                    <?php foreach ( $provinces as $code => $name ) : ?>
                        <option value="<?php echo esc_attr( $code ); ?>">
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// Save the custom fields when a new user is created in the admin
add_action( 'user_register', 'wc_custom_signup_save_new_user_fields' );
function wc_custom_signup_save_new_user_fields( $user_id ) {
    if ( isset( $_POST['first_name'] ) ) {
        update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
    }
    if ( isset( $_POST['last_name'] ) ) {
        update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST['last_name'] ) );
    }
    if ( isset( $_POST['phone'] ) ) {
        update_user_meta( $user_id, 'phone', sanitize_text_field( $_POST['phone'] ) );
    }
    if ( isset( $_POST['company_name'] ) ) {
        update_user_meta( $user_id, 'company_name', sanitize_text_field( $_POST['company_name'] ) );
    }
    if ( isset( $_POST['legal_entity_type'] ) ) {
        update_user_meta( $user_id, 'legal_entity_type', sanitize_text_field( $_POST['legal_entity_type'] ) );
    }
    if ( isset( $_POST['gst_number'] ) ) {
        update_user_meta( $user_id, 'gst_number', sanitize_text_field( $_POST['gst_number'] ) );
    }
    if ( isset( $_POST['territory'] ) ) {
        update_user_meta( $user_id, 'territory', sanitize_text_field( $_POST['territory'] ) );
    }
}
