# 보안

- [설정](#configuration)
- [비밀번호 저장](#storing-passwords)
- [사용자 인증](#authenticating-users)
- [라우트 보호](#protecting-routes)
- [HTTP 기본 인증](#http-basic-authentication)
- [비밀번호 리마인더 & 리셋](#password-reminders-and-reset)
- [암호화](#encryption)

<a name="configuration"></a>
## 설정

Laravel은 매우 간단한 인증 구현을 만드는 것을 목표로 합니다. 사실 거의 모든 것들이 이미 셋팅되어 있습니다. 인증 설정 파일은 `app/config/auth.php`에 있으며, 인증 기능의 형태를 수정할 수 있는 잘 문서화 된 몇가지의 옵션들을 포함하고 있습니다.

기본적으로 Laravel은 `app/models` 디렉토리 안에 디폴트 엘로퀀트 인증 드라이버를 사용하는 `User` 모델을 포함하고 있습니다. 이 모델에 대한 스키마를 만들 때 비밀번호 필드는 최소한 60개의 문자가 들어갈 수 있어야 합니다.

만약 어플리케이션이 엘로퀀트를 사용하지 않는다면, Laravel 쿼리 빌더를 사용하는  `database` 인증 드라이버를 사용할 수 있습니다.

<a name="storing-passwords"></a>
## 비밀번호 저장

Laravel `Hash` 클래스는 Bcrypt 해쉬 보안을 제공합니다.:

**Bcrypt를 사용하여 비밀번호 해쉬**

    $password = Hash::make('secret');

**해쉬값에 대한 암호를 확인**

    if (Hash::check('secret', $hashedPassword))
  	{
  		// 비밀번호 일치
  	}

<a name="authenticating-users"></a>
## 사용자 인증

`Auth::attempt` 메소드를 사용하여 어플리케이션에 로그인 할 수 있습니다.

	if (Auth::attempt(array('email' => $email, 'password' => $password)))
	{
		// 유효한 사용자 자격 증명
	}

`email`은 단지 예를 들어 사용했을 뿐, 필수 옵션은 아닙니다. 데이터베이스의 "사용자아이디"에 해당하는 어떠한 컬럼명을 사용해도 좋습니다.

어플리케이션에 "로그인 유지" 기능을 제공하려면, `attempt` 메소드의 두번째 인수에 사용자 인증을 무기한으로 유지하는(또는 수동으로 로그아웃 할 때까지) `true`를 전달하면 됩니다.:

**사용자를 인증하고 "계속 유지"**

	if (Auth::attempt(array('email' => $email, 'password' => $password), true))
	{
		// 사용자 로그인 유지
	}

**메모:** `attempt` 메소드가 `true`를 반환한다면 그 사용자는 로그인 된걸로 간주됩니다.

**추가 조건과 함께 사용자 인증**

추가 조건을 사용하여 사용자가 (예를 들어) '유효한지' 또는 '정지되지 않았는지'를 확인할 수 있습니다.:

    if (Auth::attempt(array('email' => $email, 'password' => $password, 'active' => 1, 'suspended' => 0)))
    {
        // 사용자는 존재하고, 유효하며 정지되지 않음.
    }

사용자가 인증되고 나면 User 모델과 레코드을 액세스 할 수 있습니다.:

**로그인 된 사용자 액세스**

	$email = Auth::user()->email;

`loginUsingId` 메소드를 사용하여 사용자의 ID 만으로 간편하게 어플리케이션에 로그인 시킬 수 있습니다.:

	Auth::loginUsingId(1);

`validate` 메소드는 실제로 어플리케이션에 로그인 시키지 않고 사용자의 자격 증명을 검증할 수 있게 해줍니다.:

**로그인 없이 사용자의 자격 증명 검증**

	if (Auth::validate($credentials))
	{
		//
	}

또한 단일 요청을 위해 `once` 메소드를 사용하여 사용자를 어플리케이션에 로그인 시킬 수 있습니다. 이 경우 세션이나 쿠키는 사용되지 않습니다.

**단일 요청을 위해 사용자를 로그인**

	if (Auth::once($credentials))
	{
		//
	}

**사용자를 어플리케이션에서 로그아웃**

	Auth::logout();

<a name="protecting-routes"></a>
## 라우트 보호

라우트 필터는 주어진 라우트에 오직 인증된 사용자만 액세스 할수 있도록 해줍니다. Laravel 기본적으로 `auth` 필터를 제공하며 `app/filters.php`에 정의되어 있습니다.

**라우트 보호**

	Route::get('profile', array('before' => 'auth', function()
	{
		// 인증된 사용자만 들어갈 수 있습니다.
	}));

### CSRF 보호

Laravel은 크로스사이트 요청들로 부터 어플리케이션을 보호할 수 있는 간단한 메소드를 제공합니다.

`csrf_token()` 또는 `Session::getToken()`를 사용하여 **폼에 CSRF 토큰 입력**

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

**제출된 CSRF 토큰을 검증**

    Route::post('register', array('before' => 'csrf', function()
    {
        return 'You gave a valid CSRF token!';
    }));

<a name="http-basic-authentication"></a>
## HTTP 기본 인증

HTTP 기본 인증은 "로그인" 전용 페이지 없이 유저를 어플리케이션에 인증 해주는 빠른 방법을 제공 합니다. 시작하려면, `auth.basic` 필터를 라우트에 추가하면 됩니다.:

**HTTP 기본 인증으로 라우트 보호**

	Route::get('profile', array('before' => 'auth.basic', function()
	{
		// Only authenticated users may enter...
	}));

세션에 사용자의 식별 쿠키 설정없이 HTTP 기본 인증을 사용 할 수도 있으며 이는 API 인증을 할때 특히 유용합니다. 이렇게 하려면, `onceBasic` 메소드를 반환하는 필터를 정의하면 됩니다.:

**저장하지 않는 HTTP 기본 필터 설정**

	Route::filter('basic.once', function()
	{
		return Auth::onceBasic();
	});

<a name="password-reminders-and-reset"></a>
## 비밀번호 리마인더 & 리셋

### 비밀번호 리마인더 보내기

대부분의 웹 어플리케이션은 사용자가 잃어버린 비밀번호를 리셋하는 방법을 제공 합니다. 개발자가 각각의 어플리케이션에 이 기능을 매번 만들게 하는것 대신에, Laravel은 비밀번호 리마인더를 메일로 보내고 비밀번호를 리셋할 수 있는 편리한 방법을 제공합니다. 이 기능을 사용하려면 `User` 모델이 `Illuminate\Auth\RemindableInterface` 인터페이스를 구현하는지 확인해야 합니다. 물론 프레임워크에 포함된 모델은 이미 이 인터페이스를 구현하고 있습니다.

**RemindableInterface 인터페이스 구현**

	class User extends Eloquent implements RemindableInterface {

		public function getReminderEmail()
		{
			return $this->email;
		}

	}

그 다음, 비밀번호 리셋 토큰을 저장할 테이블을 만들어야 합니다. 이 테이블의 마이그레이션을 만들려면, 간단하게 `auth:reminders` 아티즌 커맨드를 실행하면 됩니다:

**리마인더 테이블 마이그레이션 생성**

	php artisan auth:reminders

	php artisan migrate

`Password::remind` 메소드를 사용하여 패스워드 리마인더를 보낼 수 있습니다.:

**패스워드 리마인더 보내기**

	Route::post('password/remind', function()
	{
		$credentials = array('email' => Input::get('email'));

		return Password::remind($credentials);
	});

`remind` 메소드에 전달된 인수들은 `Auth::attempt` 메소드에 전달되는 인수들과 비슷하다는 것에 주목하세요. 이 메소드는 `User`를 조회하고 이메일로 비밀번호 리셋 링크를 보냅니다. 비밀번호 리셋 폼의 링크를 만드는데 사용되는 `token` 변수가 이메일 뷰에 전달 됩니다.

> **메모:**  `auth.reminder.email` 설정 옵션을 변경하여 어떤 뷰가 이메일 메시지로 사용될 지 지정할 수 있습니다. 물론 디폴트 뷰가 이미 제공되어 있습니다.

`remind` 메소드의 두번째 인수에 클로저를 전달하여 사용자에게 보내지는 메시지 인스턴스를 수정할 수 있습니다.:

	return Password::remind($credentials, function($m)
	{
		$m->subject('Your Password Reminder');
	});

라우트에서 바로 `remind` 메소드의 결과를 반환한다는 것을 알고 있을 겁니다. 디폴트로 `remind` 메소드는 현재 URI로의 `Redirect`를 반환합니다. 비밀번호를 리셋을 진행하는 동안 오류가 발생한다면, `error` 변수가 세션에 플래시되며 `reminders` 언어파일에서 라인을 추출하는 `reason` 변수 또한 세션에 플래시 됩니다. 그러므로 비밀번호 리셋 폼은 다음과 같을 수 있습니다.:

	@if (Session::has('error'))
		{{ trans(Session::get('reason')) }}
	@endif

	<input type="text" name="email">
	<input type="submit" value="Send Reminder">

### 비밀번호 리셋

사용자가 리마인더 이메일에서 리셋 링크를 클릭하면, 숨겨진 `token` 필드와 `password`, `password_confirmation` 필드를 포함하고 있는 폼으로 이동 됩니다. 아래는 비밀번호 리셋 폼에 대한 라우트 예제 입니다.:

	Route::get('password/reset/{token}', function($token)
	{
		return View::make('auth.reset')->with('token', $token);
	});

그리고 비밀번호 리셋 폼은 다음과 같을 수 있습니다.:

	@if (Session::has('error'))
		{{ trans(Session::get('reason')) }}
	@endif

	<input type="hidden" name="token" value="{{ $token }}">
	<input type="text" name="email">
	<input type="password" name="password">
	<input type="password" name="password_confirmation">

다시 말하지만, 우리는 비밀번호를 리셋하는 도중 프레임워크에 의해 검출되는 오류를 표시하기 위해 `Session`을 사용하고 있습니다. 다음으로, 비밀번호를 리셋 처리를 위해 `POST` 라우트를 정의할 수 있습니다:

	Route::post('password/reset/{token}', function()
	{
		$credentials = array('email' => Input::get('email'));

		return Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();

			return Redirect::to('home');
		});
	});

비밀번호 리셋이 성공이라면, 실제로 저장을 할수 있도록 `User` 인스턴스와 비밀번호가 클로저에 전달 됩니다. 그런 다음, `Redirect`를 반환하거나, `reset` 메소드에 의해 반환 될 어떤 다른 종류의 응답을 반환할 수도 있습니다. `reset` 메소드가 자동으로 요청된 `token`의 유효성과 자격 증명의 유효성, 그리고 비밀번호 일치를 확인한다는 것을 알고 계십시오.

또한 `remind` 메소드와 비슷하게 비밀번호를 리셋하는 동안 오류가 발생하면 `reset` 메소드는 `error`, `reason` 변수와 함께 현재 URI로의 `Redirect`를 반환합니다. 

<a name="encryption"></a>
## 암호화

Laravel은 mcrypt PHP extension을 통해 강력한 AES-256 암호화 기능을 제공합니다.:

**값 암호화**

	$encrypted = Crypt::encrypt('secret');

> **메모:** `app/config/app.php` 파일의 `key` 옵션에 32자리의 랜덤 문자열을 셋팅했는지 확인하세요. 그렇지 않을경우, 암호화된 값이 안전하지 않을 수 있습니다.

**값 복호화**

	$decrypted = Crypt::decrypt($encryptedValue);
