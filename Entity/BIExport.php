<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\BIExportRepository")
 * @ORM\Table(name="bi_export")
 */
class BIExport extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="json")
     */
    private $report = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getReport(): ?array
    {
        return $this->report;
    }

    public function setReport($report): self
    {
        if ($report instanceof AbstractExport) {
            $report = json_decode(json_encode($report), true);
        }

        $this->report = $report;

        return $this;
    }
}
