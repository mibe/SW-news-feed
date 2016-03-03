<?php

/**
* Represents a single news item scraped from the site.
*/
class NewsEntry
{
	/**
	* Template for building the correct URL, depending on the supplied ID.
	*/
	const URL_TEMPLATE = 'https://www.schweinfurt.de/rathaus-politik/pressestelle/buergerinformationen/%d.html';
	
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
	* Constructor
	*
	* @param   int     ID
	* @param   string  Title
	* @param   string  Content
	*/
	public function __construct($id, $title, $message)
	{
		$this->id = $id;
		$this->title = $title;
		$this->message = $message;
	}

	/**
	* Build the news detail URL for the given news ID.
	*
	* @return   string  URL to the news detail page.
	*/
	public function buildURL()
	{
		return sprintf(self::URL_TEMPLATE, $this->id);
	}

	public function __toString()
	{
		return sprintf('%d: %s', $this->id, $this->title);
	}
}