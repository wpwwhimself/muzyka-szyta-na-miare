<?php

namespace App;

use Wpwwhimself\Shipyard\Theme;

class ShipyardTheme
{
    use Theme;

    #region theme
    /**
     * Available themes:
     * - origin - separated cells, clean background, contents floating in the middle
     * - austerity - broad background, main sections spread out
     */
    public const THEME = "austerity";
    #endregion

    #region colors
    /**
     * App accent colors:
     * - primary - for background, primary (disruptive) actions and important text
     * - secondary - for default buttons and links
     * - tertiary - for non-disruptive interactive elements
     */
    public const COLORS = [
        "primary" => "#c8a239",
        "secondary" => "#5c470e",
        "tertiary" => "#ffb400",
    ];
    #endregion

    #region fonts
    /**
     * type in the fonts as an array
     */
    public const FONTS = [
        "base" => ["Raleway", "sans-serif"],
        "heading" => ["Marcellus", "serif"],
        "mono" => ["Space Mono", "monospace"],
    ];

    // if fonts come from Google Fonts, add the URL here
    public const FONT_IMPORT_URL = 'https://fonts.googleapis.com/css2?family=Marcellus&family=Raleway:ital,wght@0,100..900;1,100..900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap';
    #endregion

    #region optional modules
    /**
     * Uncomment the modules you want to use
     */
    public const MODULES = [
        "sheetmusic",
        // "wysiwyg",
    ];
    #endregion
}
