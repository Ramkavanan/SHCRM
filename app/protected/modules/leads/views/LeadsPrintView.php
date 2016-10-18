<?php
  
    /**
     * View for showing in the user interface when the opportunity add costbooks to opportunity products.
     */
    class LeadsPrintView extends View    {
        protected $data;
	protected $leadId;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->leadId = $id;
	}
        
	public function renderContent()     {
	  $content = LeadsUtil::makePrintView($this->data,$this->leadId);
	//  $content .= $this->renderScripts();
            return $content;
        }
    }
?>
