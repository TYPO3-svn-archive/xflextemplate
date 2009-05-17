<?php
    abstract class xftWizard{
    	abstract public function render($compactField);
		
		abstract public function getCompactField($postArray);
		abstract public function renderPageElement($content,$conf);
    }
?>