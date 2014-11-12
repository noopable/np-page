<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Zend\Permissions\Acl;
use Zend\View\Variables;

interface ResourceInterface extends Acl\Resource\ResourceInterface
{
    public function getResourceClass();
    public function getResourceId();

    public function setName($name);
    public function getName();

    public function setResourceClass($class);

    public function setPrototype(ResourceInterface $prototype);

    public function setOption($name, $value);
    public function getOption($name, $default = null);

    public function setDefaultOptions($defaultOptions);
    public function getOptions();

    public function setProperties($properties, $clear = false);

    /**
     * @var Variables
     */
    public function getProperties();

    public function setProperty($name, $value);
    public function getProperty($name, $default = null);

    /**
     * prototypeが持っているかどうかをチェックするときはrecursiveをtrueとする。
     *
     * @param type $name
     * @param type $recursive
     */
    public function issetProperty($name, $recursive = true);

}