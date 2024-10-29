<?php
/*
Plugin Name: Artisteer Buddy
Plugin URI: http://withtorment.com/artisteer-buddy-wordpress-plugin
Description: A collection of functions to better Artisteer built themes. 
Author: With Torment
Author URI: http://www.withtorment.com
Version: 1.2
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

// Remove HTML attributes

add_filter('comment_form_defaults', 'remove_comment_styling_prompt');

function remove_comment_styling_prompt($defaults) {
	$defaults['comment_notes_after'] = '';
	return $defaults;
}

// Disable Comments On Pages

function ncop_comments_open_filter($open, $post_id=null)
{
    $post = get_post($post_id);
    return $open && $post->post_type !== 'page';
}

function ncop_comments_template_filter($file)
{
    return is_page() ? dirname(__FILE__).'/empty' : $file;
}

add_filter('comments_open', 'ncop_comments_open_filter', 10, 2);
add_filter('comments_template', 'ncop_comments_template_filter', 10, 1);

// Removes "Comments Are Closed" text

add_filter('gettext', 'ps_remove_comments_are_closed', 20, 3);
function ps_remove_comments_are_closed($translation, $text, $domain) {
    $translations = &get_translations_for_domain( $domain );
    if ( $text == 'Comments are closed.' ) {
        return '';
    }
    return $translation;
    }

// Add Thumbnails to RSS Feeds

function rss_post_thumbnail($content) {
       global $post;
       if(has_post_thumbnail($post->ID)) {
       $content = '<p>' . get_the_post_thumbnail($post->ID) .
       '</p>' . get_the_content();
       }
       return $content;
       }
add_filter('the_excerpt_rss', 'rss_post_thumbnail');
add_filter('the_content_feed', 'rss_post_thumbnail');

// Pagination

    add_action('wp_enqueue_scripts', 'add_my_stylesheet');
    function add_my_stylesheet() {
        $myStyleUrl = plugins_url('pagenavi.css', __FILE__);
        $myStyleFile = WP_PLUGIN_DIR . '/artisteer-buddy/pagenavi.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style('myStyleSheets', $myStyleUrl);
            wp_enqueue_style( 'myStyleSheets');
        }
    }
function wp_pagenavi($pages = '', $range = 4) 
{
      $showitems = ($range * 2)+1;
          global $paged;
      if(empty($paged)) $paged = 1;
        if($pages == '')
      {
          global $wp_query;
          $pages = $wp_query->max_num_pages;
          if(!$pages)
          {
              $pages = 1;
          }
      }
           if(1 != $pages)
      {
          echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
          if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
          if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
            for ($i=1; $i <= $pages; $i++)
          {
              if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
              {
                  echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
              }
          }
            if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
          if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
          echo "</div>\n";
      }
 }