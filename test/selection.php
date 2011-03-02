
<?php

// Takes input in the form of two get parameters 
//           (which we'll hide using mod_rewrite)
// -	one is a 'group', -> a folder in our present setup
// -	one is an 'id', -> a page in our present setup.
//
// If you think about it, 'group' is redundant at the moment, but there
// are some reasons it could be a clean way to do stuff down the road.
function selection($group, $id)
{
  // It's just a big if statement; super straightforward and boring.
  if($group == "team")
  {
    if($id == "paul")
    {
      $sticky_insert = "biopvilchez";
    }
    else if($id == "luke")
    {
      $sticky_insert = "biolrewega";
    }
    else if($id == "quincy")
    {
      $sticky_insert = "bioqjermyn";
    }
    else if($id == "martin")
    {
      $sticky_insert = "biomlindsay";
    }
    else if($id == "robyn")
    {
      $sticky_insert = "biorsmith";
    }
    else if($id == "wyatt")
    {
      $sticky_insert = "biowcarss";
    }
  }
  else if($group == "apps")
  {
    // As we release apps, comment "noapp" and uncomment "appN"
    if($id == "0")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app0";
      $latest_insert = "day0";
    }
    else if($id == "1")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app1";
      $latest_insert = "day1";
    }
    else if($id == "2")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app2";
      $latest_insert = "day2";
    }
    else if($id == "3")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app3";
      $latest_insert = "day3";
    }
    else if($id == "4")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app4";
      $latest_insert = "day4";
    }
    else if($id == "5")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app5";
      $latest_insert = "day5";
    }
    else if($id == "6")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app6";
      $latest_insert = "day6";
    }
    else if($id == "7")
    {
      $sticky_insert = "noapp";
      //$sticky_insert = "app7";
      $latest_insert = "day7";
    }
  }
  else if($group == "sponsors")
  {
    if($id == "innovationguelph")
    {
      $sticky_insert = "sponsor_innovationguelph";
      $latest_insert = "sponsors";
    }
    if($id == "threefortynine")
    {
      $sticky_insert = "sponsor_threefortynine";
      $latest_insert = "sponsors";
    }
    if($id == "ccs")
    {
      $sticky_insert = "sponsor_ccs";
      $latest_insert = "sponsors";
    }
    if($id == "sredunlimited")
    {
      $sticky_insert = "sponsor_sredunlimited";
      $latest_insert = "sponsors";
    }
    if($id == "speakfeelmobile")
    {
      $sticky_insert = "sponsor_speakfeelmobile";
      $latest_insert = "sponsors";
    }
    if($id == "melanieveltman")
    {
      $sticky_insert = "sponsort_melanieveltman";
      $latest_insert = "sponsors";
    }
  }
  else
  {
    // If the user goes anywhere we don't like, they're at the main page.
    $sticky_insert = "intro";
    $latest_insert = "news" . $ask;
  }


  // Prepare the content strings:

  $sticky = "\t\t<div id=\"blog-sticky\">\n\t\t\t<script type=\"text/javascript\" src=\"http://guelphseven.tumblr.com/tagged/" . $sticky_insert . "/js\"></script>\n\t\t</div>\n\t\t";
  $latest = "<div id=\"blog-latest\">\n\t\t\t<script type=\"text/javascript\" src=\"http://guelphseven.tumblr.com/tagged/" . $latest_insert . "/js\"></script>\n\t\t\t<span class=\"st_twitter_large\" displayText=\"Tweet\"></span><span class=\"st_facebook_large\" displayText=\"Facebook\"></span><span class=\"st_ybuzz_large\" displayText=\"Yahoo! Buzz\"></span><span class=\"st_gbuzz_large\" displayText=\"Google Buzz\"></span><span class=\"st_email_large\" displayText=\"Email\"></span><span class=\"st_sharethis_large\" displayText=\"ShareThis\"></span>\n\t\t</div>\n\t\t";
  $noscript = "<noscript>\n\t\t\t<p>Please enable JavaScript!</p>\n\t\t</noscript>\n\t\t";
  $ask = "<div id=\"blog-ask\">\n\t\t\t<script type=\"text/javascript\" src=\"http://guelphseven.tumblr.com/ask/js\"></script>\n\t\t</div>\n\t\t";

  // The following could be more compact; but it's likelier easier to maintain
  // if it's fully specified rather than clever/shorter.

  if($group == "team")
  {
    $out = $sticky . $noscript;
  }
  else if($group == "sponsors")
  {
    $out = $sticky . $latest . $noscript;
  }
  else if($group == "apps")
  {
    $out = $sticky . $latest . $ask . $noscript;
  }
  else
  {
   $out = $sticky . $latest . $ask . $noscript;
  }

  // Final output
  return $out;
}
?>
