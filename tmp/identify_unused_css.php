<?php
/*

Identify unused CSS

   Unused CSS

   Recently finished a new page design? We all know how cluttered code can get when dealing with problems in the design and during the development process. Often, CSS class
   declarations that were written for elements that were removed from your html page are not removed from the CSS too, and remain as pointless entries in your CSS file,
   increasing its size and volume of clutter unnecessarily.

   Therefore, I have written a small PHP script that uses several regular expressions to check the styles of a page you specify, and tell you which CSS classes remain in
   your CSS file(s) but are not ustilised in the HTML code. After being told these useless CSS entries, you can manually remove them from your CSS file(s). For now I will
   just post the code of this file up, so you can use it on your server if you wish. I would host it on the GoSquared server but this script is somewhat vulnerable to
   cross-site scripting attacks and the like, and has not been designed with security in mind. It is merely a tool. Therefore, I advise you to make sure only you can access
   this file on your server, and don't make it public, if you use it on your server.
*/


/********************************************************************\
* GoSquared ltd.                                    |css_checker.php *
* Authors: Main code by Geoff http://www.gosquared.com               *
* Extended by Damian http://level0.ch/                               *
* Adapted to OOP by Geoff                                            *
* Date: 10/07                                                        *
* Version: 1.2.0                                                     *
*--------------------------------------------------------------------*
* THIS SCRIPT IS OPEN SOURCE AND MAY BE USED BY ANYONE. SUBSEQUENTLY,*
* THE AUTHORS ACCEPT NO RESPONSIBILITY FOR ITS USAGE NOR GURANTEE ITS*
* RELIABILITY. NONE OF THE AUTHORS STATED ABOVE CAN BE HELD RESPONSI-*
* BLE FOR PROBLEMS RESULTING FROM THE USAGE OF THIS SCRIPT           *
*********************************************************************/

class css_scanner{
        public $helptext = "<p>
                <h3>Identify unused CSS</h3>
                Main code: <a href=\"http://www.gosquared.com/liquidicity/archives/203\">http://www.gosquared.com/liquidicity/archives/203</a> by Geoff<br />
                Bug fix & more: <a href=\"http://level0.ch/fileserv/csschecker.php.html\">http://level0.ch/fileserv/csschecker.php.html</a> by Damian<br />
                Further development, bug fixes and additions by Geoff<br />
                <b>Usage:</b><br />
                <ul>
                  <li>http://www.myserver.com/csschecker.php?page=index.html</li>
                  <li>http://www.myserver.com/csschecker.php?page=http://www.another-server.net/index.html</li>
                  <li>http://www.myserver.com/csschecker.php?page=index.html,about.html,download.html</li>
                  <li>http://www.myserver.com/csschecker.php?page=index.html&visual=inline</li>
                  <li>http://www.myserver.com/csschecker.php?page=index.html&visual=iframe</li>
                </ul><br />
                CSS Scanner will look for and scan any embedded or external CSS files in your HTML code.
                  </p>";
        public $page_content = '';
        public $unused = array();
        public $multi_page;
        public $page;
        public $page_content_visual;

        function __construct($page){
                //$multi_css = explode(',', $css);
                if(empty($page)){
                        echo "No page specified to scan" . $this->helptext;
                        exit;
                }

                $this->multi_page = explode(',', $page);
                //$css_content = '';

                if(count($this->multi_page) > 1){
                        $i = 0;
                        $this->page_content = array();
                        foreach ($this->multi_page as $page) {
                            $this->page_content[$i] = file_get_contents($page);
                            $i++;
                        }
                }
                else{
                        //$css_content .= file_get_contents($css);
                        $this->page = $page;
                        $this->page_content = file_get_contents($page);
                }

                if(is_array($this->page_content)){
                        $i=0;
                        foreach($this->page_content as $page_content){
                                $this->page = $this->multi_page[$i];
                                $this->page_content_visual = $page_content;
                                $this->unused = $this->scan($page_content);
                                $this->print_report();
                                $i++;
                        }
                }
                else{
                        $this->page_content_visual = $this->page_content;
                        $this->unused = $this->scan($this->page_content);
                        $this->print_report();
                }
        }

        function scan($page_content){
                $i = 0;
                if(ereg("<style( *[\n]*.*)>\n*(.\n*)*<\/style>", $page_content)){
                if(preg_match_all("/(@\s*import\s* (url((\"|')?)?((\"|')?)|(\"|'){1}).+(\"|')?\)?)/", $page_content, $ext_stylesheets)){
                    foreach($ext_stylesheets[0] as $stylesheet){
                        $css_content[$i] = preg_replace("/(@\s*import\s*)|(url\(?((\"|')?))|(\"|'){1}|\)?(\"|')?;|(\s)/", "", $stylesheet);
                        $i++;
                    }
                    $array = 1;
                }
                $inline_notused = $this->check_file($page_content, $page_content);
            }
            else die("No page styles, sorry!".$this->helptext);

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
        }

        function print_report(){
                echo "<h1>Report for page: ".$this->page.'</h1>';
                foreach($this->unused['external'] as $unused){
                        echo 'CSS entries not used from external CSS file '.$unused['css_file'].':<br />';
                    if(count($unused['external']) > 0){
                    foreach($unused['external'] as $entry){
                        echo $entry.'<br />';
                    }
                    }else echo 'None, good job!';
                    echo '<br /><br />';
                }
            if(count($this->unused['internal'] > 0)){
                    echo 'CSS entries not used from internal CSS code block:<br />';
                    foreach($this->unused['internal'] as $entry){
                        echo $entry.'<br />';
                    }
                    echo '<br /><br />';
                }
                if(isset($_GET["visual"]) && $_GET['visual'] == 'iframe'){
                   echo "<hr><iframe src = \"$this->page\" width=\"100%\" height=\"100%\"></iframe>";
                }
                elseif(isset($_GET["visual"]) && $_GET['visual'] == 'inline'){
                        echo "<hr>",
                            $this->page_content_visual;
                }
        }

        function check_file($css, $page_content){
                preg_match_all("/\.([a-zA-Z-_][a-zA-Z0-9-_]+)({|[\s].*{)/", $css, $css_classes);
                preg_match_all("/#([a-zA-Z-_][a-zA-Z0-9-_]+)({|[\s][^;]*{)/", $css, $css_ids );
                preg_match_all("/<[a-zA-z0-9][^>]*class\s*=\s*[\"'](.+?)[\"']/", $page_content, $page_classes);
                preg_match_all("/<[a-zA-z0-9][^>]*id\s*=\s*[\"'](.+?)[\"']/", $page_content, $page_ids);

                $clean_page_classes = array();
                $clean_page_ids = array();
                foreach($page_classes[1] as $s) {
                    $classes = explode(" ", $s);
                    foreach($classes as $class) {
                        array_push($clean_page_classes, $class);
                    }
                }
                foreach($page_ids[1] as $s) {
                    $ids = explode(" ", $s);
                    foreach($ids as $id) {
                        array_push($clean_page_ids, $id);
                    }
                }

                $not_used = array();
                foreach($css_classes[1] as $css){
                    if(!in_array($css, $clean_page_classes) && !in_array('.'.$css, $not_used)){
                        array_push($not_used, '.'.$css);
                    }
                }
                foreach($css_ids[1] as $css){
                    if(!in_array($css, $clean_page_ids) && !in_array('#'.$css, $not_used)){
                        array_push($not_used, '#'.$css);
                    }
                }
                return (count($not_used) > 0) ? $not_used : false;
        }
}

//$scanner = new css_scanner($_GET['page']);

$_page = 'http://bieli.net';
$scanner = new css_scanner($_page);


