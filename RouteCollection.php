<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing;

use Naquadria\Components\Routing\AdoptableRoutingInterface;

class RouteCollection implements AdoptableRoutingInterface {
    
    /**
     * constant of the route class, overwriteable within inheritance.
     */
    const ROUTE_CLASS = Route::class;
    
    private $routerData = [];
    
    public function register($pattern, $abstraction = null)
    {
        if ( $abstraction instanceof AdoptableRoutingInterface ) {
            foreach ( $abstraction->getRoutes() as list($route, $abstractPattern) ) {
                $implementedPattern = $pattern.'/'.ltrim($abstractPattern, '/');
                $this->routerData[] = [
                    $route,
                    $abstractPattern,
                ];
            }
            
            return null;
        }
        
        $routeClass = static::ROUTE_CLASS;
        
        $this->routerData[] = [
            $route = new $routeClass(),
            $pattern,
        ];
        
        if ( null !== $abstraction ) {
            $route->abstraction($abstraction);
        }
        
        return $route;
    }
    
    public function getRoutes()
    {
        return $this->routerData;
    }
    
}