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
 * NameFilter class
 *
 * @package naquadria.components.routing.container.filter
 */
class NameFilter extends \FilterIterator {
    
    /**
     * name of a route
     *
     * @var string
     */
    private $name;
    
    /**
     * constructor
     *
     * @param \Naquadria\Components\Routing\Container\RouteContainer $container
     * @param string $name
     */
    public function __construct(RouteContainer $container, $name)
    {
        parent::__construct($container);
        $this->name = (string)$name;
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
        return parent::current()->getName() === $this->name;
    }
    
}