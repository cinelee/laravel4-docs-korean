# 라우팅

- [기본 라우팅](#basic-routing)
- [라우트 파라미터](#route-parameters)
- [라우트 필터](#route-filters)
- [명칭이 붙여진 라우트](#named-routes)
- [라우트 그룹](#route-groups)
- [서브도메인 라우팅](#sub-domain-routing)
- [라우트 접두사](#route-prefixing)
- [404 에러 날리기](#throwing-404-errors)
- [리소스 컨트롤러](#resource-controllers)

<a name="basic-routing"></a>
## 기본 라우팅

대부분의 라우트는 `app/routes.php` 파일에 정의되어 있습니다. 가장 간단한 Laravel 라우트는 URI와 클로저 콜백으로 구성되어있습니다.

**기본 GET 라우트**

	Route::get('/', function()
	{
		return 'Hello World';
	});

**기본 POST 라우트**

	Route::post('foo/bar', function()
	{
		return 'Hello World';
	});

**모든 HTTP 동사에 응답하는 라우트 등록**

	Route::any('foo', function()
	{
		return 'Hello World';
	});

**라우트가 강제로 HTTPS에서 사용되도록 등록**

	Route::get('foo', array('https', function()
	{
		return 'Must be over HTTPS';
	}));

<a name="route-parameters"></a>
## 라우트 파라미터

	Route::get('user/{id}', function($id)
	{
		return 'User '.$id;
	});

**선택적인 라우트 파라미터**

	Route::get('user/{name?}', function($name)
	{
		return $name;
	});

**선택적인 라우트 파라미터와 기본값**

	Route::get('user/{name?}', function($name = 'John')
	{
		return $name;
	});

**정규표현식을 통한 라우트 제약**

	Route::get('user/{name}', function($name)
	{
		//
	})
	->where('name', '[A-Za-z]+');

	Route::get('user/{id}', function($id)
	{
		//
	})
	->where('id', '[0-9]+');

<a name="route-filters"></a>
## 라우트 필터

라우트 필터는 주어진 라우트에 대해 액세스를 제한하는 편리한 방법을 제공하며, 이는 인증이 필요한 사이트의 영역을 만드는데 유용합니다. Laravel 은 `auth` 필터, `guest` 필터, 그리고 `csrf` 필터를 포함하고 있습니다. 이 필터들은 `app/filters.php` 파일 안에 있습니다.

**라우트 필터 정의**

	Route::filter('old', function()
	{
		if (Input::get('age') < 200)
		{
			return Redirect::to('home');
		}
	});

만약 응답이 필터에서 리턴되는 경우, 해당 응답은 요청에 의한 응답으로 간주되며 실제 라우트는 실행되지 않습니다.

**라우트에 필터를 부여**

	Route::get('user', array('before' => 'old', function()
	{
		return 'You are over 200 years old!';
	}));

**라우트에 다수의 필터를 부여**

	Route::get('user', array('before' => 'auth|old', function()
	{
		return 'You are authenticated and over 200 years old!';
	}));

**필터 파라미터 지정**

	Route::filter('age', function($value)
	{
		//
	});

	Route::get('user', array('before' => 'age:200', function()
	{
		return 'Hello World';
	}));

**패턴 베이스 필터**

또한 필터가 URI에 따라 해당 라우트 전체에 적용되도록 지정할 수 있습니다.

	Route::filter('admin', function()
	{
		//
	});

	Route::when('admin/*', 'admin');

위 예제에서 `admin` 필터는 `admin/`으로 시작하는 모든 라우트에 적용됩니다. 별표(*)는 와일드카드로 사용되며, 어떤한 문자조합과도 일치합니다.

**필터 클래스**

클로저 대신 클래스를 사용하여 좀더 나은 필터링을 할수있습니다. 필터 클래스가 어플리케이션의 [IoC 컨테이너](/docs/ioc) 밖에서 해결 되므로 높은 테스트성을 위해 이 필터에서 디펜던시 인젝션을 사용할 수 있습니다.

**필터 클래스 정의**

	class FooFilter {

		public function filter()
		{
			// Filter logic...
		}

	}

**클래스 베이스 필터 등록**

	Route::filter('foo', 'FooFilter');

<a name="named-routes"></a>
## 명칭이 붙여진 라우트

명칭이 붙여진 라우트는 리디렉트나 URL을 생성할때 좀더 편리하게 참조할 수 있습니다. 이처럼 라우트에 대한 명칭을 지정할 수 있습니다.:

	Route::get('user/profile', array('as' => 'profile', function()
	{
		//
	}));

그리고 나서, 라우트의 명칭을 사용하여 URL이나 리디렉트를 생성할 수 있습니다.:

	$url = URL::route('profile');

	$redirect = Redirect::route('profile');

<a name="route-groups"></a>
## 라우트 그룹

가끔 필터를 라우트 무리에 적용해야 할때가 있을겁니다. 각각의 라우트에 필터를 지정하는 대신, 라우트 그룹을 사용하십시오.:

	Route::group(array('before' => 'auth'), function()
	{
		Route::get('/', function()
		{
			// Has Auth Filter
		});

		Route::get('user/profile', function()
		{
			// Has Auth Filter
		});
	});

<a name="sub-domain-routing"></a>
## 서브도메인 라우팅

Laravel 라우트는 와일드카드 서브도메인을 처리할 수 있으며, 도메인의 와일드카드 파라미터를 넘겨 줍니다.:

**서브도메인 라우트 등록**

	Route::group(array('domain' => '{account}.myapp.com'), function()
	{

		Route::get('user/{id}', function($account, $id)
		{
			//
		});

	});

<a name="route-prefixing"></a>
## 라우트 접두사

group 메소드의 배열 속성에 `prefix` 옵션을 사용하여 그룹화된 라우트들에 접두사를 추가 할 수 있습니다.:

**그룹화된 라우트들dp 접두사 부여**

	Route::group(array('prefix' => 'admin'), function()
	{

		Route::get('user', function()
		{
			//
		});

	});

<a name="throwing-404-errors"></a>
## 404 에러 날리기

라우트에서 수동으로 404 에러를 발생시키는 방법은 2가지가 있습니다. 첫번째는 `App::abort` 메소드를 사용하는 겁니다.:

	App::abort(404);

두번째는 `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` 인스턴스를 던지는 겁니다.

404 예외를 처리하고 이러한 오류에 대해 사용자 정의 응답을 사용하는 방법에 대한 자세한 내용은 매뉴얼의 [에러](/docs/errors#handling-404-errors) 섹션에서 찾을 수 있습니다.

<a name="resource-controllers"></a>
## 리소스 컨트롤러

리소스 컨트롤러는 리소스에 맞춰 쉽게 RESTful 컨트롤러를 구축하게 해줍니다.

[컨트롤러](/docs/controllers#resource-controllers) 에서 더 많은 정보를 보세요.
