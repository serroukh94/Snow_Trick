<?php

namespace App\DataFixtures;

use App\Entity\Figures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $f1 =new Figures();
        $f1->setTitle("alpe")
            ->setSlug("alpe")
            ->setDescription("lorem")
            ->setImage("assets/uploads/alpe.jpg");

        $manager->persist($f1);


        $f2 =new Figures();
        $f2->setTitle("mountain")
            ->setSlug("mountain")
            ->setDescription("lorem")
            ->setImage("assets/uploads/mountain_ski.jpg");


        $manager->persist($f2);

        $manager->flush();
    }
}
