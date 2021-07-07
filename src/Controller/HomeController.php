<?php
    namespace  App\Controller;


    use App\Entity\Catalog;
    use App\Entity\Contact;
    use App\Entity\Event;
    use App\Entity\Product;
    use App\Entity\User;
    use App\Form\CatalogType;
    use App\Form\ContactType;
    use App\Repository\EventRepository;
    use App\Repository\ProductRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Endroid\QrCode\Color\Color;
    use Endroid\QrCode\Encoding\Encoding;
    use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
    use Endroid\QrCode\Label\Label;
    use Endroid\QrCode\Logo\Logo;
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
    use Endroid\QrCode\Writer\PngWriter;
    use Illuminate\Support\Facades\Mail;
    use Knp\Snappy\Pdf;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Asset\Package;
    use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Address;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\Mime\Message;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;
    use Twig\Environment;

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
        /**
         * @var Environment
         */
        private $render;
        /**
         * @var Pdf
         */
        private $pdf;
        /**
         * @var EntityManagerInterface
         */
        private $em;


        public function __construct(EntityManagerInterface $em,Pdf $pdf,TranslatorInterface $translator,ProductRepository $productRepository, EventRepository  $eventRepository,Environment $render)
        {

            $this->productRepository = $productRepository;
            $this->eventRepository = $eventRepository;
            $this->translator = $translator;
            $this->render = $render;

            $this->pdf = $pdf;
            $this->em = $em;
        }

        /**
         * @Route("/{_locale}/profile/reserve/{id}", name="user.reserve", requirements={"_locale"="fr|ar|en"})
         * @param MailerInterface $mailer
         * @param Event $event
         * @param Request $request
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function reserveAction(MailerInterface $mailer ,Event $event,Request $request) {

            $user = $this->getUser();

            $form = $this->createFormBuilder(null,[
                'translation_domain' => 'forms'
            ])
                ->add('choices',ChoiceType::class,[
                    'label' => "forms.choice.children",
                    'choices' => $this->getReservationChoices($user),
                    'multiple' => true
                ]);
            $form = $form->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $array = $form->get('choices')->getData();

                if ( count($array) > 0 ) {
                        return $this->forward('\App\Controller\HomeController::reserve', [
                            'mailer' => $mailer,
                            'event' => $event,
                            'request' => $request,
                            'users' => $array
                    ]);
                }

            }


            return $this->render("pages/reservation.html.twig",[
                'user' => $user,
                'event' => $event,
                'form' => $form->createView()
            ]);


        }


        public function reserve($mailer,$event, $request,array $users) {

            $user = $this->getUser();

            $duplicate = [];
            foreach ($users as $reservant) {

                $reservations = [];
                $reservants = [];
                if ($event->getReservations() != null) {
                    $reservations = $event->getReservations();
                }

                if ( (in_array($reservant->getId(), $reservations )) == true ) {
                    array_push($duplicate,$reservant->getName());
                } else {
                    $reservant->addEvent($event->getId());
                    $event->addReservation($reservant->getId());
                    array_push($reservants,$reservant);
                }

            }



            if  (count($duplicate) > 0) {

                $this->addFlash('success',$this->translator->trans('event.already_joined',[
                    '%names%' => implode(" , ",$duplicate)
                ],"messages"));
                return $this->redirectToRoute('home');
            }

            $this->em->flush();




            $pdf_path = $this->createPdf($user,$event,$reservants);

            $render_path =  $this->render->render('emails/respond_after_reservation.html.twig',[
                'event' => $event,
                'user'  => $user,
                'users_length' => count($users),
                'pdf_path' => $pdf_path
            ]);

            $email = (new Email())
                ->from('user@no-reply.com')
                ->to(new Address($user->getEmail()))
                ->subject('Inscription à l\'evenement '. $event->getName())
                ->attachFromPath($pdf_path)
                ->html($render_path,'utf-8');
            $mailer->send($email);



            $this->addFlash('success',$this->translator->trans('mail.send',[],"messages"));
            return $this->redirectToRoute('home');

        }

        public function showProduct(Product $product,Request $request)
        {

            return $this->render('pages/product.html.twig',[
                'product' => $product
            ]);

        }

        public function underConstruction(Request $request)
        {
            $catalog = new Catalog();
            $form = $this->createForm(CatalogType::class,$catalog);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->em->persist($catalog);
                $this->em->flush();

                $package = new Package(new EmptyVersionStrategy());
                $url = $package->getUrl('/downloads/catalog/catalog_preview.pdf');;

                $this->addFlash('success',$this->translator->trans('message.catalog.downloaded',[],"messages"));

                return new RedirectResponse($url);
            }


            return $this->render('pages/under_construction.html.twig',[
                'form' => $form->createView()
            ]);
        }


        public function contact(Request $request,MailerInterface $mailer) {

            $contact = new Contact();
            $form = $this->createForm(ContactType::class,$contact);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $email = new Email();
                $render_path = $this->render->render('emails/respond_contact.html.twig',[
                    'contact' => $contact
                ]);

                $email
                    ->from('user@no-reply.com')
                    ->to('issiakhem.mohamedramzi@gmail.com')
                    ->subject($contact->getMotif() . "  / " . $this->translator->trans($contact->getObject(),[],'types'))
                    ->html($render_path,'utf-8');

                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                }
                $this->addFlash('success',$this->translator->trans('mail.send',[],"messages"));
                return $this->redirectToRoute('home');
            }

            return $this->render('pages/contact.html.twig',[
                'form' => $form->createView()
            ]);
        }
        public function home() {

            $products = $this->productRepository->findAll();
            $events   = $this->eventRepository->findAllOrderByState('ASC');

            $formatted_products = array_chunk($products,3);
            $formatted_events = array_chunk($events,3);

            return $this->render("pages/home.html.twig",[
                "products" => $formatted_products,
                "events" => $formatted_events
            ]);
        }


        public function aboutUs() {
            return $this->render("pages/about-us.html.twig" );
        }

        private function createPdf( UserInterface $user, Event $event,array $reservants) : string
        {

            //$qr = $this->createQRCode($reservants);
            $json = json_encode(array(
                'reservants' => $reservants
            ));

            $path = $this->getParameter('pdf_directory') . '/'. $user->getUsername() . "_" . $user->getId() . "_" . rand(0,99999) .'.pdf';

            $this->pdf->generateFromHtml($this->render->render(
                'emails/respond_pdf_template.html.twig', // Ton template représentant le pdf à générer
                [
                    'event' => $event,
                    'user' => $user,
                    'reservants' => $reservants,
                    'json' => $json
                ]
            ), $path);


            return $path;
        }


          private function getReservationChoices(UserInterface $user): array
        {
            $children = $user->getChildren();
            $length   = $children->count();
            $return_var = array();

            if ($length > 0) {
                for ($i = 0; $i < $length; $i++) {
                    $return_var[$children->get($i)->getName()] = $children->get($i);
                }
            }
            return $return_var;
        }

        /**
         * @return \Endroid\QrCode\Writer\Result\ResultInterface
         * @throws \Exception
         */
        private function createQRCode(array $reservants): \Endroid\QrCode\Writer\Result\ResultInterface
        {

            $writer = new PngWriter();
            $json = json_encode(array(
                'reservants' => $reservants
            ));

            $qrCode = QrCode::create('Data')
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(300)
                ->setMargin(10)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->setData($json)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));


            $logo = Logo::create(__DIR__.'/assets/symfony.png')
                ->setResizeToWidth(50);


            $label = Label::create('Label')
                ->setTextColor(new Color(255, 0, 0));

            return  $writer->write($qrCode, $logo, $label);
        }
    }



?>