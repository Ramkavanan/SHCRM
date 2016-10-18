<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookIntroView
 *
 * @author ideas2it
 */
class CostbookIntroView extends IntroView
    {
        protected function renderIntroContent()
        {
            $content = "<div>Welcome to costbook</div>"; 
            $this->registerScripts();
            return $content;
        }
    }