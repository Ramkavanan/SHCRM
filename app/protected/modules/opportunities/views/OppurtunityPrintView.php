<?php
  
    /**
     * View for showing the print view.
     */
    class OppurtunityPrintView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {
	  $content = OpportunityUtils::makePrintView($this->data,$this->Id);
	//  $content .= $this->renderScripts();
            return $content;
        }
    }
?>
