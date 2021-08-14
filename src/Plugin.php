<?php
/**
 * MYMO CMS - The Best Laravel CMS
 *
 * @package    juzaweb/laravel-cms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 *
 * Created by JUZAWEB.
 * Date: 8/12/2021
 * Time: 4:56 PM
 */

namespace Juzaweb\Plugin;

use Illuminate\Support\Facades\Route;

class Plugin
{
    protected static $namespace = 'Juzaweb\Plugin\Http\Controllers';

    public static function adminRoutes()
    {
        Route::namespace(self::$namespace)
            ->group(__DIR__ . '/routes/admin.php');
    }
}
