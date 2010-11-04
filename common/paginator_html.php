<?php 
// ==================================================================
//  Author: Ted Kappes (pesoto74@soltec.net)
//	Web: 	http://tkap.org/paginator/
//	Name: 	Paginator_html
// 	Desc: 	Class extension for Paginator. Adds pre-made link sets.
//
// 7/21/2003
//
//  Please send me a mail telling me what you think of Paginator
//  and what your using it for. [ pesoto74@soltec.net]
//
// ==================================================================

class Paginator_html extends Paginator {

	var $pageVars;

	function initVars() {
		$this->pageVars = array();
	}

	function setVar($var, $val) {
		$this->pageVars[$var] = $val;
	}

	function getVar($var) {
		return $this->pageVars[$var];
	}

	//outputs a link set like this 1 of 4 of 25 First | Prev | Next | Last |
	function pageLinks() {
		if($this->getCurrent()==1) {
			$first = "";
		} else {
			$first="<a href=\"playlist.php?limit=25\" title=\"First Page\" class=\"pagerw\">&laquo;</a>";
			//$first="<span class=\"pageLinkFirst\"><a href=\"playlist.php?limit=25\" title=\"First Page\">&laquo;</a></span>";
		}

		if($this->getPrevious()) {
			$prev = "<a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$this->getPrevious()."&#038;limit=".$this->limit."\" title=\"Previous Page\" class=\"pagerw\">&lt;</a>";
			//$prev = "<span class=\"pageLinkPrev\"><a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$this->getPrevious()."&#038;limit=".$this->limit."\" title=\"Previous Page\">&lt;</a></span>";
		} else {
			$prev="";
		}

		if($this->getNext()) {
			$next = "<a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$this->getNext()."&#038;limit=".$this->limit."\" title=\"Next Page\" class=\"pageff\">&gt;</a>";
			//$next = "<span class=\"pageLinkNext\"><a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$this->getNext()."&#038;limit=".$this->limit."\" title=\"Next Page\">&gt;</a></span>";
		} else {
			$next = "";
		}

		if($this->getLast()) {
			$last = "<a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$this->getLast()."&#038;limit=".$this->limit."\" title=\"Last Page\" class=\"pageff\">&raquo;</a>";
			//$last = "<span class=\"pageLinkLast\"><a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$this->getLast()."&#038;limit=".$this->limit."\" title=\"Last Page\">&raquo;</a></span>";
		} else {
			$last = "";
		}

		$links = $this->getLinkArr();
		$rowlinks = "";
		foreach($links as $link) {
			if($link == $this->getCurrent()) {
				$rowlinks .= "<a href=\"\" class=\"curnum\">$link</a>";
				//$rowlinks .= "<span class=\"pageCurrent\">$link</span>";
			} else {
				$rowlinks .= "<a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$link."&#038;limit=".$this->limit."\" title=\"Page $link\" class=\"num\">$link</a>";
				//$rowlinks .= "<span class=\"pageLink\"><a href=\"playlist.php?search=".$this->pageVars['search']."&#038;filter=".$this->pageVars['filter']."&#038;page=".$link."&#038;limit=".$this->limit."\" title=\"Page $link\">$link</a></span>";
			}
		}

		//echo $this->getFirstOf() . " of " .$this->getSecondOf() . " of " . $this->getTotalItems() . " ";
		//echo $first . " " . $prev . " " . $next . " " . $last;
		//echo "<span class=\"pageCount\">".$this->getTotalPages()." Pages</span>".$first.$prev.$rowlinks.$next.$last;
		echo $first.$prev.$rowlinks.$next.$last;
	}
}//ends class
?>