<?php
/**
 * Created by le118
 * Author: le118
 * Date: 2019/12/30
 * Time: 18:04
 * 通过PHP类的反射实现依赖注入，自动解决解决类与类的依赖关系
 * Dependency Injection
 */


class Point
{
    public $x;
    public $y;

    public function __construct($x = 0, $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }
}

class Circle
{
    /*
     * @var int
     */
    public $radius; //半径

    /**
     * @var Point
     */
    public $center; //圆心点

    const PI = 3.14;

    public function __construct(Point $center, $radius = 1)
    {
        $this->center = $center;
        $this->radius = $radius;
    }

    /**
     * 打印圆点坐标
     */
    public function printCenter()
    {
        printf('center coordinate is (%d,%d)', $this->center->x, $this->center->y);
    }

    /**
     * 计算圆的面积
     */
    public function area()
    {
        return self::PI * pow($this->radius, 2);
    }
}

$reflection = new reflectionClass(Circle::class);

/*常量*/
//$constants = $reflection->getConstants();
//var_dump($constants);

/*属性*/
//$properties = $reflection->getProperties();
//var_dump($properties);

/*方法*/
//$methods = $reflection->getMethods();
//var_dump($methods);

/*构造*/
//$constructor = $reflection->getConstructor();
//$parameters = $constructor->getParameters();
//
//var_dump($parameters);
//
//$result = $parameters[1]->isDefaultValueAvailable();
//
//if($result){
//    $value = $parameters[1]->getDefaultValue();
//}else{
//    $value = '0';
//}
//var_dump($value);

function make($class)
{
    if (!class_exists($class)) {
        throw new Exception('invalid class.');
    }
    $reflection = new ReflectionClass($class);
    $construct = $reflection->getConstructor();
    $parameters = $construct->getParameters();
    $dependencies = getDependencies($parameters);
    return $reflection->newInstanceArgs($dependencies);
}

function getDependencies($parameters)
{
    $dependencies = [];
    foreach ($parameters as $parameter) {
        $class = $parameter->getClass();
        if (is_null($class)) {
            if($parameter->isDefaultValueAvailable()){
                $dependencies[] = $parameter->getDefaultValue();
            }else{
                //如果是必填的参数，暂时先赋0值简单处理
                $dependencies[] = '0';
            }
        } else {
            $dependencies[] = make($class->name);
        }
    }
    return $dependencies;
}

$circle = make('Circle');
var_dump($circle);
$circle->center->x = 1;
$circle->center->y = 1;
$circle->radius = 2;
$area = $circle->area();
var_dump($area);

