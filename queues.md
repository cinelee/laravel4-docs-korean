# 큐

- [설정](#configuration)
- [기본적인 사용법](#basic-usage)
- [큐 리스너 실행](#running-the-queue-listener)

<a name="configuration"></a>
## 설정

Laravel 큐 컴포넌트는 서로 다른 큐 서비스의 다양성을 하나의 통합된 API로 제공합니다. 큐는 이메일 보내는 일과 같이 시간이 많이 걸리는 작업의 처리를 연기하여 어플리케이션의 요청 속도를 크게 올릴 수 있습니다.

큐 설정 파일은 `app/config/queue.php`에 있습니다. 이 파일에서는 프레임워크에 기본적으로 포함되어있는 [Beanstalkd](http://kr.github.com/beanstalkd)와 synchronous (로컬사용을 위한) 드라이버들의 연결 설정을 찾을 수 있습니다.

<a name="basic-usage"></a>
## 기본적인 사용법
`Queue::push` 메소드를 사용하여 큐에 새로운 작업을 추가합니다.:

**큐에 작업 추가**

    Queue::push('SendEmail', array('message' => $message));

`push` 메소드에 전달되는 첫번째 인수는 작업을 실행하는 클래스의 이름입니다. 두번째 인수는 핸들러에 전달될 데이터 배열입니다. 작업 핸들러는 아래와 같이 정의 되어야 합니다.:

**작업 핸들러 정의**

	class SendEmail {

		public function fire($job, $data)
		{
			//
		}

	}

꼭 필요한 메소드는 `Job` 인스턴스와 큐에 추가된 `data` 배열을 전달 받는 `fire` 메소드 입니다.

작업이 끝난 후, 그 작업은 반드시 큐에서 제거되어야 합니다. `Job` 인스턴스의 `delete` 메소드를 사용하여 작업을 제거할 수 있습니다.:

**일이 끝난 작업 제거**

	public function fire($job, $data)
	{
		// Process the job...

		$job->delete();
	}

만약 작업을 다시 큐에 넣어야 한다면, `release` 메소드를 통해 그렇게 할 수 있습니다.:

**큐에 작업을 다시 넣음**

	public function fire($job, $data)
	{
		// Process the job...

		$job->release();
	}

작업을 다시 넣기전에 대기시간(초)을 지정할 수 있습니다.

	$job->release(5);

작업을 진행하는 동안 예외가 발생하면, 그 작업은 자동으로 다시 큐에 넣어집니다. `attempts` 메소드를 사용하여 그 작업이 몇번째 실행을 시도하고 있는지 확인할 수 있습니다.

**몇번째 시도인지 확인**

	if ($job->attempts() > 3)
	{
		//
	}

<a name="running-the-queue-listener"></a>
## 큐 리스너 실행

Laravel은 새로운 작업이 큐에 추가되는 순간 작동하게 하는 Artisan 태스크를 포함하고 있습니다. `queue:listen` 커맨드를 사용하여 이 태스크를 실행할 수 있습니다.:

**큐 리스너 시작**

	php artisan queue:listen

리스너가 어떤 큐 커넥션을 사용할지 지정할 수 있습니다.:

	php artisan queue:listen connection

한번 작업이 시작되고 나면, 수동으로 정지시킬 때까지 리스너는 계속 실행 됩니다. 반드시 [Supervisor](http://supervisord.org/)같은 프로세스 모니터를 사용하여 큐 리스너가 실행을 멈추지 않도록 합니다.

`queue:work` 메소드를 사용하여 큐의 가장 첫번째 작업을 실행할 수도 있습니다:

**큐의 첫번째 작업 실행**

	php artisan queue:work
