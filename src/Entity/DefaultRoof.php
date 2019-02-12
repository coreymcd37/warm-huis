<?php

namespace One\CheckJeHuis\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class DefaultRoof
{
    protected $id;

    protected $type;

    protected $size;

    protected $inclined;

    /**
     * @Assert\Type(type="numeric", message = "dit is geen geldige waarde")
     */
    protected $surface;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getInclined()
    {
        return $this->inclined;
    }

    /**
     * @param string $inclined
     * @return $this
     */
    public function setInclined($inclined)
    {
        $this->inclined = $inclined;
        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return float
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * @param float $surface
     * @return $this
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getInclinationString()
    {
        $types = House::getRoofTypes();

        if (array_key_exists($this->inclined, $types)) {
            return $types[$this->inclined];
        }

        return '';
    }
}
