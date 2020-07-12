<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use KejawenLab\ApiSkeleton\Repository\CronReportRepository;
use KejawenLab\ApiSkeleton\Security\Model\PasswordHistoryInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=CronReportRepository::class)
 * @ORM\Table(name="core_user_password_history")
 */
class PasswordHistory implements PasswordHistoryInterface
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $source;

    /**
     * @ORM\Column(type="string", length=49)
     */
    private ?string $identifier;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password;

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
