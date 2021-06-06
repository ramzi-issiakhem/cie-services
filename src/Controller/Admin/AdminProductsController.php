<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminProductsController extends AbstractController {
    /**
     * @var ProductRepository
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
     * AdminProductsController constructor.
     * @param ProductRepository $repository
     */
    public function __construct(ProductRepository $repository,EntityManagerInterface $em,TranslatorInterface $translator)
    {

        $this->repository = $repository;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @throws ErrorException
     */
    public function create(Request $request){

        $product = new Product();


        $form = $this->createForm(ProductType::class,$product,["validation_groups" => "create"]);
        $form->handleRequest($request);


        if  ($form->isSubmitted() && $form->isValid()) {
            $logoImage = $form->get('logo')->getData();
            $coverImage = $form->get('cover_image')->getData();
            $images = $form->get('images')->getData();

            $logoImage = $this->moveUploadedImages([$logoImage],$product);
            $coverImage = $this->moveUploadedImages([$coverImage],$product);
            $images = $this->moveUploadedImages($images,$product);

            $product->setLogo($logoImage[0]) ;
            $product->setCoverImage($coverImage[0]) ;
            $product->setImages($images) ;

            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success',$this->translator->trans('products.success.create',[],'admin'));
            return $this->redirectToRoute('admin.products.show');
        }
        return $this->render('pages/admin/products/admin.product.create.html.twig',[
            'form' => $form->createView()
        ]);


    }

    public function show() {

        $products = $this->repository->findAll();
        return $this->render('pages/admin/products/admin.products.show.html.twig',[
            'products' => $products
        ]);
    }

    public function edit(Product $product,Request $request) {

       //$this->updateImagesVariable($product);

        $form = $this->createForm(ProductType::class,$product,["validation_groups" => "edit"]);
        $form->handleRequest($request);



        if ( $form->isSubmitted() && $form->isValid()) {


            $logoImage = $form->get('logo')->getData();
            $coverImage = $form->get('cover_image')->getData();
            $images = $form->get('images')->getData();

            if($logoImage) {
                $logoImage = $this->moveUploadedImages([$logoImage],$product);
                $product->setLogo($logoImage[0]) ;
            }

            if($coverImage) {
                $coverImage = $this->moveUploadedImages([$coverImage],$product);
                $product->setCoverImage($coverImage[0]) ;
            }

            if($images) {
                $images = $this->moveUploadedImages([$images],$product);
                $product->setImages($images) ;
            }




            $this->em->flush();
            $this->addFlash('success',$this->translator->trans('products.success.edit',[],'admin'));
            return $this->redirectToRoute('admin.products.show');

        }

        return $this->render('pages/admin/products/admin.product.edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function remove(Request $request,Product $product) {

        if ($this->isCsrfTokenValid('remove' . $product->getId(),$request->get("_token"))) {
            $events = $product->getEvents();
            foreach ($events as $event) {
                $event->setProduct(null);

            }
            $this->em->flush();

            $this->em->remove($product);
            $this->em->flush();
            $this->addFlash('success',$this->translator->trans('products.success.remove',[],'admin'));
            return $this->redirectToRoute('admin.products.show');
        }

        return $this->redirectToRoute('admin.products.show');

    }

    private function moveUploadedImages(Array $array,Product $product): array
    {
        $slugger = new Slugify();
        $return_array = [];

        foreach ($array as $imageData) {


            //if ($image->guessExtension() == "png" || $image->guessExtension() == "jpeg") {


                //$originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slugify($product->getName());
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageData->guessExtension();


                try {
                    $imageData->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw  new ErrorException("Error Uploading file");
                }

                array_push($return_array,$newFilename);
         //   }
        }
        return $return_array;

    }

    private function updateImagesVariable(Product $product)
    {

        $path_logo = $this->getParameter('products_directory') . '/' . $product->getLogo();
        $path_cover = $this->getParameter('products_directory') . '/' . $product->getCoverImage();
        $sample_image = $this->getParameter('products_directory') . '/sample_product.png';

        if (file_exists($path_logo)) {
            $product->setLogo(new File($path_logo));
        } else {
            $product->setLogo(new File($sample_image));
        }

        if (file_exists($path_cover)) {
            $product->setCoverImage(new File($path_cover));
        } else {
            $product->setCoverImage(new File($sample_image));
        }

        $images = [];
        foreach (($product->getImages()) as $image) {

            $path = $this->getParameter('products_directory') . '/' . $image;

            if (file_exists($path)) {
                $images[] = new File($path);
            } else {
                $images[] = new File($sample_image);
            }

        }

        $product->setImages($images);
    }

}