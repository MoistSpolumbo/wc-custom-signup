<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WC_Custom_Signup_Fields {

    private $provinces = [
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

    public function __construct() {
        add_action( 'woocommerce_register_form_start', [ $this, 'add_custom_signup_fields' ] );
    }

    public function add_custom_signup_fields() {
        ?>
        <p class="form-row form-row-first">
            <label for="reg_first_name"><?php esc_html_e( 'First Name', 'wc-custom-signup' ); ?> <span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="reg_first_name" value="<?php if ( ! empty( $_POST['first_name'] ) ) echo esc_attr( wp_unslash( $_POST['first_name'] ) ); ?>" />
        </p>
        <p class="form-row form-row-last">
            <label for="reg_last_name"><?php esc_html_e( 'Last Name', 'wc-custom-signup' ); ?> <span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="reg_last_name" value="<?php if ( ! empty( $_POST['last_name'] ) ) echo esc_attr( wp_unslash( $_POST['last_name'] ) ); ?>" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_phone"><?php esc_html_e( 'Phone', 'wc-custom-signup' ); ?> <span class="required">*</span></label>
            <input type="text" class="input-text" name="phone" id="reg_phone" value="<?php if ( ! empty( $_POST['phone'] ) ) echo esc_attr( wp_unslash( $_POST['phone'] ) ); ?>" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_company_name"><?php esc_html_e( 'Company Name', 'wc-custom-signup' ); ?></label>
            <input type="text" class="input-text" name="company_name" id="reg_company_name" value="<?php if ( ! empty( $_POST['company_name'] ) ) echo esc_attr( wp_unslash( $_POST['company_name'] ) ); ?>" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_legal_entity_type"><?php esc_html_e( 'Legal Entity Type', 'wc-custom-signup' ); ?> <span class="required">*</span></label>
            <select name="legal_entity_type" id="reg_legal_entity_type" class="input-select">
                <option value=""><?php esc_html_e( 'Select an option', 'wc-custom-signup' ); ?></option>
                <option value="Partnership" <?php selected( $_POST['legal_entity_type'], 'Partnership' ); ?>><?php esc_html_e( 'Partnership', 'wc-custom-signup' ); ?></option>
                <option value="Individual" <?php selected( $_POST['legal_entity_type'], 'Individual' ); ?>><?php esc_html_e( 'Individual', 'wc-custom-signup' ); ?></option>
                <option value="Corporation" <?php selected( $_POST['legal_entity_type'], 'Corporation' ); ?>><?php esc_html_e( 'Corporation', 'wc-custom-signup' ); ?></option>
            </select>
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_gst_number"><?php esc_html_e( 'GST Number (if applicable)', 'wc-custom-signup' ); ?></label>
            <input type="text" class="input-text" name="gst_number" id="reg_gst_number" value="<?php if ( ! empty( $_POST['gst_number'] ) ) echo esc_attr( wp_unslash( $_POST['gst_number'] ) ); ?>" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_territory"><?php esc_html_e( 'Territory', 'wc-custom-signup' ); ?> <span class="required">*</span></label>
            <select name="territory" id="reg_territory" class="input-select">
                <option value=""><?php esc_html_e( 'Select a province', 'wc-custom-signup' ); ?></option>
                <?php foreach ( $this->provinces as $code => $name ) : ?>
                    <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $_POST['territory'], $code ); ?>>
                        <?php echo esc_html( $name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
}
