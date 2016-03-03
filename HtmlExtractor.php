<?php

require_once('NewsEntry.php');

/**
* Extracts the news item content from the HTML. This content is then filled in an
* NewsEntry instance.
*/
class HtmlExtractor
{
	private $xpath;
	private $query = '/html/body/div/div[3]/div/div[3]/div/*[@class="artikel-uebersicht"]';
	private $queryDetail = '/html/body/div/div[3]/div/div[3]/div/div[2]/div[3]/*';
	private $dom;
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
	* Extracts the news detail message from the source supplied.
	*
	* @return   string  Extracted news text
	*/
	public function extractNewsDetail()
	{
		$nodes = $this->xpath->query($this->queryDetail);
		
		if ($nodes->length == 0)
			return null;
		
		$result = '';
		foreach($nodes as $node)
			$result .= $node->nodeValue . "\r\n\r\n";
		
		return $result;
	}

	/**
	* Extracts the content of a single news item
	*
	* @param    DOMNode    HTML node with the containing news
	* @return   NewsEntry  Instance containing the news
	*/
	private function getNewsEntry($node)
	{
		$a = $node->firstChild->firstChild->nextSibling->firstChild;

		// get title from the HTML elements
		$newsTitle = $a->attributes->getNamedItem('title')->nodeValue;

		// the ID is extracted from the URL
		preg_match($this->newsIdRegex, $a->attributes->getNamedItem('href')->nodeValue, $matches);
		$newsId = (int)$matches[1];

		// the news message is somewhat trickier, because it is nested in multiple paragraph elements
		$newsMessage = '';
		$p = $node->firstChild->firstChild->nextSibling->nextSibling->firstChild;

		if ($p != null && $p->nodeName == 'p')
		{
			do
			{
				// The date (dd.mm.yyyy) is /usually/ in the first paragraph. We don't want that,
				// so only fill $newsMessage when the paragraph does not contain a date.
				if (preg_match('/\d{2}\.\d{2}\.\d{4}/', $p->nodeValue) == 0)
				{
					// Add a new paragraph as soon as there was suitable text in the last paragraph.
					if (strlen($newsMessage) != 0)
						$newsMessage .= "\r\n\r\n";

					$newsMessage .= $p->nodeValue;
				}
			} while(($p = $p->nextSibling) != null && $p->nodeName == 'p');
		}

		$result = new NewsEntry($newsId, $newsTitle, $newsMessage);

		// If no message was in the intro page, crawl the detail news page and get the
		// message text from there.
		if (strlen($result->message) == 0)
		{
			$html = file_get_contents($result->buildURL());
			$miniMe = new HtmlExtractor($html);
			$result->message = $miniMe->extractNewsDetail();
		}
		
		return $result;
	}
}