<?php
/* Excite WebSite Manager
 * author: Bill Creswell
 * license: use it or not
 *
 * Goal: A flat file site that can be easily maintained by user
 * Idea:
 * -A template with fixed main top-level pages
 * -pages, events, news and newsletters will allow user drop in text files
 * Required files
*/

// get requested page
    if (isset($_REQUEST['page']) && $_REQUEST['page'] != '') {
        $page = $_REQUEST['page'];
    } else {
        $page='home';
    }

/**
 * Get Content
 * this will get the top level content
 */

    function getContent($page)
    {
        switch ($page) {

        // Home Page Content
            case 'home':
            case 'index':
            case '':
                $content = file_get_contents('pages/mcbvi.html');
                break;

        // Newsletter page content, and dir list of newletters.
            case 'newsletters':
                $content = file_get_contents('pages/newsletters.html');
                $content.= getDirList($page);
                break;

            case "newsletter":
            case "article":
            case "event":
                if(isset($_REQUEST["issue"])) {
                     $issuepage = "newsletters/" . $_REQUEST['issue'];
                     $content = file_get_contents($issuepage);

                } elseif(isset($_REQUEST["articleid"])) {
                     $viewid = "articles/" . $_REQUEST['articleid'];
                     $content = file_get_contents($viewid);
                 } elseif(isset($_REQUEST["eventid"])) {
                     $viewid = "events/" . $_REQUEST['eventid'];
                     $content = file_get_contents($viewid);
                } else {
                    $content = "no issue specified";
                }

                break;
            case "articles":
                $content = file_get_contents("pages/articles.html");
                $content .= getDirList($page);
                $content .= getFacebookContent();
                break;

            default:
                $content = file_get_contents("pages/$page.html");
                break;
        }
        return $content;
    }

    function getMenuList()
    {
        $menu = "<ul>";
        if(isset($_REQUEST["page"]) && strtoupper($_REQUEST["page"]) != "mcbvi") {
            $menu .= "<li><a href='/'>Home</a></li>";
        }

        $dirlist = getFileList("pages/", true, 1);
        foreach($dirlist as $file) {
            if ($file['name'] != "Mcbvi") {
                // Issue #6 Remove Current page link
                if(isset($_REQUEST["page"]) && strtoupper($_REQUEST["page"]) != strtoupper($file['name'])) {
                    $menu.="<li><a href='?page={$file['call']}'>{$file['name']}</a></li>";
                } elseif(isset($_REQUEST["page"]) && strtoupper($_REQUEST["page"]) === strtoupper($file['name'])) {
                    $menu.="<li>{$file['name']}</li>";
                } else {
                     $menu.="<li><a href='?page={$file['call']}'>{$file['name']}</a></li>";
                }
            }
        };
        $menu.= "<li>
<a href='https://www.facebook.com/pages/Michigan-Council-Of-The-Blind-Visually-Impaired/125509287540911'>
MCBVI on Facebook</a></li>";
        $menu.= "</ul>";
        return $menu;
    }

    function getDirList($dir)
    {
        $list = "";
        $dirlist = getFileList("$dir/", true,1);
               if(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'newsletters'){
                foreach($dirlist as $file) {$sname[]=$file["name"];}

                array_multisort($sname,SORT_DESC,$dirlist);
          }
        foreach($dirlist as $file) {
            $fname = "";
            if ($file["type"] == "dir") {
                $fname = ucwords(str_replace("_"," ", $file["name"]));
                $list .= "</ul><h3>{$fname}</h3><ul>";
            } else {
                if(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'newsletters'){
                    $issuepath = explode("/",$file["path"]);
                    $issue=$issuepath[1];

                    $list.="<li><a href='?page=newsletter&issue=$issue'>{$file['name']}</a></li>";
                }elseif(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'articles'){
                    if($file["type"]==="html") {
                    $issuepath = explode("/",$file["path"]);
                    $issue=$issuepath[1];
                    $list.="<li><a href='?page=article&articleid=$issue'>{$file['name']}</a></li>";
                    } else {
                        $fname = $file["path"];
                        $list.="<li><a href='$fname'>{$file['name']}</a> ({$file['type']})</li>";
                    }
                }elseif(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'events'){
                    $issuepath = explode("/",$file["path"]);
                    $issue=$issuepath[1];
                    $list.="<li><a href='?page=event&eventid=$issue'>{$file['name']}</a></li>";
                } else {
                     $fname = $file["path"];
                     $list.="<li><a href='$fname'>{$file['name']}</a> ({$file['type']})</li>";
                }
            }
        };
        return $list;
    }

// single directory
//$dirlist = getFileList("./");
// include all subdirectories recursively
//$dirlist = getFileList("./documents/", true);
// include just one or two levels of subdirectories
//$dirlist = getFileList("./documents/", true, 1);
//$dirlist = getFileList("./documents/", true, 2);

