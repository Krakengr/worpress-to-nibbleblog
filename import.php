<?php
/*
 * Wordpress to Nibbleblog -
 * http://homebrewgr.info/en/
 * Author Kraken

 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.

 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. 
*/
##############################################
//You have to put this file in the main dir folder

header("Content-type: text/html; charset=UTF-8");
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('memory_limit', '128M');
set_time_limit(0); //ini_set("max_execution_time", "200");

##############################################
//Main Config

//Uncomment it if you want to set different timezone
//date_default_timezone_set("America/Los_Angeles");

//Put the filename of the wordpress' xml file 
$xml_file = 'filename.xml';

$siteurl = ''; //Your main URL here, don't forget the trailing slash

$disqus_id = ''; //Your discus ID here

//In case you forgot $siteurl
if (empty($siteurl))
	$siteurl = 'http://' . $_SERVER['HTTP_HOST'] . '/';

// /Main Config
##############################################

//Don't edit from here//
$dir = getcwd() . '/';
$content_folder = $dir . 'content/public/posts/';
$comment_folder = $dir . 'content/public/comments/';
$pages_folder = $dir . 'content/public/pages/';
$posts_file = $dir . 'content/private/posts.xml';
$tags_file = $dir . 'content/private/tags.xml';
$categories_file = $dir . 'content/private/categories.xml';
$pages_file = $dir . 'content/private/pages.xml';
$comments_file = $dir . 'content/private/comments.xml';
//$images_folder = $dir . 'content/public/upload/';
##############################################################################

##############################################################################

##############################################################################

##############################################################################
if (!file_exists($dir . $xml_file)) 
	die('ERROR: no such file\n\n');

$xml = simplexml_load_string(file_get_contents($xml_file));

$postnum = 0;
$pagenum = 0;
$catnum = 0;
$tagnum = 0;
$commnum = 0;
//Arrays
$categories = array();
$tags = array();
//$comments = array();
//$posts = array();
//$comments = array();


$main_xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
$pages_xml_temp = '';
$posts_xml_temp = '';
$tags_xml_temp = '';
$categories_xml_temp = '';
$comments_xml_temp = '';
$tags_links_temp = '';
$com_xml = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:dsq="http://www.disqus.com/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:wp="http://wordpress.org/export/1.0/"
>
  <channel>';

// grab categories
foreach ( $xml->xpath('/rss/channel/wp:category') as $cat )
{
	$catid = $cat->xpath('wp:term_id');
	$slug = $cat->xpath('wp:category_nicename');
	$name = $cat->xpath('wp:cat_name');
	$categories[] = array(
		'term_id' => (int) $catid['0'],
		'cat_slug' => (string) $slug['0'],
		'cat_name' => (string) $name['0']
	);
	
	$categories_xml_temp .= '<category type="string" id="' . $catnum . '" name="' . $name['0'] . '" slug="' . $slug['0'] . '" position="' . ($catnum + 1) . '"/>';
	$catnum++;
}

// grab tags
foreach ( $xml->xpath('/rss/channel/wp:tag') as $tag )
{
	$tagid = $tag->xpath('wp:term_id');
	$slug = $tag->xpath('wp:tag_slug');
	$name = $tag->xpath('wp:tag_name');
	
	$tags[] = array (
		'term_id' => (int) $tagid['0'],
		'tag_slug' => (string) $slug['0'],
		'tag_name' => (string) $name['0']
	);
	
	$tags_xml_temp .= '<tag id="' . $tagnum . '" name="' . strtolower(str_replace(' ' , '-', $name['0'])) . '" name_human="' . $name['0'] . '"/>';
	$tagnum++;
}

