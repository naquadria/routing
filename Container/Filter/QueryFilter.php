<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing\Container\Filter;

use Naquadria\Components\Routing\Container\RouteFieldContainer;

/**
 * QueryFilter class
 *
 * @package naquadria.components.routing.container.filter
 */
final class QueryFilter extends \FilterIterator {
    
    /**
     * PathInfoFilter iterator
     *
     * @var \Naquadria\Components\Routing\Container\Filter\PathInfoFilter
     */
    private $filter;
    
    /**
     * Route field container
     *
     * @var \Naquadria\Components\Routing\Container\RouteFieldContainer
     */
    private $routeValues;
    
    /**
     * constructor
     *
     * @param \Naquadria\Components\Routing\Container\Filter\PathInfoFilter $filterIterator
     * @param \Naquadria\Components\Routing\Container\RouteFieldContainer $filter
     */
    public function __construct(PathInfoFilter $filterIterator, RouteFieldContainer $filter)
    {
        parent::__construct($filterIterator);
        $this->filter = $filter;
    }
    
    /**
     * announce()
     *
     * announces a value to a filter that has been registered under the given name
     *
     * @param string $name
     * @param value string|int|float|bool
     */
    public function announce($name, $value)
    {
        if ( ! array_key_exists($name, $this->filter) ) {
            throw new \LogicException(
                'Unknown filter: '.$filter
            );
        }
        
        $this->routeValues[$name] = $value;
    }
    
    /**
     * validateRouteValues()
     *
     * validates the setted route values
     *
     * @return bool
     */
    public function validateRouteValues()
    {
        return empty(
            array_diff(
                array_keys($this->filter), 
                array_keys($this->routeValues)
            )
        );
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
        foreach ( $this->filter as $name => $filter ) {
            if ( false === $filter->get($name)->filter($this->routeValues[$name], parent::current()) ) {
                return false;
            }
        }
        
        return true;
    }
    
}