<?php
/**
 * Admin Page Template
 * 
 * @package LinksHeadlinesStudio
 * @version 0.0.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$options = get_option('lhs_options', array());
$nonce = wp_create_nonce('lhs_nonce');

// Generate code snippets based on current settings
function lhs_generate_link_css_code($options) {
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

function lhs_generate_headline_css_code($options) {
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
?>

<div class="wrap lhs-admin-container">
    <h1><?php _e('Links & Headlines Studio', 'links-headlines-studio'); ?></h1>
    <p><?php _e('Add stunning hover effects and styling to your links and headlines.', 'links-headlines-studio'); ?></p>

    <form id="lhs-settings-form" method="post" action="options.php">
        <?php settings_fields('lhs_options'); ?>
        <input type="hidden" id="lhs_nonce" value="<?php echo esc_attr($nonce); ?>">

        <!-- Link Settings -->
        <div class="lhs-form-section">
            <h2><?php _e('Link Effects', 'links-headlines-studio'); ?></h2>
            <p><?php _e('Customize hover effects and colors for links on your website.', 'links-headlines-studio'); ?></p>
            
            <table class="lhs-form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="lhs_enable_link_styling"><?php _e('Enabled', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="lhs_enable_link_styling" name="enable_link_styling" value="1" <?php checked(isset($options['enable_link_styling']) ? !empty($options['enable_link_styling']) : true); ?>>
                                <?php _e('Enable link effects', 'links-headlines-studio'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_link_color"><?php _e('Base Link Color', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="lhs_link_color" name="link_color" 
                                   value="<?php echo esc_attr(isset($options['link_color']) ? $options['link_color'] : '#2271b1'); ?>" 
                                   class="lhs-color-picker">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_link_base_decoration"><?php _e('Base Link Decoration', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <select id="lhs_link_base_decoration" name="link_base_decoration">
                                <option value="underline" <?php selected(isset($options['link_base_decoration']) ? $options['link_base_decoration'] : 'underline', 'underline'); ?>><?php _e('Underline', 'links-headlines-studio'); ?></option>
                                <option value="none" <?php selected(isset($options['link_base_decoration']) ? $options['link_base_decoration'] : '', 'none'); ?>><?php _e('None', 'links-headlines-studio'); ?></option>
                            </select>
                            <p class="description"><?php _e('Default text decoration for links in normal state', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_link_hover_color"><?php _e('Hover Color', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="lhs_link_hover_color" name="link_hover_color" 
                                   value="<?php echo esc_attr(isset($options['link_hover_color']) ? $options['link_hover_color'] : '#0073aa'); ?>" 
                                   class="lhs-color-picker">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_link_effect"><?php _e('Hover Effect', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <select id="lhs_link_effect" name="link_effect">
                                <option value="none" <?php selected(isset($options['link_effect']) ? $options['link_effect'] : 'none', 'none'); ?>><?php _e('None', 'links-headlines-studio'); ?></option>
                                <option value="underline" <?php selected(isset($options['link_effect']) ? $options['link_effect'] : '', 'underline'); ?>><?php _e('Animated Underline', 'links-headlines-studio'); ?></option>
                                <option value="slide" <?php selected(isset($options['link_effect']) ? $options['link_effect'] : '', 'slide'); ?>><?php _e('Slide Effect', 'links-headlines-studio'); ?></option>
                                <option value="glow" <?php selected(isset($options['link_effect']) ? $options['link_effect'] : '', 'glow'); ?>><?php _e('Glow Effect', 'links-headlines-studio'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_link_transition_duration"><?php _e('Effect Duration (ms)', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="lhs_link_transition_duration" name="link_transition_duration" 
                                   value="<?php echo esc_attr(isset($options['link_transition_duration']) ? $options['link_transition_duration'] : '300'); ?>" 
                                   min="100" max="2000" step="50">
                            <p class="description"><?php _e('Animation speed for hover effects', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr class="lhs-link-underline-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_link_underline_thickness"><?php _e('Underline Thickness (px)', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="lhs_link_underline_thickness" name="link_underline_thickness" 
                                   value="<?php echo esc_attr(isset($options['link_underline_thickness']) ? $options['link_underline_thickness'] : '2'); ?>" 
                                   min="1" max="10" step="1">
                        </td>
                    </tr>
                    <tr class="lhs-link-slide-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_link_slide_color"><?php _e('Slide Underline Color', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="lhs_link_slide_color" name="link_slide_color" 
                                   value="<?php echo esc_attr(isset($options['link_slide_color']) ? $options['link_slide_color'] : '#2271b1'); ?>" 
                                   class="lhs-color-picker">
                            <p class="description"><?php _e('Color for the slide effect underline', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr class="lhs-link-slide-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_link_slide_opacity"><?php _e('Underline Opacity', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="lhs_link_slide_opacity" name="link_slide_opacity" 
                                   value="<?php echo esc_attr(isset($options['link_slide_opacity']) ? $options['link_slide_opacity'] : '1.0'); ?>" 
                                   min="0.1" max="1.0" step="0.1">
                            <span class="lhs-range-value">1.0</span>
                            <p class="description"><?php _e('How transparent the underline appears (0.1 = very transparent, 1.0 = solid)', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr class="lhs-link-glow-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_link_glow_intensity"><?php _e('Glow Intensity (px)', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="lhs_link_glow_intensity" name="link_glow_intensity" 
                                   value="<?php echo esc_attr(isset($options['link_glow_intensity']) ? $options['link_glow_intensity'] : '8'); ?>" 
                                   min="2" max="20" step="1">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Headline Settings -->
        <div class="lhs-form-section">
            <h2><?php _e('Headline Effects', 'links-headlines-studio'); ?></h2>
            <p><?php _e('Add subtle hover effects to your headlines and subheadings.', 'links-headlines-studio'); ?></p>
            
            <table class="lhs-form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="lhs_enable_headline_styling"><?php _e('Enabled', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="lhs_enable_headline_styling" name="enable_headline_styling" value="1" <?php checked(isset($options['enable_headline_styling']) ? !empty($options['enable_headline_styling']) : true); ?>>
                                <?php _e('Enable headline effects', 'links-headlines-studio'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_headline_levels"><?php _e('Headline Levels', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <?php 
                            $selected_levels = isset($options['headline_levels']) ? $options['headline_levels'] : array('h1', 'h2', 'h3');
                            if (!is_array($selected_levels)) {
                                $selected_levels = array('h1', 'h2', 'h3');
                            }
                            ?>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Choose which headline levels to apply effects to', 'links-headlines-studio'); ?></legend>
                                <?php for ($i = 1; $i <= 6; $i++) : ?>
                                <label style="display: inline-block; margin-right: 15px; margin-bottom: 5px;">
                                    <input type="checkbox" name="headline_levels[]" value="h<?php echo $i; ?>" <?php checked(in_array('h' . $i, $selected_levels)); ?>>
                                    H<?php echo $i; ?>
                                </label>
                                <?php endfor; ?>
                                <p class="description"><?php _e('Select which headline levels should have hover effects applied.', 'links-headlines-studio'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_headline_effect"><?php _e('Hover Effect', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <select id="lhs_headline_effect" name="headline_effect">
                                <option value="none" <?php selected(isset($options['headline_effect']) ? $options['headline_effect'] : 'none', 'none'); ?>><?php _e('None', 'links-headlines-studio'); ?></option>
                                <option value="fade" <?php selected(isset($options['headline_effect']) ? $options['headline_effect'] : '', 'fade'); ?>><?php _e('Fade Effect', 'links-headlines-studio'); ?></option>
                                <option value="color_shift" <?php selected(isset($options['headline_effect']) ? $options['headline_effect'] : '', 'color_shift'); ?>><?php _e('Color Shift', 'links-headlines-studio'); ?></option>
                                <option value="glow" <?php selected(isset($options['headline_effect']) ? $options['headline_effect'] : '', 'glow'); ?>><?php _e('Soft Glow', 'links-headlines-studio'); ?></option>
                                <option value="brightness" <?php selected(isset($options['headline_effect']) ? $options['headline_effect'] : '', 'brightness'); ?>><?php _e('Brightness Shift', 'links-headlines-studio'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="lhs_headline_transition_duration"><?php _e('Effect Duration (ms)', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="lhs_headline_transition_duration" name="headline_transition_duration" 
                                   value="<?php echo esc_attr(isset($options['headline_transition_duration']) ? $options['headline_transition_duration'] : '300'); ?>" 
                                   min="100" max="2000" step="50">
                            <p class="description"><?php _e('Animation speed for headline hover effects', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Fade Effect Options -->
                    <tr class="lhs-headline-fade-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_fade_opacity"><?php _e('Fade Opacity', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="lhs_headline_fade_opacity" name="headline_fade_opacity" 
                                   value="<?php echo esc_attr(isset($options['headline_fade_opacity']) ? $options['headline_fade_opacity'] : '0.7'); ?>" 
                                   min="0.1" max="0.9" step="0.1">
                            <span class="lhs-range-value">0.7</span>
                            <p class="description"><?php _e('How transparent headlines become on hover (lower = more transparent)', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Color Shift Effect Options -->
                    <tr class="lhs-headline-color-shift-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_hover_color"><?php _e('Hover Color', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="lhs_headline_hover_color" name="headline_hover_color" 
                                   value="<?php echo esc_attr(isset($options['headline_hover_color']) ? $options['headline_hover_color'] : '#2271b1'); ?>" 
                                   class="lhs-color-picker">
                            <p class="description"><?php _e('Color headlines change to on hover', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Glow Effect Options -->
                    <tr class="lhs-headline-glow-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_glow_color"><?php _e('Glow Color', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="lhs_headline_glow_color" name="headline_glow_color" 
                                   value="<?php echo esc_attr(isset($options['headline_glow_color']) ? $options['headline_glow_color'] : '#2271b1'); ?>" 
                                   class="lhs-color-picker">
                            <p class="description"><?php _e('Color of the glow effect', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr class="lhs-headline-glow-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_glow_intensity"><?php _e('Glow Intensity (px)', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="lhs_headline_glow_intensity" name="headline_glow_intensity" 
                                   value="<?php echo esc_attr(isset($options['headline_glow_intensity']) ? $options['headline_glow_intensity'] : '5'); ?>" 
                                   min="1" max="20" step="1">
                            <p class="description"><?php _e('Size of the glow effect in pixels', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr class="lhs-headline-glow-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_glow_blur"><?php _e('Glow Blur (px)', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="lhs_headline_glow_blur" name="headline_glow_blur" 
                                   value="<?php echo esc_attr(isset($options['headline_glow_blur']) ? $options['headline_glow_blur'] : '3'); ?>" 
                                   min="0" max="10" step="1">
                            <p class="description"><?php _e('How soft/blurred the glow appears', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Brightness Effect Options -->
                    <tr class="lhs-headline-brightness-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_brightness"><?php _e('Brightness Level', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="lhs_headline_brightness" name="headline_brightness" 
                                   value="<?php echo esc_attr(isset($options['headline_brightness']) ? $options['headline_brightness'] : '1.2'); ?>" 
                                   min="0.5" max="2.0" step="0.1">
                            <span class="lhs-range-value">1.2</span>
                            <p class="description"><?php _e('Brightness on hover (0.5 = darker, 1.0 = normal, 2.0 = brighter)', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                    <tr class="lhs-headline-brightness-options" style="display: none;">
                        <th scope="row">
                            <label for="lhs_headline_contrast"><?php _e('Contrast Level', 'links-headlines-studio'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="lhs_headline_contrast" name="headline_contrast" 
                                   value="<?php echo esc_attr(isset($options['headline_contrast']) ? $options['headline_contrast'] : '1.1'); ?>" 
                                   min="0.5" max="2.0" step="0.1">
                            <span class="lhs-range-value">1.1</span>
                            <p class="description"><?php _e('Contrast on hover (0.5 = low contrast, 1.0 = normal, 2.0 = high contrast)', 'links-headlines-studio'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Live Preview -->
        <div class="lhs-preview-section">
            <div class="lhs-preview-header">
                <div class="lhs-preview-header-row">
                    <h3><?php _e('Live Preview', 'links-headlines-studio'); ?></h3>
                    <div class="lhs-preview-controls">
                        <label class="lhs-dark-mode-toggle">
                            <input type="checkbox" id="lhs-preview-dark-mode">
                            <span class="lhs-toggle-slider"></span>
                            <?php _e('Dark Mode Preview', 'links-headlines-studio'); ?>
                        </label>
                    </div>
                </div>
                <?php /* <p class="lhs-preview-description"><?php _e('See your changes in real-time as you adjust the settings above.', 'links-headlines-studio'); ?></p> */ ?>
            </div>
            <div class="lhs-preview-content">
                <h1>This is an H1 Headline</h1>
                <h2>This is an H2 Subheading</h2>
                <h3>This is an H3 Subheading</h3>
                <p>This is a sample paragraph with a <a href="#">sample link</a> to demonstrate the styling effects. You can see how your <a href="#">link colors and effects</a> will look in real-time as you make changes.</p>
                <h4>This is an H4 Heading</h4>
                <p>More sample content with <a href="#">another link</a> to show the hover effects in action. Try hovering over the links and headlines above!</p>
                <h5>This is an H5 Heading</h5>
                <h6>This is an H6 Heading</h6>
            </div>
        </div>

        <!-- Generated Code Section (Hidden by default) -->
        <div id="lhs-generated-code-section" class="lhs-code-section" style="display: none;">
            <div class="lhs-code-section-header">
                <h3><?php _e('Generated CSS Code', 'links-headlines-studio'); ?></h3>
                <p><?php _e('Copy and paste this CSS code into your theme\'s stylesheet or use a custom CSS plugin.', 'links-headlines-studio'); ?></p>
            </div>
            
            <!-- Link CSS Code -->
            <div class="lhs-code-block-wrapper">
                <div class="lhs-code-block-header">
                    <h4><?php _e('Link Effects CSS', 'links-headlines-studio'); ?></h4>
                    <button type="button" class="button button-small lhs-copy-code" data-target="lhs-link-css-code"><?php _e('Copy CSS', 'links-headlines-studio'); ?></button>
                </div>
                <div class="lhs-code-content">
                    <div id="lhs-link-css-code" class="lhs-code-block">
                        <pre><code class="language-css"></code></pre>
                    </div>
                </div>
            </div>
            
            <!-- Headline CSS Code -->
            <div class="lhs-code-block-wrapper">
                <div class="lhs-code-block-header">
                    <h4><?php _e('Headline Effects CSS', 'links-headlines-studio'); ?></h4>
                    <button type="button" class="button button-small lhs-copy-code" data-target="lhs-headline-css-code"><?php _e('Copy CSS', 'links-headlines-studio'); ?></button>
                </div>
                <div class="lhs-code-content">
                    <div id="lhs-headline-css-code" class="lhs-code-block">
                        <pre><code class="language-css"></code></pre>
                    </div>
                </div>
            </div>
        </div>

        <?php submit_button(__('Save Settings', 'links-headlines-studio'), 'primary', 'submit', false); ?>
        <input type="button" class="button button-secondary lhs-give-me-code" value="<?php _e('Give Me Code', 'links-headlines-studio'); ?>">
        <input type="button" class="button button-secondary lhs-reset-settings" value="<?php _e('Reset to Defaults', 'links-headlines-studio'); ?>">
        <input type="button" class="button button-secondary lhs-export-settings" value="<?php _e('Export Settings', 'links-headlines-studio'); ?>">
        <input type="button" class="button button-secondary lhs-import-settings" value="<?php _e('Import Settings', 'links-headlines-studio'); ?>">
        <input type="file" id="lhs-import-file" accept=".json" style="display: none;">
    </form>
