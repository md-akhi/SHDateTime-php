<?php
    /**
	* In the name of Allah, the Beneficent, the Merciful.
    * @package		Date and Time Related Extensions - SH(Solar Hijri, Shamsi Hijri, Iranian Hijri)
    * @author		Mohammad Amanalikhani <md.akhi.ir@gmail.com>
    * @link			http://git.akhi.ir/php/SHDateTime/		(Git)
    * @link			http://git.akhi.ir/php/SHDateTime/docs/	(wiki)
    * @license		https://www.gnu.org/licenses/agpl-3.0.en.html AGPL-3.0 License
    * @version		Release: 1.0.0
    */
	
	interface SHDATELang{
		// Languages
		const LANG = '';
        const DIGIT = array();
		const SUFFIX = array() || "";
		const MERIDIEN_FULL_NAMES = array();
		const MERIDIEN_SHORT_NAMES = array();
		const MONTH_FULL_NAMES = array();
		const MONTH_SHORT_NAMES = array();
		const DAY_FULL_NAMES = array();
		const DAY_SHORT_NAMES = array();
		const CONSTELLATIONS_FULL_NAMES = array();
		const ANIMALS_FULL_NAMES = array();
		const SEASON_FULL_NAMES = array();
		const LEAP_FULL_NAMES = array();
		const SOLSTICE_FULL_NAMES = array();
	}