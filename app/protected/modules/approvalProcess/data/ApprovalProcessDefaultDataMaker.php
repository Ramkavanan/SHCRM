<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApprovalProcessDefaultDataMaker extends DefaultDataMaker {

    public function make() {
        $values = array(
            'Pending',
            'Approved',
            'Rejected',
            'Recalled',
            'Submitted',
            'ReAssigned',		
        );
        static::makeCustomFieldDataByValuesAndDefault('APStatus', $values);

        $values = array(
            'Pending',
            'Approved',
            'Rejected',
            'Recalled',
        );
        static::makeCustomFieldDataByValuesAndDefault('overallstatus', $values);
      
    }
}
?>
