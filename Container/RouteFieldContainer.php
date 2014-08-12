<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing\Container;

use Naquadria\Components\Routing\RouteField;
use Naquadria\Components\Routing\Exceptions\ContainerException;

/**
 * RouteFieldContainer class
 *
 * @package naquadria.components.routing.container
 */
final class RouteFieldContainer {
    
    /**
     * route fields
     *
     * @var array
     */
    private $fields = [];
    
    /**
     * add()
     *
     * adds a callable associated with the given property to this container
     *
     * @param string $property
     * @param callable $callback
     */
    public function add($property, callable $callback)
    {
        $field = new RouteField();
        $field->setCallback($callback);
        $this->fields[$property] = $field;
    }
    
    /**
     * has()
     *
     * returns if an given property is registered
     *
     * @param string $property
     * @return bool
     */
    public function has($property)
    {
        return array_key_exists($property, $this->fields);
    }
    
    /**
     * get()
     *
     * getter for a property callback
     *
     * @param string $property
     * @throws \Naquadria\Components\Routing\Exceptions\ContainerException
     * @return callable
     */
    public function get($property)
    {
        if ( ! $this->has($property) ) {
            throw new ContainerException(
                'Unknown property: '.$property
            );
        }
        
        return $this->fields[$property];
    }
    
    /**
     * import()
     *
     * imports a container to this container, existing value will be overwritten
     *
     * @param \Naquadria\Components\Routing\Container\RouteFieldContainer $container
     */
    public function import(RouteFieldContainer $container)
    {
        foreach ( $container->fields as $field => $routeField ) {
            $this->fields[$field] = $routeField;
        }
    }
    
    /**
     * getFieldNames()
     *
     * returns an aray of field names
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->fields);
    }
    
}