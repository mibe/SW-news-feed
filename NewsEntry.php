<?php

/**
* Represents a single news item scraped from the site.
*/
class NewsEntry
{
	/**
	* ID of this entry.
	*/
	public $id;

	/**
	* Title of this entry.
	*/
	public $title;

	/**
	* Content of this entry.
	*/
	public $message;

	/**
	* URL of this entry.
	*/
	public $url;

	/**
	* Constructor
	*
	* @param   int     ID
	* @param   string  Title
	* @param   string  Content
	* @param   string  URL
	*/
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