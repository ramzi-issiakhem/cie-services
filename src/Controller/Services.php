<?php

namespace App\Controller;


use App\Entity\Event;
use App\Repository\ChildRepository;
use Cocur\Slugify\Slugify;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Contracts\Translation\TranslatorInterface;

class Services {


    public function createExcelSheet(String $publicDirectory,Event $event,ChildRepository $childRepository,TranslatorInterface $translator) : String {
        $spreadsheet = new Spreadsheet();
        $slug = new Slugify();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("List-". $event->getEventDateTime()->format('d|m|y'))
            ->setCellValue('A1','Nom')
            ->setCellValue('B1',"Evenement")
            ->setCellValue('C1','Ecole')
            ->setCellValue('C1',"Parent")
            ->setCellValue('D1','Niveau Scolaire')
            ->setCellValue('E1','Mobile');


        $children = $event->getReservations();

        $index=2;
        $count = 0;
        foreach ($children as $id) {

            $child = $childRepository->find($id);
            if ($child != null ) {
                $sheet->setCellValue('A' . $index, $child->getName())
                    ->setCellValue('B' . $index, $event->getName())
                    ->setCellValue('C' . $index, $child->getSchool()->getName())
                    ->setCellValue('C' . $index, $child->getParent()->getName())
                    ->setCellValue('D' . $index, $translator->trans($child->getFormattedSchoolarLevel(), [], 'types'))
                    ->setCellValue('D' . $index, '0' . $child->getParent()->getMobilePhone());
                $count = $count +1;
            }
            $index = $index +1;
        }

        $writer = new Xlsx($spreadsheet);


        // In this case, we want to write the file in the public directory
        ;
        $name = $slug->slugify($event->getName())."-". $slug->slugify($event->getSchool()->getName()). "-". uniqid() . '.xlsx';
        $excelFilepath =  $publicDirectory. '/' . $name;

        // Create the file
        $writer->save($excelFilepath);

        $package = new Package(new EmptyVersionStrategy());
        return  $package->getUrl('/downloads/excels/'.$name);
    }
}