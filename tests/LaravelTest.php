<?php

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use PhanAn\CascadingConfig\CascadingConfigServiceProvider;

class LaravelTest extends PHPUnit_Framework_TestCase
{
    /*
     * @var Application
     */
    protected $app;

    /*
     * @var Filesystem
     */
    protected $f;

    public function setUp()
    {
        // Init an Illuminate Application.
        // Set the environment to a fake foo,
        // create an empty instance of Config,
        // and populate it with some sample items.
        $this->app = new Application();
        $this->app['env'] = 'foo';
        $this->app->setBasePath(sys_get_temp_dir());
        $this->app->instance('config', new Repository());

        $this->app['config']->set('app', [
            'url' => 'http://origin.dev',
        ]);

        $this->f = new Filesystem();
    }

    public function tearDown()
    {
        $this->f->delete($this->app->configPath().'/../config.foo');
    }

    public function testConfigCascaded()
    {
        $this->f->makeDirectory($this->app->configPath().'/../config.foo', 0755, true, true);
        $this->f->put($this->app->configPath().'/../config.foo/app.php', "<?php return ['url' => 'http://cascaded.dev', 'foo' => 'bar'];");
        $this->setupServiceProvider();

        $this->assertEquals($this->app['config']->get('app.url'), 'http://cascaded.dev');
        $this->assertEquals($this->app['config']->get('app.foo'), 'bar');
    }

    public function testNestedConfigSupported()
    {
        $this->f->makeDirectory($this->app->configPath().'/../config.foo/nested', 0755, true, true);
        $this->f->put($this->app->configPath().'/../config.foo/nested/sample.php', "<?php return ['foo' => 'bar'];");
        $this->setupServiceProvider();

        $this->assertEquals($this->app['config']->get('nested.sample.foo'), 'bar');
    }

    protected function setupServiceProvider()
    {
        $provider = new CascadingConfigServiceProvider($this->app);
        $this->app->register($provider);

        return $provider;
    }
}
