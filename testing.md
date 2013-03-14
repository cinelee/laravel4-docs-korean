# 유닛 테스팅

- [소개](#introduction)
- [테스트 정의 & 실행](#defining-and-running-tests)
- [테스트 환경](#test-environment)
- [테스트에서 라우트 호출](#calling-routes-from-tests)
- [헬퍼 메소드](#helper-methods)

<a name="introduction"></a>
## 소개

Laravel은 유닛 테스팅을 염두하여 만들어졌습니다. 사실, PHPUnit을 사용한 테스트는 이미 지원되며, `phpunit.xml` 파일은 어플리케이션에 맞게 이미 설정되어있습니다. PHPUnit 뿐만 아니라 Laravel은 테스팅 하는 동안 뷰를 검사하고 조종하게 해주며 웹 브라우저를 시뮬레이션 할 수 있도록 해주는 Symfony HttpKernel, DomCrawler, BrowserKit 컴포넌트들을 사용합니다.

테스트 예제 파일은 `app/tests` 디렉토리에 제공되어 있습니다. 새로운 Laravel 어플리케이션을 설치한 다음, 테스트를 실행하려면 간단하게 커맨드 라인에서 `phpunit`을 실행하면 됩니다.

<a name="defining-and-running-tests"></a>
## 테스트 정의 & 실행

테스트 케이스를 만들기 위해서는, 간단하게 `app/tests` 디렉토리에 새로운 테스트 파일을 생성하면 됩니다. 테스트 클래스는 `TestCase`를 확장해야 합니다. 그런다음, 평소 PHPUnit을 사용하던것처럼 테스트 메소드를 정의하면 됩니다.

**테스트 클래스 예제**

    class FooTest extends TestCase {
  
  		public function testSomethingIsTrue()
  		{
  			$this->assertTrue(true);
  		}
  
  	}

터미널에서 `phpunit` 커맨드을 사용하여 모든 테스트 파일을 실행할 수 있습니다.

> **메모:** 만약 자신만의 `setUp` 메소드를 정의했다면 반드시 `parent::setUp`을 호출해야 합니다.

<a name="test-environment"></a>
## 테스트 환경

유닛 테스트를 실행할 경우, Laravel은 자동으로 설정 환경을 `testing`으로 설정합니다. 또한, Laravel은 테스트 환경의 `세션`과 `캐시` 설정 파일들을 인클루드 합니다. 테스트 환경에서 이 두 설정 파일은 테스트를 하는 동안 세션이나 캐시 데이터가 지속되지 않도록 `배열` 드라이버를 사용게끔 설정되어 있습니다.  

<a name="calling-routes-from-tests"></a>
## 테스트에서 라우트 호출

`call` 메소드를 사용하여 간편하게 라우트를 호출할 수 있습니다.:

**테스트에서 라우트 호출**

	$response = $this->call('GET', 'user/profile');

	$response = $this->call($method, $uri, $parameters, $files, $server, $content);

그런다음 객체를 `Illuminate\Http\Response` 검사할 수 있습니다.:

	$this->assertEquals('Hello World', $response->getContent());

또한 테스트에서 컨트롤러를 호출할 수도 있습니다.:

**테스트에서 컨트롤러 호출**

	$response = $this->action('GET', 'HomeController@index');

	$response = $this->action('GET', 'UserController@profile', array('user' => 1));

`getContent` 메소드는 응답의 평가된 문자열 컨텐츠를 반환합니다. 라우트가 만약 `View`를 반환한다면, `original` 프로퍼티를 사용하여 액세스 할 수 있습니다.:

	$view = $response->original;

	$this->assertEquals('John', $view['name']);

### 돔 크롤러

또한 라우트를 요청하여 컨텐츠를 검사할 수 있는 돔 크롤러 인스턴스를 받을 수도 있습니다.:

	$crawler = $this->client->request('GET', '/');

	$this->assertTrue($this->client->getResponse()->isOk());

	$this->assertCount(1, $crawler->filter('h1:contains("Hello World!")'));

크롤러 사용법은 [공식 매뉴얼](http://symfony.com/doc/master/components/dom_crawler.html)를 참조하세요.

<a name="helper-methods"></a>
## 헬퍼 메소드

`TestCase` 클래스는 어플리케이션의 테스팅을 더 쉽게 만드는 몇가지 헬퍼 메소드를 포함하고 있습니다.

`be` 메소드를 사용하여 현재 인증되어있는 사용자를 셋팅할 수 있습니다.:

**현재 인증되어 있는 사용자를 세팅**

	$user = new User(array('name' => 'John'));

	$this->be($user);

`seed` 메소드를 사용하여 테스트에서 테이터베이스를 다시 씨드할 수 있습니다.:

**테스트에서 데이터베이서를 다시 씨딩**

	$this->seed();

	$this->seed($connection);

씨딩 생성의 더 많은 정보는 매뉴얼의 [마이그레이션 & 씨딩](/docs/migrations#database-seeding) 섹션에서 찾을 수 있습니다.