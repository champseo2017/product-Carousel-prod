<?php
if (!defined('ABSPATH')) {
    exit;
}

class ClassInspector {
    protected $className;

    public function __construct($className) {
        $this->className = $className;
    }

    public function printMethods() {
        $reflection = new ReflectionClass($this->className);
        $methods = $reflection->getMethods();
        print_r($methods);
    }

    public function printProperties() {
        $reflection = new ReflectionClass($this->className);
        $properties = $reflection->getProperties();
        print_r($properties);
    }
}