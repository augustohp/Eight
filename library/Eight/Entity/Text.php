<?php
namespace Eight\Entity;

use Eight\Privacy\Control;

class Text
{
    public $id;
    public $owner_id;
    public $title;
    public $content;
    public $version;
    public $created;
    
    public function __construct($owner=null)
    {
        if (is_scalar($owner))
            return $this->owner_id = $owner;
        
        if (is_object($owner) && isset($owner->id) && !empty($owner->id))
            return $this->owner_id = $owner->id;
    }
}