<?php


namespace App\DataFixtures;


use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface

{
    const ACTORS = [
        'Tom Hanks',
        'Harrison Ford',
        'Will Smith',
        'Morgan Freeman',
        'Samuel L. Jackson',
        'Johnny Deep',
        'Denzel Washington',
        'Leonardo DiCaprio',
    ];

    public function load(ObjectManager $manager)
    {
        $i = 0;
        $slugify = new Slugify();
        foreach (self::ACTORS as $key => $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);
            $actor->setSlug($slugify->generate($actorName));
            $manager->persist($actor);
            $this->setReference('program_' . $i, $actor);
            $i++;
        }
        $manager->flush();

        $this->loadData($manager);
    }

    public function loadData(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();
        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->setSlug($slugify->generate($faker->name));
            $manager->persist($actor);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}