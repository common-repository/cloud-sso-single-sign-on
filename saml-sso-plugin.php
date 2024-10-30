<?php

/**
 * Plugin Name: Cloud SSO – Single Sign On
 * Plugin URI: https://cloudinfrastructureservices.co.uk/
 * Description:  WP Cloud SSO offers WordPress Single Sign On (SSO) for your WordPress logins using any SAML Identity Provider. It acts as a SAML Service Provider which can be configured to establish a trust between the plugin and IDP to securely authenticate and login users to WordPress.
 * Version: 1.0.16
 * Author: Cloud Infrastructures Services
 * Author URI: https://cloudinfrastructureservices.co.uk/
 * License: GPLv2 or later
 * Text Domain: cloud-sso-single-sign-on
 **/
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc.
 **/
/**
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'wpcsso_fs' ) ) {
    wpcsso_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'wpcsso_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wpcsso_fs()
        {
            global  $wpcsso_fs ;
            
            if ( !isset( $wpcsso_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wpcsso_fs = fs_dynamic_init( array(
                    'id'              => '9400',
                    'slug'            => 'cloud-sso-single-sign-on',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_7a4aea51f3e7d07e8dd51eec72d19',
                    'is_premium'      => false,
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                    'days'               => 10,
                    'is_require_payment' => true,
                ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                    'slug'    => 'csso_dashboard',
                    'support' => false,
                    'network' => true,
                ),
                    'is_live'         => true,
                ) );
            }
            
            return $wpcsso_fs;
        }
        
        // Init Freemius.
        wpcsso_fs();
        // Signal that SDK was initiated.
        do_action( 'wpcsso_fs_loaded' );
    }
    
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    wpcsso_fs()->add_filter( 'plugin_icon', function () {
        return dirname( __FILE__ ) . '/assets/resources/images/logo.png';
    } );
    function csso_activate_plugin()
    {
        if ( is_multisite() ) {
            die( 'Sorry but the current version of this single site plugin doesn`t support WordPress Multisites. Please upgrade to our Multi-Site plugin version' );
        }
        require_once 'assets/CSSO_utils.php';
        if ( class_exists( '\\CloudSingleSignOn\\base\\CSSO_Activate' ) ) {
            CloudSingleSignOn\base\CSSO_Activate::csso_activate();
        }
    }
    
    register_activation_hook( __FILE__, 'csso_activate_plugin' );
    function csso_deactivate_plugin()
    {
        if ( class_exists( '\\CloudSingleSignOn\\base\\CSSO_Deactivate' ) ) {
            CloudSingleSignOn\base\CSSO_Deactivate::csso_deactivate();
        }
    }
    
    register_deactivation_hook( __FILE__, 'csso_deactivate_plugin' );
    function wpcsso_fs_uninstall_cleanup()
    {
        require_once 'assets/base/CSSO_Uninstall.php';
        require_once 'assets/CSSO_utils.php';
        if ( class_exists( '\\CloudSingleSignOn\\base\\CSSO_Uninstall' ) ) {
            CloudSingleSignOn\base\CSSO_Uninstall::csso_destroy_plugin();
        }
    }
    
    wpcsso_fs()->add_action( 'after_uninstall', 'wpcsso_fs_uninstall_cleanup' );
    function csso_init_plugin()
    {
        if ( class_exists( '\\CloudSingleSignOn\\CSSO_Init' ) ) {
            CloudSingleSignOn\CSSO_Init::csso_register_services();
        }
    }
    
    add_action( 'init', 'csso_init_plugin', 0 );
}
