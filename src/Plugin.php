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
