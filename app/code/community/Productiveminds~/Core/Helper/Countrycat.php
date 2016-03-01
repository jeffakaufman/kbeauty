<?php
/**
 *  A Magento module by ProductiveMinds
 *
 * NOTICE OF LICENSE
 *
 * This code is the work and copyright of Productive Minds Ltd, A UK registered company.
 * The copyright owner prohibit any fom of distribution of this code
 *
 * DISCLAIMER
 *
 * You are strongly advised to backup ALL your server files and database before installing and/or configuring
 * this Magento module. ProductiveMinds will not take any form of responsibility for any adverse effects that
 * may be cause directly or indirectly by using this software. As a usual practice with Software deployment,
 * the copyright owner recommended that you first install this software on a test server verify its appropriateness
 * before finally deploying it to a live server.
 *
 * @category   	Productiveminds
 * @package    	Productiveminds_Sitesecurity
 * @copyright   Copyright (c) 2010 - 2015 Productive Minds Ltd (http://www.productiveminds.com)
 * @license    	http://www.productiveminds.com/license/license.txt
 * @author     	ProductiveMinds <info@productiveminds.com>
 */

class Productiveminds_Core_Helper_Countrycat extends Mage_Core_Helper_Abstract
{
	
	const CONTINENT_CODE_EUROPE 	= 'europe';
	const CONTINENT_CODE_N_AMERICA 	= 'nAmerica';
	const CONTINENT_CODE_AFRICA 	= 'africa';
	const CONTINENT_CODE_S_AMERICA 	= 'sAmerica';
	const CONTINENT_CODE_ANTARCTICA	= 'antarctica';
	const CONTINENT_CODE_ASIA 		= 'asia';
	const CONTINENT_CODE_OCEANIA 	= 'oceania';
	const CONTINENT_CODE_OTHER 		= 'other';
	
	
    /**
     * Renders user country
     *
     * @param string $coreRoute
     */
    public function getCountryContinent($countryCode = 1, $model)
    {
    	$europe 		= self::isEurope();
    	$northAmerica 	= self::isNorthAmerica();
    	$africa 		= self::isAfrica();
    	$southAmerica 	= self::isSouthAmerica();
    	$asia 			= self::isAsia();
    	$antarctica 	= self::isAntarctica();
    	$oceania 		= self::isOceania();
    	
    	if(in_array($countryCode, $europe)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_EUROPE, 'code');
    		return $catObj->getCatId();
    	} else if(in_array($countryCode, $africa)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_AFRICA, 'code');
    		return $catObj->getCatId();
    	} else if(in_array($countryCode, $northAmerica)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_N_AMERICA, 'code');
    		return $catObj->getCatId();
    	} else if(in_array($countryCode, $southAmerica)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_S_AMERICA, 'code');
    		return $catObj->getCatId();
    	} else if(in_array($countryCode, $asia)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_ASIA, 'code');
    		return $catObj->getCatId();
    	} else if(in_array($countryCode, $antarctica)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_ANTARCTICA, 'code');
    		return $catObj->getCatId();
    	} else if(in_array($countryCode, $oceania)) {
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_OCEANIA, 'code');
    		return $catObj->getCatId();
    	} else {
    		// Assign country to Other, if not found
    		$catObj = $model->load(Productiveminds_Core_Helper_Countrycat::CONTINENT_CODE_OTHER, 'code');
    		return $catObj->getCatId();
    	}
    }
    
    public static function isEurope() {
    	return
    	array(
    			'AL',// Albania
    			'AD',// Andorra
    			'AT',// Austria
    			'BE',// Belgium
    			'BA',// Bosnia and Herzegovina
    			'BG',// Bulgaria
    			'BY',// Belarus
    			'HR',// Croatia
    			'CY',// Republic of Cyprus
    			'CZ',// Czech Republic
    			'DK',// Denmark
    			'EE',// Estonia
    			'FO',// Faroe Islands
    			'FI',// Finland
    			'FR',// France
    			'DE',// Germany
    			'GI',// Gibraltar
    			'GR',// Greece
    			'GG',// Guernsey
    			'HU',// Hungary
    			'IS',// Iceland
    			'IE',// Ireland
    			'IM',// Isle of Man
    			'IT',// Italy
    			'JE',// Jersey
    			'LV',// Latvia
    			'LI',// Liechtenstein
    			'LT',// Lithuania
    			'LU',// Luxembourg
    			'MK',// Macedonia
    			'MT',// Malta
    			'MD',// moldova
    			'MC',// Monaco
    			'ME',// Montenegro
    			'NO',// Norway
    			'NL',// Netherlands
    			'AN',// Netherlands Antilles
    			'PL',// Poland
    			'PT',// Portugal
    			'RO',// Romania
    			'RU',//	Russia
    			'SM',// San Marino
    			'RS',// Serbia
    			'SK',// Slovak Republic
    			'SI',// Slovenia
    			'ES',// Spain
    			'SJ',// Svalbard and Jan Mayen
    			'SE',// Sweden
    			'CH',// Switzerland
    			'UA',// Ukraine
    			'GB',// Include UK
    			'VA',// Vatican City
    			'AX',// land Islands
    	);
    }
    
    public static function isNorthAmerica() {
    	return
    	array(
    			'AI',// Anguilla
    			'AG',// Antigua and Barbuda
    			'AW',// Aruba
    			'BS',// Bahamas
    			'BB',// Barbados
    			'BZ',// Belize
    			'BM',// Bermuda
    			'VG',// British Virgin Islands
    			'CA',// Canada
    			'KY',// Cayman Islands
    			'CR',// Costa Rica
    			'CU',// Cuba
    			'DM',// Dominica
    			'DO',// Dominican Republic
    			'EH',// Western Sahara
    			'SV',// El Salvador
    			'GL',// Greenland
    			'GD',// Grenada
    			'GP',// Guadeloupe
    			'GT',// Guatemala
    			'HT',// Haiti
    			'HN',// Honduras
    			'JM',// Jamaica
    			'MQ',// Martinique
    			'MX',// Mexico
    			'MS',// Montserrat
    			'NI',// Nicaragua
    			'PA',// Panama
    			'PR',// Puerto Rico
    			'BL',// Saint BarthŽlemy
    			'KN',// Saint Kitts and Nevis
    			'LC',// Saint Lucia
    			'MF',// Saint Martin
    			'PM',// Saint Pierre and Miquelon
    			'VC',// Saint Vincent and the Grenadines
    			'TT',// Trinidad and Tobago
    			'TC',// Turks and Caicos Islands
    			'UM',// U.S. Minor Outlying Islands
    			'VI',// U.S. Virgin Islands
    			'US',// United States
    	);
    }
    
    public static function isAfrica() {
    	return
    	array(
    			'DZ',// Algeria
    			'AO',// Angola
    			'BJ',// Benin
    			'BW',// Botswana
    			'BF',// Burkina Faso
    			'BI',// Burundi
    			'CM',// Cameroon
    			'CV',// Cape Verde
    			'CF',// Central African Republic
    			'TD',// Chad
    			'KM',// Comoros
    			'CG',// Congo - Brazzaville
    			'CD',// Congo - Kinshasa
    			'CI',// C™te dÕIvoire
    			'DJ',// Djibouti
    			'EG',// Egypt
    			'GQ',// Equatorial Guinea
    			'ER',// Eritrea
    			'ET',// Ethiopia
    			'GA',// Gabon
    			'GM',// Gambia
    			'GH',// Ghana
    			'GN',// Guinea
    			'GW',// Guinea-Bissau
    			'KE',// Kenya
    			'LS',// Lesotho
    			'LR',// Liberia
    			'LY',// Libya
    			'MG',// Madagascar
    			'MW',// Malawi
    			'ML',// Mali
    			'MR',// Mauritania
    			'MU',// Mauritius
    			'YT',// Mayotte
    			'MA',// Morocco
    			'MZ',// Mozambique
    			'NA',// Namibia
    			'NE',// Niger
    			'NG',// Nigeria
    			'RW',// Rwanda
    			'RE',// RŽunion
    			'SH',// Saint Helena
    			'ST',// S‹o TomŽ and Pr’ncipe
    			'SN',// Senegal
    			'SC',// Seychelles
    			'SL',// Sierra Leone
    			'SO',// Somalia
    			'ZA',// South Africa
    			'SD',// Sudan
    			'SZ',// Swaziland
    			'TZ',// Tanzania
    			'TG',// Togo
    			'TN',// Tunisia
    			'UG',// Uganda
    			'ZM',// Zambia
    			'ZW',// Zimbabwe
    	);
    }
    
    public static function isSouthAmerica() {
    	return
    	array(
    			'AR',// Argentina
    			'BO',// Bolivia
    			'BR',// Brazil
    			'CL',// Chile
    			'CO',// Colombia
    			'EC',// Ecuador
    			'FK',// Falkland Islands
    			'GF',// French Guiana
    			'GY',// Guyana
    			'PY',// Paraguay
    			'PE',// Peru
    			'GS',// South Georgia and the South Sandwich Islands
    			'SR',// Suriname
    			'UY',// Uruguay
    			'VE',// Venezuela
    	);
    }
    
    public static function isAsia() {
    	return
    	array(
    			'AF',// Afghanistan
    			'AM',// Armenia
    			'AZ',// Azerbaijan
    			'BH',// Bahrain
    			'BD',// Bangladesh
    			'BT',// Bhutan
    			'IO',// British Indian Ocean Territory
    			'BN',// Brunei
    			'KH',// Cambodia
    			'CN',// China
    			'CX',// Christmas Island
    			'CC',// Cocos [Keeling] Islands
    			'CY',// Cyprus
    			'TL',// Timor-Leste
    			'GE',// Georgia
    			'HK',// Hong Kong SAR China
    			'IN',// India
    			'ID',// Indonesia
    			'IR',// Iran
    			'IQ',// Iraq
    			'IL',// Israel
    			'JP',// Japan
    			'JO',// Jordan
    			'KZ',// Kazakhstan
    			'KW',// Kuwait
    			'KG',// Kyrgyzstan
    			'LA',// Laos
    			'LB',// Lebanon
    			'MO',// Macau SAR China
    			'MY',// Malaysia
    			'MV',// Maldives
    			'MN',// Mongolia
    			'MM',// Myanmar [Burma]
    			'NP',// Nepal
    			'KP',// North Korea
    			'OM',// Oman
    			'PK',// Pakistan
    			'PS',// Palestinian Territories
    			'PH',// Philippines
    			'QA',// Qatar
    			'SA',// Saudi Arabia
    			'SG',// Singapore
    			'KR',// South Korea
    			'LK',// Sri Lanka
    			'SY',// Syria
    			'TW',// Taiwan
    			'TJ',// Tajikistan
    			'TH',// Thailand
    			'TR',// Turkey
    			'TM',// Turkmenistan
    			'AE',// United Arab Emirates
    			'UZ',// Uzbekistan
    			'VN',// Vietnam
    			'YE',// Yemen
    	);
    }
    
    public static function isAntarctica() {
    	return
    	array(
    			'AQ',// Antarctica
    			'BV',// Bouvet Island
    			'HM',// Heard Island and McDonald Islands
    			'TF',// French Southern Territories
    	);
    }
    
    public static function isOceania() {
    	return
    	array(
    			'AS',// American Samoa
    			'AU',// Australia
    			'CK',// Cook Islands
    			'FJ',// Fiji
    			'PF',// French Polynesia
    			'GU',// Guam
    			'KI',// Kiribati
    			'MH',// Marshall Islands
    			'FM',// Micronesia
    			'NR',// Nauru
    			'NC',// New Caledonia
    			'NZ',// New Zealand
    			'NU',// Niue
    			'NF',// Norfolk Island
    			'MP',// Northern Mariana Islands
    			'PW',// Palau
    			'PG',// Papua New Guinea
    			'PN',// Pitcairn Islands
    			'WS',// Samoa
    			'SB',// Solomon Islands
    			'TK',// Tokelau
    			'TO',// Tonga
    			'TV',// Tuvalu
    			'VU',// Vanuatu
    			'WF',// Wallis and Futuna
    	);
    }

}
?>