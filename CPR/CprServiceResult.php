<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

interface CprServiceResult
{
    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getStreetName(): ?string;

    public function getHouseNumber(): ?string;

    public function getFloor(): ?string;

    public function getSide(): ?string;

    public function getPostalCode(): ?string;

    public function getCity(): ?string;
}
