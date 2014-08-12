<?php
/**
 * Naquadria PHP Framework
 * (c)2014 Matthias Kaschubowski
 *
 * This file is part of the Naquadria PHP Framework.
 * License: See LICENSE.TXT at package root.
 */

namespace Naquadria\Components\Routing;

interface RouterInterface extends RoutingInterface {
    const VAR_REGEX = '~\{\s*([a-zA-Z][a-zA-Z0-9_]*)\s*(?::\s*([^{}]*(?:\{(?-1)\}[^{}*])*))?\}~x';
    const DEFAULT_REGEX = '[^/]+';
    
    public function query($pathInfo, callable $fieldAnnounceCallback = null);
    public function dispatch($pathInfo, callable $fieldAnnounceCallback = null);
    public function buildPath($routeName, array $vars);
    public function addTag($tagName, $partialRegex);
}