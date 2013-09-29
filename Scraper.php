<?php

require_once('HtmlExtractor.php');

class Scraper
{
	const URL_TEMPLATE = 'http://www.schweinfurt.de/buergerinformationen/index.html?art_pager=%d';
	const PAGES = 3;

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

	public function getPage($number)
	{
		$source = file_get_contents($this->getURL($number));
		$extractor = new HtmlExtractor($source);

		return $extractor->extractNewsEntries();
	}

	private function getURL($pageNumber)
	{
		// page number in URL is zero based!
		return sprintf(self::URL_TEMPLATE, $pageNumber -1);
	}
}