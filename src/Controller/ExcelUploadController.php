<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelUploadController extends AbstractController
{
    public function upload(Request $request): Response
    {
        // Handle the file upload
        $excelFile = $request->files->get('excelFile');

        if (!$excelFile) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        // Check and process the Excel file
        try {
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

        // Use Doctrine ORM to persist data to the database
        $entityManager = $this->getDoctrine()->getManager();

         foreach ($data as $row) {
             $band = new Band();
             $country = new Country();
             $city = new City();
             $genre = new Genre();

             $country->setName($row[0]);
             $city->setName($row[1]);
             $band->setCountry($country);
             $band->setCity($city);
             $band->setBirthdate($row[2]);
             $band->setBreakup($row[3]);
             $band->setFounders($row[4]);

             $genre->setName($row[5]);

             $band->setGenre($genre);
             $band->setPresentation($row[6]);

             $entityManager->persist($band);
         }
         $entityManager->flush();

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
