<?php

namespace App\Test\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecetteControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RecetteRepository $repository;
    private string $path = '/admin/recette/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Recette::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Recette index');

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
            'recette[titre]' => 'Testing',
            'recette[description]' => 'Testing',
            'recette[temps_de_preparation]' => 'Testing',
            'recette[temps_de_cuisson]' => 'Testing',
            'recette[difficulte]' => 'Testing',
            'recette[ingredient]' => 'Testing',
            'recette[user]' => 'Testing',
            'recette[etape]' => 'Testing',
            'recette[ingredients]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/recette/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Recette();
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setTemps_de_preparation('My Title');
        $fixture->setTemps_de_cuisson('My Title');
        $fixture->setDifficulte('My Title');
        $fixture->setIngredient('My Title');
        $fixture->setUser('My Title');
        $fixture->setEtape('My Title');
        $fixture->setIngredients('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Recette');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Recette();
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setTemps_de_preparation('My Title');
        $fixture->setTemps_de_cuisson('My Title');
        $fixture->setDifficulte('My Title');
        $fixture->setIngredient('My Title');
        $fixture->setUser('My Title');
        $fixture->setEtape('My Title');
        $fixture->setIngredients('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'recette[titre]' => 'Something New',
            'recette[description]' => 'Something New',
            'recette[temps_de_preparation]' => 'Something New',
            'recette[temps_de_cuisson]' => 'Something New',
            'recette[difficulte]' => 'Something New',
            'recette[ingredient]' => 'Something New',
            'recette[user]' => 'Something New',
            'recette[etape]' => 'Something New',
            'recette[ingredients]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/recette/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getTemps_de_preparation());
        self::assertSame('Something New', $fixture[0]->getTemps_de_cuisson());
        self::assertSame('Something New', $fixture[0]->getDifficulte());
        self::assertSame('Something New', $fixture[0]->getIngredient());
        self::assertSame('Something New', $fixture[0]->getUser());
        self::assertSame('Something New', $fixture[0]->getEtape());
        self::assertSame('Something New', $fixture[0]->getIngredients());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Recette();
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setTemps_de_preparation('My Title');
        $fixture->setTemps_de_cuisson('My Title');
        $fixture->setDifficulte('My Title');
        $fixture->setIngredient('My Title');
        $fixture->setUser('My Title');
        $fixture->setEtape('My Title');
        $fixture->setIngredients('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/recette/');
    }
}
