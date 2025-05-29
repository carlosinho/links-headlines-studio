/**
 * Links & Headlines Studio - Frontend JavaScript
 * 
 * @package LinksHeadlinesStudio
 * @version 0.0.2
 */

(function($) {
    'use strict';

    /**
     * Initialize the plugin
     */
    function initLinksHeadlinesStudio() {
        // Add smooth scrolling for anchor links (if enabled)
        initSmoothScrolling();
    }

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initSmoothScrolling() {
        // Add smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'easeInOutQuart');
            }
        });
    }

    /**
     * Add easing function
     */
    $.easing.easeInOutQuart = function(x, t, b, c, d) {
        if ((t /= d / 2) < 1) return c / 2 * t * t * t * t + b;
        return -c / 2 * ((t -= 2) * t * t * t - 2) + b;
    };

    // Initialize when document is ready
    $(document).ready(function() {
        initLinksHeadlinesStudio();
    });

})(jQuery); 