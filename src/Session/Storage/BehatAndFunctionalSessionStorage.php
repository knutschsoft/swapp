<?php
declare(strict_types=1);

namespace App\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/** @phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint */
class BehatAndFunctionalSessionStorage implements SessionStorageInterface
{
    private SessionStorageInterface $sessionStorage;

    public function __construct(
        NativeSessionStorage $behatSessionStorage,
        MockFileSessionStorage $functionalSessionStorage,
        string $sessionStorageForTestEnv
    ) {
        if ('mock_file' === $sessionStorageForTestEnv) {
            $this->sessionStorage = $functionalSessionStorage;
        } elseif ('native' === $sessionStorageForTestEnv) {
            $this->sessionStorage = $behatSessionStorage;
        } else {
            throw new \LogicException('Invalid value for sessionStorageForTestEnv given.');
        }
    }

    /**
     * Starts the session.
     *
     * @return bool True if started
     *
     * @throws \RuntimeException if something goes wrong starting the session
     */
    public function start(): bool
    {
        return $this->sessionStorage->start();
    }

    /**
     * Checks if the session is started.
     *
     * @return bool True if started, false otherwise
     */
    public function isStarted(): bool
    {
        return $this->sessionStorage->isStarted();
    }

    /**
     * Returns the session ID.
     *
     * @return string The session ID or empty
     */
    public function getId(): string
    {
        return $this->sessionStorage->getId();
    }

    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId($id): void
    {
        $this->sessionStorage->setId($id);
    }

    /**
     * Returns the session name.
     *
     * @return mixed The session name
     */
    public function getName()
    {
        return $this->sessionStorage->getName();
    }

    /**
     * Sets the session name.
     *
     * @param string $name
     */
    public function setName($name): void
    {
        $this->sessionStorage->setName($name);
    }

    /**
     * Regenerates id that represents this storage.
     *
     * This method must invoke session_regenerate_id($destroy) unless
     * this interface is used for a storage object designed for unit
     * or functional testing where a real PHP session would interfere
     * with testing.
     *
     * Note regenerate+destroy should not clear the session data in memory
     * only delete the session data from persistent storage.
     *
     * Care: When regenerating the session ID no locking is involved in PHP's
     * session design. See https://bugs.php.net/bug.php?id=61470 for a discussion.
     * So you must make sure the regenerated session is saved BEFORE sending the
     * headers with the new ID. Symfony's HttpKernel offers a listener for this.
     * See Symfony\Component\HttpKernel\EventListener\SaveSessionListener.
     * Otherwise session data could get lost again for concurrent requests with the
     * new ID. One result could be that you get logged out after just logging in.
     *
     * @param bool $destroy  Destroy session when regenerating?
     * @param int  $lifetime Sets the cookie lifetime for the session cookie. A null value
     *                       will leave the system settings unchanged, 0 sets the cookie
     *                       to expire with browser session. Time is in seconds, and is
     *                       not a Unix timestamp.
     *
     * @return bool True if session regenerated, false if error
     *
     * @throws \RuntimeException If an error occurs while regenerating this storage
     */
    public function regenerate($destroy = false, $lifetime = null): bool
    {
        return $this->sessionStorage->regenerate($destroy, $lifetime);
    }

    /**
     * Force the session to be saved and closed.
     *
     * This method must invoke session_write_close() unless this interface is
     * used for a storage object design for unit or functional testing where
     * a real PHP session would interfere with testing, in which case
     * it should actually persist the session data if required.
     *
     * @throws \RuntimeException if the session is saved without being started, or if the session
     *                           is already closed
     */
    public function save(): void
    {
        $this->sessionStorage->save();
    }

    /**
     * Clear all session data in memory.
     */
    public function clear(): void
    {
        $this->sessionStorage->clear();
    }

    /**
     * Gets a SessionBagInterface by name.
     *
     * @param string $name
     *
     * @return SessionBagInterface
     *
     * @throws \InvalidArgumentException If the bag does not exist
     */
    public function getBag($name): SessionBagInterface
    {
        return $this->sessionStorage->getBag($name);
    }

    public function registerBag(SessionBagInterface $bag): void
    {
        $this->sessionStorage->registerBag($bag);
    }

    public function getMetadataBag(): MetadataBag
    {
        return $this->sessionStorage->getMetadataBag();
    }
}
