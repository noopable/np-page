<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Exception;

/**
 * Description of DuplicatedBlockNameException
 *
 * @author tomoaki
 */
class DuplicatedBlockNameException extends InvalidArgumentException {
    public function setBlockName($blockName)
    {
        $this->blockName = $blockName;
    }

    public function getBlockName()
    {
        return $this->blockName;
    }
}
