<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPageTest\TestAsset;

use NpPage\ProvidesResource as ProvidesResourceTrait;
use NpPage\ResourceInterface;

/**
 * Description of ProvidesResource
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ProvidesResource implements ResourceInterface
{
    use ProvidesResourceTrait;
}
