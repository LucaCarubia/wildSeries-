<?php
namespace App\DataFixtures;
use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Program;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 1; $i <= 1000; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }
        for ($i = 1; $i <= 1000; $i++) {
            $program = new Program();
            $program->setTitle($faker->sentence(4, true));
            $program->setSummary($faker->text(100));
            $program->setCategory($this->getReference('category_' . rand(1, 1000)));
            $program->setPoster($faker->word);
            $slugify = new Slugify();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $this->addReference('program_' . $i, $program);
            $manager->persist($program);
            for ($j = 1; $j <= 5; $j++) {
                $actor = new Actor();
                $actor->setName($faker->firstName);
                $slugify = new Slugify();
                $slug = $slugify->generate($actor->getName());
                $actor->setSlug($slug);
                $actor->addProgram($this->getReference('program_' . $i));
                $manager->persist($actor);
            }
        }
        $manager->flush();
    }
}