<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Product;
use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i < 15 ; $i++) {
            $event = new Event();
            $product = new Product();

            $product->setCoverImage("e")
                ->setName("Brain Game Festival")
                ->setCoverImage(" ")
                ->setLogo("dd")
                ->setImages("d")->setDescription("Test");

            $date = new \DateTime();

            $school = new School();
            $school->setName("El Itkane")
                ->setAdress(" ")
                ->setEmail($i)
                ->setMobilePhone("05515")
                ->setPassword("dsqd")
                ->setCountry("dd")
                ->setLocality(" d ")
                ->setLng(0.5);


            $school->setLat(0.5);


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
            $manager->persist($school);
            $manager->persist($product);

        }


        $manager->flush();
    }
}
