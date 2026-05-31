<?php
/**
 * Plugin Name: IWS Graphs and Charts
 * Description: A lightweight, secure, and beautiful shortcode-based charting engine built using Chart.js with an admin GUI settings dashboard, manual, and horizontal bar layout profiles.
 * Version: 1.3.1
 * Author: IT Web Solutions, Brampton
 * Author URI: https://itwebsolutions.ca
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly for core security
}

class IWSGraphsCharts {

    public function __construct() {
        // Core Front-end shortcodes and assets hooks
        add_shortcode( 'iws_gc', array( $this, 'render_chart' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

        // WordPress Administration Menu and Settings Registry Hooks
        add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
    }

    /**
     * Register assets but don't enqueue them globally.
     * They will only pull into the payload on pages hosting the shortcode.
     */
    public function register_assets() {
        wp_register_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.1', true );
        wp_register_script( 'iws-gc-core', plugin_dir_url( __FILE__ ) . 'iws-gc-core.js', array( 'chart-js' ), '1.3.1', true );
    }

    /**
     * Creates the Admin Side Menu Option
     */
    public function create_admin_menu() {
        add_menu_page(
            'IWS Graphs Settings',               // Page Title tag
            'IWS Graphs & Charts',               // Side Menu Title Text
            'manage_options',                    // Required User Capability (Admin only)
            'iws-graphs-charts',                 // Unique Menu Slug identification string
            array( $this, 'render_admin_gui' ),  // Processing Callback method to build page UI
            'dashicons-chart-bar',               // Built-in WordPress Icon
            81                                   // Position index marker placement on side menu
        );
    }

    /**
     * Register configuration properties to the WordPress Options Database Engine securely
     */
    public function register_plugin_settings() {
        register_setting( 'iws_gc_settings_group', 'iws_gc_default_type', 'sanitize_key' );
        register_setting( 'iws_gc_settings_group', 'iws_gc_default_unit', 'sanitize_text_field' );
        register_setting( 'iws_gc_settings_group', 'iws_gc_max_width', 'absint' );
    }

    /**
     * Renders the Administration Dashboard GUI HTML Panels along with User Guide, FAQ, and Developer Section
     */
    public function render_admin_gui() {
        // Check security clearing permissions explicitly before rendering
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Fetch saved options or assign structural system fallbacks
        $default_type = get_option( 'iws_gc_default_type', 'bar' );
        $default_unit = get_option( 'iws_gc_default_unit', '' );
        $max_width    = get_option( 'iws_gc_max_width', 850 );
        ?>
        <div class="wrap" style="max-width: 900px; margin-top: 20px;">
            <h1 style="font-weight: 700; color: #1d2327; margin-bottom: 5px;">IWS Graphs & Charts Configurations</h1>
            <p style="color: #646970; margin-bottom: 25px;">Engineered by <a href="https://itwebsolutions.ca" target="_blank" style="color: #2271b1; text-decoration: none; font-weight: 600;">IT Web Solutions, Brampton</a>.</p>
            
            <hr style="border: 0; height: 1px; background: #ccd0d4; margin-bottom: 25px;" />

            <!-- Configuration Form Box -->
            <form method="post" action="options.php" style="background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 1px 15px rgba(0,0,0,0.05); border: 1px solid #e0e0e0;">
                <?php 
                    // Security nonces and layout fields managed natively by WordPress settings groups
                    settings_fields( 'iws_gc_settings_group' ); 
                    do_settings_sections( 'iws_gc_settings_group' );
                ?>
                
                <table class="form-table" role="presentation" style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <th scope="row" style="width: 30%; padding: 20px 0; font-weight: 600; font-size: 14px; text-align: left;">Default Chart Type</th>
                        <td style="padding: 20px 0;">
                            <select name="iws_gc_default_type" style="min-width: 250px; padding: 6px 10px; border-radius: 4px; border: 1px solid #8c8f94;">
                                <option value="bar" <?php selected( $default_type, 'bar' ); ?>>Vertical Bar Chart</option>
                                <option value="horizontalbar" <?php selected( $default_type, 'horizontalbar' ); ?>>Horizontal Bar Chart</option>
                                <option value="line" <?php selected( $default_type, 'line' ); ?>>Curved Line Chart</option>
                                <option value="doughnut" <?php selected( $default_type, 'doughnut' ); ?>>Doughnut Chart</option>
                                <option value="pie" <?php selected( $default_type, 'pie' ); ?>>Pie Chart</option>
                                <option value="polarArea" <?php selected( $default_type, 'polarArea' ); ?>>Polar Area Chart</option>
                                <option value="radar" <?php selected( $default_type, 'radar' ); ?>>Radar (Spider) Chart</option>
                            </select>
                            <p class="description" style="margin-top: 6px; color: #646970;">Fallback visualization style if <code>type</code> attribute is omitted from shortcode blocks.</p>
                        </td>
                    </tr>

                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <th scope="row" style="width: 30%; padding: 20px 0; font-weight: 600; font-size: 14px; text-align: left;">Default Suffix Unit</th>
                        <td style="padding: 20px 0;">
                            <input type="text" name="iws_gc_default_unit" value="<?php echo esc_attr( $default_unit ); ?>" placeholder="e.g., %, kW, Meters" style="min-width: 250px; padding: 6px 10px; border-radius: 4px; border: 1px solid #8c8f94;" />
                            <p class="description" style="margin-top: 6px; color: #646970;">Fallback unit appended to numbers if <code>unit</code> parameter string field is left empty inside the content fields.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" style="width: 30%; padding: 20px 0; font-weight: 600; font-size: 14px; text-align: left;">Maximum Layout Width (px)</th>
                        <td style="padding: 20px 0;">
                            <input type="number" name="iws_gc_max_width" value="<?php echo esc_attr( $max_width ); ?>" min="300" max="2000" style="min-width: 250px; padding: 6px 10px; border-radius: 4px; border: 1px solid #8c8f94;" />
                            <p class="description" style="margin-top: 6px; color: #646970;">The universal maximum horizontal bounds constraint context width allowed for charts layout blocks (Default: 850px).</p>
                        </td>
                    </tr>
                </table>

                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                    <?php submit_button( 'Save Configuration Rules', 'primary', 'submit', false, array('style' => 'padding: 6px 20px; font-size: 14px; border-radius: 4px; cursor: pointer;') ); ?>
                </div>
            </form>

            <!-- START OF DEVELOPER HUB LINK MODULE -->
            <div style="margin-top: 30px; background: #ebf8ff; border: 1px solid #bee3f8; border-radius: 8px; padding: 20px 25px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span class="dashicons dashicons-welcome-learn-more" style="font-size: 32px; width: 32px; height: 32px; color: #2b6cb0; line-height: 32px;"></span>
                    <div>
                        <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #2c5282;">Developer Hub & Strategic Support</h3>
                        <p style="margin: 4px 0 0 0; color: #4a5568; font-size: 13px;">Need custom visualization layers, custom database integrations, or technical web management assistance?</p>
                    </div>
                </div>
                <a href="https://itwebsolutions.ca/" target="_blank" rel="opened" style="background: #2b6cb0; color: #ffffff; text-decoration: none; padding: 10px 18px; font-weight: 600; font-size: 13px; border-radius: 5px; transition: background 0.2s ease-in-out; display: inline-block; white-space: nowrap;">
                    Visit IT Web Solutions
                </a>
            </div>
            <!-- END OF DEVELOPER HUB LINK MODULE -->

            <!-- START OF DOCUMENTATION & FAQ ENGINE -->
            <div style="margin-top: 30px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 1px 15px rgba(0,0,0,0.05); overflow: hidden;">
                
                <div style="background: #1d2327; padding: 20px 30px; color: #ffffff;">
                    <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #ffffff; display: flex; align-items: center; gap: 10px;">
                        <span class="dashicons dashicons-editor-help" style="font-size: 22px; width: 22px; height: 22px; line-height: 22px;"></span>
                        Engine Documentation & User Manual
                    </h2>
                    <p style="margin: 5px 0 0 32px; color: #a7aaad; font-size: 13px;">Complete reference guide, syntax trackers, and performance optimization mechanics.</p>
                </div>

                <div style="padding: 30px;">
                    <h3 style="margin-top: 0; font-weight: 600; color: #1d2327; font-size: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px;">1. Crucial Data Formatting Rules</h3>
                    <ul style="list-style-type: disc; padding-left: 20px; color: #50575e; line-height: 1.6;">
                        <li><strong>No Spaces in Array Attributes:</strong> Comma-separate all elements inside the labels parameters.</li>
                        <li><strong>Multi-Dataset Line Separation:</strong> For multiple comparative lines, separate datasets using a pipe symbol (<code>|</code>).</li>
                    </ul>

                    <h3 style="margin-top: 30px; font-weight: 600; color: #1d2327; font-size: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px;">2. Copy-Paste Shortcode Library Variants</h3>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-top: 15px;">
                        <div style="background: #f6f7f7; border: 1px solid #dcdcde; border-left: 4px solid #7bc0c0; padding: 15px; border-radius: 0 4px 4px 0;">
                            <strong style="display: block; color: #1d2327; margin-bottom: 5px; font-size: 13px;">📈 Comparative Multi-Line Chart Template</strong>
                            <code style="display: block; background: #ffffff; padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 12px; color: #2c3338; overflow-x: auto; white-space: pre;">[iws_gc type="line" title="Product Sales Comparison" labels="Q1,Q2,Q3,Q4" data="120,185,340,290 | 90,210,280,310 | 150,130,210,400" line_labels="Product A,Product B,Product C" unit="Units"]</code>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END OF DOCUMENTATION & FAQ ENGINE -->
        </div>
        <?php
    }

    /**
     * Front-end Shortcode Handler
     * Fixed payload distribution logic to safely feed multi-nested datasets cleanly to JSON structures.
     */
    public function render_chart( $atts ) {
        $db_type  = get_option( 'iws_gc_default_type', 'bar' );
        $db_unit  = get_option( 'iws_gc_default_unit', '' );
        $db_width = get_option( 'iws_gc_max_width', 850 );

        $attributes = shortcode_atts( array(
            'type'        => $db_type, 
            'labels'      => '',    
            'data'        => '',    
            'title'       => '',    
            'unit'        => $db_unit,    
            'line_labels' => '',    
        ), $atts, 'iws_gc' );

        $chart_type  = json_encode( sanitize_key( $attributes['type'] ) );
        $chart_title = json_encode( sanitize_text_field( $attributes['title'] ) );
        $chart_unit  = json_encode( sanitize_text_field( $attributes['unit'] ) );
        
        $labels_array = array_map( 'sanitize_text_field', explode( ',', $attributes['labels'] ) );
        $labels_json = json_encode( $labels_array );

        // Normalize potential HTML character alterations to standard pipes
        $raw_data_string = html_entity_decode($attributes['data'], ENT_QUOTES, 'UTF-8');
        
        // Check for line delimiters safely
        if ( preg_match('/\||&#124;|%7C/', $raw_data_string) ) {
            $data_blocks = preg_split('/\||&#124;|%7C/', $raw_data_string);
            $compiled_matrix = array();

            foreach ( $data_blocks as $block ) {
                $compiled_matrix[] = array_map( 'floatval', explode( ',', trim($block) ) );
            }
            // Generate a clean nested JSON array matrix: [[10,20],[30,40]]
            $data_json_payload = json_encode( $compiled_matrix );
        } else {
            // Standard flat array for single charts
            $data_json_payload = json_encode( array_map( 'floatval', explode( ',', $raw_data_string ) ) );
        }

        $line_labels_json = '';
        if ( ! empty( $attributes['line_labels'] ) ) {
            $line_labels_array = array_map( 'sanitize_text_field', explode( ',', $attributes['line_labels'] ) );
            $line_labels_json = json_encode( $line_labels_array );
        }

        wp_enqueue_script( 'chart-js' );
        wp_enqueue_script( 'iws-gc-core' );

        $unique_id = 'iws_chart_' . wp_generate_password( 8, false );

        $output = '<div class="iws-chart-container" style="position: relative; margin: auto; height:45vh; width:100%; max-width:' . esc_attr($db_width) . 'px;">';
        $output .= sprintf(
            '<canvas id="%1$s" data-type=\'%2$s\' data-title=\'%3$s\' data-unit=\'%4$s\' data-labels=\'%5$s\' data-values=\'%6$s\' data-line-labels=\'%7$s\'></canvas>',
            esc_attr( $unique_id ),
            esc_attr( $chart_type ),
            esc_attr( $chart_title ),
            esc_attr( $chart_unit ),
            esc_attr( $labels_json ),
            esc_attr( $data_json_payload ),
            esc_attr( $line_labels_json )
        );
        $output .= '</div>';

        return $output;
    }
}

new IWSGraphsCharts();
