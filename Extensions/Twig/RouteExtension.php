<?php

namespace Sailor\Extensions\Twig;

use \Twig\TwigFunction;
use \Twig\Extension\AbstractExtension;
use Sailor\Core\Router;

/**
 * 與路徑相關的Twig功能
 * 
 * @author palehose
 */
class RouteExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('version', [$this, 'version']),
            new TwigFunction('pathFor', [$this, 'pathFor']),
        ];
    }

    /**
     * 替js、css的檔名加入時間戳記以避免瀏覽器暫存
     * 
     * @param string $url css或js的網址
     * @return string 加入時間戳記後的網址
     */
    public function version($url)
    {
        return Router::version($url);
    }

    /**
     * 根據 Route name 產生實際路徑
     * 
     * @param string $name Route name
     * @return string 實際路徑
     */
    public function pathFor($name)
    {
        return Router::pathFor($name);
    }
}