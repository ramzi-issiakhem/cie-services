<?php
    namespace  App\Controller;


    use App\Repository\EventRepository;
    use App\Repository\ProductRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Contracts\Translation\TranslatorInterface;

    class HomeController extends  AbstractController {

        /**
         * @var ProductRepository
         */
        private $productRepository;
        /**
         * @var EventRepository
         */
        private $eventRepository;
        /**
         * @var TranslatorInterface
         */
        private $translator;

        public function __construct(TranslatorInterface $translator,ProductRepository $productRepository, EventRepository  $eventRepository)
        {

            $this->productRepository = $productRepository;
            $this->eventRepository = $eventRepository;
            $this->translator = $translator;
        }


        public function home() {
            $products = $this->productRepository->findAll();
            $events   = $this->eventRepository->findAllOrderByState('ASC');


            return $this->render("pages/home.html.twig",[
                "products" => $products,
                "events" => $events
            ]);
        }

        public function contact() {

            return $this->render("pages/contact.html.twig" );
        }

        public function aboutUs() {
            return $this->render("pages/about-us.html.twig" );

        }
    }



?>