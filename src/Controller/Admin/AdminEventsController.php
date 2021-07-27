<?php

namespace  App\Controller\Admin;

use App\Controller\Services;
use App\Entity\Event;
use App\Entity\EventSearch;
use App\Form\EventSearchType;
use App\Form\EventType;
use App\Repository\ChildRepository;
use App\Repository\EventRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    /**
     * @var ChildRepository
     */
    private $childRepository;


    public function __construct(ChildRepository $childRepository,EventRepository $repository,EntityManagerInterface $em,TranslatorInterface  $translator)
            {
                $this->repository = $repository;
                $this->em = $em;
                $this->translator = $translator;

                $this->childRepository = $childRepository;
            }

    /**
     * @param Request $request
     * @param Event $event
     * @return Response
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function downloadChildrenList(Request $request, Event $event) {

        $serivce = new Services();
        $url = $serivce->createExcelSheet($this->getParameter('excels_directory'),$event,$this->childRepository,$this->translator);

        // Return a text response to the browser saying that the excel was succesfully created
        return new RedirectResponse($url);

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request,PaginatorInterface $paginator) {

                $search = new EventSearch();
                $form_search = $this->createForm(EventSearchType::class,$search);
                $form_search->handleRequest($request);


                $page = $request->get('page',1);
                $events = $paginator->paginate($this->repository->findAllByState($search),$page,3);





                return $this->render('pages/admin/admin.events.show.html.twig',[
                    'events' => $events,
                    'search_form' => $form_search->createView()
                ]);

            }



    public function edit(Event $event,Request $request) {

                $form = $this->createForm(EventType::class,$event);
                $form->handleRequest($request);

                $children = [];
                foreach ($event->getReservations() as $id) {
                    $children[] = $this->childRepository->find($id);
                }


                if ( $form->isSubmitted() && $form->isValid()) {
                    $this->em->flush();
                    $this->addFlash('success',$this->translator->trans('events.success.edit',[],'admin'));
                    return $this->redirectToRoute('admin.events.show');
                }

                return $this->render('pages/admin/admin.event.edit.html.twig',[
                    'form' => $form->createView(),
                    'childrens' => $children,
                    'event_id' => $event->getId()
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
                $event->setReservations([]);
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