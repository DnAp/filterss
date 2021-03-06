<?php

/**
 * Keyword-based filter for RSS/Atom feeds
 *
 * @copyright   Copyright (c) Thomas Muguet (http://thomasmuguet.info)
 * @license     GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 * @author      Thomas Muguet <t.muguet@thomasmuguet.info>
 * @link        https://github.com/tmuguet/filterss Project on Github
 * @version     0.1.0
 */
class Filterss
{

    /// XML Document
    private $_doc;

    /**
     * Deletes a node and its children
     * 
     * @param DOMNode $node Node to delete
     */
    private function _deleteNode(DOMNode $node)
    {
        $this->_deleteChildren($node);
        $parent = $node->parentNode;
        $parent->removeChild($node);
    }

    /**
     * Deletes all children of a node
     * 
     * @param DOMNode $node Node to delete children from
     */
    private function _deleteChildren(DOMNode $node)
    {
        while (isset($node->firstChild)) {
            $this->_deleteChildren($node->firstChild);
            $node->removeChild($node->firstChild);
        }
    }

    /**
     * Deletes all items where keywords are not found
     *
     * @param DOMNodeList $elements List of nodes to filter
     * @param callable $callback
     */
    private function _filter(DOMNodeList $elements, $callback)
    {
        $elementsToDelete = array();
        /** @var DOMElement $element */
        foreach ($elements as $element) {
            if (!$callback($element)) {
                $elementsToDelete[] = $element;
            }
        }

        foreach ($elementsToDelete as $element) {
            $this->_deleteNode($element);
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_doc = new DOMDocument();
    }
    
    /**
     * Loads a feed from an URL
     * 
     * @param string $feedUrl URL of the RSS/Atom feed
     * @return self instance
     * @chainable
     */
    public function loadFromUrl($feedUrl) {
        $content = file_get_contents($feedUrl);
        $this->_doc->loadXML($content);
        return $this;
    }
    
    /**
     * Loads a feed from its XML content
     * 
     * @param string $feedXml XML content of the RSS/Atom feed
     * @return self instance
     * @chainable
     */
    public function loadFromXml($feedXml) {
        $this->_doc->loadXML($feedXml);
        return $this;
    }

    /**
     * Filters-out the unwanted items
     * 
     * @param callable $function
     * @return self instance
     * @chainable
     */
    public function filter($function)
    {
        $this->_filter($this->_doc->getElementsByTagName('item'), $function);
        $this->_filter($this->_doc->getElementsByTagName('entry'), $function);
        
        return $this;
    }

    /**
     * Returns the XML document
     * @return string
     */
    public function out()
    {
        return $this->_doc->saveXML();
    }
}

