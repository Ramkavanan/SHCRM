<?php
     /**
     * Class for working the Approval process link in home page
     */
    class AcceptLinkListViewColumnAdapter extends TextListViewColumnAdapter
    {
        /**
         * @return array
         */
        public function renderGridViewData()
        {
            if ($this->getIsLink())
            {
                return array(
                    'name' => $this->attribute,
                    'header'      => 'Action',
                    'type' => 'raw',
                    'value' => 'AcceptLinkListViewColumnAdapter::getActionLinks($data, true)',
                    'sortable' => false,
                );
            }
            else
            {
                return array(
                    'name'  => $this->attribute,
                    'header'      => 'Action',
                    'value' => 'AcceptLinkListViewColumnAdapter::getActionLinks($data, false)',
                    'sortable' => false,
                );
            }
        }

        public static function getActionLinks($data, $isLink = false)
        {
            if($data->opportunity->id > 0){
                $linkString = '<a href="/app/index.php/approvalProcess/default/Reassign?id='.$data->id.'">ReAssign</a>&nbsp;<a href="/app/index.php/approvalProcess/default/approvalProcessing?optId='.$data->opportunity->id.'&apId='.$data->id.'">Accept/Reject</a>';
            }
            else {
                $linkString = '<a href="/app/index.php/approvalProcess/default/Reassign?id='.$data->id.'">ReAssign</a>&nbsp;<a href="/app/index.php/approvalProcess/default/approvalProcessing?agmntId='.$data->agreement->id.'&apId='.$data->id.'">Accept/Reject</a>';
            }
            $text = ZurmoHtml::tag('div', array(), $linkString);
            return trim($text, ';');
        }

    }
?>
