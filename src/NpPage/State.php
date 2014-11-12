<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Serializable;

/**
 * Description of State
 *
 * @author tomoaki
 */
final class State implements Serializable{

    const CONFIGURED = 'CONFIGURED';
    const INITIALIZED = 'INITIALIZED';
    const PREPARE_VIEW_MODEL = 'PREPARE_VIEW_MODEL';
    const BUILT = 'BUILT';

    protected $values = array(
        'CONFIGURED' => false,
        'INITIALIZED' => false,
        'BUILT' => false,
        'PREPARE_VIEW_MODEL' => false,
    );

    public function __construct(array $states = null)
    {
        if (is_array($states)) {
            $this->values = $states;
        }
    }

    public function checkFlag($flag)
    {
        return (isset($this->values[$flag]) && $this->values[$flag]);
    }

    public function setFlag($flag, $bool = true)
    {
        $this->values[$flag] = (bool) $bool;
    }

    public function serialize() {
        return serialize($this->values);
    }

    public function unserialize($serialized) {
        $this->__construct(unserialize($serialized));
    }
}
