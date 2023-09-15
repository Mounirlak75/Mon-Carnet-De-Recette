<?php

namespace App\Test\Controller;

use App\Entity\Etape;
use App\Repository\EtapeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EtapeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EtapeRepository $repository;
    private string $path = '/admin/etape/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Etape::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Etape index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'etape[text]' => 'Testing',
            'etape[image]' => 'Testing',
            'etape[temps]' => 'Testing',
            'etape[numero_etape]' => 'Testing',
            'etape[recettes]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/etape/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etape();
        $fixture->setText('My Title');
        $fixture->setImage('My Title');
        $fixture->setTemps('My Title');
        $fixture->setNumero_etape('My Title');
        $fixture->setRecettes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Etape');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etape();
        $fixture->setText('My Title');
        $fixture->setImage('My Title');
        $fixture->setTemps('My Title');
        $fixture->setNumero_etape('My Title');
        $fixture->setRecettes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'etape[text]' => 'Something New',
            'etape[image]' => 'Something New',
            'etape[temps]' => 'Something New',
            'etape[numero_etape]' => 'Something New',
            'etape[recettes]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/etape/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getText());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getTemps());
        self::assertSame('Something New', $fixture[0]->getNumero_etape());
        self::assertSame('Something New', $fixture[0]->getRecettes());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Etape();
        $fixture->setText('My Title');
        $fixture->setImage('My Title');
        $fixture->setTemps('My Title');
        $fixture->setNumero_etape('My Title');
        $fixture->setRecettes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/etape/');
    }
}
