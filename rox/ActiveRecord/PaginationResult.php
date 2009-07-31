<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Pagination Result
 *
 * @package Rox
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_ActiveRecord_PaginationResult extends ArrayObject {

	/**
	 * Total of pages
	 * 
	 * @var integer
	 */
	protected $_pages;

	/**
	 * The current page
	 * 
	 * @var integer
	 */
	protected $_currentPage;

	/**
	 * Next page number
	 *
	 * @var integer
	 */
	protected $_nextPage;

	/**
	 * Previous page number
	 *
	 * @var integer
	 */
	protected $_previousPage;

	/**
	 * Total of entries
	 *
	 * @var string
	 */
	protected $_totalEntries;

	/**
	 * Constructor
	 * 
	 * @param array $array
	 * @param integer $pages
	 * @param integer $currentPage
	 * @param integer $nextPage
	 * @param integer $previousPage
	 * @param integer $totalEntries
	 * @return void
	 */
	public function __construct($array, $pages, $currentPage, $nextPage, $previousPage, $totalEntries) {
		parent::__construct($array);
		$this->_pages = $pages;
		$this->_currentPage = $currentPage;
		$this->_nextPage = $nextPage;
		$this->_previousPage = $previousPage;
		$this->_totalEntries = $totalEntries;
	}

	/**
	 * Returns the total of pages.
	 * 
	 * @return integer
	 */
	public function getPages() {
		return $this->_pages;
	}

	/**
	 * Returns the current page.
	 * 
	 * @return integer
	 */
	public function getCurrentPage() {
		return $this->_currentPage;
	}

	/**
	 * Returns the next page number.
	 *
	 * @return integer
	 */
	public function getNextPage() {
		return $this->_nextPage;
	}

	/**
	 * Returns the previous page numner.
	 *
	 * @return integer
	 */
	public function getPreviousPage() {
		return $this->_previousPage;
	}

	/**
	 * Returns the total of entries
	 *
	 * @return integer
	 * @author ramon
	 */
	public function getTotalEntries() {
		return $this->_totalEntries;
	}
}
