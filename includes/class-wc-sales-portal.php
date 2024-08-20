<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Sales_Portal {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_sales_portal_menu' ] );
        add_action( 'show_user_profile', [ $this, 'show_sales_territories' ] );
        add_action( 'edit_user_profile', [ $this, 'show_sales_territories' ] );
        add_action( 'personal_options_update', [ $this, 'save_sales_territories' ] );
        add_action( 'edit_user_profile_update', [ $this, 'save_sales_territories' ] );
        add_shortcode( 'sales_portal', [ $this, 'sales_portal_page_content' ] );
    }

    public function add_sales_portal_menu() {
        add_menu_page(
            __( 'Sales Portal', 'wc-custom-signup' ),
            __( 'Sales Portal', 'wc-custom-signup' ),
            'sales_portal_access',
            'sales-portal',
            [ $this, 'sales_portal_page_content' ],
            'dashicons-businessman',
            6
        );
    }

    public function sales_portal_page_content() {
        if ( ! current_user_can( 'sales_portal_access' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'wc-custom-signup' ) );
        }

        echo '<div class="wrap">';
        echo '<h1>' . __( 'Sales Portal', 'wc-custom-signup' ) . '</h1>';

        // Admins see all pending retailers and retailers
        if ( current_user_can( 'manage_options' ) ) {
            $args = [
                'role__in' => ['pending_retailer', 'retailer'],
            ];
        } else {
            // Sales users see only retailers within their assigned territories
            $user_id = get_current_user_id();
            $assigned_territories = get_user_meta( $user_id, 'sales_territories', true );

            if ( empty( $assigned_territories ) ) {
                $assigned_territories = [];
            }

            $args = [
                'role__in'    => ['pending_retailer', 'retailer'],
                'meta_query'  => [
                    [
                        'key'     => 'territory',
                        'value'   => $assigned_territories,
                        'compare' => 'IN',
                    ],
                ],
            ];
        }

        $retailers = get_users( $args );

        if ( ! empty( $retailers ) ) {
            echo '<table class="wp-list-table widefat fixed striped users">';
            echo '<thead><tr><th>' . __( 'Retailer', 'wc-custom-signup' ) . '</th><th>' . __( 'Territory', 'wc-custom-signup' ) . '</th><th>' . __( 'Actions', 'wc-custom-signup' ) . '</th></tr></thead>';
            echo '<tbody>';

            foreach ( $retailers as $retailer ) {
                $territory = get_user_meta( $retailer->ID, 'territory', true );
                echo '<tr>';
                echo '<td>' . esc_html( $retailer->display_name ) . '</td>';
                echo '<td>' . esc_html( $territory ) . '</td>';
                echo '<td>';
                echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $retailer->ID ) ) . '" class="button">' . __( 'Edit', 'wc-custom-signup' ) . '</a> ';
                if ( in_array( 'pending_retailer', $retailer->roles ) ) {
                    echo '<a href="' . esc_url( admin_url( 'users.php?action=approve_retailer&user_id=' . $retailer->ID ) ) . '" class="button button-primary">' . __( 'Approve', 'wc-custom-signup' ) . '</a>';
                }
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>' . __( 'No retailers found.', 'wc-custom-signup' ) . '</p>';
        }

        echo '</div>';
    }

    public function show_sales_territories( $user ) {
        if ( ! current_user_can( 'edit_user', $user->ID ) || ! in_array( 'sales', $user->roles ) ) {
            return;
        }

        $territories = [
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

        $assigned_territories = get_user_meta( $user->ID, 'sales_territories', true );
        if ( ! is_array( $assigned_territories ) ) {
            $assigned_territories = [];
        }

        echo '<h3>' . __( 'Assign Territories', 'wc-custom-signup' ) . '</h3>';
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="sales_territories">' . __( 'Territories', 'wc-custom-signup' ) . '</label></th>';
        echo '<td>';

        foreach ( $territories as $code => $name ) {
            $checked = in_array( $code, $assigned_territories ) ? 'checked="checked"' : '';
            echo '<label><input type="checkbox" name="sales_territories[]" value="' . esc_attr( $code ) . '" ' . $checked . ' /> ' . esc_html( $name ) . '</label><br>';
        }

        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }

    public function save_sales_territories( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        $territories = isset( $_POST['sales_territories'] ) ? array_map( 'sanitize_text_field', $_POST['sales_territories'] ) : [];
        update_user_meta( $user_id, 'sales_territories', $territories );
    }
}

new WC_Sales_Portal();
