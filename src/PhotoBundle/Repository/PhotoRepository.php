<?php

namespace PhotoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PhotoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PhotoRepository extends EntityRepository
{
    public function getLastPhotos() {
        return $this->findBy(array(), array('dateCreate' => 'DESC'), 20);
    }
}
