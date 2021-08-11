<?php
/**
 * MYMO CMS - The Best Laravel CMS
 *
 * @package    mymocms/mymocms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/mymocms/mymocms
 * @license    MIT
 *
 * Created by The Anh.
 * Date: 8/9/2021
 * Time: 7:54 PM
 */

return [
    /**
     * Enable upload plugins
     *
     * Default: true
     */
    'enable_upload' => true,

    /**
     * Enable autoload plugins
     * If disable, you can require plugin by composer
     *
     * Default: true
     */
    'autoload' => true,

    /**
     * Plugins path
     *
     * This path used for save the generated plugin. This path also will added
    automatically to list of scanned folders.
     */
    'path' => base_path('plugins'),
    /**
     * Plugins assets path
     *
     * Path for assets when it was publish
     * Default: plugins
     */
    'assets' => public_path('plugins'),
];
