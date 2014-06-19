<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\ForumBaseTest;

class AbstractViewForumControllerTest extends ForumBaseTest {

	protected function getPackageProviders()
	{
		return array('\Atrakeur\Forum\ForumServiceProvider');
	}

	private function createController($categories, $topics)
	{
		return $this->getMockForAbstractClass(
			'\Atrakeur\Forum\Controllers\AbstractViewForumController',
			array($categories, $topics)
		);
	}

	private function createCategoriesMock()
	{
		return \Mockery::mock('Eloquent', '\Atrakeur\Forum\Repositories\CategoriesRepository');
	}

	private function createTopicsMock()
	{
		return \Mockery::mock('Eloquent', '\Atrakeur\Forum\Models\ForumTopic');
	}

	public function testGetIndex()
	{
		$categoriesMock = $this->createCategoriesMock();
		$topicsMock = $this->createTopicsMock();

		$categoryMock = new \stdClass();
		$categoryMock->url = 'url';
		$categoryMock->title = 'title';
		$categoryMock->subtitle = 'title';
		$categoryMock->subcategories = array();
		$categoriesMock->shouldReceive('getByParent')->andReturn(array($categoryMock));

		$controller = $this->createController($categoriesMock, $topicsMock);

		\App::instance('\Atrakeur\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Atrakeur\Forum\Models\ForumTopic', $topicsMock);
		\App::instance('\Atrakeur\Forum\Controllers\AbstractViewForumController', $controller);

		\Route:: get('testRoute', '\Atrakeur\Forum\Controllers\AbstractViewForumController@getIndex');
		$this->call('GET', 'testRoute');

		//$this->assertViewHas('categories');
	}

	public function testGetCategoryInvalid()
	{
		$categoriesMock = $this->createCategoriesMock();
		$topicsMock = $this->createTopicsMock();

		$categoriesMock->shouldReceive('getById')->once()->with(31415, array('parentCategory', 'subCategories', 'topics'))->andReturn(null);

		$controller = $this->createController($categoriesMock, $topicsMock);

		\App::instance('\Atrakeur\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Atrakeur\Forum\Models\ForumTopic', $topicsMock);
		\App::instance('\Atrakeur\Forum\Controllers\AbstractViewForumController', $controller);

		$this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
		\Route:: get('testRoute/{categoryId}/{categoryUrl}', '\Atrakeur\Forum\Controllers\AbstractViewForumController@getCategory');
		$this->call('GET', 'testRoute/31415/FalseTestName');
	}

	public function testGetCategory()
	{
		$categoriesMock = $this->createCategoriesMock();
		$topicsMock = $this->createTopicsMock();

		$categoryMock = new \stdClass();
		$categoryMock->url = 'url';
		$categoryMock->title = 'title';
		$categoryMock->subtitle = 'title';
		$categoryMock->subCategories = array();
		$categoryMock->topics = array();
		$categoryMock->parentCategory = null;
		$categoriesMock->shouldReceive('getById')->once()->with(1, array('parentCategory', 'subCategories', 'topics'))->andReturn($categoryMock);

		$controller = $this->createController($categoriesMock, $topicsMock);

		\App::instance('\Atrakeur\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Atrakeur\Forum\Models\ForumTopic', $topicsMock);
		\App::instance('\Atrakeur\Forum\Controllers\AbstractViewForumController', $controller);

		//$this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
		\Route:: get('testRoute/{categoryId}/{categoryUrl}', '\Atrakeur\Forum\Controllers\AbstractViewForumController@getCategory');
		$this->call('GET', 'testRoute/1/title');
	}

	public function tearDown()
	{
		\Mockery::close();
	}

}