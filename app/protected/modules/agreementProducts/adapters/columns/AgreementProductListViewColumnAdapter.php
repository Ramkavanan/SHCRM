<?php
    class AgreementProductListViewColumnAdapter extends TextListViewColumnAdapter {
        public function renderGridViewData()
        {
            if ($this->getIsLink())
            {
                return array(
                    'name' => $this->attribute,
                    'type' => 'raw',
                    'value' => $this->view->getRelatedLinkString(
                               '$data->' . $this->attribute, $this->attribute, 'AgreementProduct'),
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
