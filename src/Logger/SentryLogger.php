<?php declare(strict_types = 1);

namespace Trejjam\Sentry\Logger;

use Contributte\Logging\ILogger;
use Sentry\Event;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Throwable;

final class SentryLogger implements ILogger
{

	private HubInterface $hub;

	public function __construct(HubInterface $hub)
	{
		$this->hub = $hub;
	}

	public function log(mixed $message, string $priority = ILogger::INFO): void
	{
		$severity = $this->getSeverityFromPriority($priority);

		if ($severity === null) {
			return;
		}

		$event = Event::createEvent();

		$event->setLevel($severity);

		if ($message instanceof Throwable) {
			$this->hub->captureException($message);

			return;
		}

		$event->setMessage((string) $message);

		$this->hub->captureEvent($event);
	}

	private function getSeverityFromPriority(string $priority): ?Severity
	{
		switch ($priority) {
			case ILogger::WARNING:
				return Severity::warning();
			case ILogger::ERROR:
				return Severity::error();
			case ILogger::EXCEPTION:
			case ILogger::CRITICAL:
				return Severity::fatal();
			default:
				return null;
		}
	}

}
