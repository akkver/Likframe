<?php declare(strict_types=1);

define("FRAME_BASE_PATH", __DIR__); // 框架
define("FRAME_START_TIME", microtime(true)); // 开始时间
define("FRAME_START_MEMORY", memory_get_usage()); // 开始闪存

class App implements Psr\Container\ContainerInterface
{
    public array $binding = []; // 绑定关系
    // if you declared a property such as private ?int $id;,
    // if you don't want it to be set at __construct,
    // it must have a default value through private ?int $id = null;
    // PHP7.4 property type hints
    private static ?App $instance = NULL; // 当前实例
    protected array $instances = []; // 存放所有实例

    private function __construct()
    {
        self::$instance = $this; // App类的实例
        $this->register(); // 注册绑定
        $this->boot(); // 服务注册之后 才能启动
    }

    public function get($abstract)
    {
        // 此服务已经实例
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        // 服务是闭包 加()就可以执行
        $instance = $this->binding[$abstract]['concrete']($this);
        if ($this->binding[$abstract]['is_singleton']) {
            // 设置为单例
            $this->instances[$abstract] = $instance;
        }
        return $instance;
    }

    public function has($id)
    {

    }

    // 当前App实例 单例
    public static function getContainer()
    {
        return self::$instance ?: self::$instance = new self();
    }

    /**
     * 生成绑定关系
     *
     * @param string $abstract 设置为key
     * @param void|string $concrete 设置为value
     * @param boolean $is_singleton 此服务要不要变成单例
     * @return void
     */
    public function bind($abstract, $concrete, $is_singleton = false)
    {
        // 如果具体实现不是闭包 则生成闭包
        if (!$concrete instanceof Closure) {
            $concrete = function ($app) use ($concrete) {
                return $app->build($concrete);
            };
        }
        $this->binding[$abstract] = compact('concrete', 'is_singleton');
    }

    protected function getDependencies($parameters)
    {
        $dependencies = array(); // 当前类的所有依赖
        foreach ($parameters as $parameter) {
            if ($parameter->getClass()) {
                $dependencies[] = $this->build($parameter->getClass()->name);
            }
        }
        return $dependencies;
    }

    // 解析依赖
    public function build($concrete)
    {
        $reflector = null;
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            echo $e->getMessage();
        } // 反射
        $constructor = $reflector->getConstructor(); // 获取构造函数
        // 没有构造函数 说明没有依赖  直接返回实例
        if (is_null($constructor)) {
            return $reflector->newInstance();
        }
        $dependencies = $constructor->getParameters(); // 获取构造函数的所有参数
        $instances = $this->getDependencies($dependencies); // 当前类的所有实例的依赖
        return $reflector->newInstance($instances); // 和 new 类（$instances）一样了
    }

    protected function register()
    {
        $registers = [
            'response' => \core\Response::class,
            'router' => \core\RouteCollection::class,
            'pipeline' => \core\PipeLine::class,
            'config' => \core\Config::class,
            'db' => \core\Database::class,
        ];

        foreach ($registers as $name => $concrete) {
            $this->bind($name, $concrete, true);
        }
    }

    protected function boot()
    {
        App::getContainer()->get('config')->init();
        App::getContainer()->get('router')->group([
            'namespace' => 'App\\controller',
            'middleware' => [
                \App\middleware\WebMiddleWare::class,
            ]
        ], function ($router) {
            // 这里require之后，web.php也就有$router了
            require_once FRAME_BASE_PATH.'/routes/web.php';
        });

        App::getContainer()->get('router')->group([
            'namespace' => 'App\\controller',
            'prefix' => 'api'
        ], function ($router) {
            require_once FRAME_BASE_PATH.'/routes/api.php';
        });
    }

}
