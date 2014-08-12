<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing;

use Naquadria\Components\Routing\Exceptions\RouteFieldException;

/**
 * RouteField class
 * 
 * @package naquadria.components.routing
 */
class RouteField {
    
    /**
     * property <-> classname storage
     *
     * @var array
     */
    private $properties;
    
    /**
     * field filter callback
     *
     * @var callable
     */
    private $callback;
    
    /**
     * setRouteProperty()
     *
     * sets the property binding to a given classname
     *
     * @param string $property
     * @param string $bindingClassName
     */
    public function setRouteProperty($property, $bindingClassName)
    {
        $this->properties[$property] = $bindingClassName;
    }
    
    /**
     * setCallback()
     *
     * sets the filter callback for the current field
     *
     * @param callable $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
    
    /**
     * filter()
     *
     * filter method for this route field
     *
     * @param string|int|float|bool $value
     * @param \Naquadria\Components\Routing\Route $route
     * @throws \Naquadria\Components\Routing\Exceptions\RouteFieldException
     */
    public function filter($value, Route $route)
    {
        $routeExporter = function($property) {
            if ( ! property_exists($this, $property) ) {
                throw new RouteFieldException(
                    'Unknown Property: '.$property
                );
            }
            
            return $this->$property;
        };
        
        $routeData = [];
        foreach ( $this->properties as $property => $binding ) {
            $propertyExporter = $routeExporter->bind($route, $binding);
            $routeData[$property] = $propertyExporter($property);
        }
        
        return (boolean) $this->callback($value, $routeData);
    }
    
}