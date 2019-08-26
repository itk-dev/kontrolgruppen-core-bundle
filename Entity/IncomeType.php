<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\IncomeTypeRepository")
 * @Gedmo\Loggable()
 */
class IncomeType extends AbstractTaxonomy
{

}
