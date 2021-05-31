<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Comparator\NumberComparator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;


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


    public function __construct(EntityManagerInterface $em,UserPasswordEncoderInterface $encoder,TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    public function registerChoice() {
        return $this->render('pages/security/register/choice_register.html.twig');
   }

    public function profile() {
        $user = $this->getUser();
        return $this->render("pages/security/profile.html.twig",[
            'user' => $user
        ]);
    }

    public function register(Request $request) {

        $type = $request->get("type");
        $user = new User();



        $form = $this->createFormBuilder($user,[
            'translation_domain' => 'forms'
        ])
            ->add('email',EmailType::class,[
                'label' => 'forms.email'
            ])
            ->add('password',PasswordType::class,[
                'label' => 'forms.password'
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

            if ($type == 1 ) {
                 $form
                    ->add('birthday_date',DateType::class,[
                    'widget' => "single_text",
                        'label' => 'forms.birthday'
                        ])
                    ->add('scholar_level',ChoiceType::class ,[
                        'choices' => $this->getSchoolarChoices(),
                        'choice_translation_domain' => "types",
                        'label' => 'forms.schoolarlevel',
                        'group_by' => function($choice, $key, $value) {
                            if (strpos($key,"section")) {
                                return $this->translator->trans("levels.categories.section",[],"types");
                            } elseif (strpos($key,"primary")) {
                                return $this->translator->trans("levels.categories.primary",[],"types");
                            } elseif (strpos($key,"secondary")) {
                                return $this->translator->trans("levels.categories.secondary",[],"types");
                            } else {
                                return $this->translator->trans("levels.categories.high",[],"types");
                            }
                    }
                     ])
                    ->add('related_school',EntityType::class,[
                        'class' => User::class,
                        'label' => 'forms.relatedschool',
                        'choice_label' => 'name',
                        'query_builder' => function (UserRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where('u.type = 0');
                        },
                    ]);

             }

            $form = $form->getForm();
            $form->handleRequest($request);


        if ( $form->isSubmitted() && $form->isValid()) {
            // TODO Localisation Functionnality

            $logoImage = $form->get('logo')->getData();
            $logoImage = $this->moveUploadedImages($logoImage);
            $user->setLogo($logoImage);


            $user->setLng(50);
            $user
                ->setType($type)
                ->setLocality("ee")
                ->setAdress("dd")
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


    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @return array
     */
    private function getSchoolarChoices(): array
    {
        return array_flip(User::SCHOOLAR_LEVEL);
    }

    private function moveUploadedImages($imageData): String
    {
        $slugger = new Slugify();


            if ($imageData->guessExtension() == "png" || $imageData->guessExtension() == "jpeg") {


            $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slugify($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageData->guessExtension();


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




}
