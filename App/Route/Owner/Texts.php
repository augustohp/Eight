<?php
namespace App\Route\Owner;

use Eight\Route;
use Eight\Privacy\Control;

class Texts extends Route
{
    const URL = '/owner/texts';
    
    public function get()
    {
		$ownerId = $this->app->getOwner()->id;
        $all     = $this->app->db()->text->owner[$ownerId]->fetchAll();
		$privacy = new Control($this->app->getOwner());
		foreach ($all as $idx=>$text)
			$all[$idx] = $privacy->decode($text);
		
        return array('texts'=>$all, 'view'=>'Text/List.twig');
    }
}