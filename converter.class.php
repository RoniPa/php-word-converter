<?php

class Converter {
    const CONTENT_XML_NAME = 'word/document.xml';
    const RELATIONSHIPS_XML_NAME = 'word/_rels/document.xml.rels';
    
    private $xmlData;
    private $linkData;
    private $data_addr;
    
    public function __construct($data_addr = "") {
        $this->data_addr = $data_addr;
        $this->xmlData = null;
        $this->linkData = null;
    }
    
    public function __destruct() {}
    
    private function _xmlConvert($uri, $elem) {
        $xmlIter = null;
        
        if (strlen($uri) < 1) return null;
        
        $zip = new ZipArchive();

        if ($zip->open($uri) === true) {
            $content = $zip->getFromName($elem);
            
            // Clean XML so that simplexml can convert it
            $content = preg_replace('$(</?|\s)(w|r)(:)$', '$1', $content);
            
            $xmlIter = simplexml_load_string($content, "SimpleXMLIterator");
            $zip->close();
            
        }
        
        return $xmlIter;
    }
    
    private function _getUrl($id) {
        $d = $this->linkData;
        
        if ($d === null) return "";
        
        for ($d->rewind(); $d->valid(); $d->next()) {
            $attr = $d->current()->attributes();
            $curId = $attr->Id->__toString();
            
            if (strcmp($id, $curId) === 0)
                return $attr->Target->__toString();
        }
        
        return "";
    }
    
    private function _iterateElems($d, $header = false) {
        $first_row = true;
        $o = "";
        
        for($d->rewind(); $d->valid(); $d->next()) {
            $name = $d->key();
            $attributes = $d->current()->attributes();
            $addBreak = false;
            $isContent = false;
            $tag = false;
            
            switch( $name ) {
                case "p":
                    $tag = 'p';
                    break;
                case "r":
                    if (!$first_row) {
                        $addBreak = true;
                    }
                    $first_row = false;
                    break;
                case "hyperlink":
                    $tag = 'a';
                    $params = 'target="_blank" href="'. $this->_getUrl($attributes->id->__toString()) .'"';
                    break;
                case "t":
                    if ($header) {
                        $tag = $header;
                        $header = false;
                    }
                    $isContent = true;
                    break;
                case "pPr":
                    $te = $d->current()->children()->pStyle;
                    $te->rewind();
                    $type =  $te->current()->attributes()->val->__toString();
                    
                    switch( $type ) {
                        case 'Heading1': $header = 'h1'; break;
                        case 'Heading2': $header = 'h2'; break;
                        case 'Heading3': $header = 'h3'; break;
                        case 'Heading4': $header = 'h4'; break;
                        default: $header = false;
                    }
                    
                    break;
                default: $tag = null;
            }
            
            if ($tag) $o .= "<$tag $params>";
            
            if ($d->hasChildren())
                $o .= $this->_iterateElems($d->getChildren(), $header);
            
            if ($isContent) $o .= $d->t->__toString();
            if ($addBreak) $o .= '<br/>';
            if ($tag) $o .= "</$tag>";
        }
        return $o;
    }
        
    private function _contentToHtml() {
        if ($this->xmlData === null) return null;
        
        $output = "";
        
        $d = $this->xmlData->body;
        $d->rewind();
        if ($d->hasChildren()) {
            $d = $d->getChildren();
            
            $output .= $this->_iterateElems($d);

        } else {
            echo "Error has occured.";
        }
        
        return $output;
    }
    
    public function readData() {
        $this->xmlData = $this->_xmlConvert($this->data_addr, self::CONTENT_XML_NAME);
        $this->linkData = $this->_xmlConvert($this->data_addr, self::RELATIONSHIPS_XML_NAME);
        
        if ($this->xmlData === null || $this->linkData === null) return -1;
        else return 1;
    }
    
    public function getHtml() {
        $output = $this->_contentToHtml();
        include('base.php');
    }
    
    public function getData() {
        return $this->xmlData;
    }
}