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

//You have to put this file in the main dir folder

header("Content-type: text/html; charset=UTF-8");
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('memory_limit', '128M');
set_time_limit(0); //ini_set("max_execution_time", "200");
//error_reporting(0);
$dir = getcwd() . '/';
$error = '';
$message = '';

//Change it if you want to set different timezone
date_default_timezone_set("America/Los_Angeles");

//Find the url
$url = get_url(); 
$siteurl = $url['path'];

$content_folder = $dir . 'content/public/posts/';
$comment_folder = $dir . 'content/public/comments/';
$pages_folder = $dir . 'content/public/pages/';
$posts_file = $dir . 'content/private/posts.xml';
$tags_file = $dir . 'content/private/tags.xml';
$categories_file = $dir . 'content/private/categories.xml';
$pages_file = $dir . 'content/private/pages.xml';
$comments_file = $dir . 'content/private/comments.xml';
//$images_folder = $dir . 'content/public/upload/';

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
	
//XMLs
$main_xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
$pages_xml_temp = '';
$posts_xml_temp = '';
$tags_xml_temp = '';
$categories_xml_temp = '';
$comments_xml_temp = '';
$tags_links_temp = '';
$com_xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$com_xml .= '<rss version="2.0"' . PHP_EOL;
$com_xml .= 'xmlns:content="http://purl.org/rss/1.0/modules/content/"' . PHP_EOL;
$com_xml .= 'xmlns:dsq="http://www.disqus.com/"' . PHP_EOL;
$com_xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/"' . PHP_EOL;
$com_xml .= 'xmlns:wp="http://wordpress.org/export/1.0/"' . PHP_EOL;
$com_xml .= '>' . PHP_EOL;
$com_xml .= '<channel>' . PHP_EOL;
##############################################

