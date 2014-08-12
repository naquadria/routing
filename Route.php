<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing;

use Naquadria\Components\IoC\ContainerAwareInterface;

/**
 * Route Class
 *
 * @package naquadria.components.routing
 */
class Route {
    
    /**
     * name of this route
     * @var string
     */
    private $name;
    
    /**
     * abstraction definition
     * @var mixed
     */
    private $abstraction;
    
    /**
     * before- and after-queues
     * @var array
     */
    private $queue = ['before' => [], 'after' => []];
    
    /**
     * constructor
     *
     * Assigns automaticly a uniqid-based request aware name for this route
     */
    public function __construct()
    {
        $this->name = uniqid('route_');
    }
    
    /**
     * name()
     *
     * sets $name as the current name of this route
     *
     * @param string $name
     * @return $this
     */
    final public function name($name)
    {
        $this->name = (string)$name;
        
        return $this;
    }
    
    /**
     * abstraction()
     *
     * sets $abstraction as the current abstraction of this route
     *
     * @param mixed $abstraction
     * @return $this
     */
    final public function abstraction($abstraction)
    {
        $this->abstraction = $abstraction;
        
        return $this;
    }
    
    /**
     * before()
     *
     * appends an abstraction to the before queue with a given numeric priority
     *
     * @param mixed $abstraction
     * @param int $priority
     * @return $this
     */
    final public function before($abstraction, $priority = 0)
    {
        $this->queue['before'][] = [
            $abstraction,
            (int)$priority,
        ];
        
        return $this;
    }
    
    /**
     * after()
     *
     * appends an abstraction to the after queue with a give numeric priority
     *
     * @param mixed $abstraction
     * @param int $priority
     * @return $this
     */
    final public function after($abstraction, $priority = 0)
    {
        $this->queue['after'][] = [
            $abstraction,
            (int)$priority,
        ];
        
        return $this;
    }
    
    /**
     * getName()
     *
     * name getter
     *
     * @return string
     */
    final public function getName()
    {
        return $this->name;
    }
    
    /**
     * getAbstraction()
     *
     * abstraction getter
     *
     * @return mixed
     */
    final public function getAbstraction()
    {
        return $this->abstraction;
    }
    
    /**
     * getBeforeAbstractions()
     *
     * abstraction array getter for the before queue
     *
     * @return array
     */
    final public function getBeforeAbstractions()
    {
        return $this->queue['before'];
    }
    
    /**
     * getAfterAbstractions()
     *
     * abstraction array getter for the after queue
     *
     * @return array
     */
    final public function getAfterAbstractions()
    {
        return $this->queue['after'];
    }
    
}