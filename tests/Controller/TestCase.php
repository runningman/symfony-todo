<?php

namespace App\Tests\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\SchemaTool;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class TestCase extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    /** @var LoaderInterface */
    protected $fixtureLoader;

    /** @var Registry */
    protected $doctrine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $kernel = static::bootKernel();

        $this->doctrine = $kernel->getContainer()->get('doctrine');
        $this->setUpDatabase();

        $this->fixtureLoader = $kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
    }

    /**
     * Load the database schema.
     *
     * @return void
     */
    protected function setUpDatabase(): void
    {
        $entityManager = $this->doctrine->getManager();
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        (new SchemaTool($entityManager))->updateSchema($metadatas);
    }

    /**
     * Parse the json response decoded to array.
     *
     * @return array
     */
    protected function getJsonResponse(): array
    {
        $response = $this->client->getResponse();

        return json_decode($response->getContent(), true);
    }

    /**
     * Post data as json and return the response.
     *
     * @return array
     */
    protected function postJson($url, array $data): array
    {
        $data = json_encode($data);
        $this->client->request('POST', $url, [], [], [], $data);

        return $this->getJsonResponse();
    }
}