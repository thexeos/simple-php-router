<?php

require_once 'Dummy/DummyMiddleware.php';
require_once 'Dummy/DummyController.php';
require_once 'Dummy/Handler/ExceptionHandler.php';
require_once 'Helpers/TestRouter.php';

class RouterUrlTest extends PHPUnit_Framework_TestCase
{

    public function testSimilarUrls()
    {
        // Match normal route on alias
        TestRouter::resource('/url11', 'DummyController@method1');
        TestRouter::resource('/url1', 'DummyController@method1', ['as' => 'match']);

        TestRouter::debugNoReset('/url1', 'get');

        $this->assertEquals(TestRouter::getUrl('match'), TestRouter::getUrl());

        TestRouter::router()->reset();
    }

    public function testUrls()
    {
        // Match normal route on alias
        TestRouter::get('/', 'DummyController@method1', ['as' => 'home']);

        TestRouter::get('/about', 'DummyController@about');

        TestRouter::group(['prefix' => '/admin', 'as' => 'admin'], function () {

            // Match route with prefix on alias
            TestRouter::get('/{id?}', 'DummyController@method2', ['as' => 'home']);

            // Match controller with prefix and alias
            TestRouter::controller('/users', 'DummyController', ['as' => 'users']);

            // Match controller with prefix and NO alias
            TestRouter::controller('/pages', 'DummyController');

        });

        TestRouter::group(['prefix' => 'api', 'as' => 'api'], function () {

            // Match resource controller
            TestRouter::resource('phones', 'DummyController');

        });

        TestRouter::controller('gadgets', 'DummyController', ['names' => ['getIphoneInfo' => 'iphone']]);

        // Match controller with no prefix and no alias
        TestRouter::controller('/cats', 'CatsController');

        // Pretend to load page
        TestRouter::debugNoReset('/', 'get');

        $this->assertEquals('/gadgets/iphoneinfo/', TestRouter::getUrl('gadgets.iphone'));

        $this->assertEquals('/api/phones/create/', TestRouter::getUrl('api.phones.create'));

        // Should match /
        $this->assertEquals('/', TestRouter::getUrl('home'));

        // Should match /about/
        $this->assertEquals('/about/', TestRouter::getUrl('DummyController@about'));

        // Should match /admin/
        $this->assertEquals('/admin/', TestRouter::getUrl('DummyController@method2'));

        // Should match /admin/
        $this->assertEquals('/admin/', TestRouter::getUrl('admin.home'));

        // Should match /admin/2/
        $this->assertEquals('/admin/2/', TestRouter::getUrl('admin.home', ['id' => 2]));

        // Should match /admin/users/
        $this->assertEquals('/admin/users/', TestRouter::getUrl('admin.users'));

        // Should match /admin/users/home/
        $this->assertEquals('/admin/users/home/', TestRouter::getUrl('admin.users@home'));

        // Should match /cats/
        $this->assertEquals('/cats/', TestRouter::getUrl('CatsController'));

        // Should match /cats/view/
        $this->assertEquals('/cats/view/', TestRouter::getUrl('CatsController', 'view'));

        // Should match /cats/view/
        //$this->assertEquals('/cats/view/', TestRouter::getUrl('CatsController', ['view']));

        // Should match /cats/view/666
        $this->assertEquals('/cats/view/666/', TestRouter::getUrl('CatsController@getView', ['666']));

        // Should match /funny/man/
        $this->assertEquals('/funny/man/', TestRouter::getUrl('/funny/man'));

        // Should match /?jackdaniels=true&cola=yeah
        $this->assertEquals('/?jackdaniels=true&cola=yeah', TestRouter::getUrl('home', null, ['jackdaniels' => 'true', 'cola' => 'yeah']));

        TestRouter::router()->reset();

    }

}