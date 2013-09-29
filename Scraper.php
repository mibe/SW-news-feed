<?php

require_once('HtmlExtractor.php');

/**
* Scrapes the news entries from the Web.
*/
class Scraper
{
	/**
	* URL which contains the news. Contains printf format string for page number.
	*/
	const URL_TEMPLATE = 'http://www.schweinfurt.de/buergerinformationen/index.html?art_pager=%d';

	/**
	* Number of pages to be scraped.
	*/
	const PAGES = 3;

	/**
	* Scrapes all pages and returns the news items.
	*
	* @return   array  Array of NewsEntry instances containing the news
	*/
	public function getAllPages()
	{
		$entries = array();

		for($a = self::PAGES; $a > 0; $a--)
		{
			$page = $this->getPage($a);
			$entries = array_merge($page, $entries);
		}

		return $entries;
	}

	/**
	* Scrapes a single page and returns the news items of that page.
	*
	* @params   int    Number of page, one-based
	* @return   array  Array of NewsEntry instances containing the news
	*/
	public function getPage($number)
	{
		$source = file_get_contents($this->getURL($number));
		$extractor = new HtmlExtractor($source);

		return $extractor->extractNewsEntries();
	}

	/**
	* Generate the URL from the page number.
	*
	* @params   int  Page number, one-based
	*/
	private function getURL($pageNumber)
	{
		// page number in URL is zero based!
		return sprintf(self::URL_TEMPLATE, $pageNumber -1);
	}
}