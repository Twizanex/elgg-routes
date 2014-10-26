<?php

/**
 * Base controller for routing
 *
 * Provides routing functions
 *
 * @package Aclook
 * @author Gabriel Vargas <angel.wrt@gmail.com>
 */
class Controller
{
    /**
    * @var array $segments Saves the URL segments of the call
    */ 
    private $segments;
    
    /**
    * @var bool $autoLoadEntity Auto load entities on $segment[1]?
    */ 
    protected $autoLoadEntity = true;
    
    /**
    * @var ElggEntity $entity Entity of the request
    */ 
    protected $entity = null;
    
    /**
    * Route not found
    */
    function __call($func, $args) {
        return false;
    }
    
    /**
    * Build the controller
    * 
    * @param  array $pages Array of pages present in the URL
    */
    function __construct($pages) {
        $this->segments = $pages;
        
        if($this->autoLoadEntity && !empty($pages[1])) {
        
            if(is_numeric($pages[1])) {
                $this->entity = get_entity($pages[1]);
            }
            else {
                $this->entity = get_user_by_username($pages[1]);
            }
            
        }
    }
    
    /**
    * Get a specific URL segment
    *
    * @param  int $number Position of the segment
    */
    protected function getSegment($number) {
        return $this->segments[$number];
    }
    
    /**
    * Execute the function that will handle the request
    */ 
    public function run() {
        $func = $this->getSegment(0);
        
        if($func){
            return $this->$func();
        }
        else {
            return $this->index();
        }
        
        
    }
    
    /**
    * Register a page to be handled
    * @param  string $page The name of the page to be handled
    */
    static function add($page) {
        elgg_register_page_handler($page, 'route_handler');
    }
    
    /**
    * Find the controller to handle the request
    *
    * @param  array $page Array with the URL segments
    * @param  string $handler The page requested
    */
    static function route($page, $handler) {
        
        $controller = ucfirst($handler)."Controller";
        $controller = new $controller($page);
        
        return $controller->run();
    }
    
}