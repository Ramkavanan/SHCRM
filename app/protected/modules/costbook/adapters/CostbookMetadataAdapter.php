<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookMetadataAdapter
 *
 * @author ideas2it
 */
class CostbookMetadataAdapter implements StateMetadataAdapterInterface{
    protected $metadata;

    public function __construct(array $metadata)
    {
        assert('isset($metadata["clauses"])');
        assert('isset($metadata["structure"])');
        $this->metadata = $metadata;
    }

    
    public function getAdaptedDataProviderMetadata() {
        $metadata = $this->metadata;
        return $metadata;
    }

//put your code here
}
