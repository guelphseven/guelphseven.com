<?php
/* Get a value from an array by key only if the key exists, otherwise return or NULL */
function getArrayValueOrNULL( $array, $key) {
    if(array_key_exists($key, $array)) { 
        return $array[$key];
    } else {
        return NULL;
    }
}

/* Retrieve and format raw XML for posts tagged with $tag, and optionally include the post date */
function getTumblrPostsAsHTML( $tag, $ignore_date, $current_page, $start, $num) {
    /*TODO: rewrite using curl and simplexml_load_string */
    if(!$xml = simplexml_load_file("http://guelphseven.tumblr.com/api/read?tagged=" . $tag)) {
        return NULL;
    }
    $info = $xml->xpath('/tumblr/posts');
    $info = $info[0];
    $posts = $xml->xpath('/tumblr/posts/post');
    $post_count = count($posts);
    if($post_count <= 0 || $start >= $post_count){
        return NULL;
    }

    $page_size = $num;
    if($start + $num > $post_count) {
        $num = $post_count - $start;
    }

    $html = '<ol class="tumblr_posts">' . "\n";
    for ($i = $start; $i < $start + $num; ++$i) {
        $post = $posts[$i];
        $html .= '    <li class="tumblr_post">'. "\n";
        $html .= '        <div class="tumblr_title">' . $post->{'regular-title'} . '</div>' . "\n";
        $html .= '        <div class="tumblr_body">' . $post->{'regular-body'} . '</div>' . "\n";
        if(!$ignore_date) {
            $html .= '        <div class="tumblr_date">' . $post['date'] . '</div>' . "\n";
        }
        $html .= '    </li>' . "\n";
    }

    if(!empty($current_page) && $num < $post_count) {
        $html .= '    <li class="tumblr_pagenav">' . "\n";
        $html .= '        <div class="tumblr_title">';
        if($start > 0) {
            $html .= '<a href="/' . $current_page . '/page' . ($start<$page_size?0:floor(($start-$page_size)/$page_size)) . '">Previous</a>';
        }
        if($start + $num < $post_count) {
            if($start > 0) {
                $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $html .= '<a href="/' . $current_page . '/page' . floor(($start + $page_size)/$page_size) . '">Next</a>';
        }
        $html .= '</div>' . "\n";
        $html .= '    </li>' . "\n";
    }
    $html .= '</ol>' . "\n";

    return $html;
}

/* Get all tumblr posts under tag, as html, cached if possible */
function renderTumblrPosts( $tag, $ignore_date = true, $current_page = "", $start = 0, $num = 5 ) {
    if($tag !== NULL) {
        // Thanks to @dlachapelle for caching
        $cachefile = "./tumblr_cache/" . $tag . "_s" . $start . "_c" . $num . ($ignore_date ? "" : "_dated");
        if(!file_exists($cachefile)) {
            if($posts = getTumblrPostsAsHTML($tag, $ignore_date, $current_page, $start, $num)) {
                // save posts to cache
                $posts_cache = serialize($posts);
                if($fp = fopen($cachefile, 'w')) {
                    if(flock($fp, LOCK_EX)) {
                        fwrite($fp, $posts_cache);
                        flock($fp, LOCK_UN);
                    }
                    fclose($fp);
                }
                echo $posts;
                return;
            }
        }

        if($fp = fopen($cachefile, 'r')) {
            if(flock($fp, LOCK_SH)) {
                $contents = fread($fp, filesize($cachefile));
                flock($fp, LOCK_UN);
            }
            fclose($fp);
            $posts = unserialize($contents);
        }
        if($posts) {
            echo $posts;
        } else if ($tag != "404") {
            renderTumblrPosts("404");
        }
        return;
    }
}

$pages = array(
    '404' => array('sticky' => "404"),
    'index' => array('sticky' => "intro", 'latest' => "news"),
    'team/paul' => array('sticky' => "biopvilchez"),
    'team/luke' => array('sticky' => "biolrewega"),
    'team/martin' => array('sticky' => "biomlindsay"),
    'team/wyatt' => array('sticky' => "biowcarss"),
    'team/quincy' => array('sticky' => "bioqjermyn"),
    'team/chris' => array('sticky' => "biocstatham"),
    'team/kiel' => array('sticky' => "biokmonk"),
    'apps/day1' => array('sticky' => "app1description", 'latest' => "day1"),
    'apps/day2' => array('sticky' => "app2description", 'latest' => "day2"),
    'apps/day3' => array('sticky' => "app3description", 'latest' => "day3"),
    'apps/day4' => array('sticky' => "app4description", 'latest' => "day4"),
    'apps/day5' => array('sticky' => "app5description", 'latest' => "day5"),
    'apps/day6' => array('sticky' => "app6description", 'latest' => "day6"),
    'apps/day7' => array('sticky' => "app7description", 'latest' => "day7")
);

$page = getArrayValueOrNULL($_GET, "page");
if($page === NULL) {
    $page = "index";
} else {
    $page = ltrim($page, "/");
}

$page_start = 0;
if(preg_match('/(.+)\/page(\d+)/',$page,$matches)) {
    $page = $matches[1];
    $page_start = intval($matches[2]);
}

if(!array_key_exists($page, $pages)) {
    header("HTTP/1.0 404 Not Found");
    $page = "404";
}

$content = getArrayValueOrNULL($pages, $page);
?>
<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>The Guelph Seven - 7 Students make 7 Apps in 7 Days</title>
    <meta name="description" content="The Guelph Seven: 7 Students, 7 Apps, 7 Days, March 5th-11th, 2011.">
    <meta name="author" content="The Guelph Seven">
    <meta name="keywords" content="guelph seven guelphseven 7 cubed android startups development coding apps uog uoguelph uwaterloo">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width">
    <link href="http://fonts.googleapis.com/css?family=Molengo" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/libs/modernizr-1.6.min.js"></script>
</head>
<body>
    <div id="container">
        <header>
            <a href="http://www.guelphseven.com/"><h1>The Guelph Seven</h1></a>
        </header>
        <div id="main" class="clearfix">
            <div id="apps">
                <h2>The<br/>Apps</h2>
                <ul>
                    <li><a href="/apps/day1"><div class="icon"><img src="/img/thumb_app1.png" /></div></a></li>
                    <li><a href="/apps/day2"><div class="icon"><img src="/img/thumb_app2.png" /></div></a></li>
                    <li><a href="/apps/day3"><div class="icon"><img src="/img/thumb_app3.png" /></div></a></li>
                    <li><a href="/apps/day4"><div class="icon"><img src="/img/thumb_app4.png" /></div></a></li>
                    <li><a href="/apps/day5"><div class="icon"><img src="/img/thumb_app5.png" /></div></a></li>
                    <li><a href="/apps/day6"><div class="icon"><img src="/img/thumb_app6.png" /></div></a></li>
                    <li><a href="/apps/day7"><div class="icon"><img src="/img/thumb_app7.png" /></div></a></li>
                </ul>
            </div>
            <div id="team">
                <h2>The<br/>Team</h2>
                <ul>
                    <li><a href="/team/paul"><div class="icon"><img src="/img/thumb_paul.png" /></div></a></li>
                    <li><a href="/team/luke"><div class="icon"><img src="/img/thumb_luke.png" /></div></a></li>
                    <li><a href="/team/quincy"><div class="icon"><img src="/img/thumb_quincy.png" /></div></a></li>
                    <li><a href="/team/martin"><div class="icon"><img src="/img/thumb_martin.png" /></div></a></li>
                    <li><a href="/team/wyatt"><div class="icon"><img src="/img/thumb_wyatt.png" /></div></a></li>
                    <li><a href="/team/chris"><div class="icon"><img src="/img/thumb_chris.png" /></div></a></li>
                    <li><a href="/team/kiel"><div class="icon"><img src="/img/thumb_kiel.png" /></div></a></li>
                </ul>
            </div>
            <div id="content">
                <div id="blog-sticky">
<?php renderTumblrPosts(getArrayValueOrNULL($content,'sticky')); ?>
                </div>
                <div id="blog-latest">
<?php renderTumblrPosts(getArrayValueOrNULL($content,'latest'), false, $page, $page_start * 5); ?>
                </div>
                <div id="blog-ask">
<?php renderTumblrPosts(getArrayValueOrNULL($content,'ask')); ?>
                </div>
            </div>
        </div>
            <div id="sponsors" class="clearfix">
                <p>
                    <em class="focus">A big thank you to all of our generous sponsors!</em>
                </p>
                <br />
                <p>
                    <div id="spons-left">
                        <a href="http://innovationguelph.com">
                            <img src="/img/innovation_guelph.jpg" alt="Innovation Guelph" />
                        </a>
                    </div>
                    <div id="spons-right">
                        <a href="http://threefortynine.com/">
                            <img src="/img/threefortynine.png" alt="ThreeFortyNine" />
                        </a>
                        <br />
                        <a href="#">
                            <img src="/img/mveltman.png" alt="Melanie Veltman" />
                        </a>
                        <br />
                        <a href="http://www.speakfeel.ca/">
                            <img src="/img/speakfeel.png" alt="SpeakFeel" />
                        </a>
                    </div>
                    <div id="spons-mid">
                        <a href="http://www.uoguelph.ca/ccs/">
                            <img src="/img/ccs.png" alt="Computing & Communications Services" />
                        </a>
                        <br />
                        <a href="http://www.sredunlimited.com/">
                            <img src="/img/sred_unlimited.png" alt="SRED Unlimited" />
                        </a>
                    </div>
                </p>
            </div>
    </div>
    <!--[if lt IE 7 ]>
    <script src="/js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .ipng_bg'); </script>
    <![endif]-->
    <script src="http://static.getclicky.com/js"></script> 
    <noscript><p><img alt="Clicky" width="1" height="1" src="http://in.getclicky.com/66383767ns.gif" /></p></noscript>
    <script>
        var _gaq = [['_setAccount', 'UA-21712078-1'], ['_trackPageview']];
        (function(d, t) {
            var g = d.createElement(t),
            s = d.getElementsByTagName(t)[0];
            g.async = true;
            g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g, s);
        })(document, 'script');
        try{ clicky.init(66383767); }catch(err){}
    </script>
</body>
</html>
