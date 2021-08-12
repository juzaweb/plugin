<?php
/**
 * MYMO CMS - Free Laravel CMS
 *
 * @package    juzawebcms/juzawebcms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzawebcms/juzawebcms
 * @license    MIT
 *
 * Created by The Anh.
 * Date: 5/29/2021
 * Time: 2:24 PM
 */

Route::group(['prefix' => 'plugins'], function () {
    Route::get('/', 'PluginController@index')->name('admin.plugin');

    Route::get('/get-data', 'PluginController@getDataTable')->name('admin.plugin.get-data');

    Route::post('/bulk-actions', 'PluginController@bulkActions')->name('admin.plugin.bulk-actions');
});
