<?php

interface IdentifyUnusedCssInterface
{
    public function init($i_VerboseMode = false);
    public function setPageUrl($i_PageUrl);

//TODO: in the future
//    public function addPageUrl($i_PageUrl);

    public function runScanner();
    public function getAllCssData();
    public function getRaport();
}

