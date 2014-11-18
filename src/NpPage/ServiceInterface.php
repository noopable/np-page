<?php
/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ServiceInterface
{
    public function setSubscriber(ListenerAggregateInterface $subscriber);

    /**
     * @return ServiceSubscriber
     */
    public function getSubscriber();
    public function subscribe(EventManagerInterface $eventManager);

    public function activate($requestedName);
    public function isActivated();

    public function setRequestedName($requestedName);
    public function getRequestedName();

    public function setRepository(RepositoryInterface $repository);
    public function getRepository();
}
