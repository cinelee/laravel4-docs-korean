# 이벤트

- [기본적인 사용법](#basic-usage)
- [리스너로 클래스를 사용](#using-classes-as-listeners)
- [이벤트 등록자](#event-subscribers)

<a name="basic-usage"></a>
## 기본적인 사용법

Laravel `Event` 클래스는 어플리케이션에서 이벤트들을 등록하고 청취할수 있게 해주는 간단한 옵저버 구현을 제공합니다.

**이벤트 등록**

    Event::listen('user.login', function($user)
  	{
  		$user->last_login = new DateTime;
  
  		$user->save();
  	});

**이벤트 실행**

	$event = Event::fire('user.login', array($user));

이벤트를 등록할때 운선순위를 지정할 수도 있습니다. 높은 우선순위를 가진 리스너가 먼저 실행되지만 우선순위가 같을 경우 등록된 순서대로 실행됩니다.

**우선순위와 함께 이벤트 등록**

	Event::listen('user.login', 'LoginHandler', 10);

	Event::listen('user.login', 'OtherHandler', 5);

가끔 다른 이벤트리스너로의 전달을 멈추고 싶을 때도 있습니다. 이럴 경우, 리스너에서 `false`를 반환하여 전달을 멈출수 있습니다.:

**이벤트의 전달 중지**

	Event::listen('user.login', function($event)
	{
		// Handle the event...

		return false;
	});

<a name="using-classes-as-listeners"></a>
## 리스너로 클래스를 사용

어떤 경우에는, 클로저말고 클래스를 사용하여 이벤트를 처리할 수도 있습니다.클래스 이벤트 리스너는 dependency injection을 제공하는 [Laravel IoC container](/docs/ioc)에서 해결됩니다.

**클래스 리스너 등록**

    Event::listen('user.login', 'LoginHandler');

디폴트로, `LoginHandler` 클래스의 `handle` 메소드가 호출됩니다.:

**이벤트 리스너 클래스 정의**

	class LoginHandler {

		public function handle($data)
		{
			//
		}

	}

디폴트 `handle` 메소드가 아닌 다른 메소드를 지정 할 수도 있습니다.:

**어떤 메소드를 등록할지 지정**

	Event::listen('user.login', 'LoginHandler@onLogin');

<a name="event-subscribers"></a>
## 이벤트 등록자

이벤트 등록자는 클래스 자체 내에서 여러 이벤트를 등록하게 해주는 클래스 입니다. 등록자는 이벤트 디스패처 인스턴스를 전달하는 `subscribe` 메소드를 정의해야 합니다.:

**이벤트 등록자 정의**

	class UserEventHandler {

		/**
		 * Handle user login events.
		 */
		public function onUserLogin($event)
		{
			//
		}

		/**
		 * Handle user logout events.
		 */
		public function onUserLogout($event)
		{
			//
		}

		/**
		 * Register the listeners for the subscriber.
		 *
		 * @param  Illuminate\Events\Dispatcher  $events
		 * @return array
		 */
		public static function subscribe($events)
		{
			$events->listen('user.login', 'UserEventHandler@onUserLogin');

			$events->listen('user.logout', 'UserEventHandler@onUserLogout');
		}

	}

이벤트 등록자가 정의되면 `Event` 클래스에 등록될 수 있습니다.

**이벤트 등록자 등록**

	$subscriber = new UserEventHandler;

	Event::subscribe($subscriber);