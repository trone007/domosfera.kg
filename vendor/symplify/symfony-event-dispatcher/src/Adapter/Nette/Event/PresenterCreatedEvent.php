<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Nette\Application\IPresenter;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event occurs when a presenter is created.
 *
 * @see \Nette\Application\Application::$onPresenter
 */
final class PresenterCreatedEvent extends Event
{
    /**
     * @var string
     */
    public const NAME = Application::class . '::$onPresenter';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var IPresenter|callable $presenter
     */
    private $presenter;

    /**
     * @param Application $application
     * @param IPresenter|callable $presenter
     */
    public function __construct(Application $application, $presenter)
    {
        $this->application = $application;
        $this->presenter = $presenter;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return callable|$presenter
     */
    public function getPresenter()
    {
        return $this->presenter;
    }
}
