<?php

namespace  App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;


class AdminEventsController extends  AbstractController {
    /**
     * @var EventRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TranslatorInterface
     */
    private $translator;


    public function __construct(EventRepository $repository,EntityManagerInterface $em,TranslatorInterface  $translator)
            {
                $this->repository = $repository;
                $this->em = $em;
                $this->translator = $translator;

            }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request,PaginatorInterface $paginator) {

                $page = $request->get('page',1);
                $still_events = $paginator->paginate($this->repository->findAllByState(0,'ASC'),$page,3);
                $waiting_events = $paginator->paginate($this->repository->findAllByState(1,'ASC'),$page,3);
                $confirmed_events = $paginator->paginate($this->repository->findAllByState(2,'ASC'),$page,3);
                $finished_events = $paginator->paginate($this->repository->findAllByState(3,'ASC'),$page,3);




                return $this->render('pages/admin/admin.events.show.html.twig',[
                    'still_events' => $still_events,
                    'waiting_events' => $waiting_events,
                    'confirmed_events' => $confirmed_events,
                    'finished_events' => $finished_events
                ]);

            }



            public function edit(Event $event,Request $request) {

                $form = $this->createForm(EventType::class,$event);
                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid()) {
                    $this->em->flush();
                    $this->addFlash('success',$this->translator->trans('events.success.edit',[],'admin'));
                    return $this->redirectToRoute('admin.events.show');
                }

                return $this->render('pages/admin/admin.event.edit.html.twig',[
                    'form' => $form->createView()
                ]);
            }

            public function remove(Request $request,Event $event) {

                    if ($this->isCsrfTokenValid('remove' . $event->getId(),$request->get("_token"))) {
                        $this->em->remove($event);
                        $this->em->flush();
                        $this->addFlash('success',$this->translator->trans('events.success.remove',[],'admin'));
                        return $this->redirectToRoute('admin.events.show');
                    }

                    return $this->redirectToRoute('admin.events.show');

            }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request  $request): \Symfony\Component\HttpFoundation\Response
    {

                $event = new Event();
                $event->setReservations(0);
                $form = $this->createForm(EventType::class,$event);
                $form->handleRequest($request);


                if  ($form->isSubmitted() && $form->isValid()) {
                    $this->em->persist($event);
                    $this->em->flush();
                    $this->addFlash('success',$this->translator->trans('events.success.create',[],'admin'));
                    return $this->redirectToRoute('admin.events.show');
                }


                return $this->render('pages/admin/admin.event.create.html.twig',[
                    'form' => $form->createView()
                ]);
            }


    }


?>