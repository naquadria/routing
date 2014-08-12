<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing;

/**
 * RouteEntity class
 *
 * @package naquadria.components.routing
 */
final class RouteEntity {
    
    /**
     * path data array of the discovered route
     *
     * @var array
     */
    private $data;
    
    /**
     * replacement regex array of the discovered route
     *
     * @var array
     */
    private $replacer;
    
    /**
     * the route instance of the discovered route
     *
     * @var \Naquadria\Components\Routing\Route
     */
    private $route;
    
    /**
     * constructor
     *
     * @param \Naquadria\Components\Routing\Route $route
     * @param array $routeData
     * @param array $replacer
     */
    public function __construct(Route $route, array $routeData, array $replacer)
    {
        $this->name = $route->getName();
        $this->abstraction = $route->getAbstraction();
        $this->data = $routeData;
        $this->replacer = $replacer;
        $this->route = $route;
    }
    
    /**
     * getName()
     *
     * getter for the name of the discovered route
     * 
     * @return string
     */
    public function getName()
    {
        return $this->route->getName();
    }
    
    /**
     * getAbstraction()
     *
     * getter for the abstraction of the discovered route
     *
     *
     * @return mixed
     */
    public function getAbstraction()
    {
        return $this->route->getAbstraction();
    }
    
    /**
     * getData()
     *
     * getter for the path data array of the discovered route
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * getReplacer()
     *
     * getter for the replacement regex array of the discovered route
     *
     * @return array
     */
    public function getReplacer()
    {
        return $this->replacer;
    }
    
    /**
     * getRoute()
     *
     * @return \Naquadria\Components\Routing\Route
     */
    public function getRoute()
    {
        return $this->route;
    }
    
}