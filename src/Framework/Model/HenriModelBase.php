<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 13-12-2019 16:05
 */

namespace Henri\Framework\Model;

use Henri\Framework\Model\Entity\EntityManagerSingle;
use Henri\Framework\Model\Entity\EntityManagerList;

class HenriModelBase
{

	/**
	 * @var EntityManagerSingle $entityManager
	 */
	protected $entityManagerSingle;

	/**
	 * @var EntityManagerList $entityManagerList
	 */
	protected $entityManagerList;

	/**
	 * HenriModelBase constructor.
	 *
	 * @param EntityManagerSingle $entityManagerSingle
	 * @param EntityManagerList   $entityManagerList
	 */
	public function __construct(
			EntityManagerSingle $entityManagerSingle,
			EntityManagerList $entityManagerList
	) {
		$this->entityManagerSingle  = $entityManagerSingle;
		$this->entityManagerList    = $entityManagerList;
	}
}