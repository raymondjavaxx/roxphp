<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\template\helper;

use \rox\active_record\PaginationResult;

/**
 * Pagination Helper
 *
 * @package Rox
 */
class Pagination extends \rox\template\Helper {

	/**
	 * Generates the pagination nav
	 * 
	 * @param \rox\active_record\PaginationResult $collection
	 * @param array $options
	 * @return string
	 */
	public function links(PaginationResult $collection, $options = array()) {
		$defaults = array(
			'class'          => 'pagination',
			'next_class'     => 'next-page',
			'prev_class'     => 'prev-page',
			'prev_label'     => '&laquo; Previous',
			'next_label'     => 'Next &raquo;',
			'max_items'      => 8,
			'link_separator' => ' '
		);

		$options += $defaults;
		$currentPage = $collection->getCurrentPage();

		$output = array();

		if ($collection->getPreviousPage() != $currentPage) {
			$output[] = $this->_link($collection->getPreviousPage(), $options['prev_label'], array(
				'rel' => 'prev',
				'class' => $options['prev_class']
			));
		}

		$start = max(1, $currentPage - floor($options['max_items'] / 2));
		$end = min($collection->getPages(), $options['max_items'] + $start - 1);

		if ($start > 1) {
			$output[] = $this->_link(1);
			$output[] = '<span>...</span>';
		}

		for ($i = $start; $i<=$end; $i++) {
			$output[] = ($i == $currentPage) ? $this->_tag('em', $currentPage) : $this->_link($i);
		}

		if ($end < $collection->getPages()) {
			$output[] = '<span>...</span>';
			$output[] = $this->_link($collection->getPages());
		}

		if ($collection->getNextPage() != $currentPage) {
			$output[] = $this->_link($collection->getNextPage(), $options['next_label'], array(
				'rel' => 'next',
				'class' => $options['next_class']
			));
		}

		$output = implode($options['link_separator'], $output);
		return $this->_tag('div', $output, array('class' => $options['class']));
	}

	/**
	 * Generates a pagination link
	 *
	 * @param integer $page 
	 * @param string $text 
	 * @param string $rel
	 * @return string
	 */
	protected function _link($page, $text = false, $attributes = array()) {
		if ($text === false) {
			$text = (string)$page;
		}

		$vars = array_merge($_GET, array('page' => $page));
		unset($vars['route']);
		$href = '?' . http_build_query($vars);

		return $this->_tag('a', $text, compact('href') + $attributes);
	}
}
