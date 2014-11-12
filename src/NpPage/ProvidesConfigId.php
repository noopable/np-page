<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

/**
 * Module設定用初期値
 *
 * @author tomoaki
 */
trait ProvidesConfigId {
    public function getConfigId()
    {
        return 'np-page';
    }
}
