<?php

require_once('NewsEntry.php');

class HtmlExtractor
{
	private $xpath;
	private $query = '/html/body/div/div[3]/div/div[3]/*[@class="artikeluebersicht"]';
	private $dom;
	private $baseUrl = 'http://www.schweinfurt.de';
	private $newsIdRegex = '/\/(\d+)\./';		//	"/3141."

	public function __construct($source)
	{
		$this->dom = new DOMDocument();
		@$this->dom->loadHTML($source);

		$this->xpath = new DOMXPath($this->dom);
	}

	public function extractNewsEntries()
	{
		$nodes = $this->xpath->query($this->query);
		$entries = array();

		foreach($nodes as $node)
			$entries[] = $this->getNewsEntry($node);

		return $entries;
	}

	private function getNewsEntry($node)
	{
		$a = $node->firstChild->firstChild->firstChild;

		$newsUrl = $this->baseUrl. $a->attributes->getNamedItem('href')->nodeValue;
		$newsTitle = $a->attributes->getNamedItem('title')->nodeValue;
		preg_match($this->newsIdRegex, $newsUrl, $matches);
		$newsId = (int)$matches[1];

		$p = $node->firstChild->firstChild->nextSibling;
		
		$newsMessage = $p->nodeValue;

		return new NewsEntry($newsId, $newsTitle, $newsMessage, $newsUrl);
	}
}