<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-image_gallery_rss.php,v 1.22.2.4 2005/04/23 23:30:30 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/imagegals/imagegallib.php');
require_once ('lib/rss/rsslib.php');

if ($rss_image_gallery != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_image_gallery != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["galleryId"])) {
        $errmsg=tra("No galleryId specified");
        require_once ('tiki-rss_error.php');
}

$feed = "imggal";
$tmp = $imagegallib->get_gallery($_REQUEST["galleryId"]);
$title = tra("Tiki RSS feed for the image gallery: ").$tmp["name"];
$now = date("U");
$desc = $tmp["description"]; // TODO: make configurable
$id = "imageId";
$titleId = "name";
$descId = "description";
$authorId = "user";
$dateId = "created";
$readrepl = "tiki-browse_image.php?imageId=%s";
$uniqueid = "$feed.id=".$_REQUEST["galleryId"];

$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
if ($tmp<>'') $title = $tmp;
$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
if ($desc<>'') $desc = $tmp;

$changes = $imagegallib->get_images( 0,$max_rss_image_gallery,$dateId.'_desc', '', $_REQUEST["galleryId"]);
$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
