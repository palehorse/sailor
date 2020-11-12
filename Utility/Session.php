<?php
namespace Sailor\Utility;

class Session
{
    /**
     * Check the session value exist or not
     * 
     * @param string $key
     * @return boolean
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Return the session value by the given key
     * 
     * @return mixed|null
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }

    /**
     * Set the session value by the given key
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Delete the session value by the given key
     * 
     * @param string $key
     */
    public static function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy session
     */
    public static function destroy()
    {
        session_destroy();
    }
}