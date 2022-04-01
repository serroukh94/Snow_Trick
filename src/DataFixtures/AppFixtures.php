<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\Figures;
use App\Entity\Images;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        $grabs = new Category();
        $grabs->setName('GRABS');
        $manager->persist($grabs);

        $rotation = new Category();
        $rotation->setName('ROTATION');
        $manager->persist($rotation);

        $flips = new Category();
        $flips->setName('FLIPS');
        $manager->persist($flips);

        $this->addReference('category-grabs', $grabs);
        $this->addReference('category-rotation', $rotation);
        $this->addReference('category-flips', $flips);


        $grabsCategory = $this->getReference('category-grabs');
        $rotationCategory = $this->getReference('category-rotation');
        $flipsCategory = $this->getReference('category-flips');


        // creation fake user

        $users = [];


        for($i = 1; $i <= 6; $i++) {


            $user = new User();


            $this->hasher->hashPassword($user, 'password');

            $user->setFirstName($faker->firstname)
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setPassword($faker->password)
                ->setIsVerified(isVerified: true);

            $manager->persist($user);
            $users[] = $user;
        }

        $figure = [];
        for ($j = 1; $j < 6; $j++) {
        $figures =new Figures();
        $figures->setTitle("alpe")
            ->setDescription("Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n'a pas fait que survivre cinq siècles, mais s'est aussi adapté à la bureautique informatique, sans que son contenu n'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.")
            ->setCategory($grabsCategory);



        for($j = 1; $j <= mt_rand(1,4); $j++ ){

            $images = new Images();
            $images->setName('alpe.jpg')
                   ->setFigures($figures);

            $manager->persist($images);
        }


        for($v = 1; $v <= mt_rand(1,3); $v++){
            $video = new Video();
            $video->setUrl("https://www.youtube.com/embed/rvBXbmBCt0I")
                  ->setFigure($figures);

            $manager->persist($video);
        }

        for($c = 1; $c <= mt_rand(100,150); $c++){
            $user = $users[mt_rand(0, count($users) - 1)];
            $comments = new Comments();
            $comments->setUser($user)
                ->setContent($faker->sentence)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setFigure($figures);
            $manager->persist($comments);
        }
        $manager->persist($figures);
            $figure[] = $figures;

        }

        $manager->flush();
    }


}
