<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Config\Loader;

/**
 * Description of ConfigLoader
 *
 * @author tomoaki
 */
interface ConfigLoaderInterface {

    public function __construct(array $config = null);
    
    public function load($name);
}
