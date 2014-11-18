<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Config;

/**
 *
 * @author tomoaki
 */
interface ConfigResolverInterface {
    public function resolve($name);
}

