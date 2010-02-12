<?php

class IdentifyUnusedCss implements IdentifyUnusedCssInterface
{
    const STYLE_TAGS_EXISTS_PATTERN = '<style( *[\n]*.*)>\n*(.\n*)*<\/style>';
    const CSS_IMPORTS_PATTERN 
        = "/(@\s*import\s* (url((\"|')?)?((\"|')?)|(\"|'){1}).+(\"|')?\)?)/";
    const CSS_IMPORTS_URL_REPLACE_PATTERN 
        = "/(@\s*import\s*)|(url\(?((\"|')?))|(\"|'){1}|\)?(\"|')?;|(\s)/";

    private $VerboseMode = false;

    private $PageUrl = '';

    private $PageContents = '';

    private $IncludedCssData = array();

    public function init($i_VerboseMode = false)
    {
        $this->VerboseMode = $i_VerboseMode;

        return true;
    }

    public function setPageUrl($i_PageUrl)
    {
        $o_Result = false;

        if ( true === $this->validateUrlAddress($i_PageUrl) )
        {
            $this->PageUrl      = $i_PageUrl;

            //TODO: add user error handler only for function "file_get_contents"
            $this->PageContents = file_get_contents($this->PageUrl);

            if ( false !== $this->PageContents )
            {
                $this->PageContents = trim($this->PageContents);

                if ( 0 < strlen($this->PageContents) )
                {
                    $o_Result = true;
                }
                else
                {
                    throw new IdentifyUnusedCssException(
                        'Problem with empty page contents from page: '
                        . $this->PageUrl);
                }
            }
            else
            {
                throw new IdentifyUnusedCssException(
                    'Problem with get contents from page: ' . $this->PageUrl);
            }
        }
        else
        {
            throw new IdentifyUnusedCssException('Not valid URL address');
        }

        return $o_Result;
    }

    public function runScanner()
    {
        if ( true === is_array($this->IncludedCssData)
             && 0 < count($this->IncludedCssData) )
        {
            $this->allStylesDefinitions = $this->getAllStylesDefinitions(
                                                    $this->IncludedCssData);

            foreach ( $this->allStylesDefinitions as $_styleDef => $_value )
            {
                if ( false !== strpos($this->PageContents, $_styleDef) )
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
                'Scener has no data to scen - maybe page has not CSS');
        }
    }

    public function getAllCssData()
    {
        if ( 0 < strlen($this->PageUrl) )
        {
           $_result = $this->getAllCssLinksAndIncludes($this->PageContents);
        }
        else
        {
            throw new IdentifyUnusedCssException(
                'Please use metdhod "->setPageUrl()" first with validated URL '
                . ' address parameter');
        }
    }

    public function getRaport()
    {
        
    }

    private validateUrlAddress($i_UrlAddress)
    {
        //TODO: use sfValidator for URL address from framework
    }

    private getAllCssLinksAndIncludes($i_PageContent)
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


