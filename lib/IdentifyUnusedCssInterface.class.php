<?php

interface IdentifyUnusedCssInterface
{
    public function init($i_VerboseMode = false);
    public function addPageUrl($i_PageUrl);
    public function runScanner();
    public function getAllCssData();
    public function getRaport();
}

