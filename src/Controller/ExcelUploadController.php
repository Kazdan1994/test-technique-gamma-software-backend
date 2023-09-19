<?php

namespace App\Controller;

use App\Entity\Band;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelUploadController extends AbstractController
{
    private CountryRepository $countryRepository;
    private CityRepository $cityRepository;
    private GenreRepository $genreRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        GenreRepository $genreRepository,
        EntityManagerInterface $entityManager
    )
    {

        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->genreRepository = $genreRepository;
        $this->entityManager = $entityManager;
    }

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

            foreach ($data as $row) {
                $band = new Band();

                $band->setName($row[0]);

                $country = $this->countryRepository->findOrCreateByName($row[1]);
                $band->setCountry($country);

                $city = $this->cityRepository->findOrCreateByName($row[2]);
                $band->setCity($city);

                $band->setBirthdate((int)$row[3]);

                $band->setBreakup((int)$row[4]);

                $band->setFounders($row[5]);

                $band->setMembers((int)$row[6]);

                $genre = $this->genreRepository->findOrCreateByName($row[7]);
                $band->setGenre($genre);

                $band->setPresentation($row[8]);

                $this->entityManager->persist($band);
            }

            $this->entityManager->flush();

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
