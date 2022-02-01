<?php

namespace  App\Controller\Admin;

use App\Controller\Services;
use App\Entity\Event;
use App\Entity\EventSearch;
use App\Form\EventSearchType;
use App\Form\EventType;
use App\Repository\CatalogRepository;
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


class AdminCatalogController extends  AbstractController {
    /**
     * @var CatalogRepository
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

    public function __construct(CatalogRepository $repository,EntityManagerInterface $em,TranslatorInterface  $translator)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request,PaginatorInterface $paginator) {

        $catalogs = $this->repository->findAll();

        return $this->render('pages/admin/admin.catalogs.show.html.twig',[
            'catalogs' => $catalogs,
        ]);

    }

    public function downloadList() {

        $spreadsheet = new Spreadsheet();
        $slug = new Slugify();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle("List-Formulaires-Catalog")
            ->setCellValue('A1','Prenom')
            ->setCellValue('B1',"Nom")
            ->setCellValue('C1','Ecole')
            ->setCellValue('D1',"Email")
            ->setCellValue('E1','Mobile Phone');


        $forms = $this->repository->findAll();

        $index=2;
        $count = 0;
        foreach ($forms as $form) {


            if ($form != null ) {
                $sheet->setCellValue('A' . $index, $form->getFirstName())
                    ->setCellValue('B' . $index, $form->getFamilyName())
                    ->setCellValue('C' . $index, $form->getBuisenessName())
                    ->setCellValue('D' . $index, $form->getEmail())
                    ->setCellValue('E' . $index, '0' . $form->getMobilePhone());
                        $count = $count +1;
            }
            $index = $index +1;
        }

        $writer = new Xlsx($spreadsheet);


        $time = new \DateTime();


        $name = "List-Formulaires-Catalog" ."-". uniqid() . '.xlsx';
        $publicDirectory = $this->getParameter('excels_directory');
        $excelFilepath =  $publicDirectory. '/' . $name;

        // Create the file
        $writer->save($excelFilepath);

        $package = new Package(new EmptyVersionStrategy());
        return new  RedirectResponse( $package->getUrl('/downloads/excels/'.$name));
    }

}


?>