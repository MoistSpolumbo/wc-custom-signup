<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Custom_Signup_Handler {

    public function __construct() {
        add_action( 'woocommerce_register_post', [ $this, 'validate_custom_signup_fields' ], 10, 3 );
        add_action( 'woocommerce_created_customer', [ $this, 'save_custom_signup_fields' ] );
    }

    public function validate_custom_signup_fields( $username, $email, $validation_errors ) {
        if ( isset( $_POST['first_name'] ) && empty( $_POST['first_name'] ) ) {
            $validation_errors->add( 'first_name_error', __( 'First Name is required!', 'wc-custom-signup' ) );
        }
        if ( isset( $_POST['last_name'] ) && empty( $_POST['last_name'] ) ) {
            $validation_errors->add( 'last_name_error', __( 'Last Name is required!', 'wc-custom-signup' ) );
        }
        if ( isset( $_POST['phone'] ) && empty( $_POST['phone'] ) ) {
            $validation_errors->add( 'phone_error', __( 'Phone number is required!', 'wc-custom-signup' ) );
        }
        if ( isset( $_POST['legal_entity_type'] ) && empty( $_POST['legal_entity_type'] ) ) {
            $validation_errors->add( 'legal_entity_type_error', __( 'Legal Entity Type is required!', 'wc-custom-signup' ) );
        }
        if ( isset( $_POST['territory'] ) && empty( $_POST['territory'] ) ) {
            $validation_errors->add( 'territory_error', __( 'Territory is required!', 'wc-custom-signup' ) );
        }
        return $validation_errors;
    }

    public function save_custom_signup_fields( $customer_id ) {
        if ( isset( $_POST['first_name'] ) ) {
            update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
        }
        if ( isset( $_POST['last_name'] ) ) {
            update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['last_name'] ) );
        }
        if ( isset( $_POST['phone'] ) ) {
            update_user_meta( $customer_id, 'phone', sanitize_text_field( $_POST['phone'] ) );
        }
        if ( isset( $_POST['company_name'] ) ) {
            update_user_meta( $customer_id, 'company_name', sanitize_text_field( $_POST['company_name'] ) );
        }
        if ( isset( $_POST['legal_entity_type'] ) ) {
            update_user_meta( $customer_id, 'legal_entity_type', sanitize_text_field( $_POST['legal_entity_type'] ) );
        }
        if ( isset( $_POST['gst_number'] ) ) {
            update_user_meta( $customer_id, 'gst_number', sanitize_text_field( $_POST['gst_number'] ) );
        }
        if ( isset( $_POST['territory'] ) ) {
            update_user_meta( $customer_id, 'territory', sanitize_text_field( $_POST['territory'] ) );
        }

        // Assign the user to the "pending_retailer" role
        $user = new WP_User( $customer_id );
        $user->set_role( 'pending_retailer' );
    }
}
