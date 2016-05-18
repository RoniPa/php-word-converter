<?php

class Converter {
    const CONTENT_XML_NAME = 'word/document.xml';
    
    private $xmlData;
    private $data_addr;
    
    public function __construct($data_addr = "") {
        $this->data_addr = $data_addr;
        $this->xmlData = null;
    }
    
    public function __destruct() {}
    
    private function _getUrl($id) { return ""; }
    
    private function _iterateElems($d, $parent = null) {
        $last_row_parent = 0;
        $o = "";
        
        for($d->rewind(); $d->valid(); $d->next()) {
            $name = $d->key();
            $addBreak = false;
            $isContent = false;
            $tag = false;
            
            switch( $name ) {
                case "p":
                    $tag = 'p';
                    break;
                case "r":
                    if ($last_row_parent === $parent) {
                        var_dump($name_before);
                        $addBreak = true;
                    }
                    
                    $last_row_parent = $d;
                    break;
                case "hyperlink":
                    $tag = 'a';
                    $params = 'href="'. $this->_getUrl($id) .'"';
                    break;
                case "t":
                    $isContent = true;
                    break;
                default: $tag = null;
            }
            
            if ($tag) $o .= "<$tag $params>";
            
            if ($d->hasChildren())
                $o .= $this->_iterateElems($d->getChildren(), $d);
            
            if ($isContent) $o .= $d->t->__toString();
            if ($tag) $o .= "</$tag>";
            if ($addBreak) $o .= '<br/>';
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
        
        /*
        foreach ($textElems->children() as $pElem) {
            $styleElem = $pElem->pPr->pStyle;
            $contentElem = $pElem->r->t;
            $linkElem = $pElem->r->
            $style = "";
            $text =  "";
            
            if ($styleElem !== null) $style = $styleElem->attributes()->val->__toString();
            if ($contentElem !== null) $text = $contentElem->__toString();
            
            if (strlen($text) > 0) {
                switch($style) {
                    case "Heading1": $tag = 'h1'; break;
                    case "Heading2": $tag = 'h2'; break;
                    case "Heading3": $tag = 'h3'; break;
                    case "Heading4": $tag = 'h4'; break;
                    default: $tag = 'p';
                }
                
                $output .= "<$tag>$text</$tag>";
            }
        }
        */
        return $output;
    }
    
    public function readData() {
        if (strlen($this->data_addr) < 1) return -1;
        
        $zip = new ZipArchive();

        if ($zip->open($this->data_addr) === true) {
            $content = $zip->getFromName(self::CONTENT_XML_NAME);
            
            // Clean XML so that simplexml can convert it
            $content = preg_replace('$(</?)(w|r)(:)$', '$1', $content);
            
            $this->xmlData = simplexml_load_string($content, "SimpleXMLIterator");
            $zip->close();
            return 1;
            
        } else throw new Exception('Can not open zip file.');
    }
    
    public function getHtml() {
        $output = $this->_contentToHtml();
        include('base.php');
    }
    
    public function getData() {
        return $this->xmlData;
    }
}