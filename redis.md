# 레디스

- [소개](#introduction)
- [설정](#configuration)
- [사용법](#usage)

<a name="introduction"></a>
## 소개

[레디스](http://redis.io)는 오픈소스이며 고급 key-value 저장소 입니다. 레디스는 키가 [문자열](http://redis.io/topics/data-types#strings), [해쉬](http://redis.io/topics/data-types#hashes), [리스트](http://redis.io/topics/data-types#lists), [세트](http://redis.io/topics/data-types#sets), [정렬 세트](http://redis.io/topics/data-types#sorted-sets)를 포함할수 있기 때문에 종종 데이터베이스 구조 서버로 지칭됩니다.

<a name="configuration"></a>
## 설정

어플리케이션의 레디스 설정은 **app/config/database.php** 파일에 있습니다. 이 파일에서 어플리케이션에서 사용하는 레디스 서버를 포함하고 있는 **redis** 배열을 볼 수 있습니다.:

    'redis' => array(
  
  		'default' => array('host' => '127.0.0.1', 'port' => 6379),
  
  	),

기본 서버 설정은 로컬개발 환경에 충분합니다. 개발 환경에 따라 이 배열을 수정할 수 있습니다. 간단히 레디스 서버의 이름을 지정하고 호스트와 포트를 명시하면 됩니다.

<a name="usage"></a>
## 사용법

`Redis::connection` 메소드를 호출하여 레디스 인스턴스를 얻을 수 있습니다.:

	$redis = Redis::connection();

이 구문은 기본 레디스 서버 인스턴스를 제공합니다. 레디스 설정에 명시된 서버를 연결하려면 `connection` 메소드에 서버 이름을 전달하면 됩니다.:

	$redis = Redis::connection('other');

레디스 클라이언트 인스턴스가 연결되었다면 어떠한 [레디스 커맨드](http://redis.io/commands)도 레디스 인스턴스에 발행할 수 있습니다. Laravel은 매직 메소드를 사용하여 레디스 서버에 커맨드를 전달 합니다.:

	$redis->set('name', 'Taylor');

	$name = $redis->get('name');

	$values = $redis->lrange('names', 5, 10);

커맨드의 인자는 단순히 매직 메소드에 전달됩니다. 물론 꼭 매직 메소드를 사용해야 하는 것은 아닙니다. `command` 메소드를 사용하여 커맨드를 서버에 전달 할 수 있습니다.:

	$values = $redis->command('lrange', array(5, 10));

기본 커넥션에 커맨드를 실행하는 거라면, 그냥 `Redis` 클래스의 static 매직 메소드를 사용하세요.

	Redis::set('name', 'Taylor');

	$name = Redis::get('name');

	$values = Redis::lrange('names', 5, 10);

> **메모:** 레디스 [캐시](/docs/cache)와 [세션](/docs/session) 드라이버는 Laravel에 이미 포함되어 있습니다.