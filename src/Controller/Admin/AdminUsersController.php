<?php
namespace App\Controller\Admin;


use App\Controller\SecurityController;
use App\Entity\Child;
use App\Entity\User;
use App\Entity\UserSearch;
use App\Form\UserSearchType;
use App\Form\UserType;
use App\Repository\ChildRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Sodium\add;

class AdminUsersController extends  AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var ChildRepository
     */
    private $child_repository;
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em,TranslatorInterface $translator,UserRepository $repository,ChildRepository $child_repository)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->repository = $repository;
        $this->encoder = $encoder;
        $this->child_repository = $child_repository;
        $this->eventRepository = $eventRepository;
    }

    public function editPassword(User $user,Request $request) {

        $form = $this->createFormBuilder($user, [
            'translation_domain' => 'forms'
        ])->add('password', PasswordType::class, [
            'label' => 'forms.password',
        ]);

        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user,$user->getPassword()));
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('users.success.edit', [], 'admin'));
            return $this->redirectToRoute('admin.users.show');

        }

        return $this->render('pages/admin/users/admin.user.edit.password.html.twig', [
            'form' => $form->createView(),

        ]);


    }

    public function edit(User $user,Request $request)
    {
        $type = $user->getType();

        $form = $this->createFormBuilder($user, [
            'translation_domain' => 'forms'
        ])
            ->add('email', EmailType::class, [
                'label' => 'forms.emails'
            ])

            ->add('mobile_phone', NumberType::class, [
                'label' => 'forms.mobilephone'
            ])
            ->add('name', TextType::class, [
                'label' => 'forms.username'
            ]);

            if ($type == 1) {
                    $form->add('birthday_date', DateType::class, [
                                'widget' => "single_text",
                                'label' => 'forms.birthday'
                            ])

                         ->add('roles',CollectionType::class,[
                             'entry_type' => ChoiceType::class,
                             'entry_options' =>[
                                 'choices' => User::ROLES,
                                 'translation_domain' => 'types'
                             ]
                         ])
                            /*->add('scholar_level', ChoiceType::class, [
                                    'choices' => array_flip(User::SCHOOLAR_LEVEL),
                                    'choice_translation_domain' => "types",
                                    'label' => 'forms.schoolarlevel',
                                    'group_by' => function ($choice, $key, $value) {
                                        if (strpos($key, "section")) {
                                            return $this->translator->trans("levels.categories.section", [], "types");
                                        } elseif (strpos($key, "primary")) {
                                            return $this->translator->trans("levels.categories.primary", [], "types");
                                        } elseif (strpos($key, "secondary")) {
                                            return $this->translator->trans("levels.categories.secondary", [], "types");
                                        } else {
                                            return $this->translator->trans("levels.categories.high", [], "types");
                                        }
                                    }
                                ])
                         /*->add('related_school', EntityType::class, [
                                    'class' => User::class,
                                    'label' => 'forms.relatedschool',
                                    'choice_label' => 'name',
                                    'query_builder' => function (UserRepository $er) {
                                        return $er->createQueryBuilder('u')
                                            ->where('u.type = 0');
                                    },
                                ])*/
                    ;
                }


            $form =  $form->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->em->flush();
                $this->addFlash('success', $this->translator->trans('users.success.edit', [], 'admin'));
                return $this->redirectToRoute('admin.users.show');

            }

            return $this->render('pages/admin/users/admin.user.edit.html.twig', [
                'form' => $form->createView(),
                'user'  => $user
            ]);


    }

    public function show(Request $request,PaginatorInterface $paginator) {

        $search = new UserSearch();
        $form = $this->createForm(UserSearchType::class,$search);
        $page = $request->get('page',1);
        $form->handleRequest($request);

        $type= $search->getType();
        if ($type == 2) {
            $users = $this->child_repository->findAllBySearch($search);
        } else {
            $users = $this->repository->findAllBySearch($search);
        }



        $users_result = $paginator->paginate($users,$page,10);



        return $this->render('pages/admin/users/admin.users.show.html.twig',[
            'users' => $users_result,
            'search_form' => $form->createView(),

        ]);
    }

    public function remove(Request $request,User $user) {

            if ($this->isCsrfTokenValid('remove' . $user->getId(),$request->get("_token"))) {



                if ($user->getType() == 1) {

                    foreach ( $user->getChildren() as $child) {
                        $this->removeChild($child,$user);
                    }

//                    $collection = $user->getEvents();
                } elseif ($user->getType() == 0) {

                    $collection = $user->getUsers();
                    foreach ($collection as $child) {
                        $child->setSchool(null);
                    }

                    $collection = $user->getEvents();
                    foreach ($collection as $event) {
                        $event->setSchool(null);
                    }
                }

                $this->em->flush();
                $this->em->remove($user);
                $this->em->flush();
                $this->addFlash('success',$this->translator->trans('users.success.remove',[],'admin'));
                return $this->redirectToRoute('admin.users.show');
            }

            return $this->redirectToRoute('admin.users.show');

        }



    private function removeChild(Child $child,User $user) {

            $events = $child->getEvents();

            foreach ($events as $id) {
                $event = $this->eventRepository->find($id);
                if ($event) {
                    $array = $event->getReservations();
                    if (count($array) > 0)
                    {
                        if ($array[0] == $child->getId()) {
                            unset($array[0]);
                            if (count($array) == 0 ) {$array = [];};
                            $event->setReservations(array_values($array));
                            $this->em->flush();

                        }

                        $index = array_search($child->getId(), $array);
                        if ($index != false) {

                            unset($array[$index]);
                            if (count($array) == 0 ) {$array = [];};
                            $event->setReservations(array_values($array));
                            $this->em->flush();
                        }
                    }
                }


            }

            $user->removeChild($child);
            $this->em->remove($child);
            $this->em->flush();
        }




}


?>