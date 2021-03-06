<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Waste
 *
 * @ORM\Table(name="waste")
 * @ORM\Entity
 */
class Waste
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="degradation_time", type="string", length=20, nullable=false)
     */
    private $degradationTime;

    /**
     * @var string
     *
     * @ORM\Column(name="trash_color", type="string", length=20, nullable=false)
     */
    private $trashColor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="img_url", type="string", length=50, nullable=true)
     */
    private $imgUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDegradationTime(): ?string
    {
        return $this->degradationTime;
    }

    public function setDegradationTime(string $degradationTime): self
    {
        $this->degradationTime = $degradationTime;

        return $this;
    }

    public function getTrashColor(): ?string
    {
        return $this->trashColor;
    }

    public function setTrashColor(string $trashColor): self
    {
        $this->trashColor = $trashColor;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(?string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }


}