foreach($xml->channel->item as $item) 
{
	
	$status = $item->xpath('wp:status');
	$status = $status['0'];
	
	$date = $item->xpath('wp:post_date');
	$date = $date['0'];
	
	$comm = $item->xpath('wp:comment_status');
	$comm = $comm['0'];
	
	$type = $item->xpath('wp:post_type');
	$type = $type['0'];
	
	if ($status == 'trash')
		continue;
		
	foreach ($item->category as $c) 
	{
		$att = $c->attributes();

		if ($att['domain'] == 'post_format') 
		{
			$format = (string) $att['nicename'];
			$post_format = ($format == 'post-format-video') ? 'video' : 'post';
		}
	}
	
	$post_status = ($status == 'draft') ? 'draft' : 'NULL';
	$comment_status = ($comm == 'open') ? '1' : '0';
	
	$type_type = ($post_format == 'video') ? 'video' : 'simple';
	
	$title = $item->title;
	
	$seo = $item->xpath('wp:post_name');
	$seo = $seo['0'];
	
	$content = $item->xpath('content:encoded');
	$content = $content['0'];
	$descr = html_excerpt( $content, 120 ) ;
	$content = str_replace('<!--more-->', '<!-- pagebreak -->', $content);
	$content = nl2p($content);
	$content = htmlspecialchars($content); //htmlentities($content, ENT_IGNORE, "UTF-8");
	
	$net = explode(" ", $date);
	$dt = $net['0'];
	$nedt = explode("-", $dt);
	
	$dt2 = $net['1'];
	$nedt2 = explode(":", $dt2);
	
	$time = mktime($nedt2['0'], $nedt2['1'], $nedt2['2'], $nedt['1'], $nedt['2'], $nedt['0']);
	
	$mdate = str_replace(array('-', ':', ' '), '.', $date);
	
	if ($type == 'post') 
	{
		$postnum++;		
		
		foreach ($item->category as $c) 
		{
			$att = $c->attributes();
			
			if ($att['domain'] == 'category') 
			{
				$cat_name = (string) $c;
				foreach ($categories as $cat) 
				{
					if ($cat['cat_name'] == $cat_name)
						$s_catid = $cat['term_id'];
				}
			} 
			elseif ($att['domain'] == 'post_tag') 
			{
				$tagc = (string) $c;
				foreach ($tags as $tag) 
				{
					if ($tag['tag_name'] == $tagc) 
					{
						echo $tag['tag_name'] . 'is  ' . $postnum . '<br>';
						$tags_links_temp .= '<link id_tag="' . $tag['term_id'] . '" id_post="' . $postnum . '"/>';
					}
				}
			} 
		}
		
		if ($post_format == 'video') 
		{
			if (preg_match('!https?://\S+!', $content, $matches) )
			{
				$url = trim($matches[0]);
				$has_url = 1;
				$content = str_replace($url, '', $content);
				$content = trim($content);
			} 
			else 
			{
				$type_type = 'simple';
				$has_url = 0;
			}
		}
		
		$file_name = $time . '.' . $postnum . '.' . $s_catid . '.0.' . $post_status . '.' . $mdate . '.xml';
				
		$xml_post = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<post><type type="string">' . $type_type . '</type><title type="string">' . $title . '</title><content type="string">' . $content . '</content><description type="string">' . $descr . '</description><allow_comments type="integer">' . $comment_status . '</allow_comments><pub_date type="integer">' . $time . '</pub_date><mod_date type="string">0</mod_date><visits type="string">0</visits>';
	
	if ( ($post_format == 'video') && (!empty($has_url)) )
		$xml_post .= '<video type="string">' . $url . '</video>';
	
	$xml_post .= '</post>';

	writefile($content_folder . $file_name, $xml_post);
	
	$posts_xml_temp .= '<url id="' . $postnum . '" slug="' . $seo . '"></url>';
	
	$comment = $item->xpath('wp:comment');
		
	if ( ($comment) && (!empty($disqus_id)) )
	{
			$com_xml .= '<item><title>' . $title . '</title>
				<link>' . $siteurl . 'post/' . $seo . '/</link>
				<content:encoded><![CDATA[' . $descr . ']]></content:encoded>
				<dsq:thread_identifier>' . $postnum . '</dsq:thread_identifier>
				<wp:post_date_gmt>' . $com['comment_date'] . '</wp:post_date_gmt>
				<wp:comment_status>open</wp:comment_status>';
			$comments = array();
			foreach ($item->xpath('wp:comment') as $c) {
				$author = $c->xpath('wp:comment_author');
				$author_email = $c->xpath('wp:comment_author_email');
				$author_IP = $c->xpath('wp:comment_author_IP');
				$comment_date = $c->xpath('wp:comment_date');
				$comment_content = $c->xpath('wp:comment_content');
				$approved = $c->xpath('wp:comment_approved');
				
				$comments[] = array(
					//'comm_id' => (int) $tagid['0'],
					'author' => (string) $author['0'],
					'author_email' => (string) $author_email['0'],
					'author_IP' => (string) $author_IP['0'],
					'comment_content' => (string) $comment_content['0'],
					'comment_date' => (string) $comment_date['0'],
					'approved' => (int) $approved['0']
				);
			}
			
			foreach ($comments as $com) {
				$commnum++;
			
				$comm_type = (!empty($com['approved'])) ? 'NULL' : 'unapproved';
			
      			$com_xml .= '<wp:comment>
        			<wp:comment_id>' . $commnum . '</wp:comment_id>
					<wp:comment_author>' . $com['author'] . '</wp:comment_author>
					<wp:comment_author_email>' . $com['author_email'] . '</wp:comment_author_email>
					<wp:comment_author_url></wp:comment_author_url>
					<wp:comment_author_IP>' . $com['author_IP'] . '</wp:comment_author_IP>
					<wp:comment_date_gmt>' . $com['comment_date'] . '</wp:comment_date_gmt>
					<wp:comment_content><![CDATA[' . $com['comment_content'] . ']]></wp:comment_content>
					<wp:comment_approved>' . $com['approved'] . '</wp:comment_approved>
					<wp:comment_parent>0</wp:comment_parent>
      			</wp:comment>';
				
			}
		
		
    	$com_xml .= '</item>';
		
		}
	
	}
	
	elseif ($type == 'page') 
	
	{
		$pagenum++;
		$file_name = $pagenum . '.NULL.NULL.' . $post_status . '.' . $mdate . '.xml';
		$xml_post = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<page><title type="string">' . $title . '</title><content type="string">' . $content . '</content><description type="string">' . $descr . '</description><keywords type="string"></keywords><position type="integer">' . $pagenum . '</position><pub_date type="integer">' . $time . '</pub_date><mod_date type="string"></mod_date><visits type="string">0</visits></page>';

		writefile($pages_folder . $file_name, $xml_post);
		$pages_xml_temp .= '<url id="' . $pagenum . '" slug="' . $seo . '"></url>';
		unset($xml_post);
	} 
	
	else
		
		continue;
	
}

