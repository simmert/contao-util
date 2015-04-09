<?php

namespace Util;

/**
* Adds namespace support to the classic Contao session
*/
class NamespacedSession
{
    protected $session = null,
              $namespace = null;


    public function __construct($namespace)
    {
        $this->session = \Contao\Session::getInstance();

        $this->setNamespace($namespace);
    }


    public function get($key)
    {
        return $this->session->get($this->getGlobaKey($key));
    }


    public function set($key, $value)
    {
        return $this->session->set($this->getGlobaKey($key), $value);
    }


    public function remove($key)
    {
        return $this->session->remove($this->getGlobaKey($key));
    }


    public function reset()
    {
        if ($this->namespace === null) {
            return $this->session->setData(array());
        }

        $data = $this->session->getData();

        foreach ($data as $key => &$value) {
            if (\Util\StringHelper::startsWith($key, $this->namespace . '_')) {
                $this->session->remove($key);
            }
        }
    }


    public function getData()
    {
        $data = $this->session->getData();

        if ($this->namespace === null) {
            return $data;
        }

        foreach ($data as $key => &$value) {
            if (!\Util\StringHelper::startsWith($key, $this->namespace . '_')) {
                unset($data[$key]);
            }
        }

        return $data;
    }


    public function setData(array $data)
    {
        if ($this->namespace === null) {
            return $this->session->setData($data);
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }


    protected function getGlobaKey($key)
    {
        if ($this->namespace !== null) {
            return $this->namespace . '_' . $key;
        }

        return $key;
    }


    public function setObjectNamespace($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Namespace must be an object.');
        }

        $this->setNamespace(get_class($object));
    }


    public function setNamespace($namespace)
    {
        if (is_object($namespace)) {
            $this->setObjectNamespace($namespace);
            return;
        }

        if (!is_string($namespace)) {
            throw new \InvalidArgumentException('Namespace must be of type string.');
        }

        $this->namespace = strtolower(str_replace('\\', '_', trim($namespace)));
    }


    public function getNamespace()
    {
        return $this->namespace;
    }
}