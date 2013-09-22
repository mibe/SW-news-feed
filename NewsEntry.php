<?php

class NewsEntry
{
	public $id;
	public $title;
	public $message;
	public $url;

	public function __construct($id, $title, $message, $url)
	{
		$this->id = $id;
		$this->title = $title;
		$this->message = $message;
		$this->url = $url;
	}

	public function __toString()
	{
		return sprintf('%d: %s', $this->id, $this->title);
	}
}