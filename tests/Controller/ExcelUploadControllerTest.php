<?php

namespace App\Tests\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExcelUploadControllerTest extends WebTestCase
{
    public function testUploadExcelFile()
    {
        // Create a Symfony test client
        $client = static::createClient();

        // Create a mock Excel file for testing
        $excelFile = new UploadedFile(
            __DIR__.'/../../../test.xlsx',
            'test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // Send a POST request to your API endpoint
        $client->request('POST', '/api/excel/upload', [], ['excelFile' => $excelFile]);

        // Check the response status code
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Check the response content for success messages or validation errors
        $this->assertJsonStringEqualsJsonString('{"success": true}', $client->getResponse()->getContent());
    }
}
