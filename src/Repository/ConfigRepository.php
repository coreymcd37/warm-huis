<?php

namespace One\CheckJeHuis\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use One\CheckJeHuis\Entity\Config;
use One\CheckJeHuis\Entity\ConfigCategory;
use One\CheckJeHuis\Entity\ConfigTransformation;
use One\CheckJeHuis\Entity\House;

class ConfigRepository extends EntityRepository
{
    /**
     * @return ConfigCategory[]
     */
    public function getAllCategories()
    {
        $qb = $this->_em->createQueryBuilder();

        /** @var Config[] $configs */
        $query = $qb
            ->select('cat, conf, trans')
            ->from(ConfigCategory::class, 'cat')
            ->leftJoin('cat.configs', 'conf')
            ->leftJoin('conf.transformations', 'trans')
            ->orderBy('cat.ordering')
        ;

        /** @var ConfigCategory[] $cats */
        $cats = $query->getQuery()->getResult();

        $formatted = array();
        foreach ($cats as $c) {
            $formatted[$c->getSlug()] = $c;
        }

        return $formatted;
    }

    /**
     * @param House $house
     * @return \One\CheckJeHuis\Entity\ConfigCategory[]
     */
    public function getAllCategoriesForHouse(House $house)
    {
        $ignoreCategories = array();
        if ($house->hasElectricHeating()) {
            $ignoreCategories[] = ConfigCategory::CAT_HEATING;
        } else {
            $ignoreCategories[] = ConfigCategory::CAT_HEATING_ELEC;
        }

        /** @var Config[] $configs */
        $qb = $this->_em->createQueryBuilder();

        $query = $qb
            ->select(array('cat', 'conf', 'trans'))
            ->from(ConfigCategory::class, 'cat')
            ->leftJoin('cat.configs', 'conf')
            ->leftJoin('conf.transformations', 'trans')
            ->where($qb->expr()->notIn('cat.slug', $ignoreCategories))
            ->orderBy('cat.ordering')
        ;

        /** @var ConfigCategory[] $cats */
        $cats = $query->getQuery()->getResult();

        $formatted = array();
        foreach ($cats as $c) {
            $formatted[$c->getSlug()] = $c;
        }

        return $formatted;
    }

    /**
     * @param $id
     * @return ConfigCategory
     */
    public function getCategory($id)
    {
        return parent::find($id);
    }

    /**
     * @param $id
     * @return Config
     */
    public function getConfig($id)
    {
        return parent::find($id);
    }

    /**
     * Updates a matrix value if the value is set to 0, the transformation is removed
     *
     * @param ConfigTransformation $configTransformation
     * @return $this
     */
    public function updateConfigTransformation(ConfigTransformation $configTransformation)
    {
        if ($configTransformation->getValue() == 0) {
            $this->_em->remove($configTransformation);
        } else {
            $this->_em->persist($configTransformation);
        }
        $this->_em->flush();

        return $this;
    }

    /**
     * removes transformations with the same start and end config
     */
    public function removeDuplicateTransformations()
    {
        /**
         * @var Config[] $configs
         */
        $configs = $this->findAll();

        foreach ($configs as $c) {
            $transformations = $c->getTransformations();
            $to = [];
            $toInverse = [];
            foreach ($transformations as $t) {
                $conf = $t->getToConfig()->getId();
                if ($t->isInverse()) {
                    if (in_array($conf, $toInverse, true)) {
                        $this->_em->remove($t);
                        continue;
                    }
                    $toInverse[] = $conf;
                } else {
                    if (in_array($conf, $to, true)) {
                        $this->_em->remove($t);
                        continue;
                    }
                    $to[] = $conf;
                }
            }
        }

        $this->_em->flush();
    }
}
