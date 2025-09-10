<?php

namespace nn;

if (!defined('ABSPATH')) die;

class NNPL_Loader {

    protected $filters;
    protected $actions;

    public function __construct() {
        $this->filters = array();
        $this->actions = array();
    }

    public function add_action(string $hook, string $component, string $callback, int $priority=10, int $accepted_args=1): void {
        $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    public function add_filter(string $hook, string $component, string $callback, int $priority=10, int $accepted_args=1): void {
        $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    protected function add(array $hooks, string $hook, string $component, string $callback, int $priority=10, int $accepted_args=1): void {
        $hooks[] = array(
            'hook'          =>  $hook,
            'component'     =>  $component,
            'callback'      =>  $callback,
            'priority'      =>  $priority,
            'accepted_args' =>  $accepted_args,
        );
    }

    public function run(): void {
        
        foreach ($this->filters as $filter) 
            add_filter($filter['hook'], array($filter['component'], $filter['callback']), $filter['priority'], $filter['accepted_args']);
        

        foreach ($this->actions as $action) 
            add_action($action['hook'], array($action['component'], $action['callback']), $action['priority'], $action['accepted_args']);
        
    }
}