//HTML
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="el">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Wordpress to Nibbleblog Converter</title>
	<style>html{background:#f7f7f7;}body{background:#fff;color:#333;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;margin:2em auto 0 auto;width:700px;padding:1em 2em;-moz-border-radius:11px;-khtml-border-radius:11px;-webkit-border-radius:11px;border-radius:11px;border:1px solid #dfdfdf;}a{color:#2583ad;text-decoration:none;}a:hover{color:#d54e21;}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px Georgia,"Times New Roman",Times,serif;margin:5px 0 0 -4px;padding:0;padding-bottom:7px;}h2{font-size:16px;}p,li{padding-bottom:2px;font-size:12px;line-height:18px;}code{font-size:13px;}ul,ol{padding:5px 5px 5px 22px;}.submit input,.button,.button-secondary{font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;text-decoration:none;font-size:14px!important;line-height:16px;padding:6px 12px;cursor:pointer;border:1px solid #bbb;color:#464646;-moz-border-radius:15px;-khtml-border-radius:15px;-webkit-border-radius:15px;border-radius:15px;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;-khtml-box-sizing:content-box;box-sizing:content-box;}.button:hover,.button-secondary:hover,.submit input:hover{color:#000;border-color:#666;}.form-table{border-collapse:collapse;margin-top:1em;width:100%;}.form-table td{margin-bottom:9px;padding:10px;border-bottom:8px solid #fff;font-size:12px;}.form-table th{font-size:13px;text-align:left;padding:16px 10px 10px 10px;border-bottom:8px solid #fff;width:110px;vertical-align:top;}.form-table tr{background:#f3f3f3;}.form-table code{line-height:18px;font-size:18px;}.form-table p{margin:4px 0 0 0;font-size:11px;}.form-table input{line-height:20px;font-size:15px;padding:2px;}#error-page{margin-top:50px;}#error-page p{font-size:12px;line-height:18px;margin:25px 0 20px;}#error-page code{font-family:Consolas,Monaco,Courier,monospace;}</style>
</head>
<?php
if (!$_POST['run']) { ?>
<body id="page">
<?php if (file_exists($dir . 'locked')) echo '<p>Remove the file "locked" before you can continue with the conversion.</p>'; ?>
	<form method="post" action="<?=$url['full'];?>">
 <p>wordpress' xml filename (ex filename.xml)   
 	<input type="text" name="xml_file" size="30" maxlength="80">
 </p>
 
 <p>Your discus ID (leave it empty if you don't want to convert comments)
 	<input type="text" name="disqus_id" size="10" maxlength="20">
 </p>

 <p>
 	<input type="submit" value="Convert" name="run">
 </p>
 </form>
</body>
</html>
<?php 
} 
else 
{	
	$disqus_id = (empty($_POST['disqus_id'])) ? '' : trim($_POST['disqus_id']);
	$xml_file = trim($_POST['xml_file']);
	
	if (file_exists($dir . 'locked')) 
	{
		echo '<body id="error-page">
		<p><strong>An error occurred and the conversion could not be completed</strong>:<br />
		The converter is locked. Remove the file "locked" and try again. <a href="javascript: history.go(-1)">Go Back</a></p>
		</body>
		</html>';
		
		exit;
	}
	
	if (!file_exists($dir . $xml_file) || (empty($xml_file)) ) 
	{
		echo '<body id="error-page">
		<p><strong>An error occurred and the conversion could not be completed</strong>:<br />
		No such file. Check the XML filename and try again. <a href="javascript: history.go(-1)">Go Back</a></p>
		</body>
		</html>';
		
		exit;
	}
	
	$xml = simplexml_load_string(file_get_contents($xml_file));

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
		
		$type_type = (!empty($post_format) && $post_format == 'video') ? 'video' : 'simple';

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
		
		if (empty($time))
			$time = time();
		
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
							$tags_links_temp .= '<link id_tag="' . $tag['term_id'] . '" id_post="' . $postnum . '"/>';
						}
					}
				} 
			}
			
			//Find if any Youtube video is here and replace the content
			if( ($type_type == 'video') && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content, $matches) )
			{
				$video_url = 'https://www.youtube.com/watch?v=' . trim($matches[1]);
				$yt_id = trim($matches[1]);
				$content = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=$yt_id([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "", $content);
				$descr = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=$yt_id([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "", $descr);
			}
			else 
			{
				$video_url = '';
			}
			
			$file_name = $time . '.' . $postnum . '.' . $s_catid . '.0.' . $post_status . '.' . $mdate . '.xml';
			
			//Build the XML Array
			$xml_post = array
			 (
				 'type' => array("data" => $type_type, "type" => 'string'),
				 'title' => array("data" => $title, "type" => 'string'),
				 'content' => array("data" => $content, "type" => 'string'),
				 'description' => array("data" => $descr, "type" => 'string'),
				 'allow_comments' => array("data" => $comment_status, "type" => 'integer'),
				 'pub_date' => array("data" => $time, "type" => 'integer'),
				 'mod_date' => array("data" => '0', "type" => 'integer'),
				 'video' => array("data" => $video_url, "type" => 'string'),
				 'visits' => array("data" => '1', "type" => 'integer')
			 );
			 
			 add_xml_file ($content_folder . $file_name, $xml_post);
			 
			 unset($xml_post);
		
			$posts_xml_temp .= '<url id="' . $postnum . '" slug="' . $seo . '"></url>';
		
			$comment = $item->xpath('wp:comment');
			
		if ( ($comment) && (!empty($disqus_id)) )
		{
				$com_xml .= '<item><title>' . $title . '</title>' . PHP_EOL;
				$com_xml .= '<link>' . $siteurl . 'post/' . $seo . '/</link>' . PHP_EOL;
				$com_xml .= '<content:encoded><![CDATA[' . $descr . ']]></content:encoded>' . PHP_EOL;
				$com_xml .= '<dsq:thread_identifier>' . $postnum . '</dsq:thread_identifier>' . PHP_EOL;
				$com_xml .= '<wp:post_date_gmt>' . $com['comment_date'] . '</wp:post_date_gmt>' . PHP_EOL;
				$com_xml .= '<wp:comment_status>open</wp:comment_status>' . PHP_EOL;
				
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
				
					$com_xml .= '<wp:comment>' . PHP_EOL;
					$com_xml .= '<wp:comment_id>' . $commnum . '</wp:comment_id>' . PHP_EOL;
					$com_xml .= '<wp:comment_author>' . $com['author'] . '</wp:comment_author>' . PHP_EOL;
					$com_xml .= '<wp:comment_author_email>' . $com['author_email'] . '</wp:comment_author_email>' . PHP_EOL;
					$com_xml .= '<wp:comment_author_url></wp:comment_author_url>' . PHP_EOL;
					$com_xml .= '<wp:comment_author_IP>' . $com['author_IP'] . '</wp:comment_author_IP>' . PHP_EOL;
					$com_xml .= '<wp:comment_date_gmt>' . $com['comment_date'] . '</wp:comment_date_gmt>' . PHP_EOL;
					$com_xml .= '<wp:comment_content><![CDATA[' . $com['comment_content'] . ']]></wp:comment_content>' . PHP_EOL;
					$com_xml .= '<wp:comment_approved>' . $com['approved'] . '</wp:comment_approved>' . PHP_EOL;
					$com_xml .= '<wp:comment_parent>0</wp:comment_parent>' . PHP_EOL;
					$com_xml .= '</wp:comment>' . PHP_EOL;
					
				}
			
			
			$com_xml .= '</item>';
			
			}
		
		}
		
		elseif ($type == 'page') 
		
		{
			$pagenum++;
			$file_name = $pagenum . '.NULL.NULL.' . $post_status . '.' . $mdate . '.xml';
			
			//Build the XML Array
			$xml_post = array
			 (
				 'title' => array("data" => $title, "type" => 'string'),
				 'content' => array("data" => $content, "type" => 'string'),
				 'description' => array("data" => $descr, "type" => 'string'),
				 'position' => array("data" => $pagenum, "type" => 'integer'),
				 'pub_date' => array("data" => $time, "type" => 'integer'),
				 'mod_date' => array("data" => '0', "type" => 'string'),
				 'visits' => array("data" => '1', "type" => 'string')
			 );

			add_xml_file ($pages_folder . $file_name, $xml_post, 'page');
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
	
	echo '<body id="page">
		<p><strong>Success</strong>: The conversion has been finished successfully. <a href="' . $siteurl . '">Go to your site</a></p>
		</body>
		</html>';
	
	 file_put_contents($dir . 'locked', 'NULL');	
}


