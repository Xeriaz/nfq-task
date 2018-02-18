<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
	public function searchBooksByTitle($title)
	{
		$qb = $this->createQueryBuilder('book');

		$qb
			->where($qb->expr()->like('book.title', ':title'))
			->setParameter('title', sprintf('%%%s%%', $title));

		return $qb->getQuery()->getResult();
	}
}
