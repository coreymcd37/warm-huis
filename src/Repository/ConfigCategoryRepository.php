<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\ConfigCategory;

class ConfigCategoryRepository extends EntityRepository
{
    /**
     * @param string $slug
     * @return ConfigCategory
     */
    public function getCategoryBySlug($slug)
    {
        return parent::findOneBy(array('slug' => $slug));
    }

    /**
     * Updates the category's percentage, aka it's weight in the calculations
     *
     * @param string $categorySlug
     * @param float $percent
     * @throws EntityNotFoundException
     */
    public function updateCategoryPercentBySlug($categorySlug, $percent)
    {
        $category = $this->findOneBy(array('slug' => $categorySlug));

        if (!$category) {
            throw new EntityNotFoundException('no config category found for slug: ' . $categorySlug);
        }

        $category->setPercent($percent);
        $this->_em->persist($category);
        $this->_em->flush();
    }
}
