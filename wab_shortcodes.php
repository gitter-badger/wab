<?php
if (!defined('e107_INIT')) {
    exit;
}

/**
*
* @package wab
* @subpackage wab
* @version 1.0.1
* @author baz
*
* 	wab shortcodes
*/

/**
* wabShortcodes
*
* @package
* @author Barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class wab_shortcodes extends e_shortcode {
    public $dataRow;
    public $perPage;
    /**
    * wabShortcodes::__construct()
    */
    function __construct() {
        parent::__construct();
    }
    /**
    * wab_shortcodes::makeToolTip()
    *
    * @param string $placement
    * @param string $toolTip
    * @return
    */
    protected function makeToolTip($placement = 'bottom', $toolTip = 'Default') {
        return " data-toggle='wabToolTip' data-placement='{$placement}' data-original-title='{$toolTip}'";
    }
    /**
    * wab_shortcodes::makeBlankURL()
    *
    * @param mixed $action
    * @param mixed $subAction
    * @return
    */
    protected function makeBlankURL($action, $subAction) {
        $retval = e_PLUGIN . "wab/index.php?wabAction={$action}&amp;wabSubAction={$subAction}&amp;wabFrom=0&amp;waID=0&amp;wabColumn=&amp;wab-search=&amp;wabSort=";
        return $retval;
    }
    /**
    * wab::iconFlag()
    *
    * @param string $countryCode
    * @param string $countryName
    * @return
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function iconFlag($countryCode = '', $countryName = '', $size = 32) {
        if (empty($countryCode)) {
            $retval = '&nbsp;';
        } else {
            $flagname = "images/flags/{$size}/{$countryCode}.png";
            // die($flagname);
            if (file_exists($flagname)) {
                $retval = "<img src='{$flagname}' title='{$countryName}' alt='{$countryName}' style='width:{$size}px;height:{$size}px;' />";
            } else {
                $retval = $countryName;
            }
        }
        return $retval;
    }
    protected function iconCountyFlag($countyCode = '', $countyName = '', $size = 32) {
        if (empty($countyCode)) {
            $retval = '&nbsp;';
        } else {
            $flagname = "images/counties/{$size}/{$countyCode}.png";
            // die($flagname);
            if (file_exists($flagname)) {
                $retval = "<img src='{$flagname}' title='{$countyName}' alt='{$countyName}' style='width:{$size}px;height:{$size}px;' />";
            } else {
                $retval = ' ';
            }
        }
        return $retval;
    }
    protected function tickShow($value = false, $showNot = false, $title = '') {
        if (vartrue($value)) {
            $retval = "<img src='images/true16.png' alt='true'/>";
        } elseif (vartrue($showNot)) {
            $retval = "<img src='images/false16.png' alt='false' />";
        } else {
            $retval = "&nbsp;";
        }
        return $retval;
    }
    function sc_wab_perpage() {
        return $this->dataRow['perpagefilter'];
    }
    public function showFreq($freq = 7.16, $dec = 3) {
        return sprintf("%8.3f", $freq);
    }
    /**
    * wabShortcodes::sc_wab_myLogbooksIcon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_mylogbooksicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('mylogs', 'list') . "' " . $this->makeToolTip('right' , 'View modify and delete my logbooks and their entries') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/logbook24.png' alt='My Logs' /><br />My Logs</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_userlogicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_userlogicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('userlogs', 'list') . "' " . $this->makeToolTip('right' , 'View the log books from other registered users.') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/logbookusers24.png'  alt='View Logs' /><br />All Logs</a>";
        return $retval;
    }

    /**
    * wab_shortcodes::sc_wab_usericon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_usericon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('users', 'list') . "' " . $this->makeToolTip('right' , 'View the public details about the other registered users.') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/users24.png' alt='Users' /><br />Users</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_chartsicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_chartsicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('chart', 'list') . "' " . $this->makeToolTip('right' , 'View graphs, charts and league tables') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/chart24.png' alt='Charts'  /><br />Charts</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_countyicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_countyicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('counties', 'list') . "' " . $this->makeToolTip('left' , 'View the public details about the others.') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/uk24.png' alt='Counties' /><br />Counties</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_booksicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_booksicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('books', 'list') . "' " . $this->makeToolTip('left' , 'View and export book lists.') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/book24.png' alt='Books' /><br />Books</a>	";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_islandsicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_islandsicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('islands', 'list') . "' " . $this->makeToolTip('left' , 'View the list of islands.') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/island24.png' alt='Islands' /><br />Islands</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_trigicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_trigicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('trig', 'list') . "' " . $this->makeToolTip('left' , 'View details about trig points') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/trig24.png' alt='Trig Points' /><br />Trig</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_countryicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_countryicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('countries', 'list') . "' " . $this->makeToolTip('right' , 'View details about countries.') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/globe24.png' alt='Countries' /><br />Countries</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_coastalicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_coastalicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('coastal', 'list') . "' " . $this->makeToolTip('right' , 'View and export details about coastal areas') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/coastal24.png' alt='Coastal' /><br />Coastal</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_importicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_importicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('import', 'get') . "' " . $this->makeToolTip('right' , 'Import ADIF files from other logging programs') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/import24.png' alt='Import' /><br />Import</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_exporticon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_exporticon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('export', 'list') . "' " . $this->makeToolTip('right' , 'Export ADIF file to other logging programs') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/export24.png' alt='Export' /><br />Export</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_syncicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_syncicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('sync', 'list') . "' " . $this->makeToolTip('left' , 'Syncronise with mobile app') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/sync24.png' alt='Sync' /><br />Sync</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_settingsicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_settingsicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('settings', 'edit') . "' " . $this->makeToolTip('left' , 'Modify my settings') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/mysettings24.png' alt='Settings' /><br />Settings</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_newsicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_newsicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . e_BASE . "news.php' " . $this->makeToolTip('left' , 'View announcements') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/announce24.png' alt='News' /><br />News</a>";
        return $retval;
    }
    /**
    * wab_shortcodes::sc_wab_eventsicon()
    *
    * @param string $parm
    * @return
    */
    function sc_wab_eventsicon($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . e_PLUGIN . "calendar_menu/calendar.php' " . $this->makeToolTip('left' , 'View forthcoming events') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/menu/events24.png' alt='Events' /><br />Events</a>";
        return $retval;
    }

    /**
    * wab_shortcodes::sc_wab_userselect()
    *
    * @return
    */
    function sc_wab_userselect() {
        global $userSel;
        return $userSel;
    }
    function sc_wab_logselect() {
        global $selLog;
        return $selLog;
    }
    function sc_wab_logfilter() {
        global $selectOptions;
        return $selectOptions;
    }
    function sc_wab_logfiltergo() {
        $retval = "<input type='image' src='images/filter24.png' name='image' alt='Filter' />";
        return $retval;
    }
    function sc_wab_logsearch() {
        global $search;
        return $search;
    }

    function sc_wab_logid() {
        return $this->dataRow['wablogID'];
    }
    function sc_wab_logstartdate() {
        if ($this->dataRow['wabLogStartDate'] > 0) {
            return date('d M Y', $this->dataRow['wabLogStartDate']);
        } else {
            return '&nbsp;';
        }
        // return date('d-m-Y', $this->dataRow['wabLogStartDate']);
    }
    function sc_wab_logtimeon() {
        if ($this->dataRow['wabLogStartDate'] > 0) {
            return date('H:i', $this->dataRow['wabLogStartDate']);
        } else {
            return '&nbsp;';
        }
    }
    function sc_wab_logtimeoff() {
        if ($this->dataRow['wabLogEndDate'] > 0) {
            return date('H:i', $this->dataRow['wabLogEndDate']);
        } else {
            return '&nbsp;';
        }
    }
    function sc_wab_logcallsign() {
        return $this->dataRow['wabLogCallsign'];
    }
    function sc_wab_logarea() {
        return $this->dataRow['wabSquare'];
    }
    function sc_wab_logflag() {
        return $this->iconFlag($this->dataRow['wabCountryCode'], $this->dataRow['wabCountryName'], 16);
    }
    function sc_wab_logband() {
        return $this->dataRow['wabBandsName'];
    }
    function sc_wab_logfreq() {
        $this->showFreq($this->dataRow['wabLogFreq']);
    }
    public function sc_wab_logcoastal() {
        return $this->tickShow($this->dataRow['wabCoastal'] == 1, false);
    }
    function sc_wab_logmode() {
        return $this->dataRow['wabModesName'];
    }
    function sc_wab_logrstin() {
        return $this->dataRow['wabLogRSIn'];
    }
    function sc_wab_logrstout() {
        return $this->dataRow['wabLogRSOut'];
    }
    function sc_wab_logname() {
        return $this->dataRow['wabLogName'];
    }
    function sc_wab_logpower() {
        return $this->dataRow['wabLogPower'];
    }
    function sc_wab_logqslout() {
        return $this->tickShow($this->dataRow['wabLogQSLOut'] == 1, false);
    }
    function sc_wab_logqslin() {
        return $this->tickShow($this->dataRow['wabLogQSLIn'], false);
    }
    function sc_wab_logedit() {
    }
    // islands shortcodes
    function sc_wab_islandpdf($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('island', 'pdf') . "' " . $this->makeToolTip('right' , 'Export selection as PDF') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/pdf24.png' alt='PDF' /><br />PDF</a>";
        return $retval;
    }
    function sc_wab_islandxls($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('island', 'xls') . "' " . $this->makeToolTip('right' , 'Export selection as Excel file') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/excel24.png' alt='xls' /><br />Excel</a>";
        return $retval;
    }
    function sc_wab_islandcsv($parm = '') {
        $retval = "
	<a class='wabIconContainer' href='" . $this->makeBlankURL('island', 'csv') . "' " . $this->makeToolTip('right' , 'Export selection as CSV file') . ">
		<img class='wabMenuIcon' src='" . e_PLUGIN . "wab/images/csv24.png' alt='PDF' /><br />CSV</a>";
        return $retval;
    }
    function sc_wab_islandid() {
        return $this->dataRow['wabIslandID'];
    }
    function sc_wab_islandname() {
        return ucwords(strtolower($this->dataRow['wabIsland']));
    }
    function sc_wab_islandarea() {
        return $this->dataRow['wabSquare'];
    }
    function sc_wab_islandflag() {
        return $this->iconFlag($this->dataRow['wabCountryCode'], $this->dataRow['wabCountryName']);
    }
    function sc_wab_islandcount() {
        return $this->dataRow['wabIslandsCount'];
    }
    function sc_wab_islandcomment() {
        return $this->dataRow['wabIslandComment'];
    }
    // books shortcodes
    function sc_wab_booksnumber() {
        return $this->dataRow['wabBookSeries'];
    }
    function sc_wab_bookscall() {
        return $this->dataRow['wabBookCallsign'];
    }
    function sc_wab_booksissued() {
        $fred = strtotime($this->dataRow['wabBookDate']);
        if ($fred > 0) {
            $arr = convert::convert_date($fred, 'short');
            $out = explode(':', $arr, 1);
            return $out[0];
        } else {
            return '&nbsp;';
        }
    }
    function sc_wab_booksseries() {
        return $this->dataRow['wabBookSeries'];
    }
    function sc_wab_booksreissue() {
        return $this->tickShow($this->dataRow['wabBookReissue'], false);
    }
    function sc_wab_booksnlv() {
        return $this->tickShow($this->dataRow['wabBookNLV'], false);
    }
    /**
    * wab_shortcodes::sc_wab_countyid()
    *
    * @return
    */
    function sc_wab_countyid() {
        return $this->dataRow['wabCountyID'];
    }
    function sc_wab_countyname() {
        return $this->dataRow['wabCountyName'];
    }
    function sc_wab_countyflag() {
        return $this->iconCountyFlag($this->dataRow['wabCountyFlag'], $this->dataRow['wabCountyName'], 16);
    }
    function sc_wab_countynotes() {
        return $this->dataRow['wavCountyNotes'];
    }

    /**
    * wab_shortcodes::sc_wab_countyid()
    *
    * @return
    */
    function sc_wab_countryid() {
        return $this->dataRow['wabCountryCode'];
    }
    function sc_wab_countryname() {
        return $this->dataRow['wabCountryName'];
    }
    function sc_wab_countryflag() {
        return $this->iconFlag($this->dataRow['wabCountryCode'], $this->dataRow['wabCountryName'], 32);
    }
    function sc_wab_countrynotes() {
        return $this->dataRow['wabCountryNotes'];
    }
    /**
    * wab_shortcodes::sc_wab_countyid()
    *
    * @return
    */
    function sc_wab_coastalid() {
        return $this->dataRow['wabTidalID'];
    }
    function sc_wab_coastalarea() {
        return $this->dataRow['wabCountryName'];
    }
    function sc_wab_coastalflag() {
        return $this->iconFlag($this->dataRow['wabCountryCode'], $this->dataRow['wabCountryName'], 32);
    }
    function sc_wab_coastalnotes() {
        return $this->dataRow['wavCountryNotes'];
    }
    function sc_wab_userid() {
        return $this->dataRow['wabUserID'];
    }
    function sc_wab_usercall() {
        return $this->dataRow['wabUserCallsign'];
    }
    function sc_wab_username() {
        return $this->dataRow['user_name'];
    }
    function sc_wab_userwabname() {
        return $this->dataRow['wabUserName'];
    }
    function sc_wab_userlogs() {
        return $this->dataRow['numlogs'];
    }
    function sc_wab_userbooks() {
        return $this->dataRow['counted'];
    }
    function sc_wab_userarea() {
        return $this->dataRow['wabSquare'];
    }
    function sc_wab_userflag() {
        return $this->iconFlag($this->dataRow['wabCountryCode'], $this->dataRow['wabCountryName'], 32);
    }
    function sc_wab_importtype() {
        return $this->dataRow['select'];
    }
    function sc_wab_importfiles() {
        return $this->dataRow['file'];
    }
    function sc_wab_importselect() {
        return $this->dataRow['fileselect'];
    }
    function sc_wab_importdelete() {
        return $this->dataRow['filedelete'];
    }
    function sc_wab_importtoggle() {
        return $this->dataRow['filedelete'];
    }
    function sc_wab_importupload() {
        return $this->dataRow['fileup'];
    }
    function sc_wab_doimport() {
        return $this->dataRow['doimp'];
    }
    function sc_wab_doupload() {
        return $this->dataRow['doup'];
    }
	// export sc
	function sc_wab_exportlog() {
		return $this->dataRow['exportLogs'];
	}
	function sc_wab_exportwab() {
		return $this->dataRow['exportWab'];
	}
	function sc_wab_exportactive() {
		return $this->dataRow['exportActive'];
	}
	function sc_wab_exportprivate() {
		return $this->dataRow['exportPrivate'];
	}
	function sc_wab_exportbooks() {
		return $this->dataRow['exportBooks'];
	}
	function sc_wab_exportcountry() {
		return $this->dataRow['exportCountry'];
	}
	function sc_wab_exportcoastal() {
		return $this->dataRow['exportCoastal'];
	}
	function sc_wab_exportislands() {
		return $this->dataRow['exportIslands'];
	}
	function sc_wab_exportmode() {
		return $this->dataRow['exportModes'];
	}
		function sc_wab_exportband() {
		return $this->dataRow['exportBands'];
	}
	function sc_wab_exportcontinent() {
		return $this->dataRow['exportCont'];
	}
	function sc_wab_exportqslsent() {
		return $this->dataRow['exportQslS'];
	}
	function sc_wab_exportlimit() {
		return $this->dataRow['exportLimit'];
	}
	function sc_wab_exportorder() {
		return $this->dataRow['exportOrder'];
	}
	function sc_wab_exportformat() {
		return $this->dataRow['exportFormat'];
	}
	function sc_wab_exportdo() {
		return $this->dataRow['exportDo'];
	}
	function sc_wab_exportqslrcvd() {
		return $this->dataRow['exportQslR'];
	}
	function sc_wab_exportreset() {
		return $this->dataRow['exportReset'];
	}
	// quick menu
    function sc_wab_quickmine() {
        return $this->dataRow['wabQuickMyLog'];
    }
    function sc_wab_quickstart() {
        return $this->dataRow['wabQuickStart'];
    }
    function sc_wab_quickcall() {
        return $this->dataRow['wabQuickCall'];
    }
    function sc_wab_quickfreq() {
        return $this->dataRow['wabQuickFreq'];
    }
    function sc_wab_quickrrs() {
        return $this->dataRow['wabQuickRrs'];
    }
    function sc_wab_quicksrs() {
        return $this->dataRow['wabQuickSrs'];
    }
    function sc_wab_quickmode() {
        return $this->dataRow['wabQuickMode'];
    }
    function sc_wab_quicksqr() {
        return $this->dataRow['wabQuickWab'];
    }
    function sc_wab_quickarea() {
        return $this->dataRow['wabQuickMine'];
    }

    function sc_wab_quicksub() {
        return $this->dataRow['wabQuickSubmit'];
    }
    // Settings shortcodes
    function sc_wab_settingsname() {
        return $this->dataRow['wabUserName'];
    }

    function sc_wab_settingscall() {
        return $this->dataRow['wabUserCallsign'];
    }
    function sc_wab_settingsarea() {
        return $this->dataRow['wabUserHomeArea'];
    }
    function sc_wab_settingscounty() {
        return $this->dataRow['wabUserCountyfk'];
    }
    function sc_wab_settingscountry() {
        return $this->dataRow['wabUserCountryfk'];
    }
    function sc_wab_settingsiaru() {
        return $this->dataRow['wabUserHomeIARU'];
    }
    function sc_wab_settingsqrzlogin() {
    	// WAB_SETTINGSQRZLOGIN
        return $this->dataRow['wabUserQrzLogin'];
    }
    function sc_wab_settingsqrzpassword() {
        return $this->dataRow['wabUserQrzPassword'];
    }

    function sc_wab_settingstitle($parm = 0) {
        return $this->dataRow['wabLogTitle'][$parm];
    }
    function sc_wab_settingstype($parm = 0) {
        return $this->dataRow['wabLogListLogType'][$parm];
    }
    function sc_wab_settingsprivate($parm = 0) {
        return $this->dataRow['wabLogListPrivate'][$parm];
    }
    function sc_wab_settingsactive($parm = 0) {
        return $this->dataRow['wabLogListActive'][$parm];
    }
    function sc_wab_settingsdefault($parm = 0) {
        return $this->dataRow['wabLogListDefault'][$parm];
    }
    function sc_wab_settingssave() {
        return $this->dataRow['wabLogSettingsSave'];
    }
    function sc_wab_settingscreate() {
        return $this->dataRow['wabLogSettingscreate'];
    }
}