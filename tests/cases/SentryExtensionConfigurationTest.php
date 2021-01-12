<?php declare(strict_types = 1);

namespace Tests\Cases;

use Trejjam\Sentry\DI\SentryExtensionConfiguration;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class SentryExtensionConfigurationTest extends TestCase
{

	public function testFoo(): void
	{
		$sentryExtensionConfiguration = new SentryExtensionConfiguration();
		$sentryExtensionConfiguration->dsn = "dsn";
		Assert::same('dsn', $sentryExtensionConfiguration->dsn);
	}

}

(new SentryExtensionConfigurationTest())->run();