</div>

<script type="text/javascript">
// Add WordPress-specific styling classes to preview
jQuery(document).ready(function($) {
    $('.lhs-preview-content').addClass('lhs-styled-links lhs-styled-headlines');
    
    // Show/hide conditional options based on effect selection
    function toggleEffectOptions() {
        // Link effect options
        var linkEffect = $('#lhs_link_effect').val();
        $('.lhs-link-underline-options, .lhs-link-slide-options, .lhs-link-glow-options').hide();
        if (linkEffect === 'underline' || linkEffect === 'slide') {
            $('.lhs-link-underline-options').show();
        }
        if (linkEffect === 'slide') {
            $('.lhs-link-slide-options').show();
        } else if (linkEffect === 'glow') {
            $('.lhs-link-glow-options').show();
        }
        
        // Headline effect options
        var headlineEffect = $('#lhs_headline_effect').val();
        $('.lhs-headline-fade-options, .lhs-headline-color-shift-options, .lhs-headline-glow-options, .lhs-headline-brightness-options').hide();
        if (headlineEffect === 'fade') {
            $('.lhs-headline-fade-options').show();
        } else if (headlineEffect === 'color_shift') {
            $('.lhs-headline-color-shift-options').show();
        } else if (headlineEffect === 'glow') {
            $('.lhs-headline-glow-options').show();
        } else if (headlineEffect === 'brightness') {
            $('.lhs-headline-brightness-options').show();
        }
    }
    
    // Initialize
    toggleEffectOptions();
    
    // Bind events for conditional options
    $('#lhs_link_effect, #lhs_headline_effect').on('change', function() {
        toggleEffectOptions();
    });
});
</script> 