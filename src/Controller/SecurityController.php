<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\User;
use App\Form\ChildType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $encoder;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var Environment
     */
    private $render;


    public function __construct(Environment $render, EntityManagerInterface $em,UserPasswordEncoderInterface $encoder,TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->translator = $translator;
        $this->render = $render;
    }

    public function registerChoice(): Response
    {
        return $this->render('pages/security/register/choice_register.html.twig');
   }

    public function editPassword(User $user,Request $request) {

        $form = $this->createFormBuilder($user)
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'forms.password.new',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'forms.password.repeat',
                ],
                'invalid_message' => 'forms.password.invalid',
                'translation_domain' => 'forms'
            ]);
        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($this->encoder->encodePassword($user,$user->getPassword()));
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('user.success.password.edited', [], 'messages'));
            return $this->redirectToRoute('user.profile',[
                'slug' => $user->getSlug()
            ]);
        }

        return $this->render('pages/security/edit_password.html.twig',[
            'form' => $form->createView(),
            'user' => $user
        ]);

    }

    public function edit(User $user,Request $request) {

        $form = $this->createForm(UserType::class,$user);

        if ($user->getType() == 1) {
            $form->add("birthday_date",DateType::class,[
                'label' => 'forms.birthday_date',
                'widget' => "single_text"
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $path = $this->moveUploadedImages($form->get('logo')->getData(),$form->get('name')->getData());
            $user->setLogo($path);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('user.success.edited', [], 'messages'));
            return $this->redirectToRoute('user.profile',[
                'slug' => $user->getSlug()
            ]);

        }

        return $this->render('pages/security/edit.html.twig',[
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    public function profilePage(Request $request): Response
    {
        $user = $this->getUser();
        if ($request->get('slug') != $user->getSlug()) {
            return $this->redirectToRoute('home');
        }

        return $this->render("pages/security/profile.html.twig",[
            'user' => $user
        ]);
    }

    public function createChild(Request $request) {


        $child = new Child();
        $user = $this->getUser();
        $type = $user->getType();


        $form = $this->createForm(ChildType::class,$child);

        // If it's a parent
        if ($type == 1 ) {


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // TODO Localisation Functionnality
                    $child->setParent($user);
                    $user->addChild($child);

                    $this->em->persist($child);
                    $this->em->flush();
                    $this->addFlash('success', $this->translator->trans('child.success.created',[],'messages'));

                return $this->redirectToRoute('home', [
                    //'slug' => $user->slugify()
                ]);

            }


            return $this->render('pages/security/create_child.html.twig', [
                'form' => $form->createView(),
                'type' => $type
            ]);
        }

        return $this->redirectToRoute('user.profile',[
            'slug' => $user->getSlug()
        ]);
    }
    /**
     * @param Request $request
     * @param Child $child
     * @return Response
     */
    public function editChild(Request $request, Child $child): Response
    {

        $user = $this->getUser();

        if ($user->getType() == 1) {
                $form = $this->createForm(ChildType::class,$child);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->em->flush();
                    $this->addFlash('success', $this->translator->trans('child.success.edited', [], 'messages'));
                    return $this->redirectToRoute('user.profile',[
                        'slug' => $user->getSlug()
                    ]);
                }

                return $this->render("pages/security/edit_child.html.twig",[
                    'user' => $user,
                    'form' =>$form->createView()
                ]);

        }

        return $this->redirectToRoute('user.profile',[
            'slug' => $user->getSlug()
        ]);

    }

    /**
     * @param Request $request
     * @param Child $child
     * @return Response
     */
    public function removeChild(Request $request, Child $child): Response
    {
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('remove' . $child->getId(),$request->get("_token"))) {

            $user->removeChild($child);
            $this->em->remove($child);
            $this->em->flush();
            $this->addFlash('success',$this->translator->trans('child.success.remove',[],'messages'));
            return $this->redirectToRoute('user.profile',[
                'slug' => $user->getSlug()
            ]);
        }

        return $this->redirectToRoute('user.profile',[
            'slug' => $user->getSlug()
        ]);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws ErrorException
     */
    public function register(Request $request) {

        $type = $request->get("type");
        $user = new User();
        // TODO Change all %trans to  {{trans


        $form = $this->createFormBuilder($user,[
            'translation_domain' => 'forms'
        ])
            ->add('email',EmailType::class,[
                'label' => 'forms.email'
            ])
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'forms.password.new',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'forms.password.repeat',
                ],
                'invalid_message' => 'forms.password.invalid',
                'translation_domain' => 'forms'
            ])
            ->add('mobile_phone',NumberType::class,[
                'label' => 'forms.mobilephone'
            ])
            ->add('name',TextType::class,[
                'label' => 'forms.username'
            ])->add('logo',FileType::class,[
                    'required' => true,
                    'multiple' => false,
                    'mapped' => true,
                    "label" => 'forms.logo'
            ]);

            // If it's a parent
            if ($type == 1 ) {
                 $form
                    ->add('birthday_date',DateType::class,[
                    'widget' => "single_text",
                        'label' => 'forms.birthday'
                        ]);

                 }

            $form = $form->getForm();
            $form->handleRequest($request);


        if ( $form->isSubmitted() && $form->isValid()) {
            // TODO Localisation Functionnality

            $logoImage = $form->get('logo')->getData();
            $logoImage = $this->moveUploadedImages($logoImage,$form->get('name')->getData());
            $user->setLogo($logoImage);


            $user->setLng(50);
            $user
                ->setType($type)
                ->setLocality("")
                ->setAdress("")
                ->setCountry("Algeria")
                ->setRoles(["ROLE_USER"])
                ->setPassword($this->encoder->encodePassword($user,$user->getPassword()) )
                ->setLat(10);


            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success',$this->translator->trans('users.register'));

            return $this->redirectToRoute('home',[
                //'slug' => $user->slugify()
            ]);

        }


        return $this->render('pages/security/register/register.html.twig',[
            'form' => $form->createView(),
            'type' => $type
        ]);
    }


    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('pages/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/logout", name="user.logout")
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }




    private function moveUploadedImages($imageData,String $name): String
    {
        $slugger = new Slugify();


            if ($imageData->guessExtension() == "png" || $imageData->guessExtension() == "jpeg") {


            $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slugify($originalFilename);
            $newFilename = $this->formatLogoName($imageData,$name) ;


            try {
                $imageData->move(
                    $this->getParameter('users_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw  new ErrorException("Error Uploading file");
            }

                return $newFilename;
            }
            return "";
        }

    private function formatLogoName($imageData,String $name)
    {
        $slug = new Slugify();
        return  $slug->slugify($name) . '-' . uniqid() . '.' . $imageData->guessExtension();
    }


}
