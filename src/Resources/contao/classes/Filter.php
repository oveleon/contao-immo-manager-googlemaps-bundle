<?php
/**
 * This file is part of Contao EstateManager.
 *
 * @link      https://www.contao-estatemanager.com/
 * @source    https://github.com/contao-estatemanager/reference
 * @copyright Copyright (c) 2019  Oveleon GbR (https://www.oveleon.de)
 * @license   https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

namespace ContaoEstateManager\GoogleMaps;

use ContaoEstateManager\Translator;

class Filter extends \Controller
{

    /**
     * Table
     * @var string
     */
    protected $strTable = 'tl_real_estate';

    /**
     * Set google location filter parameters
     *
     * @param $arrColumns
     * @param $arrValues
     * @param $arrOptions
     * @param $mode
     * @param $context
     */
    public function setLocationParameter(&$arrColumns, &$arrValues, &$arrOptions, $mode, $addFragments, $objModule, $context)
    {
        $t = $this->strTable;

        if ($_SESSION['FILTER_DATA']['radius-google'] && $_SESSION['FILTER_DATA']['latitude'] && $_SESSION['FILTER_DATA']['longitude'])
        {
            $arrColumns[] = "(6371*acos(cos(radians(?))*cos(radians($t.breitengrad))*cos(radians($t.laengengrad)-radians(?))+sin(radians(?))*sin(radians($t.breitengrad)))) <= ?";
            $arrValues[] = $_SESSION['FILTER_DATA']['latitude'];
            $arrValues[] = $_SESSION['FILTER_DATA']['longitude'];
            $arrValues[] = $_SESSION['FILTER_DATA']['latitude'];
            $arrValues[] = $_SESSION['FILTER_DATA']['radius-google'];
        }
        else
        {
            if ($_SESSION['FILTER_DATA']['city'])
            {
                $arrColumns[] = "$t.ort=?";
                $arrValues[] = $_SESSION['FILTER_DATA']['city'];
            }

            if ($_SESSION['FILTER_DATA']['postal'])
            {
                $arrColumns[] = "$t.plz=?";
                $arrValues[] = $_SESSION['FILTER_DATA']['postal'];
            }

            if ($_SESSION['FILTER_DATA']['district'])
            {
                $arrColumns[] = "$t.regionalerZusatz=?";
                $arrValues[] = $_SESSION['FILTER_DATA']['district'];
            }
        }

        if ($objModule->type === 'realEstateResultList' && $objModule->googleFilterAddSorting && $_SESSION['SORTING'] === 'location')
        {
            if ($objModule->googleFilterLat && $objModule->googleFilterLng)
            {
                $arrOptions['order'] = "(6371*acos(cos(radians($objModule->googleFilterLat))*cos(radians($t.breitengrad))*cos(radians($t.laengengrad)-radians($objModule->googleFilterLng))+sin(radians($objModule->googleFilterLat))*sin(radians($t.breitengrad)))) ASC";
            }
        }
    }

    /**
     * Add real estate location sorting option.
     *
     * @param $arrOptions
     * @param $context
     */
    public function addRealEstateSorting(&$arrOptions, $context)
    {
        $arrOptions['location'] = Translator::translateFilter('location');
    }
}
