<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class EventFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $school = new User();
        $etudiant = new User();

        $school->setEmail("test@test.com")
            ->setPassword($this->encoder->encodePassword($school,"test"))
            ->setMobilePhone("0558")
            ->setName("Itkane")
            ->setBirthdayDate(new  \DateTime())
            ->setLocality("cherga")
            ->setCountry("Algeria")
            ->setType(0)
            ->setAdress("Rue Hassnaoui")
            ->setLng(54.0);
        $school->setLat(58.0);


        $etudiant = $school;
        $etudiant->setRelatedSchool($school)
            ->setPassword($this->encoder->encodePassword($etudiant,"test"))
            ->setType(1)
            ->setScholarLevel(5);
        $manager->persist($school);
        $manager->persist($etudiant);


        /*
         * Creating events and products
         */
        for ($i = 1; $i < 15 ; $i++) {
            $event = new Event();
            $product = new Product();

            $product->setCoverImage("e")
                ->setName("Brain Game Festival")
                ->setCoverImage(" ")
                ->setLogo("dd")
                ->setImages("d")->setDescription("Test");

            $date = new \DateTime();




            $event->setName("Nom d'un evenement")
                ->setPrice(150)
                ->setProduct($product)
                ->setDescription("Test test test")
                ->setReservationPlaces(100)
                ->setReservations(50)
                ->setState(0)
                ->setDeadlineDate($date)
                ->setSchool($school)
                ->setEventDateTime($date);

            $manager->persist($event);
            $manager->persist($product);

        }




        $manager->flush();
    }
}
