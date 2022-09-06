<?php

namespace Acms\Plugins\GoogleAnalytics4\GET\GoogleAnalytics4;

use ACMS_GET;
use ACMS_Corrector;
use Template;

/**
 *  Analytics Data APIと連携して、
 *  ページをアクセス数に基づいたランキング形式で表示するモジュール
 */
class Ranking extends ACMS_GET
{
    public function get()
    {
        $Tpl = new Template($this->tpl, new ACMS_Corrector());
        $this->buildModuleField($Tpl);
    }
}
