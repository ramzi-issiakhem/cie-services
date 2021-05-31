<?php
    namespace  App\Controller;


    use App\Entity\Event;
    use App\Entity\User;
    use App\Repository\EventRepository;
    use App\Repository\ProductRepository;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Knp\Snappy\Pdf;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\HttpFoundation\Request;
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


        public function __construct(Pdf $pdf,TranslatorInterface $translator,ProductRepository $productRepository, EventRepository  $eventRepository,Environment $render)
        {

            $this->productRepository = $productRepository;
            $this->eventRepository = $eventRepository;
            $this->translator = $translator;
            $this->render = $render;

            $this->pdf = $pdf;
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

            $form = $this->createFormBuilder()
                ->add('choices',ChoiceType::class,[
                    'label' => "Test",
                    'choices' => $this->getReservationChoices($user)
                ]);
            $form = $form->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $array =$request->request->get('choices');
                if ( $array ) {
                        return $this->forward('App\Controller\HomeController::reserve', [
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
        public function reserve( $mailer,$event, $request,array $users) {


            $user = $this->getUser();

            $before = $user->getEvents()->toArray();
            $user->addEvent($event);
            $after = $user->getEvents()->toArray();

            $before_reservations = $event->getReservations();
            foreach ($users as $reservant) {
                $event->addReservation($reservant);
            }
            $after_reservations = $event->getReservations();

            if ((count (array_diff($before,$after)) > 0) && (count (array_diff($before_reservations,$after_reservations)) > 0)) {
                $this->addFlash('success',$this->translator->trans('event.already_joined',[],"messages"));
                return $this->redirectToRoute('home');
            }


            $render_path =  $this->render->render('emails/respond_after_reservation.html.twig',[
                'event' => $event,
                'user'  => $user
            ]);

            $pdf_path = $this->createPdf($user,$event);

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

        public function contact() {

            return $this->render("pages/contact.html.twig" );
        }

        public function aboutUs() {
            return $this->render("pages/about-us.html.twig" );
        }

        private function createPdf( UserInterface $user, Event $event) : string
        {

            $path = $this->getParameter('pdf_directory') . '/'. $user->getUsername() . "_" . $user->getId(). '.pdf';

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
            $children = $user->getChildren() ;
            $length   = count($children);
            $return_var = array();

            if ($length > 0) {
                for ($i = 0; $i <= $length; $i++) {
                    $return_var["test"] = $children[$i];

                }
            }
            return $return_var;
        }
    }



?>