<?php
namespace App\Route\Text;

use Eight\Route;
use Eight\Privacy\Control;

class Single extends Route
{
    const URL = '/text/*';
    
    public function get($id)
    {
		$text = $this->getText($id);
        if (!$text)
            header('Location: /owner/texts');
        
		$privacy = new Control($this->app->getOwner(), $text);
		$privacy->decode();
		
        $data                 = array();
        $data['view']         = 'Text/Form.twig';
        $data['text_title']   = $text->title;
        $data['text_content'] = $text->content;
        $data['text_id']      = $text->id;
        return $data;
    }
    
    public function post($id)
    {
        $text = $this->getText($id);
        if (!$text)
            header('Location: /owner/texts');
        
		$privacy = new Control($this->app->getOwner(), $text);
		$privacy->encode();

        $text->content = $_POST['content'];
        $text->title   = $_POST['title'];
        $this->app->db()->persist($text, 'text');
        $this->app->db()->flush();
        header('Location: /owner/texts');
    }

	protected function getText($id)
	{
		$ownerId = $this->app->getOwner()->id;
        $text    = $this->app->db()->text[$id]->fetch();

		if ($text->owner_id == $ownerId)
			return $text;

		return null;
	}
}