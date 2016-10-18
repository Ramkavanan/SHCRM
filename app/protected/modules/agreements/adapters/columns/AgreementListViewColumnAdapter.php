<?php
    /**
     * Render agreement grid view data adapter
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementListViewColumnAdapter extends TextListViewColumnAdapter
    {
        public function renderGridViewData()
        {
            if ($this->getIsLink())
            {
                return array(
                    'name' => $this->attribute,
                    'type' => 'raw',
                    'value' => $this->view->getRelatedLinkString(
                               '$data->' . $this->attribute, $this->attribute, 'agreements'),
                );
            }
            else
            {
                return array(
                    'name'  => $this->attribute,
                    'value' => 'strval($data->' . $this->attribute . ')',
                    'type'  => 'raw',
                );
            }
        }
    }
?>
