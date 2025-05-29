# Links & Headlines Studio by KK

A WordPress plugin that provides stunning styling options for links and headlines/subheadings on your WordPress website.

## 🚀 Features

### Link Styling
- **Custom hover colors** with color picker
- **Multiple hover effects**:
  - Animated underline
  - Slide effect
  - Glow effect
- **Customizable transition duration**
- **Smooth animations** with jQuery easing

### Headlines & Subheadings
- **Custom font families** including web-safe fonts
- **Font weight customization** (thin to black)
- **Hover effects**:
  - Fade effect
  - Scale effect
- **Responsive design** optimization

### Advanced Features
- **Live preview** in admin panel
- **Auto-apply to content** or manual implementation
- **Custom CSS** input for advanced users
- **Import/Export settings** for easy backup and migration
- **Modern admin interface** with toggle switches and color pickers
- **Intersection Observer** for scroll animations
- **WordPress coding standards** compliant
- **Translation ready** with i18n support

## 📁 File Structure

```
links-headlines-studio/
├── links-headlines-studio.php    # Main plugin file
├── css/
│   ├── frontend.css              # Frontend styles
│   └── admin.css                 # Admin panel styles
├── js/
│   ├── frontend.js               # Frontend JavaScript
│   └── admin.js                  # Admin panel JavaScript
├── includes/
│   └── admin-page.php            # Admin page template
├── languages/                    # Translation files (future)
└── README.md                     # Documentation
```

## 🔧 Installation

1. **Download/Clone** the plugin files to your WordPress plugins directory:
   ```
   /wp-content/plugins/links-headlines-studio/
   ```

2. **Activate** the plugin through the WordPress admin panel:
   - Go to `Plugins > Installed Plugins`
   - Find "Links & Headlines Studio"
   - Click "Activate"

3. **Configure** the plugin:
   - Navigate to `LH Studio` in your WordPress admin menu
   - Customize your link and headline settings
   - Use the live preview to see changes in real-time

## ⚙️ Configuration

### General Settings
- **Enable/Disable** the plugin functionality
- **Auto-apply** styles to content automatically

### Link Styling Options
- **Link Hover Color**: Choose any color using the color picker
- **Hover Effects**: Select from multiple animation styles
- **Transition Duration**: Control animation speed (100-2000ms)

### Headlines & Subheadings
- **Font Family**: Choose from web-safe fonts or inherit from theme
- **Font Weight**: Select from thin (100) to black (900)
- **Hover Effects**: Add interactive animations to headlines

### Advanced Settings
- **Custom CSS**: Add your own CSS for further customization
- **Import/Export**: Backup and restore your settings

## 🎨 Usage

### Automatic Application
If "Auto Apply to Content" is enabled, the plugin will automatically style all links and headlines on your site.

### Manual Implementation
Add these CSS classes to specific elements for targeted styling:

```html
<!-- For links -->
<div class="lhs-styled-links">
    <a href="#">This link will have custom styling</a>
</div>

<!-- For headlines -->
<div class="lhs-styled-headlines">
    <h1>This headline will have custom styling</h1>
    <h2>This subheading too</h2>
</div>
```

### PHP Implementation
```php
// Add classes programmatically
add_filter('the_content', function($content) {
    return '<div class="lhs-styled-links lhs-styled-headlines">' . $content . '</div>';
});
```

## 🛠️ Development

### WordPress Hooks Used
- `init` - Plugin initialization
- `wp_enqueue_scripts` - Frontend assets
- `admin_enqueue_scripts` - Admin assets
- `admin_menu` - Admin menu registration

### AJAX Actions
- `lhs_get_settings` - Retrieve plugin settings
- `lhs_save_settings` - Save plugin settings
- `lhs_reset_settings` - Reset to default settings

### Security Features
- **Nonce verification** for all AJAX requests
- **Data sanitization** and validation
- **Capability checks** for admin access
- **Direct access prevention** for all files

## 🎯 Browser Support

- **Modern browsers** with CSS3 and ES5+ support
- **Internet Explorer 11+**
- **Graceful degradation** for older browsers
- **Mobile responsive** design

## 📝 Customization Examples

### Custom Link Effects
```css
.lhs-styled-links a {
    position: relative;
    text-decoration: none;
}

.lhs-styled-links a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: currentColor;
    transition: width 0.3s ease;
}

.lhs-styled-links a:hover::after {
    width: 100%;
}
```

### Custom Headline Animations
```css
@keyframes headlineGlow {
    0% { text-shadow: none; }
    50% { text-shadow: 0 0 20px currentColor; }
    100% { text-shadow: none; }
}

.lhs-styled-headlines h1:hover {
    animation: headlineGlow 1s ease-in-out;
}
```

## 🔄 Updates & Maintenance

### Version History
- **v0.0.1** - Initial release with core functionality

### Planned Features
- **Google Fonts integration**
- **More animation effects**
- **Typography presets**
- **Performance optimizations**

## 📞 Support

For support, feature requests, or bug reports:

1. Check the plugin settings and live preview
2. Review the documentation above
3. Test with default WordPress themes
4. Check browser console for JavaScript errors

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 👨‍💻 Developer Notes

### WordPress Coding Standards
- PSR-4 autoloading structure
- Proper sanitization and validation
- Security best practices implemented
- Performance optimized with conditional loading

### JavaScript Architecture
- jQuery-based with fallbacks
- Intersection Observer for performance
- Modular code structure
- Error handling and graceful degradation

### CSS Architecture
- BEM-inspired naming convention
- Mobile-first responsive design
- CSS custom properties ready
- Minimal specificity conflicts

---

**Links & Headlines Studio** - Transform your WordPress site's typography with style! ✨ 