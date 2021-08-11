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
 * Date: 7/10/2021
 * Time: 3:18 PM
 */

use Juzaweb\Core\Facades\HookAction;

HookAction::addAdminMenu(
    trans('juzaweb::app.plugins'),
    'plugins',
    [
        'icon' => 'fa fa-plug',
        'position' => 50
    ]
);
