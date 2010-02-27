<?php

require_once 'IdentifyUnusedCssInterface.class.php';
require_once 'IdentifyUnusedCssException.class.php';
require_once 'SimpleVerboseSupport.class.php';

class IdentifyUnusedCss extends SimpleVerboseSupport
implements IdentifyUnusedCssInterface
{
    const STYLE_TAGS_EXISTS_PATTERN = '<style( *[\n]*.*)>\n*(.\n*)*<\/style>';
    const CSS_IMPORTS_PATTERN 
        = "/(@\s*import\s* (url((\"|')?)?((\"|')?)|(\"|'){1}).+(\"|')?\)?)/";
    const CSS_IMPORTS_URL_REPLACE_PATTERN 
        = "/(@\s*import\s*)|(url\(?((\"|')?))|(\"|'){1}|\)?(\"|')?;|(\s)/";

    private $pagesUrls = array();

    private $pageContents = '';

    private $includedCssData = array();

    public function init($i_VerboseMode = false)
    {
        $this->setVerbose($i_VerboseMode);
//        $this->setDebug($i_VerboseMode);

        return true;
    }

    public function addPageUrl($i_PageUrl)
    {
        $o_Result = false;

        if ( 0 < strlen($i_PageUrl) )
        {
            if ( true === $this->validateUrlAddress($i_PageUrl) )
            {
                $this->addLog('Get contents from validated URL: ' . $i_PageUrl);

                $this->pagesUrls[ $i_PageUrl ] = array();

                //TODO: add user error handler only
                // for function "file_get_contents"
                $this->pageContents = file_get_contents($i_PageUrl);

                if ( false !== $this->pageContents )
                {
                    $this->pageContents = trim($this->pageContents);

                    $this->addLog('Got content lenght: '
                                  . strlen($this->pageContents));

                    if ( 0 < strlen($this->pageContents) )
                    {
                        $this->pagesUrls[ $i_PageUrl ] = $this->pageContents;

                        $this->addLog('Add page URL: "' . $i_PageUrl
                                      . '" to scanning data');

                        $o_Result = true;
                    }
                    else
                    {
                        throw new IdentifyUnusedCssException(
                            'Problem with empty page contents from page: '
                            . $this->pagesUrls);
                    }
                }
                else
                {
                    throw new IdentifyUnusedCssException(
                        'Problem with get contents from page: '
                        . $this->pagesUrls);
                }
            }
            else
            {
                throw new IdentifyUnusedCssException('Not valid URL address');
            }
        }
        else
        {
            throw new IdentifyUnusedCssException('No page specify to scan');
        }

        return $o_Result;
    }

    public function runScanner()
    {
        if ( true === is_array($this->includedCssData)
             && 0 < count($this->includedCssData) )
        {
            $this->allStylesDefinitions = $this->getAllStylesDefinitions(
                                                    $this->includedCssData);

            foreach ( $this->allStylesDefinitions as $_styleDef => $_value )
            {
                if ( false !== strpos($this->pageContents, $_styleDef) )
                {
                    //TODO
                }
                else
                {
                    
                }
            }
        }
        else
        {
            throw new IdentifyUnusedCssException(
                'Scaner has no data to scan - maybe page has not CSS');
        }
    }

    public function getAllCssData()
    {
        if ( 0 < strlen($this->pagesUrls) )
        {
           $_result = $this->getAllCssLinksAndIncludes($this->pageContents);
        }
        else
        {
            throw new IdentifyUnusedCssException(
                'Please use metdhod "->addpagesUrls()" first with validated URL '
                . ' address parameter');
        }
    }

    public function getRaport()
    {
        
    }

    private function validateUrlAddress($i_UrlAddress)
    {
        $o_Result = true;

        //TODO: use sfValidator for URL address from framework

        return $o_Result;
    }

    private function getAllCssLinksAndIncludes($i_PageContent)
    {
        if ( true === ereg(self::STYLE_TAGS_EXISTS_PATTERN, $i_PageContent) )
        {
            $_i              = 0;
            $_extStylesheets = null;

            $_cssNr = preg_match_all(self::CSS_IMPORTS_PATTERN,
                                     $i_PageContent,
                                     $_extStylesheets);

            if ( 0 < $_cssNr )
            {
                foreach ( $_extStylesheets[0] as $_stylesheetValue )
                {
                    $_cssContent[ $i ] = preg_replace(
                        self::CSS_IMPORTS_URL_REPLACE_PATTERN,
                        '',
                        $_stylesheetValue);

                    $_i++;
                }

                $_array = 1;

/*
//TODO: debug and use below code
            $unused = array('internal' => array(), 'external' => array());

                if(isset($array) && $array == 1){
                        foreach($css_content as $css_file){
                                $css = file_get_contents($css_file);
                                if(!empty($css)){
                                    $not_used = $this->check_file($css, $page_content);
                                    array_push($unused['external'], array('css_file' => $css_file, 'external' => $not_used));
                                }
                        }
                }
                if($inline_notused != false){
                        $unused['internal'] = $inline_notused;
                }
                return $unused;
*/
            }
            else
            {
                throw new IdentifyUnusedCssException('Page not used "CSS"');
            }
        }
        else
        {
            throw new IdentifyUnusedCssException('Page not used "<style>" tags');
        }
    }
}


