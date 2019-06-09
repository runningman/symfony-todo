<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class TodoListControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureLoader->load([
            'fixtures/todolist.yml',
        ]);
    }

    /**
     * Access the homepage and see the title.
     *
     * @test
     */
    public function it_can_access_the_homepage(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Create your todo list ✅', $crawler->filter('h1')->text());
    }

    /**
     * Access the first to do list in database.
     *
     * @test
     */
    public function it_can_view_a_todolist_detail(): void
    {
        $this->client->request('GET', '/12345678-1234-1234-1234-123456781234');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertPageTitleSame('Test todo list');
    }

    /**
     * 404 triggered for non existent lists.
     *
     * @test
     */
    public function it_returns_not_found_response_for_non_existent_list(): void
    {
        $this->client->request('GET', '/not-found-list');

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * After creating a list it redirects to the to do list page.
     *
     * @test
     */
    public function it_creates_a_new_list_and_redirects(): void
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Create')->form();
        $form->setValues(['todo_list' => ['title' => 'My new list']]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertRouteSame('todoList.view');
    }

    /**
     * It's not possible to create a list with empty title.
     *
     * @test
     */
    public function it_cant_create_todo_list_with_empty_title(): void
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Create')->form();
        $this->client->submit($form);

        $this->assertFalse($this->client->getResponse()->isRedirect());
        $this->assertRouteSame('todoList');
        $this->assertContains(
            'This value should not be blank.',
            $this->client->getResponse()->getContent()
        );
    }
}