<?php
namespace App\Route\Text;

use \DateTime;
use \StdClass;
use Eight\Route;
use Eight\Privacy\Control;

class NewText extends Route
{
    const URL = '/text';
    
    public function get()
    {
        return array('view'=>'Text/Form.twig');
    }
    
    public function post()
    {
        if (count($_POST) <= 0)
            return array('view'=>'Text/Form.twig');

        $now            = new DateTime();
		$text           = new StdClass();
		$privacy        = new Control($this->app->getOwner(), $text);
        $text->id       = null;
        $text->owner_id = $this->app->getOwner()->id;
        $text->title    = $_POST['title'];
        $text->content  = $_POST['content'];
        $text->created  = $now->format(DateTime::ISO8601);

		$privacy->encode();
        $this->app->db()->persist($text, 'text');
        $this->app->db()->flush();
        header('Location: /owner/texts');
    }
}