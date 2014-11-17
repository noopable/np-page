<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

use NpPage\BlockInitializer;
use NpPage\ResourceInterface;
use Zend\View\Model\ViewModel;

/**
 *
 * @author tomoaki
 */
interface BlockInterface extends ResourceInterface {

    /**
     * ブロックの定義はブロッククラス内に直接書くような、クラスファイルによる
     * コンフィグレーションを行う可能性が高い。
     * データや設定をクラスファイルとして表現するので、Configuratorを使わず、
     * 自分でconfigureする。
     *
     * @param array $config
     */
    public function configure(array $config);

    /**
     * どのような初期化を行うかは各クラスにまかされている。
     * 必要に応じて、子ブロックの読込等を行う。
     * それに必要なオプションやオブジェクトはBlockInitializerから取得する。
     *
     * @param \NpPage\Block\BlockInitializer $blockInitializer
     */
    public function init(BlockInitializer $blockInitializer);
    
    /**
     * configureメソッドと同様、ブロックのクラスファイル内で実装する可能性が高い
     * 外出しする場合は、別途そのように実装してください。
     *
     * buildに際して、ServiceやBlockPluginManagerが必要な場合は、
     * builderに依存して実行するとよい。
     *
     */
    public function build();

    public function setPriority($order);

    public function getPriority();

    /**
     * 名称はtemplateが実質的にviewScriptのパス
     *
     * @param string $template
     */
    public function setTemplate($template);

    /**
     *
     * @return string
     */
    public function getTemplate();

    /**
     *
     * @param ViewModel $viewModel
     */
    public function setViewModel(ViewModel $viewModel = null);

    /**
     *
     * @return \Zend\View\ViewModel
     */
    public function getViewModel();

    /**
     * BlockInitializer等で、ブロックの状態を確認する
     *
     * @return \NpPage\State
     */
    public function getState();
}

