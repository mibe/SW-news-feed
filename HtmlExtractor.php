<?php

require_once('NewsEntry.php');

/**
* Extracts the news item content from the HTML. This content is then filled in an
* NewsEntry instance.
*/
class HtmlExtractor
{
	private $xpath;
	private $query = '/html/body/div/div[3]/div/div[3]/*[@class="artikeluebersicht"]';
	private $dom;
	private $baseUrl = 'http://www.schweinfurt.de';
	private $newsIdRegex = '/\/(\d+)\./';		//	"/3141."

	/**
	* Constructor
	*
	* @param   string  The HTML source
	*/
	public function __construct($source)
	{
		$this->dom = new DOMDocument();
		@$this->dom->loadHTML($source);

		$this->xpath = new DOMXPath($this->dom);
	}

	/**
	* Extracts all news items from the source supplied.
	*
	* @return   array  Extracted news items as NewsEntry array
	*/
	public function extractNewsEntries()
	{
		$nodes = $this->xpath->query($this->query);
		$entries = array();

		foreach($nodes as $node)
			$entries[] = $this->getNewsEntry($node);

		return $entries;
	}

	/**
	* Extracts the content of a single news item
	*
	* @param    DOMNode    HTML node with the containing news
	* @return   NewsEntry  Instance containing the news
	*/
	private function getNewsEntry($node)
	{
		$a = $node->firstChild->firstChild->firstChild;

		// get URL and title from the HTML elements
		$newsUrl = $this->baseUrl. $a->attributes->getNamedItem('href')->nodeValue;
		$newsTitle = $a->attributes->getNamedItem('title')->nodeValue;

		// the ID is extracted from the URL
		preg_match($this->newsIdRegex, $newsUrl, $matches);
		$newsId = (int)$matches[1];

		$p = $node->firstChild->firstChild->nextSibling;
		$newsMessage = $p->nodeValue;

		return new NewsEntry($newsId, $newsTitle, $newsMessage, $newsUrl);
	}
}