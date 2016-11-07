<?php


namespace SeedStars;

use BadMethodCallException;

class Session
{

    protected static $instance = null;
    protected $started = false;

    private function __construct()
    {
    }

    /**
     * @return null|static
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function start()
    {
        if ($this->started) {
            return;
        }

        session_start();
        $this->started = true;

        return $this;
    }

    /**
     * @param $key
     * @return bool|mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return false;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function put($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function regenerate()
    {
        return session_regenerate_id();
    }

    public function destroy()
    {
        return session_destroy();
    }

    private function __clone()
    {
        throw new BadMethodCallException(
            "This class cannot be cloned "
        );
    }
}
