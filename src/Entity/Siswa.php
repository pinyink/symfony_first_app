<?php

namespace App\Entity;

use App\Repository\SiswaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiswaRepository::class)]
class Siswa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nis = null;

    #[ORM\Column(length: 64)]
    private ?string $nama = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $alamat = null;

    #[ORM\Column(nullable: true)]
    private ?int $umur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNis(): ?int
    {
        return $this->nis;
    }

    public function setNis(int $nis): static
    {
        $this->nis = $nis;

        return $this;
    }

    public function getNama(): ?string
    {
        return $this->nama;
    }

    public function setNama(string $nama): static
    {
        $this->nama = $nama;

        return $this;
    }

    public function getAlamat(): ?string
    {
        return $this->alamat;
    }

    public function setAlamat(?string $alamat): static
    {
        $this->alamat = $alamat;

        return $this;
    }

    public function getUmur(): ?int
    {
        return $this->umur;
    }

    public function setUmur(?int $umur): static
    {
        $this->umur = $umur;

        return $this;
    }
}
