<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Config\Loader;

use NpPage\Config\ConfigResolverInterface;

/**
 * Description of ConfigFileLoader
 *
 * @author tomoaki
 */
class File implements ConfigLoaderInterface {

    protected $resolver;

    protected $config;

    public function __construct(array $config = null)
    {
        if (is_array($config)) {
            $this->config = $config;
        }
    }

    public function load($name) {
        if (isset($this->resolver)) {
            $__file__ = $this->resolver->resolve($name);
            unset($name);
            if (is_file($__file__)) {
                ob_start();
                $config = include $__file__;
                ob_end_clean();
            }
        }
        if (!isset($config)) {
            $config = array();
        }
        return $config;
    }

    /**
     *
     * @param \NpPage\Config\ConfigResolverInterface $resolver
     */
    public function setResolver(ConfigResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getResolver()
    {
        return $this->resolver;
    }

    public function hasResolver()
    {
        return isset($this->resolver);
    }

}
