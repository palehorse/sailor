## Sailor
Sailor 是一個以 Slim 和 Phinx 為基礎開發的輕量框架

### 建立Controller

在專案目錄下建立一個名為 Controllers 的目錄，再建立一個 Controller 的 PHP 檔案，例如：

```php
<?php

namespace Sailor\Controllers;

use Sailor\Core\Controller;

class TestContrller extends Contrller 
{
    public function show()
    {
        echo 'This is a test page';
    }
} 
```

### Router

#### 建立 Router

在專案目錄下建立一個名為 routes 的目錄，再建立一個 route 的 PHP 檔案，例如：

```php
<?php
  
use Sailor\Core\Router;

Router::get('/test', 'TestController::show')->setName('TestShow');
```

完成後，在瀏覽器的網址內輸入：https://yourwebsite/test，即可執行您在 Controller 內的程式。

#### 建立群組型的Router

在 route 檔案裡使用 group function，例如：

```php
<?php
  
use Sailor\Core\Router;

Router::group('/test', function() {
    Router::get('/show', 'TestController::show')->setName('TestShow');
});
```

完成後，在瀏覽器的網址內輸入：https://yourwebsite/test/show，即可執行您在 Controller 內的程式。

### MiddleWare

在專案目錄下建立一個名為 MiddleWares 的目錄，再建立一個 MiddleWare 的 PHP 檔案，例如：

```php
<?php
namespace Sailor\MiddleWares;

use Sailor\Core\Router;
use Slim\Http\Request;
use Slim\Http\Response;

class MustSignInMiddleWare
{
    public function __invoke(Request $request, Response $response, Callable $next)
    {
        $member = $_SESSION['member'];
        if (empty($member)) {
            return $response->withRedirect(Router::pathFor('ShowSignIn'));
        }

        return $next($request, $response);
    }
}
```

完成後，將其加入 route 檔案中適當的位置，例如：

```php
<?php
  
use Sailor\Core\Router;

Router::group('/test', function() {
    Router::get('/show', 'TestController::show')->setName('TestShow');
})->add(new MustSignInMiddleWare);
```

如此一來，所有網址中包含 test 的請求(request)都必須先進行會員登入。

### 資料處理

#### Model

在專案目錄下建立一個名為 Models 的目錄，並以資料庫的 table 為基本單位，建立 Model 檔案，例如：

```php
<?php
namespace Sailor\Models;

use Pussle\ORM\Model;

class TestTable extends Model
{
    protected $table = 'test_table';
}
```

#### Repository

在專案目錄下建立一個名為 Repository 的目錄，再建立一個 Repository 的 PHP 檔案，並在適當的地方使用 Model，例如：

```php
<?php
namespace Sailor\Repository;

use Sailor\Models\TestTable;

class TestRepository
{
    public function getTestDataById($id)
    {
        $Test = new TestTable;
        $Test->select(['id', 'name', 'desc', 'created_at']);
        $Test->where('id', $id);
        return $Test->fetch();
    }
}
```

#### 在 Controller 中處理資料

```php
<?php

namespace Sailor\Controllers;

use Sailor\Core\Controller;
use Sailor\Repository\TestRepository;

class TestContrller extends Contrller 
{
    public function show(TestRepository $TestRepostory)
    {
        $data = $TestRepostory->getTestDataById($id);
    }
} 
```

### 前端程式壓縮與合併

#### 在 bullets.mix.js 中設定好要壓縮與合併的 CSS 和 JavaScript

```javascript
const bullets = require('./build/bullets');

/** 共用的檔案 */
var commonCss = [
    '../../node_modules/bootstrap/dist/css/bootstrap.css', 
    'common.css',
    'dialog.css',
    'switch.css',
    '../../node_modules/bootstrap-select/dist/css/bootstrap-select.min.css'
];

/** 欲合併的檔案 */
bullets.js(['app.js', 'signin/signin.js'], 'signin.js') 
       .js(['app.js', '../../node_modules/drag2upload/drag2upload.jquery.js', 'member.js'], 'member.js')
       .css(commonCss.concat(['signin.css']), 'signin.css')
       .css(commonCss.concat(['error.css']) , 'error.css')
       .css(commonCss.concat(['member.css']) , 'member.css');

module.exports = (function(bullets) {
    return bullets.getConfig();
})(bullets);
```

#### 設定完成後，使用 npm 進行壓縮與合併

```bash
npm run prod
```

以上述的 app.js 和 signin/signin.js 合併為例，新產生的壓縮合併檔案為 signin.js，位於 public/js 之下，在 HTML 中僅需加入：

```html
<script src="/js/signin.js"></script>
```

或是加入編號避免 JS 和 CSS 的檔案暫存：

```php+HTML
<script src="{{version('/js/signin.js')}}"></script>
```

