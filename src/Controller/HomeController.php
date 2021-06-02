<?php
    namespace  App\Controller;


    use App\Entity\Contact;
    use App\Entity\Event;
    use App\Entity\User;
    use App\Form\ContactType;
    use App\Repository\EventRepository;
    use App\Repository\ProductRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Illuminate\Support\Facades\Mail;
    use Knp\Snappy\Pdf;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Address;
    use Symfony\Component\Mime\Email;
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

        /** @var array $before_reservations */
        /** @var array $after_reservations */
        /** @var array $before */
        public function reserve($mailer,$event, $request,array $users) {

            $user = $this->getUser();


            $before = $user->getEvents();
            if (!($before->contains($event))) {
                $user->addEvent($event);
            }


            $duplicate = [];
            foreach ($users as $reservant) {

                if ( (in_array($reservant->getId(), $event->getReservations() )) == true ) {
                    array_push($duplicate,$reservant->getName());

                } else {
                    $event->addReservation($reservant->getId());
                }
            }



            if  (count($duplicate) > 0) {

                $this->addFlash('success',$this->translator->trans('event.already_joined',[
                    '%names%' => implode(" , ",$duplicate)
                ],"messages"));
                return $this->redirectToRoute('home');
            }

            $this->em->flush();




            $pdf_path = $this->createPdf($user,$event);

            $render_path =  $this->render->render('emails/respond_after_reservation.html.twig',[
                'event' => $event,
                'user'  => $user,
                'users_length' => count($users),
                'pdf_path' => $pdf_path
            ]);

            $email = (new Email())
                ->from('user@no-reply.com')
                ->to(new Address('mi.section8.2020@gmail.com'))
                ->subject('Inscription à l\'evenement '. $event->getName())
                ->attachFromPath($pdf_path)
                ->html($render_path,'utf-8');
            $mailer->send($email);



            $this->addFlash('success',$this->translator->trans('mail.send',[],"messages"));
            return $this->redirectToRoute('home');

        }

        public function home() {
            $products = $this->productRepository->findAll();
            $events   = $this->eventRepository->findAllOrderByState('ASC');


            return $this->render("pages/home.html.twig",[
                "products" => $products,
                "events" => $events
            ]);
        }


        public function aboutUs() {
            return $this->render("pages/about-us.html.twig" );
        }

        private function createPdf( UserInterface $user, Event $event) : string
        {

            $path = $this->getParameter('pdf_directory') . '/'. $user->getUsername() . "_" . $user->getId() . "_" . rand(0,99999) .'.pdf';

            $this->pdf->generateFromHtml($this->render->render(
                'emails/respond_pdf_template.html.twig', // Ton template représentant le pdf à générer
                [
                    'event' => $event,
                    'user' => $user
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
    }



?>