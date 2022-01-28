<?php
/**
 * Attendees plugin for Craft CMS 3.x
 *
 * A plugin to manage attendees
 *
 * @link      https://percipio.london
 * @copyright Copyright (c) 2021 percipiolondon
 */

namespace percipiolondon\attendees\assetbundles\craftattendees;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * DashboardCPSectionAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    percipiolondon
 * @package   attendees
 * @since     1.0.0
 */
class AttendeesAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@percipiolondon/attendees/web/assets/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        parent::init();
    }
}
