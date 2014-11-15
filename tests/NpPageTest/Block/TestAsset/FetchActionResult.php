<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest\Block\TestAsset;

use NpPage\Block\FetchActionResultTrait;

/**
 * Description of FetchActionResult
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FetchActionResult
{
    use FetchActionResultTrait;

    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setControllerName($controller)
    {
        $this->controllerName = $controller;
    }
}
