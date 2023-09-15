<?php

namespace App\Test\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private IngredientRepository $repository;
    private string $path = '/admin/ingredient/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Ingredient::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ingredient index');

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
            'ingredient[nom]' => 'Testing',
            'ingredient[quantite]' => 'Testing',
            'ingredient[unite_de_mesure]' => 'Testing',
            'ingredient[recettes]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/ingredient/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ingredient();
        $fixture->setNom('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setUnite_de_mesure('My Title');
        $fixture->setRecettes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ingredient');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ingredient();
        $fixture->setNom('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setUnite_de_mesure('My Title');
        $fixture->setRecettes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ingredient[nom]' => 'Something New',
            'ingredient[quantite]' => 'Something New',
            'ingredient[unite_de_mesure]' => 'Something New',
            'ingredient[recettes]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/ingredient/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getUnite_de_mesure());
        self::assertSame('Something New', $fixture[0]->getRecettes());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Ingredient();
        $fixture->setNom('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setUnite_de_mesure('My Title');
        $fixture->setRecettes('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/ingredient/');
    }
}
