<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage\Block;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Description of FetchActionResultTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait FetchActionResultTrait
{
    use EventManagerAwareTrait;
    use ListenerAggregateTrait;

    protected $populateViewModel;

    /**
     * injectViewModelListenerで、$eのViewModel(レイアウト用ルートレベル)
     * -100で実行される
     * createViewModelListener が-80
     * -90あたりで、フェッチしてイベントループを止めてしまってもよいかもしれない。
     * -90では、templateを割り当てている。
     * createViewModelを待つ必要もないかもしれないが。
     * -10あたりで結果を配列で取得して取り込むだけでもいいかもしれない。
     *
     * そうすると、fetchViewModelではなく、fetchActionResultになると思うが。
     *
     * templateがほしいなら -95てところか。
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'postDispatch'), -95);
    }


    public function postDispatch(MvcEvent $e)
    {
        return $this->fetchActionResult($e);
    }

    /**
     * fetchできたときは、イベントループを停止して、そのViewModelを返す。
     * 該当しなかったときはnullを返す。
     *
     * @param MvcEvent $e
     * @return ViewModel|null
     */
    public function fetchActionResult(MvcEvent $e)
    {
        $resultModel = $e->getResult();
        if (!$resultModel instanceof ViewModel) {
            return;
        }

        $params = $e->getRouteMatch()->getParams();

        if (isset($params[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
            $controllerName = $params[ModuleRouteListener::ORIGINAL_CONTROLLER];
        } elseif (isset($params['controller'])) {
            $controllerName = $params['controller'];
        }

        if (!isset($controllerName)
            || !isset($this->signature)
            || !is_array($this->signature)
            || (count($this->signature) === 0)
            || ! isset($this->controllerName)
            || $this->controllerName !== $controllerName) {
            return;
        }

        foreach ($this->signature as $key => $val) {
            if (!isset($params[$key]) || $params[$key] !== $val) {
                return;
            }
        }
        /**
         * 結果を受け取って、Dispatchイベントを停止する
         */
        $this->populateViewModel($resultModel);
        $e->stopPropagation();

        return $resultModel;
    }

    protected function populateViewModel(ViewModel $viewModel)
    {
        $this->populateViewModel = $viewModel;
        if (method_exists($this, 'getViewModel')) {
            $this->getViewModel()->setVariables($viewModel);
        }
    }
}
