/**
 * Links & Headlines Studio - Admin JavaScript
 * 
 * @package LinksHeadlinesStudio
 * @version 0.0.1
 */

(function($) {
    'use strict';

    /**
     * Initialize admin functionality
     */
    function initLHSAdmin() {
        // Initialize color pickers
        initColorPickers();
        
        // Initialize live preview
        initLivePreview();
        
        // Initialize form handlers
        initFormHandlers();
        
        // Initialize conditional options
        initConditionalOptions();
    }

    /**
     * Initialize color pickers
     */
    function initColorPickers() {
        $('.lhs-color-picker').wpColorPicker({
            change: function(event, ui) {
                // Slight delay to ensure the input value is updated
                setTimeout(updateLivePreview, 50);
            },
            clear: function() {
                setTimeout(updateLivePreview, 50);
            }
        });
    }

    /**
     * Initialize conditional options
     */
    function initConditionalOptions() {
        // Update range displays and trigger preview updates
        $('#lhs_headline_fade_opacity').on('input', function() {
            $(this).next('.lhs-range-value').text($(this).val());
            updateLivePreview();
        });
        
        $('#lhs_link_slide_opacity').on('input', function() {
            $(this).next('.lhs-range-value').text($(this).val());
            updateLivePreview();
        });
        
        $('#lhs_headline_brightness').on('input', function() {
            $(this).next('.lhs-range-value').text($(this).val());
            updateLivePreview();
        });
        
        $('#lhs_headline_contrast').on('input', function() {
            $(this).next('.lhs-range-value').text($(this).val());
            updateLivePreview();
        });
        
        // Initialize range displays
        $('#lhs_headline_fade_opacity').next('.lhs-range-value').text($('#lhs_headline_fade_opacity').val());
        $('#lhs_link_slide_opacity').next('.lhs-range-value').text($('#lhs_link_slide_opacity').val());
        $('#lhs_headline_brightness').next('.lhs-range-value').text($('#lhs_headline_brightness').val());
        $('#lhs_headline_contrast').next('.lhs-range-value').text($('#lhs_headline_contrast').val());
    }

    /**
     * Initialize live preview functionality
     */
    function initLivePreview() {
        // Update preview when form inputs change
        $('.lhs-form-table input, .lhs-form-table select, .lhs-form-table textarea').on('change keyup input', function() {
            updateLivePreview();
        });
        
        // Special handling for checkboxes
        $('.lhs-form-table input[type="checkbox"]').on('change', function() {
            updateLivePreview();
        });
        
        // Dark mode toggle
        $('#lhs-preview-dark-mode').on('change', function() {
            $('.lhs-preview-content').toggleClass('lhs-dark-mode', this.checked);
        });
        
        // Initial preview update
        updateLivePreview();
    }

    /**
     * Update live preview
     */
    function updateLivePreview() {
        var preview = $('.lhs-preview-content');
        
        if (!preview.length) {
            return;
        }

        // Get current form values
        var settings = getFormSettings();
        
        // Show/hide headlines based on selected levels
        updateHeadlineVisibility(settings);
        
        // Apply link styles
        var linkColor = settings.link_color || '#2271b1';
        var hoverColor = settings.link_hover_color || '#0073aa';
        var duration = settings.link_transition_duration || '300';
        var baseDecoration = settings.link_base_decoration || 'underline';
        
        preview.find('a').css({
            'color': linkColor,
            'transition': 'all ' + duration + 'ms ease'
        });

        // Apply link effects
        var linkEffect = settings.link_effect || 'none';
        preview.removeClass('lhs-link-effect-underline lhs-link-effect-slide lhs-link-effect-glow');
        
        // Reset link styles
        preview.find('a').css({
            'border-bottom': '',
            'position': '',
            'overflow': '',
            'text-shadow': ''
        });
        
        if (linkEffect !== 'none') {
            preview.addClass('lhs-link-effect-' + linkEffect);
            
            // Apply specific effect styles for preview
            switch(linkEffect) {
                case 'underline':
                    var thickness = settings.link_underline_thickness || '2';
                    preview.find('a').css({
                        'text-decoration': 'none',
                        'border-bottom': thickness + 'px solid transparent'
                    });
                    break;
                case 'slide':
                    preview.find('a').css({
                        'position': 'relative',
                        'text-decoration': 'none',
                        'overflow': 'hidden'
                    });
                    break;
            }
        }

        // Apply headline effects based on selected levels
        var headlineEffect = settings.headline_effect || 'none';
        var selectedLevels = settings.headline_levels || [];
        var headlineDuration = settings.headline_transition_duration || '300';
        
        preview.removeClass('lhs-headline-effect-fade');
        
        // Reset all headline styles first
        preview.find('h1, h2, h3, h4, h5, h6').css({
            'transition': '',
            'cursor': '',
            'opacity': '',
            'transform': '',
            'position': '',
            'overflow': '',
            'display': ''
        });
        
        if (headlineEffect !== 'none' && selectedLevels.length > 0) {
            var headlineSelectors = selectedLevels.map(function(level) {
                return level;
            }).join(', ');
            
            preview.find(headlineSelectors).css({
                'transition': 'all ' + headlineDuration + 'ms ease',
                'cursor': 'default'
            });
            
            if (headlineEffect !== 'none') {
                preview.addClass('lhs-headline-effect-' + headlineEffect);
            }
        }

        // Create dynamic hover styles for preview
        updatePreviewHoverStyles(settings);
    }

    /**
     * Update headline visibility based on selected levels
     */
    function updateHeadlineVisibility(settings) {
        var preview = $('.lhs-preview-content');
        var selectedLevels = settings.headline_levels || [];
        
        // Show all headlines first
        preview.find('h1, h2, h3, h4, h5, h6').show();
        
        // If specific levels are selected and headline styling is enabled
        if (settings.enable_headline_styling && selectedLevels.length > 0) {
            // Hide all headlines first
            preview.find('h1, h2, h3, h4, h5, h6').hide();
            
            // Show only selected levels
            selectedLevels.forEach(function(level) {
                preview.find(level).show();
            });
        }
    }

    /**
     * Update preview hover styles
     */
    function updatePreviewHoverStyles(settings) {
        // Remove existing preview styles
        $('#lhs-preview-styles').remove();
        
        var css = '';
        var hoverColor = settings.link_hover_color || '#0073aa';
        var linkEffect = settings.link_effect || 'none';
        var headlineEffect = settings.headline_effect || 'none';
        var duration = settings.link_transition_duration || '300';
        var headlineDuration = settings.headline_transition_duration || '300';
        var selectedLevels = settings.headline_levels || [];
        
        // Link customization options
        var underlineThickness = settings.link_underline_thickness || '2';
        var linkGlowIntensity = settings.link_glow_intensity || '8';
        var slideColor = settings.link_slide_color || '#2271b1';
        var slideOpacity = settings.link_slide_opacity || '1.0';
        var baseDecoration = settings.link_base_decoration || 'underline';
        
        // Headline customization options
        var fadeOpacity = settings.headline_fade_opacity || '0.7';
        var headlineHoverColor = settings.headline_hover_color || '#2271b1';
        var glowColor = settings.headline_glow_color || '#2271b1';
        var headlineGlowIntensity = settings.headline_glow_intensity || '5';
        var glowBlur = settings.headline_glow_blur || '3';
        var brightness = settings.headline_brightness || '1.2';
        var contrast = settings.headline_contrast || '1.1';
        
        // Base link styles
        css += '.lhs-preview-content a { ';
        css += 'text-decoration: ' + baseDecoration + ' !important; ';
        css += '}\n';
        
        // Link hover styles
        css += '.lhs-preview-content a:hover { color: ' + hoverColor + ' !important; ';
        
        if (linkEffect === 'underline') {
            css += 'border-bottom-color: currentColor !important; ';
        } else if (linkEffect === 'glow') {
            css += 'text-shadow: 0 0 ' + linkGlowIntensity + 'px currentColor !important; ';
        }
        
        css += '}\n';
        
        // Link slide effect
        if (linkEffect === 'slide') {
            css += '.lhs-preview-content a::before { ';
            css += 'content: ""; position: absolute; bottom: 0; left: 0; width: 0; height: ' + underlineThickness + 'px; ';
            css += 'background-color: ' + slideColor + '; opacity: ' + slideOpacity + '; transition: width ' + duration + 'ms ease; ';
            css += '}\n';
            css += '.lhs-preview-content a:hover::before { width: 100%; }\n';
        }
        
        // Headline hover styles (only for selected levels)
        if (headlineEffect !== 'none' && selectedLevels.length > 0) {
            var headlineSelectors = selectedLevels.map(function(level) {
                return '.lhs-preview-content ' + level + ':hover';
            }).join(', ');
            
            switch (headlineEffect) {
                case 'fade':
                    css += headlineSelectors + ' { opacity: ' + fadeOpacity + '; }\n';
                    break;
                case 'color_shift':
                    css += headlineSelectors + ' { color: ' + headlineHoverColor + ' !important; }\n';
                    break;
                case 'glow':
                    css += headlineSelectors + ' { text-shadow: 0 0 ' + glowBlur + 'px ' + glowColor + ', 0 0 ' + headlineGlowIntensity + 'px ' + glowColor + '; }\n';
                    break;
                case 'brightness':
                    css += headlineSelectors + ' { filter: brightness(' + brightness + ') contrast(' + contrast + '); }\n';
                    break;
            }
        }
        
        // Inject styles
        if (css) {
            $('<style id="lhs-preview-styles">').html(css).appendTo('head');
        }
    }

    /**
     * Get current form settings
     */
    function getFormSettings() {
        var settings = {};
        
        $('.lhs-form-table input, .lhs-form-table select, .lhs-form-table textarea').each(function() {
            var $input = $(this);
            var name = $input.attr('name');
            
            if (name) {
                if ($input.is(':checkbox')) {
                    if (name === 'headline_levels[]') {
                        // Handle multiple checkboxes for headline levels
                        if (!settings.headline_levels) {
                            settings.headline_levels = [];
                        }
                        if ($input.is(':checked')) {
                            settings.headline_levels.push($input.val());
                        }
                    } else {
                        settings[name] = $input.is(':checked');
                    }
                } else {
                    settings[name] = $input.val();
                }
            }
        });
        
        return settings;
    }

    /**
     * Initialize form handlers
     */
    function initFormHandlers() {
        // Save settings - using WordPress submit button
        $('#submit').on('click', function(e) {
            e.preventDefault();
            saveSettings();
        });

        // Reset settings
        $('.lhs-reset-settings').on('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to reset all settings to their default values?')) {
                resetSettings();
            }
        });

        // Import/Export functionality
        $('.lhs-export-settings').on('click', function(e) {
            e.preventDefault();
            exportSettings();
        });

        $('.lhs-import-settings').on('click', function(e) {
            e.preventDefault();
            $('#lhs-import-file').click();
        });

        $('#lhs-import-file').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                importSettings(file);
            }
        });
    }

    /**
     * Save settings via AJAX
     */
    function saveSettings() {
        var $button = $('#submit');
        var originalText = $button.val();
        
        $button.val('Saving...').prop('disabled', true);
        
        var settings = getFormSettings();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'lhs_save_settings',
                settings: settings,
                nonce: $('#lhs_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Settings saved successfully!', 'success');
                } else {
                    showNotification('Error saving settings: ' + response.data, 'error');
                }
            },
            error: function() {
                showNotification('Error saving settings. Please try again.', 'error');
            },
            complete: function() {
                $button.val(originalText).prop('disabled', false);
            }
        });
    }

    /**
     * Reset settings to defaults
     */
    function resetSettings() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'lhs_reset_settings',
                nonce: $('#lhs_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showNotification('Error resetting settings: ' + response.data, 'error');
                }
            },
            error: function() {
                showNotification('Error resetting settings. Please try again.', 'error');
            }
        });
    }

    /**
     * Export settings
     */
    function exportSettings() {
        var settings = getFormSettings();
        var dataStr = JSON.stringify(settings, null, 2);
        var dataBlob = new Blob([dataStr], {type: 'application/json'});
        
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(dataBlob);
        link.download = 'lhs-settings-' + new Date().toISOString().split('T')[0] + '.json';
        link.click();
        
        showNotification('Settings exported successfully!', 'success');
    }

    /**
     * Import settings
     */
    function importSettings(file) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                var settings = JSON.parse(e.target.result);
                
                // Apply imported settings to form
                $.each(settings, function(key, value) {
                    if (key === 'headline_levels' && Array.isArray(value)) {
                        // Handle headline levels checkboxes
                        $('[name="headline_levels[]"]').prop('checked', false);
                        value.forEach(function(level) {
                            $('[name="headline_levels[]"][value="' + level + '"]').prop('checked', true);
                        });
                    } else {
                        var $input = $('[name="' + key + '"]');
                        
                        if ($input.length) {
                            if ($input.is(':checkbox')) {
                                $input.prop('checked', value);
                            } else {
                                $input.val(value);
                            }
                        }
                    }
                });
                
                // Update color pickers
                $('.lhs-color-picker').each(function() {
                    $(this).wpColorPicker('color', $(this).val());
                });
                
                // Update range displays
                $('#lhs_headline_fade_opacity').next('.lhs-range-value').text($('#lhs_headline_fade_opacity').val());
                $('#lhs_link_slide_opacity').next('.lhs-range-value').text($('#lhs_link_slide_opacity').val());
                $('#lhs_headline_brightness').next('.lhs-range-value').text($('#lhs_headline_brightness').val());
                $('#lhs_headline_contrast').next('.lhs-range-value').text($('#lhs_headline_contrast').val());
                
                updateLivePreview();
                showNotification('Settings imported successfully!', 'success');
                
            } catch (error) {
                showNotification('Error importing settings: Invalid file format.', 'error');
            }
        };
        
        reader.readAsText(file);
    }

    /**
     * Show notification
     */
    function showNotification(message, type) {
        var $notification = $('<div class="lhs-notification lhs-notification-' + type + '">')
            .text(message)
            .hide()
            .appendTo('body')
            .fadeIn();
        
        setTimeout(function() {
            $notification.fadeOut(function() {
                $(this).remove();
            });
        }, 4000);
    }

    /**
     * Add custom CSS for notifications
     */
    function addNotificationStyles() {
        var css = `
            .lhs-notification {
                position: fixed;
                top: 30px;
                right: 30px;
                padding: 15px 20px;
                border-radius: 4px;
                color: white;
                font-weight: 600;
                z-index: 9999;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .lhs-notification-success {
                background-color: #10b981;
            }
            .lhs-notification-error {
                background-color: #ef4444;
            }
        `;
        
        $('<style>').prop('type', 'text/css').html(css).appendTo('head');
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initLHSAdmin();
        addNotificationStyles();
    });

})(jQuery); 