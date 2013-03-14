# 캐싱

- [설정](#configuration)
- [캐시 사용법](#cache-usage)
- [데이터베이스 캐싱](#database-cache)

<a name="configuration"></a>
## 설정

Laravel은 다양한 캐싱 시스템에 대해 통합 된 API를 제공합니다. 캐시 설정은 `app/config/cache.php` 파일에 있습니다. 이 파일에서 어플리케이션에 어떤 캐시 드라이버를 디폴트로 사용할 지 지정할 수 있습니다. Laravel은 [Memcached](http://memcached.org)나 [Redis](http://redis.io)같이 인기있는 백엔드 캐시를 지원합니다.

또한 캐시 설정 파일은 다양한 옵션을 포함하고 있으므로, 이 옵션들을 읽어보시길 바랍니다. 기본적으로 Laravel은 파일시스템에 시리얼라이즈된 캐시 오브젝트를 저장하는 `file` 캐시 드라이버를 사용 하도록 설정되어 있습니다. 규모가 큰 어플리케이션의 경우, Memcached나 APC등의 메모리 캐시를 사용할 것을 권장합니다.

<a name="cache-usage"></a>
## 캐시 사용법

**캐시에 아이템 저장**

    Cache::put('key', 'value', $minutes);

**캐시에서 아이템 조회**

	$value = Cache::get('key');

**아이템을 조회하거나 디폴트 값을 반환**

	$value = Cache::get('key', 'default');

	$value = Cache::get('key', function() { return 'default'; });

**캐시에 영구적인 아이템 저장**

	Cache::forever('key', 'value');

때때로 캐시에서 아이템을 조회할 수도 있지만 만약 요청한 아이템이 없을 경우 디폴드 값을 저장할 수도 있습니다. `Cache::remember` 메소드를 사용 하여 그렇게 할 수 있습니다.:

	$value = Cache::remember('users', $minutes, function()
	{
		return DB::table('users')->get();
	});

또한 `remember` 메소드와 `forever` 메소드를 혼합 할 수도 있습니다.:

	$value = Cache::rememberForever('users', function()
	{
		return DB::table('users')->get();
	});

캐시에 저장되는 모든 아이템은 시리얼라이즈 되므로 어떠한 종류의 데이터를 저장해도 상관없습니다.

**캐시에서 아이템 제거**

	Cache::forget('key');

<a name="database-cache"></a>
## 데이터베이스 캐시

`database`를 캐시 드라이버로 사용할 때는 캐쉬 아이템을 저장 할 테이블을 만들어야 합니다. 아래 코드는 테이블에 대한 `Schema` 선언입니다.:

	Schema::create('cache', function($t)
	{
		$t->string('key')->unique();
		$t->text('value');
		$t->integer('expiration');
	});
