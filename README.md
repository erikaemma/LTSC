# 东拼西凑PHP框架一枚


## 目标
一个最简化的PHP框架，只保证最基本的路由功能和输出功能。

## 过程
主体设计框架来源于[TIGERB/easy-php](https://github.com/TIGERB/easy-php)。  
最主要的是其中对异常处理、Handler、Request/Response、Container注入以及路由策略的设计。  
其中，Container做了较大修改，无论注入什么类（object、回调函数返回的object、字符串或者单例模式的字符串），返回的都是一个实例化的object。  
然后对路由策率中的UserDefine做了修改：最初是想保留easy-php的路由策率再加上[zqhong/route](https://github.com/zqhong/route)的路由以实现按需配置。但后来发现了[parvinShi/eagle](https://github.com/parvinShi/eagle)里提供的路由解析方法并惊为天人（没见过什么大世面哈）所以决定采用parvinShi/eagle的路由解析方法并替换UserDefine。  
但是无论是zqhong/route还是parvinShi/eagle里面的正则表达式我都看不懂，但zqhong/route稍微简单就是代码有问题，就算是修正后的也无法直接替换parvinShi/eagle的路由的功能，所以最后的方案是按我自己的理解重写了parvinShi/eagle的路由解析方法，并使用zqhong/route的uri解析方法，然后自己实现的低速度匹配方法——字符串比对。  
最后，本项目对request使用了[vlucas/phpdotenv](https://packagist.org/packages/vlucas/phpdotenv)的rquired方法作为数据校验方法。

## 食用方法

实例化一个App或者Core：
1. 参数是给vlucas/phpdotenv读取`.env`文件用的
2. App使用了自带的各种Handler，如需自配（比如使用其他Handler，比如symfony/http-foundation提供的request之类的，可以使用后面提供的接口自配并按照App类中的方法注入即可
3. 配置`.env`
4. （可选）代码中配置动态路由
5. （可选）重写ResponseHandler和HttpException实现自己需要的输出接口
6. run

## 接口

### 接口
`LTSC\Helper\InterfaceHandler`: 自定义Handler的接口，必须

### 抽象类
`LTSC\Helper\AbstractAction`: Action类
`LTSC\Helper\AbstractFilter`: 给App::run()或者Core::run()用的过滤类，用来过滤request，也可以在则使对IP、访问频率或者API Key做过滤
`LTSC\Helper\AbstractRequest`: Request基类
`LTSC\Helper\AbstractResponse`: Response基类
`LTSC\Helper\AbstractRouter`: 路由策略基类

### 其他
src/Handler目录下除了EvnHandler.php外其余均是final类，因此你可以直接继承并重写EnvHandler而不用`LTSC\Helper\AbstractEnv`完全重写——如果还是基于phpdotenv的话。

## 不可能的目标
反反复复重写了好几遍，再也不想写TEST了……