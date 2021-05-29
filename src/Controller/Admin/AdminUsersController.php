<?php
namespace App\Controller\Admin;


use App\Controller\SecurityController;
use App\Entity\User;
use App\Form\UserType;
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

    public function __construct(UserPasswordEncoderInterface $encoder,EntityManagerInterface $em,TranslatorInterface $translator,UserRepository $repository)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->repository = $repository;
        $this->encoder = $encoder;
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
                'label' => 'forms.email'
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
                         ->add('scholar_level', ChoiceType::class, [
                                    'choices' => $this->getSchoolarChoices(),
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
                         ->add('related_school', EntityType::class, [
                                    'class' => User::class,
                                    'label' => 'forms.relatedschool',
                                    'choice_label' => 'name',
                                    'query_builder' => function (UserRepository $er) {
                                        return $er->createQueryBuilder('u')
                                            ->where('u.type = 0');
                                    },
                                ])
                         ->add('roles',CollectionType::class,[
                             'entry_type' => ChoiceType::class,
                             'entry_options' =>[
                                 'choices' => User::ROLES,
                                 'translation_domain' => 'types'
                             ]
                         ]);
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

        $page = $request->get('page',1);
        $students = $paginator->paginate($this->repository->getAllStudentsUsers(),$page,3);
        $schools = $paginator->paginate($this->repository->getAllSchoolsUsers(),$page,3);
        $admins = $paginator->paginate($this->repository->getAllAdminsUsers(),$page,3);




        return $this->render('pages/admin/users/admin.users.show.html.twig',[
            'students_users' => $students,
            'schools_users' => $schools,
            'admins_users' => $admins
        ]);
    }

    public function remove(Request $request,User $user) {

            if ($this->isCsrfTokenValid('remove' . $user->getId(),$request->get("_token"))) {
                $this->em->remove($user);
                $this->em->flush();
                $this->addFlash('success',$this->translator->trans('users.success.remove',[],'admin'));
                return $this->redirectToRoute('admin.users.show');
            }

            return $this->redirectToRoute('admin.users.show');

        }


    /**
     * @return array
     */
    private function getSchoolarChoices(): array
    {
        return array_flip(User::SCHOOLAR_LEVEL);
    }




}


?>