<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing;

use Naquadria\Components\Routing\Container\RouteContainer;
use Naquadria\Components\Routing\Container\RouteFieldContainer;
use Naquadria\Components\Routing\Container\Filter\QueryFilter;
use Naquadria\Components\Routing\Container\Filter\PathInfoFilter;
use Naquadria\Components\Routing\Container\Filter\NameFilter;
use Naquadria\Components\Routing\Exceptions\RouteNotFoundException;
use Naquadria\Components\Routing\Exceptions\InvalidRouteTagException;
use Naquadria\Components\Routing\Exceptions\UnknownRouteTagException;
use Naquadria\Components\Routing\Exceptions\RouteAggregationException;
use Naquadria\Components\Routing\Exceptions\ContainerAggregationException;
use Naquadria\Components\IoC\ContainerAwareInterface;

/**
 * Router Class
 *
 * @package naquadria.components.routing
 */
class Router implements RouterInterface {
    
    /**
     * var of the route class, overwriteable within inheritance.
     */
    protected $routeClass = Route::class;
    
    /**
     * var of the route entity class, overwriteable within inheritance.
     */
    protected $routeEntityClass = RouteEntity::class;
    
    /**
     * var of the route tag validation regular expression, overwriteable
     * within inheritance.
     */
    protected $routeTagValidationRegex = "~^[a-z0-9\-]+$~i";
    
    /**
     * route container
     *
     * @var \Naquadria\Components\Router\Container\RouteContainer
     */
    private $routes;
    
    /**
     * route field container
     *
     * @var \Naquadria\Components\Router\Container\RouteFieldContainer
     */
    private $routeFields;
    
    /**
     * route tags array
     *
     * @var array
     */
    private $routeTags = [
        'pattern' => [], 
        'replacement' => []
    ];
    
    /**
     * constructor
     *
     * @param \Naquadria\Components\Router\Container\RouteFieldContainer $routeFields
     */
    public function __construct(RouteFieldContainer $routeFields = null)
    {
        $this->routes = new RouteContainer();
        $this->routeFields = new RouteFieldContainer();
        
        if ( $routeFields instanceof RouteFieldContainer ) {
            $this->routeFields->import($routeFields);
        }
        
        foreach ( $this->getRoutePropertyStages() as $field => $binding ) {
            if ( ! $this->routeFields->has($field) ) {
                continue;
            }
            
            $this->routeFields->get($field)->setRouteProperty($field, $binding);
        }
    }
    
    /**
     * register()
     *
     * adds a route for a given pattern to the route container and attaches the abstraction
     * to the route.
     *
     * @param string $pattern
     * @param string $abstraction
     * @return null|\Naquadria\Components\Router\Route
     */
    final public function register($pattern, $abstraction = null)
    {
        if ( $abstraction instanceof AdoptableRoutingInterface ) {
            foreach ( $abstraction->getRoutes() as list($route, $abstractPattern) ) {
                $implementedPattern = $pattern.'/'.ltrim($abstractPattern, '/');
                $this->routes->add($route, $this->buildRouteData($implementedPattern));
            }
            
            return null;
        }
        
        $routeClass = $this->routeClass;
        $this->routes->add($route = new $routeClass, $this->buildRouteData($pattern));
        
        if ( null !== $abstraction ) {
            $route->abstraction($abstraction);    
        }
        
        return $route;
    }
    
    /**
     * addTag()
     *
     * adds a router tag with a given name and a given partial regular expression
     * to this router. Router tags are used to replace a part of a route pattern with
     * a sub-sequence of pattern.
     *
     * @param string $tagName
     * @param string $partialRegex
     * @throws \Naquadria\Components\Routing\Exceptions\InvalidRouteTagException
     */
    final public function addTag($tagName, $partialRegex)
    {
        if ( 0 === preg_match($this->routeTagValidationRegex, $tagName) ) {
            throw new InvalidRouteTagException('Invalid route tag name');
        }
        
        $this->routeTags['pattern'][$tagName] = '~'.preg_quote('[!:'.$tagName.':!]').'~i';
        $this->routeTags['replacement'][$tagName] = '(?:'.$partialRegex.')';
    }
    
    /**
     * query()
     *
     * creates a filter iterator by a given path info value and field data and returns it.
     *
     * @params string $pathInfo
     * @params array $fields
     * @return \Naquadria\Components\Routing\Container\Filter\QueryFilter
     */
    final public function query($pathInfo, callable $fieldAnnounceCallback = null)
    {
        $filter = new QueryFilter(new PathInfoFilter($this->routes), $this->routeFields);
        $filter->setPathInfo($pathInfo);
        
        $fields = [];
        if ( is_callable($fieldAnnounceCallback) ) {
            $fields = call_user_func($fieldAnnounceCallback, $this->routeFields->getFieldNames());
        }
        
        foreach ( $fields as $name => $value ) {
            $filter->announce($name, $value);
        }
        
        return $filter;
    }
    
