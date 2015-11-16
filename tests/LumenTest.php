<?php

use Laravel\Lumen\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository;
use PhanAn\CascadingConfig\CascadingConfigServiceProvider;

class CascadingConfigTest extends PHPUnit_Framework_TestCase
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
        // Init an Lumen Application.
        // Set the environment to a fake foo,
        // create an empty instance of Config,
        // and populate it with some sample items.
        $this->app = new Application(sys_get_temp_dir());

        putenv('APP_ENV=foo');

        $this->app->instance('config', new Repository());

        $this->app['config']->set('app', [
            'url' => 'http://origin.dev',
        ]);

        $this->f = new Filesystem();
    }

    public function tearDown()
    {
        $this->f->delete($this->app->getConfigurationPath().'/../config.foo');
    }

    public function testConfigCascaded()
    {
        $this->f->makeDirectory($this->app->getConfigurationPath().'/../config.foo', 0755, true, true);
        $this->f->put($this->app->getConfigurationPath().'/../config.foo/app.php', "<?php return ['url' => 'http://cascaded.dev', 'foo' => 'bar'];");
        $this->setupServiceProvider();

        $this->assertEquals($this->app['config']->get('app.url'), 'http://cascaded.dev');
        $this->assertEquals($this->app['config']->get('app.foo'), 'bar');
    }

    public function testNestedConfigSupported()
    {
        $this->f->makeDirectory($this->app->getConfigurationPath().'/../config.foo/nested', 0755, true, true);
        $this->f->put($this->app->getConfigurationPath().'/../config.foo/nested/sample.php', "<?php return ['foo' => 'bar'];");
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
