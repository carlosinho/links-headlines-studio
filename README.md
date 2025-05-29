# Links & Headlines Studio by KK

A WordPress plugin that provides nice styling options for links and headlines on your WordPress website with real-time preview and code generation.

## 🚀 Features

### Link Effects
- **Custom Colors**: Base and hover colors with color picker
- **Multiple Hover Effects**:
  - Animated Underline (with thickness control)
  - Slide Effect (with custom color and opacity)
  - Glow Effect (with intensity control)
  - None (color changes only)
- **Base Decoration**: Choose underline or none
- **Transition Duration**: Customizable animation speed (100-2000ms)
- **Enable/Disable Toggle**: Turn link effects on/off

### Headline Effects
- **Selective Application**: Choose which headline levels (H1-H6) to style
- **Multiple Hover Effects**:
  - Fade Effect (with opacity control)
  - Color Shift (with custom hover color)
  - Soft Glow (with color, intensity, and blur control)
  - Brightness Shift (with brightness and contrast control)
  - None (no effects)
- **Transition Duration**: Customizable animation speed (100-2000ms)
- **Enable/Disable Toggle**: Turn headline effects on/off

### Advanced Features
- **Live Preview**: Real-time preview with dark/light mode toggle
- **Code Generation**: "Give Me Code" button generates ready-to-use CSS
- **Settings Management**:
  - Save settings to WordPress database
  - Reset to defaults
  - Export settings as JSON
  - Import settings from JSON
- **Professional Admin Interface**: Modern WordPress-style UI
- **Frontend Integration**: Automatic style injection based on saved settings

## 📁 File Structure

```
links-headlines-studio/
├── links-headlines-studio.php    # Main plugin file with AJAX handlers
├── css/
│   ├── frontend.css              # Frontend styles (basic)
│   └── admin.css                 # Admin panel styles
├── js/
│   ├── frontend.js               # Frontend JavaScript (basic)
│   └── admin.js                  # Admin panel JavaScript with live preview
├── includes/
│   └── admin-page.php            # Admin page template
└── README.md                     # Documentation
```

## 🔧 Installation

1. **Upload** the plugin files to your WordPress plugins directory:
   ```
   /wp-content/plugins/links-headlines-studio/
   ```

2. **Activate** the plugin through the WordPress admin panel:
   - Go to `Plugins > Installed Plugins`
   - Find "Links & Headlines Studio by KK"
   - Click "Activate"

3. **Configure** the plugin:
   - Navigate to `Tools > Links & Headlines` in your WordPress admin
   - Customize your link and headline settings
   - Use the live preview to see changes in real-time
   - Click "Give Me Code" to generate CSS for manual implementation

## ⚙️ Configuration Options

### Link Effects Settings
- **Enabled**: Toggle to enable/disable all link effects
- **Base Link Color**: Default color for links
- **Base Link Decoration**: Underline or none
- **Hover Color**: Color when hovering over links
- **Hover Effect**: Choose from multiple animation styles
- **Effect Duration**: Animation speed in milliseconds

#### Effect-Specific Options
- **Underline Effect**: Thickness control (1-10px)
- **Slide Effect**: Custom color and opacity (0.1-1.0)
- **Glow Effect**: Intensity control (2-20px)

### Headline Effects Settings
- **Enabled**: Toggle to enable/disable all headline effects
- **Headline Levels**: Select which levels (H1-H6) to apply effects to
- **Hover Effect**: Choose from multiple animation styles
- **Effect Duration**: Animation speed in milliseconds

#### Effect-Specific Options
- **Fade Effect**: Target opacity (0.1-0.9)
- **Color Shift**: Custom hover color
- **Soft Glow**: Color, intensity (1-20px), and blur (0-10px)
- **Brightness**: Brightness (0.5-2.0) and contrast (0.5-2.0) levels

## 🎨 Usage

### Option 1: Automatic Frontend Application
The plugin automatically injects styles based on your saved settings to:
```css
.entry-content a, .post-content a, .page-content a, article a
.entry-content h1-h6, .post-content h1-h6, .page-content h1-h6, article h1-h6
```

### Option 2: Manual CSS Implementation
1. Configure your settings in the admin panel
2. Click "Give Me Code" button
3. Copy the generated CSS code
4. Add it to your theme's `style.css` or use a custom CSS plugin

### Option 3: Targeted Implementation
Use the generated CSS code but modify the selectors for specific areas:
```css
/* Only style links in specific containers */
.my-custom-container a {
    /* Generated link CSS here */
}

/* Only style headlines in specific areas */
.my-custom-area h1:hover {
    /* Generated headline CSS here */
}
```

## 🛠️ Technical Details

### WordPress Integration
- **Settings Storage**: Uses WordPress Options API (`lhs_options`)
- **AJAX Handlers**: Secure nonce-verified AJAX for all operations
- **Admin Menu**: Added under Tools submenu
- **Asset Management**: Proper enqueuing with version control

### AJAX Actions
- `lhs_save_settings` - Save configuration to database
- `lhs_reset_settings` - Reset to default configuration
- `lhs_generate_code_preview` - Generate CSS code on demand

### Security Features
- **Nonce verification** for all AJAX requests
- **Capability checks** (`manage_options`) for admin access
- **Data sanitization** using WordPress functions
- **Direct access prevention** for all PHP files

### Frontend Performance
- **Conditional Loading**: Styles only injected when effects are enabled
- **Optimized CSS**: Minimal impact on page load
- **No Frontend JavaScript**: Unless specifically needed

## 🎯 Browser Support

- **Modern browsers** with CSS3 support
- **Progressive Enhancement**: Graceful degradation for older browsers
- **Mobile Responsive**: All effects work on touch devices

## 📝 Customization Examples

### Link Slide Effect CSS
```css
.entry-content a {
    position: relative;
    overflow: hidden;
    text-decoration: none;
    color: #2271b1;
    transition: all 300ms ease;
}

.entry-content a::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: #2271b1;
    opacity: 1.0;
    transition: width 300ms ease;
}

.entry-content a:hover::before {
    width: 100%;
}
```

### Headline Glow Effect CSS
```css
.entry-content h1:hover, .entry-content h2:hover {
    text-shadow: 0 0 3px #2271b1, 0 0 5px #2271b1;
    transition: all 300ms ease;
}
```

## 🔄 Version History

### v0.0.2 (Current)
- **Live Preview**: Real-time preview with dark/light mode
- **Code Generation**: On-demand CSS generation with copy functionality
- **Settings Management**: Save, reset, import/export functionality
- **Multiple Effects**: Comprehensive link and headline effect options
- **Professional UI**: WordPress-style admin interface
- **AJAX Integration**: Seamless admin experience

### v0.0.1
- Initial development release

### Planned Features
- **Google Fonts Integration**
- **Effect Presets**
- **Animation Timing Functions**
- **Advanced Selectors**
- **Performance Dashboard**

## 📞 Support

For support or feature requests:

1. **Test Settings**: Use the live preview to verify your configuration
2. **Generate Code**: Use "Give Me Code" to get implementation-ready CSS
3. **Check Compatibility**: Test with your theme's CSS selectors
4. **Browser Console**: Check for JavaScript errors in admin panel

## 🏷️ Plugin Information

- **Version**: 0.0.2
- **Requires WordPress**: 5.0+
- **Tested up to**: 6.4
- **License**: GPL v2 or later
- **Author**: Karol K
- **Text Domain**: links-headlines-studio 