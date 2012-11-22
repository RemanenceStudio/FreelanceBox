<?php

/**
 * @author 23rd and Walnut
 * @copyright 2010
 */

function pagination_links($current_page, $total_pages, $base_url)
{	
	//No need for pagination if there is only one page
	if($total_pages <=1)
	{
		return '';
	}
	
	$links='<div class="pagination"><ul class="clearfix">';
	
	//make sure we have a value for current page
	if (!isset($current_page) || $current_page == '')
	{
		$current_page=1;
	}
		
	//set up the previous link and disable if on current page
	if($current_page <= 1)
	{
		$class="disabled";
		$href="";
        $element = "div";
	}
	else
	{
		$class="";
		$href="href='".$base_url.($current_page-1)."'";
        $element = "a";
	}
	
	$links .= "<li class='$class'><$element $href >&laquo; Prev</$element></li>";
	
	//create the pagination links
	if($current_page <= 8)
	{
		for($i=1; $i<=10 && $i <=$total_pages; $i++)
		{
			$links .= build_link($i, $current_page, $base_url);
		}
		
		if($total_pages >= 8)
			$links .= "<span class='gap'>...</span>";
			
		for($i=$total_pages-1; $i > ($current_page + 4) && $i <= $total_pages; $i++)
		{
			$links .= build_link($i, $current_page, $base_url);
		}
	}
	else if($current_page > 8 && $current_page <= $total_pages)
	{
		$links.= "<li><a href='".$base_url."1'>1</a> <a href='".$base_url."2'>2</a> <span class='gap'>...</span></li>";
		
		for($i=$current_page-5; $i<=($current_page+4) && $i <=$total_pages; $i++)
		{
			$links .= build_link($i, $current_page, $base_url);
		}
		
		if (($current_page+5) < $total_pages)
		{
			$links .= "<span class='gap'>...</span>";
			
			for($i=$total_pages-1; $i > ($current_page + 4) && $i <= $total_pages; $i++)
			{
				$links .= build_link($i, $current_page, $base_url);
			}
		}
	}
	
	
	//set up the next link	
	if($current_page >= $total_pages)
	{
		$class="disabled";
		$href="";
         $element = "div";
	}
	else
	{
		$class="";
		$href="href='".$base_url.($current_page+1)."'";
         $element = "a";
	}
	
	$links .= "<li class='$class'><$element $href >Next &raquo;</$element></li>";
	$links .= "</ul></div>";
	
	return $links;
}

function build_link($page_num, $current_page, $base_url)
{
	$class = ($page_num == $current_page) ? "class='current'" : "";
	return "<li $class><a href='".$base_url.$page_num."'>".$page_num."</a></li>";
}

?>