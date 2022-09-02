<?php

namespace Acms\Plugins\Event;

use ACMS_App;
use Acms\Services\Common\InjectTemplate;

class ServiceProvider extends ACMS_App
{
    /**
     * @var string
     */
    public $version = '0.0.0';

    /**
     * @var string
     */
    public $name = 'GoogleAnalytics4';

    /**
     * @var string
     */
    public $author = 'com.appleple';

    /**
     * @var bool
     */
    public $module = false;

    /**
     * @var bool|string
     */
    public $menu = 'google-analytics4_index';

    /**
     * @var string
     */
    public $desc = 'Google Analytics4（GA4）と連携するための拡張アプリです。';

    /**
     * サービスの初期処理
     */
    public function init()
    {
        $inject = InjectTemplate::singleton();
        // $inject->add('admin-module-config-Event_Calendar', PLUGIN_DIR. 'Event/template/event-calendar_body.html');
        // $inject->add('admin-module-config-Event_Summary', PLUGIN_DIR. 'Event/template/event-summary_body.html');
        // $inject->add(
        //     'admin-module-config-Event_ArchiveList',
        //     PLUGIN_DIR. 'Event/template/event-archive-list_body.html'
        // );
        // $inject->add('admin-module-config-Event_Month', PLUGIN_DIR. 'Event/template/event-month_body.html');
        $inject->add('admin-module-select', PLUGIN_DIR . 'GoogleAnalytics4/template/admin/module/select.html');

        if (ADMIN === 'app_' . $this->menu) {
            $inject->add('admin-topicpath', PLUGIN_DIR . 'GoogleAnalytics4/template/topicpath.html');
            $inject->add('admin-main', PLUGIN_DIR . 'GoogleAnalytics4/template/main.html');
        }
    }
    /**
     * インストールする前の環境チェック処理
     *
     * @return bool
     */
    public function checkRequirements()
    {
        return true;
    }

    /**
     * インストールするときの処理
     * データベーステーブルの初期化など
     *
     * @return void
     */
    public function install()
    {
    }

    /**
     * アンインストールするときの処理
     * データベーステーブルの始末など
     *
     * @return void
     */
    public function uninstall()
    {
    }

    /**
     * アップデートするときの処理
     *
     * @return bool
     */
    public function update()
    {
        return true;
    }

    /**
     * 有効化するときの処理
     *
     * @return bool
     */
    public function activate()
    {
        return true;
    }

    /**
     * 無効化するときの処理
     *
     * @return bool
     */
    public function deactivate()
    {
        return true;
    }
}