############################################################################################

//Functions
function get_url ()
{
	$url = array();
	$uri = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$uri = parse_url($uri);
	$url['full'] = $uri['scheme'] . '://' . $uri['host'] . $uri['path'];
	$url['path'] = $uri['scheme'] . '://' . $uri['host'] . str_replace('import.php', '', $uri['path']);
	
	return $url;
}

function add_xml_file ($file, $attrs, $type = 'post') 
{
	$library = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><' . $type . '/>');
	
	foreach ($attrs as $data => $row) {
		$data = $library->addChild($data, $row['data']);
		$data->addAttribute('type', $row['type']);
	}
	
	$library->asXML($file);
	
}

function html_excerpt( $str, $count, $more = null ) 
{
	//function taken from Wordpress
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

function strip_all_tags($string, $remove_breaks = false) 
{
	//function taken from Wordpress
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags($string);
	
	if ( $remove_breaks )
		$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	return trim( $string );
}

function writefile($folder, $data) 
{
   	$fp = fopen($folder, 'w');
	fwrite($fp, $data);
	fclose($fp);
}

function seo($text) 
{
    $url = preg_replace('/[^a-zA-Z0-9 *]/', '', $text);
    $url = str_replace(array('/', '*', ' ', '.', '--'), array('', '', '-', '-', '-'), $url);
    $url = strtolower($url);
	if (strlen($url > 40))
		$url = substr($url, 0, 40);
    return $url;
}

function nl2p($string, $line_breaks = true, $xml = true) 
{
	$text = trim($string);
	$text = preg_replace('~(\r\n|\n){2,}|$~', "\001", $text);

	# convert remaining (i.e. single) newlines into html br's
	$text = nl2br($text);

	# finally, replace SOH chars with paragraphs
	$text = preg_replace('/(.*?)\001/s', "<p>$1</p>\n", $text); 
	return $text; 
}
?>
