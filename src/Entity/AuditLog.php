<?php

namespace One\CheckJeHuis\Entity;

class AuditLog
{
    protected $id;

    protected $username;

    protected $objectName;

    protected $changes;

    protected $identifier;

    protected $createdAt;

    private function __construct($username, $objectName, $identifier, $changes)
    {
        $this->username = $username;
        $this->objectName = $objectName;
        $this->changes = $changes;
        $this->identifier = $identifier;
        $this->createdAt = new \DateTime();
    }

    public static function createEntry($username, $objectName, $identifier, $changes)
    {
        return new self(
            $username, $objectName, $identifier, $changes
        );
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @return mixed
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getChangesJson()
    {
        return json_encode($this->changes);
    }
}
