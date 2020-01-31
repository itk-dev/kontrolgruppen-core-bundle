<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

/**
 * Interface CprServiceResult.
 */
interface CprServiceResultInterface
{
    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @return string
     */
    public function getStreetName(): string;

    /**
     * @return string
     */
    public function getHouseNumber(): string;

    /**
     * @return string|null
     */
    public function getFloor(): ?string;

    /**
     * @return string|null
     */
    public function getSide(): ?string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @return string
     */
    public function getCity(): string;
}