$pages_xml = $main_xml;
$pages_xml .= '<pages autoinc="' . ($pagenum + 1) . '"><friendly>';
$pages_xml .= $pages_xml_temp;
$pages_xml .= '</friendly></pages>';
writefile($pages_file, $pages_xml);

$posts_xml = $main_xml;
$posts_xml .= '<post autoinc="' . ($postnum + 1) . '"><friendly>';
$posts_xml .= $posts_xml_temp;
$posts_xml .= '</friendly></post>';
writefile($posts_file, $posts_xml);

//Write Categories file
$categories_xml = $main_xml;
$categories_xml .= '<categories autoinc="' . ($catnum + 1) . '">';
$categories_xml .= $categories_xml_temp;
$categories_xml .= '</categories>';
writefile($categories_file, $categories_xml);

//Write Tags file
$tags_xml = $main_xml;
$tags_xml .= '<tags autoinc="' . ($tagnum + 1) . '"><list>';
$tags_xml .= $tags_xml_temp;
$tags_xml .= '</list><links>';
$tags_xml .= $tags_links_temp;
$tags_xml .= '</links></tags>';
writefile($tags_file, $tags_xml);

if (!empty($disqus_id)) 
{
	//Write Disqus file
	$com_xml .= '</channel>
	</rss>';
	writefile('disqus.xml', $com_xml);
}
############################################################################################

//Functions

function html_excerpt( $str, $count, $more = null ) {
	if ( null === $more )
		$more = '';
	$str = strip_all_tags( $str, true );
	$excerpt = mb_substr( $str, 0, $count );
	// remove part of an entity at the end
	$excerpt = preg_replace( '/&[^;\s]{0,6}$/', '', $excerpt );
	if ( $str != $excerpt )
		$excerpt = trim( $excerpt ) . $more;
	return $excerpt;
}

function strip_all_tags($string, $remove_breaks = false) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags($string);
	
	if ( $remove_breaks )
		$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	return trim( $string );
}

function writefile($folder, $data) {
   	$fp = fopen($folder, 'w');
	fwrite($fp, $data);
	fclose($fp);
}

function seo($text) {
    $url = preg_replace('/[^a-zA-Z0-9 *]/', '', $text);
    $url = str_replace(array('/', '*', ' ', '.', '--'), array('', '', '-', '-', '-'), $url);
    $url = strtolower($url);
	if (strlen($url > 40))
		$url = substr($url, 0, 40);
    return $url;
}

function nl2p($string, $line_breaks = true, $xml = true) {
	$text = trim($string);
	$text = preg_replace('~(\r\n|\n){2,}|$~', "\001", $text);

	# convert remaining (i.e. single) newlines into html br's
	$text = nl2br($text);

	# finally, replace SOH chars with paragraphs
	$text = preg_replace('/(.*?)\001/s', "<p>$1</p>\n", $text); 
	return $text; 
}
?>
