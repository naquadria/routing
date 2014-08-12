<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing\Container\Filter;

use Naquadria\Components\Routing\Container\RouteContainer;

/**
 * PathInfoFilter class
 *
 * @package naquadria.components.routing.container.filter
 */
class PathInfoFilter extends \FilterIterator {
    
    /**
     * RouteContainer instance
     *
     * @var \Naquadria\Components\Routing\Container\RouteContainer
     */
    private $pathInfo;
    
    /**
     * constructor
     *
     * @param \Naquadria\Components\Routing\Container\RouteContainer $container
     */
    public function __construct(RouteContainer $container)
    {
        parent::__construct($container);
    }
    
    /**
     * setPathInfo()
     *
     * sets the path information string
     *
     * @param string $pathInfo
     */
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;
    }
    
    /**
     * accept()
     *
     * accept caller for a filter iterator
     *
     * @return bool
     */
    public function accept()
    {
        $data = $this->getInnerIterator()->offsetGet(parent::current())['data'];
        return (bool) preg_match('~^'.$data['regex'].'$~', $this->pathInfo);
    }
    
}