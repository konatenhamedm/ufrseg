<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EntrepriseFixtures extends Fixture
{
    public const DEFAULT_ENTEPRISE_REFERENCE = 'default-entreprise';
    public function load(ObjectManager $manager): void
    {
        $entreprise = new Entreprise();
        $entreprise->setDenomination('Default');
        $entreprise->setCode('ENT1');
        // $product = new Product();
        $manager->persist($entreprise);

        $manager->flush();

        $this->addReference(self::DEFAULT_ENTEPRISE_REFERENCE, $entreprise);
    }
}
