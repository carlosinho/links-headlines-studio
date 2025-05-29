<?php
/**
 * Plugin Name: Links & Headlines Studio by KK
 * Plugin URI: https://karol.cc/
 * Description: Give your WordPress site cool styling options for links and headlines/subheads with advanced customization features.
 * Version: 0.0.2
 * Author: Karol K
 * Author URI: https://karol.cc/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: links-headlines-studio
 * Domain Path: /languages
 * @package LinksHeadlinesStudio
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('LHS_PLUGIN_VERSION', '0.0.2');
define('LHS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LHS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('LHS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class LinksHeadlinesStudio
{
    /**
     * Plugin instance
     *
     * @var LinksHeadlinesStudio
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return LinksHeadlinesStudio
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // AJAX handlers
        add_action('wp_ajax_lhs_get_settings', array($this, 'ajax_get_settings'));
        add_action('wp_ajax_lhs_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_lhs_reset_settings', array($this, 'ajax_reset_settings'));
        add_action('wp_ajax_lhs_generate_code_preview', array($this, 'ajax_generate_code_preview'));
        
        // Frontend style injection
        add_action('wp_head', array($this, 'inject_dynamic_styles'));
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Load text domain for internationalization
        load_plugin_textdomain('links-headlines-studio', false, dirname(LHS_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style(
            'lhs-frontend-style',
            LHS_PLUGIN_URL . 'css/frontend.css',
            array(),
            LHS_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'lhs-frontend-script',
            LHS_PLUGIN_URL . 'js/frontend.js',
            array('jquery'),
            LHS_PLUGIN_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('lhs-frontend-script', 'lhs_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lhs_nonce')
        ));
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook)
    {
        // Only load on our plugin pages
        if ($hook !== 'tools_page_links-headlines-studio') {
            return;
        }

        wp_enqueue_style(
            'lhs-admin-style',
            LHS_PLUGIN_URL . 'css/admin.css',
            array(),
            LHS_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'lhs-admin-script',
            LHS_PLUGIN_URL . 'js/admin.js',
            array('jquery', 'wp-color-picker'),
            LHS_PLUGIN_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('lhs-admin-script', 'lhs_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lhs_nonce')
        ));

        // Color picker
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_submenu_page(
            'tools.php',
            __('Links & Headlines Studio', 'links-headlines-studio'),
            __('Links & Headlines', 'links-headlines-studio'),
            'manage_options',
            'links-headlines-studio',
            array($this, 'admin_page')
        );
    }

    /**
     * Admin page callback
     */
    public function admin_page()
    {
        include_once LHS_PLUGIN_PATH . 'includes/admin-page.php';
    }

    /**
     * AJAX handler to get settings
     */
    public function ajax_get_settings()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'lhs_nonce')) {
            wp_die('Security check failed');
        }

        $options = get_option('lhs_options', array());
        wp_send_json_success($options);
    }

    /**
     * AJAX handler to save settings
     */
    public function ajax_save_settings()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'lhs_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        // Sanitize and save settings
        $settings = $_POST['settings'];
        $sanitized_settings = array();

        // Basic settings
        $sanitized_settings['enable_link_styling'] = !empty($settings['enable_link_styling']);
        $sanitized_settings['enable_headline_styling'] = !empty($settings['enable_headline_styling']);
        $sanitized_settings['link_color'] = sanitize_hex_color($settings['link_color']);
        $sanitized_settings['link_hover_color'] = sanitize_hex_color($settings['link_hover_color']);
        $sanitized_settings['link_base_decoration'] = sanitize_text_field($settings['link_base_decoration']);
        $sanitized_settings['link_effect'] = sanitize_text_field($settings['link_effect']);
        $sanitized_settings['link_transition_duration'] = intval($settings['link_transition_duration']);
        $sanitized_settings['headline_effect'] = sanitize_text_field($settings['headline_effect']);
        $sanitized_settings['headline_transition_duration'] = intval($settings['headline_transition_duration']);
        
        // Link effect customization
        $sanitized_settings['link_underline_thickness'] = intval($settings['link_underline_thickness']);
        $sanitized_settings['link_glow_intensity'] = intval($settings['link_glow_intensity']);
        $sanitized_settings['link_slide_color'] = sanitize_hex_color($settings['link_slide_color']);
        $sanitized_settings['link_slide_opacity'] = floatval($settings['link_slide_opacity']);
        
        // Headline effect customization
        $sanitized_settings['headline_fade_opacity'] = floatval($settings['headline_fade_opacity']);
        $sanitized_settings['headline_hover_color'] = sanitize_hex_color($settings['headline_hover_color']);
        $sanitized_settings['headline_glow_color'] = sanitize_hex_color($settings['headline_glow_color']);
        $sanitized_settings['headline_glow_intensity'] = intval($settings['headline_glow_intensity']);
        $sanitized_settings['headline_glow_blur'] = intval($settings['headline_glow_blur']);
        $sanitized_settings['headline_brightness'] = floatval($settings['headline_brightness']);
        $sanitized_settings['headline_contrast'] = floatval($settings['headline_contrast']);
        
        // Handle headline levels array
        $sanitized_settings['headline_levels'] = array();
        if (isset($settings['headline_levels']) && is_array($settings['headline_levels'])) {
            foreach ($settings['headline_levels'] as $level) {
                $sanitized_level = sanitize_text_field($level);
                if (in_array($sanitized_level, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
                    $sanitized_settings['headline_levels'][] = $sanitized_level;
                }
            }
        }

        update_option('lhs_options', $sanitized_settings);
        wp_send_json_success('Settings saved successfully');
    }

    /**
     * AJAX handler to reset settings
     */
    public function ajax_reset_settings()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'lhs_nonce')) {
            wp_die('Security check failed');
        }

        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        // Reset to default options
        $default_options = array(
            'enable_link_styling' => true,
            'enable_headline_styling' => true,
            'link_color' => '#2271b1',
            'link_hover_color' => '#0073aa',
            'link_base_decoration' => 'underline',
            'link_effect' => 'none',
            'link_transition_duration' => '300',
            'link_underline_thickness' => '2',
            'link_glow_intensity' => '8',
            'link_slide_color' => '#2271b1',
            'link_slide_opacity' => '1.0',
            'headline_effect' => 'none',
            'headline_transition_duration' => '300',
            'headline_fade_opacity' => '0.7',
            'headline_hover_color' => '#2271b1',
            'headline_glow_color' => '#2271b1',
            'headline_glow_intensity' => '5',
            'headline_glow_blur' => '3',
            'headline_brightness' => '1.2',
            'headline_contrast' => '1.1',
            'headline_levels' => array('h1', 'h2', 'h3')
        );

        update_option('lhs_options', $default_options);
        wp_send_json_success('Settings reset successfully');
    }

    /**
     * AJAX handler to generate code preview
     */
    public function ajax_generate_code_preview()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'lhs_nonce')) {
            wp_die('Security check failed');
        }

        // Get the form data and generate code snippets
        $settings = $_POST;
        
        // Sanitize the settings like in ajax_save_settings but don't save them
        $sanitized_settings = array();
        $sanitized_settings['enable_link_styling'] = !empty($settings['enable_link_styling']);
        $sanitized_settings['enable_headline_styling'] = !empty($settings['enable_headline_styling']);
        $sanitized_settings['link_color'] = sanitize_hex_color($settings['link_color']);
        $sanitized_settings['link_hover_color'] = sanitize_hex_color($settings['link_hover_color']);
        $sanitized_settings['link_base_decoration'] = sanitize_text_field($settings['link_base_decoration']);
        $sanitized_settings['link_effect'] = sanitize_text_field($settings['link_effect']);
        $sanitized_settings['link_transition_duration'] = intval($settings['link_transition_duration']);
        $sanitized_settings['headline_effect'] = sanitize_text_field($settings['headline_effect']);
        $sanitized_settings['headline_transition_duration'] = intval($settings['headline_transition_duration']);
        
        // Link effect customization
        $sanitized_settings['link_underline_thickness'] = intval($settings['link_underline_thickness']);
        $sanitized_settings['link_glow_intensity'] = intval($settings['link_glow_intensity']);
        $sanitized_settings['link_slide_color'] = sanitize_hex_color($settings['link_slide_color']);
        $sanitized_settings['link_slide_opacity'] = floatval($settings['link_slide_opacity']);
        
        // Headline effect customization
        $sanitized_settings['headline_fade_opacity'] = floatval($settings['headline_fade_opacity']);
        $sanitized_settings['headline_hover_color'] = sanitize_hex_color($settings['headline_hover_color']);
        $sanitized_settings['headline_glow_color'] = sanitize_hex_color($settings['headline_glow_color']);
        $sanitized_settings['headline_glow_intensity'] = intval($settings['headline_glow_intensity']);
        $sanitized_settings['headline_glow_blur'] = intval($settings['headline_glow_blur']);
        $sanitized_settings['headline_brightness'] = floatval($settings['headline_brightness']);
        $sanitized_settings['headline_contrast'] = floatval($settings['headline_contrast']);
        
        // Handle headline levels array
        $sanitized_settings['headline_levels'] = array();
        if (isset($settings['headline_levels']) && is_array($settings['headline_levels'])) {
            foreach ($settings['headline_levels'] as $level) {
                $sanitized_level = sanitize_text_field($level);
                if (in_array($sanitized_level, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
                    $sanitized_settings['headline_levels'][] = $sanitized_level;
                }
            }
        }

        // Generate code snippets using the same functions from admin-page.php
        $link_css = $this->generate_link_css_code($sanitized_settings);
        $headline_css = $this->generate_headline_css_code($sanitized_settings);

        wp_send_json_success(array(
            'link_css' => $link_css,
            'headline_css' => $headline_css
        ));
    }

    /**
     * Generate link CSS code (same logic as in admin-page.php)
     */
    private function generate_link_css_code($options) {
        if (empty($options['enable_link_styling'])) {
            return "/* Link effects are disabled */";
        }
        
        $link_color = isset($options['link_color']) ? $options['link_color'] : '#2271b1';
        $hover_color = isset($options['link_hover_color']) ? $options['link_hover_color'] : '#0073aa';
        $duration = isset($options['link_transition_duration']) ? $options['link_transition_duration'] : '300';
        $base_decoration = isset($options['link_base_decoration']) ? $options['link_base_decoration'] : 'underline';
        $link_effect = isset($options['link_effect']) ? $options['link_effect'] : 'none';
        
        $css = "/* Base link styles */\n";
        $css .= ".entry-content a, .post-content a, .page-content a, article a {\n";
        $css .= "    color: {$link_color} !important;\n";
        $css .= "    transition: all {$duration}ms ease;\n";
        $css .= "    text-decoration: none !important;\n";
        $css .= "    border-bottom: none !important;\n";
        $css .= "    background-image: none !important;\n";
        $css .= "}\n\n";
        
        $css .= ".entry-content a:hover, .post-content a:hover, .page-content a:hover, article a:hover {\n";
        $css .= "    color: {$hover_color} !important;\n";
        $css .= "}\n\n";
        
        if ($link_effect !== 'none') {
            $css .= "/* Link effect: {$link_effect} */\n";
            switch ($link_effect) {
                case 'underline':
                    $underline_thickness = isset($options['link_underline_thickness']) ? $options['link_underline_thickness'] : '2';
                    $css .= ".entry-content a, .post-content a, .page-content a, article a {\n";
                    $css .= "    text-decoration: {$base_decoration} !important;\n";
                    $css .= "    border-bottom: {$underline_thickness}px solid transparent !important;\n";
                    $css .= "}\n";
                    $css .= ".entry-content a:hover, .post-content a:hover, .page-content a:hover, article a:hover {\n";
                    $css .= "    border-bottom-color: currentColor !important;\n";
                    $css .= "}\n";
                    break;
                case 'slide':
                    $underline_thickness = isset($options['link_underline_thickness']) ? $options['link_underline_thickness'] : '2';
                    $slide_color = isset($options['link_slide_color']) ? $options['link_slide_color'] : '#2271b1';
                    $slide_opacity = isset($options['link_slide_opacity']) ? $options['link_slide_opacity'] : '1.0';
                    $css .= ".entry-content a, .post-content a, .page-content a, article a {\n";
                    $css .= "    position: relative !important;\n";
                    $css .= "    text-decoration: {$base_decoration} !important;\n";
                    $css .= "    overflow: hidden !important;\n";
                    $css .= "}\n";
                    $css .= ".entry-content a::before, .post-content a::before, .page-content a::before, article a::before {\n";
                    $css .= "    content: '' !important;\n";
                    $css .= "    position: absolute !important;\n";
                    $css .= "    bottom: 0 !important;\n";
                    $css .= "    left: 0 !important;\n";
                    $css .= "    width: 0 !important;\n";
                    $css .= "    height: {$underline_thickness}px !important;\n";
                    $css .= "    background-color: {$slide_color} !important;\n";
                    $css .= "    opacity: {$slide_opacity} !important;\n";
                    $css .= "    transition: width {$duration}ms ease !important;\n";
                    $css .= "    z-index: 1 !important;\n";
                    $css .= "}\n";
                    $css .= ".entry-content a:hover::before, .post-content a:hover::before, .page-content a:hover::before, article a:hover::before {\n";
                    $css .= "    width: 100% !important;\n";
                    $css .= "}\n";
                    break;
                case 'glow':
                    $glow_intensity = isset($options['link_glow_intensity']) ? $options['link_glow_intensity'] : '8';
                    $css .= ".entry-content a, .post-content a, .page-content a, article a {\n";
                    $css .= "    text-decoration: {$base_decoration} !important;\n";
                    $css .= "}\n";
                    $css .= ".entry-content a:hover, .post-content a:hover, .page-content a:hover, article a:hover {\n";
                    $css .= "    text-shadow: 0 0 {$glow_intensity}px currentColor;\n";
                    $css .= "}\n";
                    break;
            }
        } else {
            $css .= "/* No effects - base decoration only */\n";
            $css .= ".entry-content a, .post-content a, .page-content a, article a {\n";
            $css .= "    text-decoration: {$base_decoration} !important;\n";
            $css .= "}\n";
        }
        
        return $css;
    }

    /**
     * Generate headline CSS code (same logic as in admin-page.php)
     */
    private function generate_headline_css_code($options) {
        if (empty($options['enable_headline_styling'])) {
            return "/* Headline effects are disabled */";
        }
        
        $headline_effect = isset($options['headline_effect']) ? $options['headline_effect'] : 'none';
        $headline_levels = isset($options['headline_levels']) ? $options['headline_levels'] : array('h1', 'h2', 'h3');
        $headline_duration = isset($options['headline_transition_duration']) ? $options['headline_transition_duration'] : '300';
        
        if ($headline_effect === 'none' || empty($headline_levels)) {
            return "/* No headline effects selected */";
        }
        
        $css = "/* Headline effects: {$headline_effect} */\n";
        
        // Create selectors for the selected headline levels
        $selectors = array();
        $hover_selectors = array();
        foreach ($headline_levels as $level) {
            if (in_array($level, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
                $selectors[] = ".entry-content {$level}, .post-content {$level}, .page-content {$level}, article {$level}";
                $hover_selectors[] = ".entry-content {$level}:hover, .post-content {$level}:hover, .page-content {$level}:hover, article {$level}:hover";
            }
        }
        
        if (!empty($selectors)) {
            $selector_string = implode(",\n", $selectors);
            $hover_selector_string = implode(",\n", $hover_selectors);
            
            $css .= "{$selector_string} {\n";
            $css .= "    transition: all {$headline_duration}ms ease;\n";
            $css .= "    cursor: default;\n";
            $css .= "}\n\n";
            
            switch ($headline_effect) {
                case 'fade':
                    $fade_opacity = isset($options['headline_fade_opacity']) ? $options['headline_fade_opacity'] : '0.7';
                    $css .= "{$hover_selector_string} {\n";
                    $css .= "    opacity: {$fade_opacity};\n";
                    $css .= "}\n";
                    break;
                case 'color_shift':
                    $hover_color = isset($options['headline_hover_color']) ? $options['headline_hover_color'] : '#2271b1';
                    $css .= "{$hover_selector_string} {\n";
                    $css .= "    color: {$hover_color} !important;\n";
                    $css .= "}\n";
                    break;
                case 'glow':
                    $glow_color = isset($options['headline_glow_color']) ? $options['headline_glow_color'] : '#2271b1';
                    $glow_intensity = isset($options['headline_glow_intensity']) ? $options['headline_glow_intensity'] : '5';
                    $glow_blur = isset($options['headline_glow_blur']) ? $options['headline_glow_blur'] : '3';
                    $css .= "{$hover_selector_string} {\n";
                    $css .= "    text-shadow: 0 0 {$glow_blur}px {$glow_color}, 0 0 {$glow_intensity}px {$glow_color};\n";
                    $css .= "}\n";
                    break;
                case 'brightness':
                    $brightness = isset($options['headline_brightness']) ? $options['headline_brightness'] : '1.2';
                    $contrast = isset($options['headline_contrast']) ? $options['headline_contrast'] : '1.1';
                    $css .= "{$hover_selector_string} {\n";
                    $css .= "    filter: brightness({$brightness}) contrast({$contrast});\n";
                    $css .= "}\n";
                    break;
            }
        }
        
        return $css;
    }

    /**
     * Inject dynamic styles based on plugin settings
     */
    public function inject_dynamic_styles()
    {
        $options = get_option('lhs_options', array());
        
        // Only inject styles if enabled
        if (empty($options['enable_link_styling']) && empty($options['enable_headline_styling'])) {
            return;
        }

        echo '<style type="text/css" id="lhs-dynamic-styles">' . "\n";

        // Link styling
        if (!empty($options['enable_link_styling'])) {
            $link_color = isset($options['link_color']) ? $options['link_color'] : '#2271b1';
            $hover_color = isset($options['link_hover_color']) ? $options['link_hover_color'] : '#0073aa';
            $duration = isset($options['link_transition_duration']) ? $options['link_transition_duration'] : '300';
            $base_decoration = isset($options['link_base_decoration']) ? $options['link_base_decoration'] : 'underline';
            $link_effect = isset($options['link_effect']) ? $options['link_effect'] : 'none';
            
            // Base link styles - always override defaults first
            echo ".entry-content a, .post-content a, .page-content a, article a {\n";
            echo "    color: {$link_color} !important;\n";
            echo "    transition: all {$duration}ms ease;\n";
            echo "    text-decoration: none !important;\n"; // Always remove defaults first
            echo "    border-bottom: none !important;\n"; // Remove any border-bottom
            echo "    background-image: none !important;\n"; // Remove background underlines
            echo "}\n\n";

            echo ".entry-content a:hover, .post-content a:hover, .page-content a:hover, article a:hover {\n";
            echo "    color: {$hover_color} !important;\n";
            echo "}\n\n";

            // Apply link effects or base decoration
            if ($link_effect !== 'none') {
                $underline_thickness = isset($options['link_underline_thickness']) ? $options['link_underline_thickness'] : '2';
                $glow_intensity = isset($options['link_glow_intensity']) ? $options['link_glow_intensity'] : '8';
                $slide_color = isset($options['link_slide_color']) ? $options['link_slide_color'] : '#2271b1';
                $slide_opacity = isset($options['link_slide_opacity']) ? $options['link_slide_opacity'] : '1.0';

                switch ($link_effect) {
                    case 'underline':
                        echo ".entry-content a, .post-content a, .page-content a, article a {\n";
                        echo "    text-decoration: {$base_decoration} !important;\n";
                        echo "    border-bottom: {$underline_thickness}px solid transparent !important;\n";
                        echo "}\n";
                        echo ".entry-content a:hover, .post-content a:hover, .page-content a:hover, article a:hover {\n";
                        echo "    border-bottom-color: currentColor !important;\n";
                        echo "}\n\n";
                        break;
                    case 'slide':
                        echo ".entry-content a, .post-content a, .page-content a, article a {\n";
                        echo "    position: relative !important;\n";
                        echo "    text-decoration: {$base_decoration} !important;\n";
                        echo "    overflow: hidden !important;\n";
                        echo "    border-bottom: none !important;\n"; // Ensure no border-bottom interference
                        echo "    background-image: none !important;\n"; // Remove any background underlines
                        echo "}\n";
                        echo ".entry-content a::before, .post-content a::before, .page-content a::before, article a::before {\n";
                        echo "    content: '' !important;\n";
                        echo "    position: absolute !important;\n";
                        echo "    bottom: 0 !important;\n";
                        echo "    left: 0 !important;\n";
                        echo "    width: 0 !important;\n";
                        echo "    height: {$underline_thickness}px !important;\n";
                        echo "    background-color: {$slide_color} !important;\n";
                        echo "    opacity: {$slide_opacity} !important;\n";
                        echo "    transition: width {$duration}ms ease !important;\n";
                        echo "    z-index: 1 !important;\n";
                        echo "}\n";
                        echo ".entry-content a:hover::before, .post-content a:hover::before, .page-content a:hover::before, article a:hover::before {\n";
                        echo "    width: 100% !important;\n";
                        echo "}\n\n";
                        break;
                    case 'glow':
                        echo ".entry-content a, .post-content a, .page-content a, article a {\n";
                        echo "    text-decoration: {$base_decoration} !important;\n";
                        echo "}\n";
                        echo ".entry-content a:hover, .post-content a:hover, .page-content a:hover, article a:hover {\n";
                        echo "    text-shadow: 0 0 {$glow_intensity}px currentColor;\n";
                        echo "}\n\n";
                        break;
                }
            } else {
                // No effect - just apply base decoration
                echo ".entry-content a, .post-content a, .page-content a, article a {\n";
                echo "    text-decoration: {$base_decoration} !important;\n";
                echo "}\n\n";
            }
        }

        // Headline styling
        if (!empty($options['enable_headline_styling'])) {
            $headline_effect = isset($options['headline_effect']) ? $options['headline_effect'] : 'none';
            $headline_levels = isset($options['headline_levels']) ? $options['headline_levels'] : array('h1', 'h2', 'h3');
            $headline_duration = isset($options['headline_transition_duration']) ? $options['headline_transition_duration'] : '300';
            
            if ($headline_effect !== 'none' && !empty($headline_levels)) {
                // Create selectors for the selected headline levels
                $selectors = array();
                foreach ($headline_levels as $level) {
                    if (in_array($level, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
                        $selectors[] = ".entry-content {$level}, .post-content {$level}, .page-content {$level}, article {$level}";
                    }
                }
                
                if (!empty($selectors)) {
                    $selector_string = implode(",\n", $selectors);
                    
                    echo "{$selector_string} {\n";
                    echo "    transition: all {$headline_duration}ms ease;\n";
                    echo "    cursor: default;\n";
                    echo "}\n\n";

                    // Create hover selectors
                    $hover_selectors = array();
                    foreach ($headline_levels as $level) {
                        if (in_array($level, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
                            $hover_selectors[] = ".entry-content {$level}:hover, .post-content {$level}:hover, .page-content {$level}:hover, article {$level}:hover";
                        }
                    }
                    
                    if (!empty($hover_selectors)) {
                        $hover_selector_string = implode(",\n", $hover_selectors);
                        
                        switch ($headline_effect) {
                            case 'fade':
                                $fade_opacity = isset($options['headline_fade_opacity']) ? $options['headline_fade_opacity'] : '0.7';
                                echo "{$hover_selector_string} {\n";
                                echo "    opacity: {$fade_opacity};\n";
                                echo "}\n\n";
                                break;
                            case 'color_shift':
                                $hover_color = isset($options['headline_hover_color']) ? $options['headline_hover_color'] : '#2271b1';
                                echo "{$hover_selector_string} {\n";
                                echo "    color: {$hover_color} !important;\n";
                                echo "}\n\n";
                                break;
                            case 'glow':
                                $glow_color = isset($options['headline_glow_color']) ? $options['headline_glow_color'] : '#2271b1';
                                $glow_intensity = isset($options['headline_glow_intensity']) ? $options['headline_glow_intensity'] : '5';
                                $glow_blur = isset($options['headline_glow_blur']) ? $options['headline_glow_blur'] : '3';
                                echo "{$hover_selector_string} {\n";
                                echo "    text-shadow: 0 0 {$glow_blur}px {$glow_color}, 0 0 {$glow_intensity}px {$glow_color};\n";
                                echo "}\n\n";
                                break;
                            case 'brightness':
                                $brightness = isset($options['headline_brightness']) ? $options['headline_brightness'] : '1.2';
                                $contrast = isset($options['headline_contrast']) ? $options['headline_contrast'] : '1.1';
                                echo "{$hover_selector_string} {\n";
                                echo "    filter: brightness({$brightness}) contrast({$contrast});\n";
                                echo "}\n\n";
                                break;
                        }
                    }
                }
            }
        }

        echo '</style>' . "\n";
    }
}

/**
 * Plugin activation hook
 */
function lhs_activate()
{
    // Set default options
    $default_options = array(
        'enable_link_styling' => true,
        'enable_headline_styling' => true,
        'link_color' => '#2271b1',
        'link_hover_color' => '#0073aa',
        'link_base_decoration' => 'underline',
        'link_effect' => 'none',
        'link_transition_duration' => '300',
        'link_underline_thickness' => '2',
        'link_glow_intensity' => '8',
        'link_slide_color' => '#2271b1',
        'link_slide_opacity' => '1.0',
        'headline_effect' => 'none',
        'headline_transition_duration' => '300',
        'headline_fade_opacity' => '0.7',
        'headline_hover_color' => '#2271b1',
        'headline_glow_color' => '#2271b1',
        'headline_glow_intensity' => '5',
        'headline_glow_blur' => '3',
        'headline_brightness' => '1.2',
        'headline_contrast' => '1.1',
        'headline_levels' => array('h1', 'h2', 'h3')
    );
    
    add_option('lhs_options', $default_options);
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'lhs_activate');

/**
 * Plugin deactivation hook
 */
function lhs_deactivate()
{
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'lhs_deactivate');

/**
 * Plugin uninstall hook
 */
function lhs_uninstall()
{
    // Remove options
    delete_option('lhs_options');
    
    // Clean up any custom database tables if needed
    global $wpdb;
    // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}lhs_custom_table");
}
register_uninstall_hook(__FILE__, 'lhs_uninstall');

// Initialize the plugin
LinksHeadlinesStudio::get_instance(); 