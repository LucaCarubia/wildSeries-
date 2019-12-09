<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();
        for ($i = 0; $i < 50; $i++) {
            $episode = new Episode();
            $episodeTitle = $faker->realText(32);
            $episode->setSeason($this->getReference('season_' . random_int(0, 49)));
            $episode->setTitle($faker->title);
            $episode->setSlug($slugify->generate($episodeTitle));
            $episode->setNumber($faker->randomNumber());
            $episode->setSynopsis($faker->title);
            $manager->persist($episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

}