<?php declare(strict_types = 1);

namespace Trejjam\Sentry\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\Hub;

final class SentryExtension extends CompilerExtension
{

	// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	/** @var SentryExtensionConfiguration */
	protected $config;
	// phpcs:enable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint

	public function __construct()
	{
		$this->config = new SentryExtensionConfiguration();
	}

	public function getConfigSchema(): Schema
	{
		return Expect::from($this->config);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('clientBuilder'))
			->setFactory(
				ClientBuilder::class . '::create',
				[
						[
							'dsn' => $this->config->dsn,
						],
					]
			);

		$builder->addDefinition($this->prefix('client'))
			->setType(ClientInterface::class)
			->setFactory($this->prefix('@clientBuilder') . '::getClient');

		$builder->addDefinition($this->prefix('sentryHub'))
			->setFactory(Hub::class);

		$builder->addDefinition($this->prefix('sentrySdk'))
			->setFactory(SentrySdk::class . '::setCurrentHub')
			->setAutowired(false);
	}

	public function afterCompile(ClassType $class): void
	{
		$initialize = $class->getMethod('initialize');

		$initialize->addBody('$this->getService(?);', [$this->prefix('sentrySdk')]);
	}

}
