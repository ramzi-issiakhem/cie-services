<?php
    namespace  App\Controller;


    use App\Entity\Event;
    use App\Repository\EventRepository;
    use App\Repository\ProductRepository;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Address;
    use Symfony\Component\Mime\Email;
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



        public function __construct(TranslatorInterface $translator,ProductRepository $productRepository, EventRepository  $eventRepository,Environment $render)
        {

            $this->productRepository = $productRepository;
            $this->eventRepository = $eventRepository;
            $this->translator = $translator;
            $this->render = $render;

        }

        public function reserve(MailerInterface $mailer,Event $event,Request $request) {

            $user = $this->getUser();
            $render_path =  $this->render->render('emails/respond_after_reservation.html.twig',[
                'event' => $event,
                'user'  => $user
            ]);

            /*$pdf_path = $this->createPdf($user,$event);*/

            $email = (new Email())
                ->from('user@no-reply.com')
                ->to(new Address('mi.section8.2020@gmail.com'))
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Inscription à l\'evenement '. $event->getName())
                //->attachFromPath($pdf_path)
                ->html($render_path,'utf-8');


            $mailer->send($email);
            $this->addFlash('success',$this->translator->trans('mail.send'));
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

        /*private function createPdf( UserInterface $user, Event $event) : string
        {
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($pdfOptions);
            $dompdf->setPaper('A4', 'portrait');
            $html = $this->renderView('emails\respond_pdf_template.html.twig');
            $dompdf->loadHtml($html);
            $output = $dompdf->output();

            $path = $this->getParameter('pdf_directory') . '/'. $user->getUsername() . "_" . $user->getId(). '.pdf';
            file_put_contents($path, $output);
            return $path;
        }*/
    }



?>