<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Custom_Signup_Columns {

    public function __construct() {
        add_filter( 'manage_users_columns', [ $this, 'add_custom_user_columns' ] );
        add_action( 'manage_users_custom_column', [ $this, 'show_custom_user_column_data' ], 10, 3 );
        add_filter( 'manage_users_sortable_columns', [ $this, 'make_user_id_column_sortable' ] );
    }

    public function add_custom_user_columns( $columns ) {
        // Add User ID column
        $columns['user_id'] = __( 'User ID', 'wc-custom-signup' );
        $columns['first_name'] = __( 'First Name', 'wc-custom-signup' );
        $columns['last_name'] = __( 'Last Name', 'wc-custom-signup' );
        $columns['phone'] = __( 'Phone', 'wc-custom-signup' );
        $columns['company_name'] = __( 'Company Name', 'wc-custom-signup' );
        $columns['legal_entity_type'] = __( 'Legal Entity Type', 'wc-custom-signup' );
        $columns['gst_number'] = __( 'GST Number', 'wc-custom-signup' );
        $columns['territory'] = __( 'Territory', 'wc-custom-signup' );
        return $columns;
    }

    public function show_custom_user_column_data( $value, $column_name, $user_id ) {
        switch ( $column_name ) {
            case 'user_id':
                return $user_id; // Display the user ID
            case 'first_name':
                return get_user_meta( $user_id, 'first_name', true );
            case 'last_name':
                return get_user_meta( $user_id, 'last_name', true );
            case 'phone':
                return get_user_meta( $user_id, 'phone', true );
            case 'company_name':
                return get_user_meta( $user_id, 'company_name', true );
            case 'legal_entity_type':
                return get_user_meta( $user_id, 'legal_entity_type', true );
            case 'gst_number':
                return get_user_meta( $user_id, 'gst_number', true );
            case 'territory':
                return get_user_meta( $user_id, 'territory', true );
            default:
                return $value;
        }
    }

    public function make_user_id_column_sortable( $columns ) {
        $columns['user_id'] = 'ID';
        return $columns;
    }
}

new WC_Custom_Signup_Columns();
