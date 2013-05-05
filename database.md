# 기본적인 데이터베이스 사용법

- [설정](#configuration)
- [쿼리 실행](#running-queries)
- [데이터베이스 트랜잭션](#database-transactions)
- [커넥션 액세스](#accessing-connections)

<a name="configuration"></a>
## 설정

Laravel은 데이터베이스 접속과 쿼리 실행을 매우 간단하게 만들어 줍니다. 데이터베이스 설정 파일은 `app/config/database.php` 입니다. 이 파일에서 모든 데이터베이스 커넥션뿐만 아니라 기본적으로 어떤 커넥션을 사용해야 하는지도 지정할 수 있습니다. 지원되는 모든 데이터베이스 시스템의 예제는 이 파일안에 제공되어 있습니다.

현재 Laravel은 MySQL, Postgres, SQLite, SQL Server 4가지의 데이터베이스 시스템을 지원합니다.

<a name="running-queries"></a>
## 쿼리 실행

데이터베이스 커넥션을 설정했다면 `DB` 클래스를 사용하여 쿼리를 실행할 수 있습니다.

**select 쿼리문 실행**

    $results = DB::select('select * from users where id = ?', array(1));

`select` 메소드는 결과물을 항상 `array`로 반환합니다.

**insert 쿼리문 실행**

	DB::insert('insert into users (id, name) values (?, ?)', array(1, 'Dayle'));

**update 쿼리문 실행**

	DB::update('update users set votes = 100 where name = ?', array('John'));

**delete 쿼리문 실행**

	DB::delete('delete from users');

> **노트:** `update`와 `delete` 문은 작업에 영향을 받은 행의 수를 반환합니다.

**일반문 실행**

	DB::statement('drop table users');

`DB::listen` 메소드를 사용하여 쿼리 이벤트를 주시 할 수 있습니다.:

**쿼리 이벤트 주시**

	DB::listen(function($sql, $bindings, $time)
	{
		//
	});

<a name="database-transactions"></a>
## 데이터베이스 트랜잭션

`transaction` 메소드를 사용하여 데이터베이스 트랜잭션에서 여러 연산을 할 수 있습니다.:

	DB::transaction(function()
	{
		DB::table('users')->update(array('votes' => 1));

		DB::table('posts')->delete();
	});

<a name="accessing-connections"></a>
## 커넥션 액세스

`DB::connection` 메소드를 사용하여 다수의 커넥션을 사용할 수 있습니다.:

	$users = DB::connection('foo')->select(...);

때때로 주어진 데이터베이스로 재접속을 해야할 때가 있습니다.:

	DB::reconnect('foo');