<?php 
class XmlParse {
    
    private $dom;
    
    public function __construct($xmlDoc){
     
        $this->dom=new DomDocument();
        $this->dom->loadXML($xmlDoc);
    }
    
    public function getValueByTagName($tagName)
    {
        $values = $this->dom->getElementsByTagName($tagName);
        $value  = $values->item(0)->nodeValue;
        return $value;
    }

}
?>