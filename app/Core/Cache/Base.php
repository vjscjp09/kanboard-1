<?php

namespace Core\Cache;

/**
 * Base class for cache drivers
 *
 * @package  cache
 * @author   Frederic Guillot
 */
abstract class Base
{
    /**
     * Proxy cache
     *
     * Note: Arguments must be scalar types
     *
     * @access public
     * @param  string    $class        Class instance
     * @param  string    $method       Container method
     * @return mixed
     */
    public function proxy($class, $method)
    {
        $args = func_get_args();
        array_shift($args);

        $key = 'proxy:'.get_class($class).':'.implode(':', $args);
        $result = $this->get($key);

        if ($result === null) {
            $result = call_user_func_array(array($class, $method), array_splice($args, 1));
            $this->set($key, $result);
        }

        return $result;
    }
}