    /**
     * dispatch()
     *
     * searchs the route for a given path info value and field data and returns a route entity
     * with the result data.
     *
     * @param string $pathInfo
     * @param array $fields
     * @return \Naquadria\Components\Routing\RouteEntity
     */
    final public function dispatch($pathInfo, callable $fieldAnnounceCallback = null)
    {
        $queryResult = iterator_to_array($this->query($pathInfo, $fieldAnnounceCallback));
        
        if ( 0 === count($queryResult) ) {
            return false;
        }
        
        $entityClass = $this->routeEntityClass;
        $data = $this->routes[current($queryResult)]['data'];
        preg_match('~^(?:'.$data['regex'].')$~', $pathInfo, $matches);
        
        $assocData = array_combine(array_keys($data['vars']), array_slice($matches, 1));
        $routeEntity = new $entityClass(
            current($queryResult),
            $assocData,
            $data['replacer']
        );

        return $routeEntity;
    }
    
    /**
     * buildPath()
     *
     * searchs for a given route name fills the pattern along the detected replacer and
     * returns the path string of this route.
     *
     * @param string $routeName
     * @param array $vars
     * @throws \Naquadria\Components\Routing\Exceptions\RouteNotFoundException
     */
    final public function buildPath($routeName, array $vars = [])
    {
        $filter = new NameFilter($this->routes, $routeName);
        $result = iterator_to_array($filter);
        
        if ( 0 === count($result) ) {
            throw new RouteNotFoundException('No route found with name: '.$routeName);
        }
        
        $replacer = $this->routes->getData(current($result))['replacer'];
        $pattern = $this->routes->getData(current($result))['pattern'];
        
        if ( 0 < count(array_diff(array_keys($vars), array_keys($replacer))) ) {
            throw new RouteAggregationException('Invalid or less vars defined');
        }
        
        ksort($replacer);
        ksort($vars);
        
        return strtr($pattern, array_combine($replacer, $vars));
    }
    
    /**
     * buildRouteData() - internal route data builder
     *
     * @throws \Naquadria\Components\Routing\Exceptions\UnknownRouteTagException
     */
    final private function buildRouteData($pattern)
    {
        // patch tags -- experemential
        if ( count(array_filter($this->routeTags)) ) {
            $pattern = preg_replace($this->routeTags['pattern'], $this->routeTags['replacement'], $pattern);
        }
        
        // find unknown tags
        preg_match_all('~'.preg_quote('[!:').'([a-z\_\-]+)'.preg_quote(':!]').'~i', $pattern, $matches);

        $exception = null;
        foreach ( $matches[1] as $tag ) {
            $exception = new UnknownRouteTagException('Unknown tag: '.$tag);
        }

        if ( $exception ) {
            throw $exception;
        }
        
        // fetch pattern data
        $data = [];
        $replacer = [];
        $offset = 0;
        if ( preg_match_all(static::VAR_REGEX, $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) ) {
            foreach ( $matches as $set ) {
                if ( $set[0][1] > $offset ) {
                    $data[] = substr($pattern, $offset, $set[0][1] - $offset);
                    $replacer[] = $set[0][0];
                }
                
                $data[] = array(
                    $set[1][0],
                    isset($set[2])
                        ? trim($set[2][0])
                        : static::DEFAULT_REGEX
                );

                $offset = $set[0][1] + strlen($set[0][0]);
            }
        }
        
        // build match credentials
        if ( empty($data) ) {
            $storage['regex'] = $pattern;
            $storage['vars'] = [];
        }
        else {
            $storage['regex'] = '';
            $storage['vars'] = [];

            foreach ( $data as $current ) {
                if ( is_string($current) ) {
                    $storage['regex'] .= preg_quote($current, '~');
                    continue;
                }

                list($name, $currentRegex) = $current;
                
                if ( isset($storage['vars'][$name]) ) {
                    throw new RouteAggregationException(
                        'Impossible to use placeholder "'.$name.'" more then once'
                    );
                }

                $storage['vars'][$name] = null;
                $storage['regex'] .= '('.$currentRegex.')';
            }
        }
        
        $storage['replacer'] = array_combine(array_keys($storage['vars']), $replacer);
        $storage['pattern'] = $pattern;
        
        return $storage;
    }
    
    /**
     * getRoutePropertyStages() - internal route property extraction method
     *
     * @throws \Naquadria\Components\Routing\Exceptions\ContainerAggregationException
     */
    final private function getRoutePropertyStages()
    {
        try {
            $reflection = new \ReflectionClass($this->routeClass);
            if ( $reflection->getName() !== Route::class && ! $reflection->isSubclassOf(Route::class) ) {
                throw new ContainerAggregationException(
                    'Incompatible class stored in ROUTE_CLASS constant of this router, '.
                    'the given class name must point to class that is a sub-class of '.
                    Route::class
                );
            }
        }
        catch ( \ReflectionException $exception ) {
            throw new ContainerAggregationException(
                'Unknown class stored in ROUTE_CLASS constant of this router',
                500,
                $exception
            );
        }
        
        $properties = [];
        foreach ( $reflection->getProperties() as $property ) {
            if ( ! $property->isPrivate() ) {
                continue;
            }
            
            $properties[$property->getName()] = $property->getDeclaringClass()->getName();
        }
        
        return $properties;
    }
    
}