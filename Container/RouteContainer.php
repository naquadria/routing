<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing\Container;

use \SplObjectStorage as Storage;
use Naquadria\Components\Routing\Route;
use Naquadria\Components\Routing\Exceptions\RouteAggregationException;
use Naquadria\Components\Routing\Exceptions\RouteNotFoundException;

/**
 * RouteContainer class
 *
 * @package naquadria.components.routing.container
 */
class RouteContainer extends Storage {

    /**
     * add()
     *
     * adds a route class instance with a given mixed set of data
     * to this container
     *
     * @param \Naquadria\Components\Routing\Route $route
     * @param mixed $data
     * @throws \Naquadria\Components\Routing\Exceptions\RouteAggregationException
     */
    public function add(Route $route, $data)
    {
        if ( parent::contains($route) ) {
            throw new RouteAggregationException(
                'Route already registered to this container'
            );
        }
        
        parent::attach(
            $route, 
            [
                'data' => $data,
            ]
        );
    }
    
    /**
     * setData()
     *
     * sets the data set of a given route
     * 
     * @param \Naquadria\Components\Routing\Route $route
     * @param mixed $data
     * @throws \Naquadria\Components\Routing\Exceptions\RouteNotFoundException
     */
    public function setData(Route $route, $data)
    {
        if ( !parent::contains($route) ) {
            throw new RouteNotFoundException(
                'Unknown route instance'
            );
        }
        
        $current = parent::offsetGet($route);
        $current['data'] = $data;
        parent::offsetSet($route, $current);
    }
    
    /**
     * getData()
     *
     * gets the data set of a given route
     *
     * @param \Naquadria\Components\Routing\Route $route
     * @throws \Naquadria\Components\Routing\Exceptions\RouteNotFoundException
     * @return mixed
     */
    public function getData(Route $route)
    {
        if ( !parent::contains($route) ) {
            throw new RouteNotFoundException(
                'Unknown route instance'
            );
        }
        
        $current = parent::offsetGet($route);
        return $current['data'];
    }
    
}