// Original PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

    function getFileList($dir, $recurse=false, $depth=false)
    {
        $types = array(
            "doc" => "word_icon.png",
            "gif" => "image_icon.png",
            "jpeg" => "image_icon.png",
            "jpg" => "image_icon.png",
            "html" => "ie_icon.png",
            "txt" => "document_icon.png",
            "pdf" => "pdf_icon.png",
            "ppt" => "powerpoint_icon.png",
            "xls" => "excel_icon.png"
        );

        $retval = array();
// add trailing slash if missing
        if (substr($dir, -1) != "/") $dir .= "/";
// open pointer to directory and read list of files
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        while (false !== ($entry = $d->read())) {
  // skip hidden files
            if ($entry[0] == ".") continue;
            if (is_dir("$dir$entry")) {
                $retval[] = array(
                    "path" => "$dir$entry/",
                    "name" => "$entry",
                    "type" => filetype("$dir$entry"),
                    "size" => 0,
                    "lastmod" => filemtime("$dir$entry")
                );
                if ($recurse && is_readable("$dir$entry/")) {
                    if($depth === false) {
                        $retval = array_merge(
                        $retval, getFileList("$dir$entry/", true));
                    } elseif($depth > 0) {
                        $retval = array_merge(
                            $retval, getFileList("$dir$entry/", true, $depth-1));
                    }
                }
            } elseif (is_readable("$dir$entry")) {
                $ic = explode(".", "$entry");
                $type = $ic[1];
                $name = ucwords(str_replace("_"," ",$ic[0]));
                $call = $ic[0];
                $icon = $types[$ic[1]];
                $retval[] = array(
                    "path" => "$dir$entry",
                    "call" => "$call",
                    "name" => "$name",
                    "type" => "$ic[1]",
                    "icon" => "$icon",
                    "size" => filesize("$dir$entry"),
                    "lastmod" => filemtime("$dir$entry") );
            }
        }
        $d->close();
        return $retval;
    }

    function getFacebookContent()
    {
        try{
            $contents = '<h3>Facebook Content</h3>';
        // fake a browser header so facebook gives us rss
            $url = 'https://www.facebook.com/feeds/page.php?format=rss20&id=125509287540911';
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";
            $header[] = "Pragma: "; // browsers keep this blank.

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_REFERER, '');
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $data = curl_exec($curl);

            curl_close($curl);

            $rss = simplexml_load_string($data);

      // define the namespaces that we are interested in
      // http://blog.stuartherbert.com/php/2007/01/07/using-simplexml-to-parse-rss-feeds/
            $ns = array (
                'content' => 'http://purl.org/rss/1.0/modules/content/',
                'wfw' => 'http://wellformedweb.org/CommentAPI/',
                'dc' => 'http://purl.org/dc/elements/1.1/'
            );

        // obtain the articles in the feeds, and construct an array of articles
            $articles = array();

        // step 1: get the feed
            $xml = new SimpleXmlElement($data);

        // step 2: extract the channel metadata
            $channel = array();
            $channel['title']       = $xml->channel->title;
            $channel['link']        = $xml->channel->link;
            $channel['description'] = $xml->channel->description;
            $channel['pubDate']     = $xml->pubDate;
            $channel['timestamp']   = strtotime($xml->pubDate);
            $channel['generator']   = $xml->generator;
            $channel['language']    = $xml->language;

        // step 3: extract the articles
            foreach ($xml->channel->item as $item) {
                if(strtotime($item->pubDate) > date('-30days')) {
                    $article = array();
                    $article['channel'] = $blog;
                    $article['title'] = $item->title;
                    $article['link'] = $item->link;
                    $article['comments'] = $item->comments;
                    $article['pubDate'] = $item->pubDate;
                    $article['timestamp'] = strtotime($item->pubDate);
                    $article['description'] = (string) trim($item->description);
                    $article['isPermaLink'] = $item->guid['isPermaLink'];

                // get data held in namespaces
                    $content = $item->children($ns['content']);
                    $dc      = $item->children($ns['dc']);
                    $wfw     = $item->children($ns['wfw']);

                    $article['creator'] = (string) $dc->creator;
                    foreach ($dc->subject as $subject) {
                        $article['subject'][] = (string)$subject;
                    }
                    $article['content'] = (string)trim($content->encoded);
                    $article['commentRss'] = $wfw->commentRss;

                // add this article to the list
                    $articles[$article['timestamp']] = $article;
                }
            }
            foreach($articles as $a) {
                $contents.="<div class='article'>";
                $contents.= "<div>";
                $contents.= date('Y-m-d', $a['timestamp']) .  "<br/>";
                $contents.= $a['description'] . "</div>";
                $contents.= "<a style='color:blue' href='" . $a['link'] ."'>Read this article on Facebook</a></div>";

            }
           return $contents;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

/**
 * Main Template
 */

?><!doctype html>
<html lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
    <title><?php echo ucfirst($page); ?> - Michigan Council of the Blind and Visually Impaired</title>
    <link rel="shortcut icon" href="/favicon.ico?" type="image/x-icon">
    <link rel="icon" href="/favicon.ico?" type="image/x-icon">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="HandheldFriendly" content="true"/><!--Blackberry Column View-->
    <meta name="viewport" content="initial-scale=1.0"/><!--iPod-->
    <meta name="viewport" content="width=device-width"/><!--android-->

    <link rel="stylesheet" href="app/mobile.css" type="text/css" media="only screen and (max-width : 39em)"/>
    <!--[if lt IE 9]>
    <link rel="stylesheet" media="all" type="text/css" href="app/miblind.css"/>
    <![endif]-->
    <link rel="stylesheet" href="app/miblind.css" type="text/css" media="only screen and (min-width :40em)"/>

</head>

<body>
<?php
// Top Menu
    if(isset($_REQUEST['page']) && ($_REQUEST['page'] != 'home')) {
?>

<div id="Banner">
    <a href="/">MCBVI</a>
    <a href="#Menu">Skip to Menu</a>
    <br style="clear:both"/>
</div>

<?php } ?>

    <div id="Content">
        <?php echo getContent($page); ?>
    </div>

    <div id="Menu" role="navigation">
        <?php echo getMenuList(); ?>
        <br style="clear:both"/>
    </div>


   <script type="text/javascript">
//<![CDATA[
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-55268114-1', 'auto');
  ga('send', 'pageview');
//]]>
    </script>

</body>
</html>
