<?php
/**
* class file for the WAB log plugin.
*
* @package WAB
* @copyright 2008-2015 Barry Keal G4HDU
* @license GPL
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @author Barry Keal G4HDU <www.g4hdu.co.uk>
* @version 1.0.1  In development. Very unstable.
*/

/**
* .
*/

if (!defined('e107_INIT')) {
    exit;
}
require_once('templates/wab_template.php');
/**
* wab::__construct()
*
* Class constructor
*
* All processes go through the wab object
* - set properties
* - process commands either by equery or post
*
* @return null
* @package WAB
* @see eMessage message_handler.php message handler class
* @see e_db_mysql mysql_class.php database class
* @see e_form form_handler.php Form Handler class
* @see e_parse e_parse_class.php text parsing class
* @version 1.0.1
* @since 1.0.1
*/
class wab {
    /**
    *
    * @var integer $from        Used in mysql limit for paged lists
    */
    protected $from = 0;
    /**
    *
    * @var integer $id          the ID of the record to work on
    */
    protected $id = 0;
    /**
    *
    * @var string $action What are we working on
    */
    /**
    *
    * @var array of all current parameters
    */
    protected $current = array();
    protected $action ;
    /**
    *
    * @var string $subAction What we are doing with it
    */
    protected $subAction;
    /**
    *
    * @var array $prefs The prefs associated with this plugin mixed
    */
    protected $prefs;
    /**
    *
    * @var object $mes Display message object
    */
    protected $mes;
    /**
    *
    * @var object $sql Database object
    */
    protected $sql;
    /**
    *
    * @var object $tp Text parsing object
    */
    protected $tp;
    protected $gen;
    /**
    *
    * @var string $column column currently sorting
    */
    protected $column;
    /**
    *
    * @var string $dirn direction currently sorting
    */
    protected $dirn;
    /**
    *
    * @var string $selectedTerm direction currently selecting
    */
    protected $selectedTerm;

    /**
    *
    * @var string $searchTerm direction currently searching
    */
    protected $searchTerm;

    public $sc;
    public $template;
    /**
    *
    * @var boolean $saving Saving flag
    * @since 1.0.1 initial creation
    */
    protected $saving = false;
    /**
    *
    * @var boolean $saving Saving flag
    * @since 1.0.1 initial creation
    */
    protected $parameters;

    /**
    * wab::__construct()
    *
    * @version 1.0.1
    * @since 1.0.1 initial creation
    */
    public function __construct() {
        // initialise all objects
        $this->prefs = e107::getPlugConfig('wab', '', true);

        $this->mes = e107::getMessage();
        $this->sql = e107::getDb();
        $this->tp = e107::getParser();
        $this->frm = e107::getForm();
        $this->ns = e107::getRender();
        $this->gen = e107::getDateConvert();
        $this->sc = e107::getScBatch('wab', true);
        $this->template = new WabTemplate;
        $tmp = '';
        $this->parameters = array();
        $this->getSession();

        /*
    if (e_QUERY) {
            $tmp = explode('.', e_QUERY);

            $this->current['action'] = $tmp[0];
            $this->current['subAction'] = $tmp[1];
            $this->current['id'] = $tmp[2]; //
            $this->current['from'] = (int)$tmp[3];
            $this->current['column'] = $tmp[4];
            $this->current['direction'] = $tmp[5];
            $this->current['logbook'] = $tmp[6];
            $this->current['selection'] = $tmp[7];
            $this->current['search'] = $tmp[8];
            unset($tmp);
        }
        // if e_QUERY then it is a get submission so explode all the bits
   */
        if (strpos(e_SELF, 'admin_config') > 0) {
            $program = 'admin';
            $this->current['adminMaxList'] = $this->prefs->getPref('wab_adminpp', 30);
        } else {
            $program = 'index';
            $this->current['userMaxList'] = $this->prefs->getPref('wab_mainpp', 20);
        }
        // if our $_GET (begins with wab...) then get all the bits
        if (isset($_GET['wabAction'])) {
            $this->current['action'] = $_GET['wabAction'];
        }
        if (isset($_GET['wabSubAction'])) {
            $this->current['subAction'] = $_GET['wabSubAction'];
        }
        if (isset($_GET['wabFrom'])) {
            $this->current['from'] = (int)$_GET['wabFrom'];
        }
        if (isset($_GET['wabID'])) {
            $this->current['id'] = $_GET['wabID'];
        }
        if (isset($_GET['wabColumn'])) {
            $this->current['column'] = $_GET['wabColumn'];
        }
        if (isset($_GET['wabSort'])) {
            $this->current['direction'] = $_GET['wabSort'];
        }
        if (isset($_GET['wabLog'])) {
            $this->current['direction'] = $_GET['wabLog'];
        }
        if (isset($_GET['wab-search'])) {
            $this->current['search'] = $_GET['wab-search'];
        }
        if (isset($_GET['wabUserlogSelect'])) {
            $this->current['selection'] = $_GET['wabUserlogSelect'];
        }
        if (isset($_GET['wabUserCallID'])) {
            $this->current['wabUserCallID'] = (int)$_GET['wabUserCallID'];
        }
        if (isset($_GET['wabUserlogID'])) {
            $this->current['wabUserlogID'] = (int)$_GET['wabUserlogID'];
        }
        if (isset($_GET['wabPerPage'])) {
            $this->current['wabPerPage'] = (int)$_GET['wabPerPage'];
        }
        if (isset($_POST['wabPerPage'])) {
            $this->current['wabPerPage'] = (int)$_POST['wabPerPage'];
        }
        if (isset($_POST['wabUserCallID'])) {
            $this->current['wabUserCallID'] = (int)$_POST['wabUserCallID'];
        }
        if (isset($_POST['wabUserlogID'])) {
            $this->current['wabUserlogID'] = (int)$_POST['wabUserlogID'];
        }
        // post actions
        if (isset($_POST['wabAction'])) {
            $this->current['action'] = $_POST['wabAction'];
        }
        if (isset($_POST['wabSubAction'])) {
            $this->current['subAction'] = $_POST['wabSubAction'];
        }
        if (isset($_POST['wabFrom'])) {
            $this->current['from'] = (int)$_POST['wabFrom'];
        }
        if (isset($_POST['wabID'])) {
            $this->current['id'] = $_POST['wabID'];
        }
        if (isset($_POST['wabColumn'])) {
            $this->current['column'] = $_POST['wabColumn'];
        }
        if (isset($_POST['wabSort'])) {
            $this->current['direction'] = $_POST['wabSort'];
        }
        if (isset($_POST['wabLog'])) {
            $this->current['direction'] = $_POST['wabLog'];
        }
        if (isset($_POST['wab-search'])) {
            $this->current['search'] = $_POST['wab-search'];
        }
        if (isset($_POST['wabUserlogSelect'])) {
            $this->current['selection'] = $_POST['wabUserlogSelect'];
        }
        // print_a($this->current);
        $this->setSession();
    }
    /**
    * wab::getSession()
    *
    * @return
    */
    protected function getSession() {
        $this->current = $_SESSION['wab'];
    }
    /**
    * wab::setSession()
    *
    * @return
    */
    protected function setSession() {
        $_SESSION['wab'] = $this->current;
    }
    /**
    * wab::nextPrev()
    *
    * @param string $action
    * @param integer $num_entry
    * @return
    */
    protected function nextPrev($action = '', $num_entry = 0) {
        $action = e_SELF . "?" . $action . "&amp;wabFrom=[FROM]";
        $parms = "{$num_entry},{$this->maxlogs},{$this->current['from']}," . $action;
        return "<div class='nextprev-bar'>&nbsp;" . $this->tp->parseTemplate("{NEXTPREV={$parms}}") . "&nbsp;</div>";
    }
    protected function buildLimit($filterElements) {
        $value = $filterElements["wabExportLimit"];
        if ($value > 0) {
            return " LIMIT {$value}";
        } else {
            return;
        }
    }
    /**
    * wab::buildOrder()
    *
    * @param mixed $filterElements
    * @return
    */
    protected function buildOrder($filterElements) {
        $ORDER = " ORDER BY ";

        $value = $filterElements["wabExportOrder"];
        switch ($value) {
            case 'bycall':
                $ORDER .= " wabLogCallsign ";
                break;
            case 'byarea':
                $ORDER .= " wabSquare ";
                break;
            case 'byctry':
                $ORDER .= " wabCountyName";
                break;
            case 'bycalldate':
                $ORDER .= " wabLogCallsign,wabLogStartDate";
                break;
            case 'byadate':
                $ORDER .= " wabSquare, wabLogStartDate";
                break;
            case 'bydate':
            default : // do nothing
                $ORDER .= " wabLogStartDate";
        }
        return $ORDER;
    }
    /**
    * wab::buildFilter()
    *
    * @param mixed $filterElements
    * @return
    */
    protected function buildFilter($filterElements = 'ee') {
        $WHERE = " WHERE wabLogUserfk = " . USERID;
        foreach($filterElements as $element => $value) {
            $element = strtolower($element);
            switch ($element) {
                case 'wabexportlogs':
                    switch ($value) {
                        case 0:
                            // do nothinh;
                            break;
                        default : $WHERE .= " and wabLogMyLog ='{$value}'";
                    } ;
                    break;
                case 'wabexportwab':
                    switch ($value) {
                        case 'wonly':
                            $WHERE .= " and wabLogAreaWorkedFK >0";
                            break;
                        case 'wnone':
                            $WHERE .= " and wabLogAreaWorkedFK =0";
                            break;
                        case 'wall':
                        default : // do nothing
                    }
                    break;
                case 'wabexportprivate':
                    switch ($value) {
                        case 'ponly':
                            $WHERE .= " and wabLogListPrivate >0";
                            break;
                        case 'pnone':
                            $WHERE .= " and wabLogListPrivate =0";
                            break;
                        case 'pall':
                        default : // do nothing
                    }
                    break;
                case 'wabexportactive':
                    switch ($value) {
                        case 'aconly':
                            $WHERE .= " and wabLogListActive >0";
                            break;
                        case 'acnone':
                            $WHERE .= " and wabLogListActive =0";
                            break;
                        case 'acall':
                        default : // do nothing
                    }
                    break;
                case 'wabexportbooks':
                    switch ($value) {
                        case 'bonly':
                            $WHERE .= " and wabLogCallsign in (SELECT wabBookCallsign from #wabbookcalls where wabLogCallsign=wabBookCallsign)";
                            break;
                        case 'bnone':
                            $WHERE .= " and wabLogCallsign not in (SELECT wabBookCallsign from #wabbookcalls where wabLogCallsign=wabBookCallsign)";
                            break;
                        case 'ball':
                        default : // do nothing
                    }
                    break;
                case 'wabexportcoastal':
                    switch ($value) {
                        case 'coastonly':
                            $WHERE .= " and wabCoastal != 0";
                            break;
                        case 'coastnone':
                            $WHERE .= " and wabCoastal = 0";
                            break;
                        case 'coastall':
                        default : // do nothing
                    }
                    break;
                case 'wabexportislands':
                    switch ($value) {
                        case 'ionly':
                            $WHERE .= " and wabIslandAreaFK != 0";
                            break;
                        case 'inone':
                            $WHERE .= " and wabIslandAreaFK = 0";
                            break;
                        case 'iall':
                        default : // do nothing
                    }
                    break;

                case 'wabexportmodes':
                    if ($value > 0) {
                        $WHERE .= " and wabLogModefk = '{$value}'";
                    } else {
                        // do nothing
                    }
                    break;
                case 'wabexportbands':
                    if ($value > 0) {
                        $WHERE .= " and wabLogBandfk = '{$value}'";
                    } else {
                        // do nothing
                    }
                    break;
                case 'wabexportqsls':
                    switch ($value) {
                        case 'qslsonly':
                            $WHERE .= " and wabLogQSLOut != 0";
                            break;
                        case 'qslsnone':
                            $WHERE .= " and wabLogQSLOut = 0";
                            break;
                        case 'qslsall':
                        default : // do nothing
                    }
                    break;
                case 'wabexportcountry':
                    switch ($value) {
                        case 'ukonly':
                            $WHERE .= " and wabCountryOrder = 0";
                            break;
                        case 'uknone':
                            $WHERE .= " and wabCountryOrder != 0";
                            break;
                        case 'ukall':
                        default : // do nothing
                    }
                    break;
                case 'wabexportcont':
                    if ($value > 0) {
                        $WHERE .= " and wabLogContinentFK = '{$value}'";
                    } else {
                        // do nothing
                    }
                    break;
                case 'wabexportqslr':
                    switch ($value) {
                        case 'qslronly':
                            $WHERE .= " and wabLogQSLIn != 0";
                            break;
                        case 'qslrnone':
                            $WHERE .= " and wabLogQSLIn = 0";
                            break;
                        case 'qslrall':
                        default : // do nothing
                    }
                    break;
                default: ;
            } // switch
        }
        return $WHERE;
    }

    /**
    * wab::formHidden()
    *
    * Returns an e107 string in a div (for html compliance) containing the standard hidden fields
    *
    * @return
    */
    protected function formHidden() {
        $retval .= '<div>';
        $retval .= $this->frm->hidden('wabAction', 'users');
        $retval .= $this->frm->hidden('wabSubAction', 'save');
        $retval .= $this->frm->hidden('wabId', $this->current['id']);
        $retval .= $this->frm->hidden('wabUserID', $userRec['wabUserID']);
        $retval .= $this->frm->hidden('wabFrom', $this->current['from']);
        $retval .= '</div>';
        return $retval;
    }

    /**
    * wab::iconSort()
    *
    * @param mixed $direction up - down - off
    * @param mixed $activity name of field to sort on
    * @version 1.0.1
    * @since 1.0.1
    * @return
    */
    function iconSort($action, $column = '', $direction = '') {
        $size = $this->prefs->getPref('wabIconSize', 11);
        $activeColour = $this->prefs->getPref('wabIconFColour', 'blue');
        $inActiveColour = $this->prefs->getPref('wabIconBColour', 'grey');

        if ($direction == 'asc') {
            // $directionL = 'desc';
            // $directionR = 'asc';
            $colourL = $inActiveColour ;
            $colourR = $activeColour;
        } elseif ($direction == 'desc') {
            // $directionL = 'asc';
            // $directionR = 'desc';
            $colourL = $activeColour ;
            $colourR = $inActiveColour;
        } else {
            // $directionL = 'null';
            // $directionR = 'null';
            $colourL = $inActiveColour;
            $colourR = $inActiveColour;
        }
        $action = e_SELF . "?" . $action;
        $retVal = "
		<a href='{$action}&amp;wabColumn={$column}&amp;wabSort=desc'>
    		<img src='images/sort/{$colourL}/16/down.png' alt='Sort desc' />
    	</a>";
        $retVal .= "
		<a href='{$action}&amp;wabColumn={$column}&amp;wabSort=asc'>
			<img src='images/sort/{$colourR}/16/up.png' alt='Sort Asc' />
		</a>";
        return $retVal;
    }
    /**
    * wab::iconAdd()
    *
    * @return
    * @version 1.0.1
    * @since 1.0.1
    */
    public function iconAdd() {
        $size = $this->prefs->getPref('wabIconSize', 22);
        $activeColour = $this->prefs->getPref('wabIconFColour', 'blue');
        $inActiveColour = $this->prefs->getPref('wabIconBColour', 'grey');
        $text = "
	<a href='" . e_SELF . "?{$this->current['action']}.edit.0.{$this->current['from']}..'>
		<img src='images/add/{$activeColour}/{$size}/add.png' alt='Add Record' />
	</a>";
        return $text;
    }
    /**
    * wab::logInsert()
    *
    * @param array $ array	$insertRecord
    * @return mixed insert status
    */
    protected function logInsert($insertRecord = array()) {
        $fields = "(wablogID,";
        $data = $insertRecord['wabLogStartDate'] . $insertRecord['wabLogCallsign'] . $insertRecord['wabLogFreq'] . $insertRecord['wabLogModefk'];
        $uniqueID = USERID . "__" . hash('sha256', $data, false);
        $values = "('{$uniqueID}',";
        foreach($insertRecord as $key => $value) {
            $fields .= $key . ", ";
            $values .= "'" . $value . "',";
        }
        $fields .= " wabLogUpdater)";
        $values .= " '" . USERID . "." . USERNAME . "')";
        $insertStr = "insert into #wablog {$fields} values {$values}";
        $this->sql->gen($insertStr, false);
        $retval['resultno'] = $this->sql->getLastErrorNumber();
        $retval['resulttxt'] = $this->sql->getLastErrorText();
        return $retval;
    }
    protected function getAreaKey($area = '') {
        $sql = e107::getDb();
        $qry = "select wabareaID from #wabareas where wabSquare='{$area}' ";
        if ($sql->gen($qry, false)) {
            $row = $sql->fetch();
            $retval = $row['wabareaID'];
        } else {
            $retval = false;
        }
        return $retval;
    }
    protected function inputArea($field = '', $value = '', $first = false) {
        $sql = e107::getDb();
    }
    protected function inputLogType($field = '', $value = '', $first = false, $parm = false) {
        $option_array = array('1' => 'General Log', '2' => 'Wab Fixed Station', '3' => 'WAB Mobile', '4' => 'WAB Portable', '5' => 'WAB Listener');
        if ($parm === false) {
            return $this->frm->select($field, $option_array, $value, $options , 'Select Log Type', $parm);
        } else {
            return $this->frm->select($field . "[{$parm}]", $option_array, $value, $options , 'Select Log Type', $parm);
        }
    }
    protected function inputCounty($field = '', $value = '', $first = false) {
        $sql = e107::getDb();
        $qry = "select wabCountyID,wabCountyName from #wabcounty order by wabCountyName";
        $sql->gen($qry, false);
        while ($row = $sql->fetch()) {
            // print_a($row);
            $option_array[$row['wabCountyID']] = $row['wabCountyName'];
        }

        return $this->frm->select($field, $option_array, $value, $options , $defaultBlank = 'Select County');
    }
    protected function inputCountry($field = '', $value = '', $first = false) {
        $sql = e107::getDb();
        $qry = "select wabCountryCode,wabCountryName from #wabcountry order by wabCountryOrder,wabCountryName";
        $sql->gen($qry, false);
        while ($row = $sql->fetch()) {
            // print_a($row);
            $option_array[$row['wabCountryCode']] = $row['wabCountryName'];
        }

        return $this->frm->select($field, $option_array, $value, $options , $first);
    }
    /**
    * Setters and Getters
    *
    * public methods to either set or get class properties. This is the only way they can be accessed
    */
    /**
    * wab::setFrom()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @param integer $from The value to store
    */
    public function setFrom($from = 0) {
        $this->current['from'] = (int)$from;
    }
    /**
    * wab::getFrom()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    public function getFrom() {
        return $this->current['from'];
    }
    /**
    * wab::setID()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @param integer $id
    */
    public function setID($id = 0) {
        $this->current['id'] = (int)$id;
    }
    /**
    * wab::getID()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    public function getID() {
        return $this->current['id'];
    }
    /**
    * wab::setAction()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @param string $action
    */
    public function setAction($action = 'prefs') {
        $this->current['id'] = $action;
    }
    /**
    * wab::getAction()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    public function getAction() {
        return $this->current['subAction'];
    }
    /**
    * wab::setSubAction()
    *
    * @param string $subAction
    * @version 1.0.1
    * @since 1.0.1
    */
    public function setSubAction($subAction = 'list') {
        $this->current['subAction'] = $subAction;
    }
    /**
    * wab::getSubAction()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    public function getSubAction() {
        return $this->current['subAction'];
    }
}

class wabAdmin extends wab {
    function __construct() {
        parent::__construct();
        $this->processAdmin();
    }
    protected function processAdmin() {
        // Next Section runs through the possible actions and checks the sub action
        // the subAction is the things Order is important because some things
        // will follow on from one action to another.
        // thats why it is if then not switch or if then else.
        if ($this->current['subAction'] == 'confirm') {
            switch ($this->current['action']) {
                case 'users':
                    $this->userConfirmed();
                    break;
                case 'logs':
                    break;
                case 'books':
                    $this->booksConfirmed();
                    break;
                case 'country':
                    $this->countryConfirmed();
                    break;
                case 'areas':
                    $this->areasConfirmed();
                    break;
                case 'islands':
                    $this->islandsConfirmed();
                    break;
                case 'counties':
                    $this->countiesConfirmed();
                    break;
                case 'nets':
                    break;
                default:
                    break;
            }
        }
        if ($this->current['subAction'] == 'delete') {
            switch ($this->current['action']) {
                case 'users':
                    $this->userDelete();
                    break;
                case 'logs':
                    break;
                case 'books':
                    $this->booksDelete();
                    break;
                case 'country':
                    $this->countryDelete();
                    break;
                case 'areas':
                    $this->areasDelete();
                    break;
                case 'islands':
                    $this->islandsDelete();
                    break;
                case 'counties':
                    $this->countiesDelete();
                    break;
                case 'nets':
                    break;
                default:
                    break;
            }
        }
        if ($this->current['subAction'] == 'save') {
            switch ($this->current['action']) {
                case 'prefs':
                    $this->prefsSave();
                    break;
                case 'users':
                    $this->userSave();
                    break;
                case 'logs':
                    break;
                case 'books':
                    $this->booksSave();
                    break;
                case 'country':
                    $this->countrySave();
                    break;
                case 'areas':
                    $this->areasSave();
                    break;
                case 'islands':
                    $this->islandsSave();
                    break;
                case 'counties':
                    $this->countiesSave();
                    break;
                case 'nets':
                    break;
                default:
                    break;
            }
        }
        if ($this->current['subAction'] == 'edit') {
            switch ($this->current['action']) {
                case 'prefs':
                    $this->prefsEdit();
                    break;
                case 'users':
                    $this->userEdit();
                    break;
                case 'logs':
                    break;
                case 'books':
                    $this->booksEdit();
                    break;
                case 'country':
                    $this->countryEdit();
                    break;
                case 'areas':
                    $this->areasEdit();
                    break;
                case 'islands':
                    $this->islandsEdit();
                    break;
                case 'counties':
                    $this->countiesEdit();
                    break;
                case 'nets':
                    break;
                default:
                    break;
            }
        }
        if ($this->current['subAction'] == 'list') {
            switch ($this->current['action']) {
                case 'users':
                    $this->userList();
                    break;
                case 'logs':
                    break;
                case 'books':
                    $this->booksList();
                    break;
                case 'country':
                    $this->countryList();
                    break;
                case 'areas':
                    $this->areasList();
                    break;
                case 'islands':
                    $this->islandsList();
                    break;
                case 'counties':
                    $this->countiesList();
                    break;
                case 'nets':
                    break;
                default:
                    break;
            }
        }
    }

    /**
    * wabAdmin::get_options()
    *
    * Create the options and links for the admin menu
    *
    * @version 1.0.1
    * @since 1.0.1
    * @return array $var
    */
    public function get_options() {
        // ##### Display options ---------------------------------------------------------------------------------------------------------
        // admin menu links
        $var['prefs']['text'] = LAN_WAB_ADMIN_MENU1;
        $var['prefs']['link'] = e_SELF . '?wabAction=prefs&amp;wabSubAction=edit';
        $var['users']['text'] = LAN_WAB_ADMIN_MENU2;
        $var['users']['link'] = e_SELF . '?wabAction=users&amp;wabSubAction=list';
        $var['logs']['text'] = LAN_WAB_ADMIN_MENU3;
        $var['logs']['link'] = e_SELF . '?wabAction=logs&amp;wabSubAction=list';
        $var['books']['text'] = LAN_WAB_ADMIN_MENU4;
        $var['books']['link'] = e_SELF . '?wabAction=books&amp;wabSubAction=list';
        $var['areas']['text'] = LAN_WAB_ADMIN_MENU6;
        $var['areas']['link'] = e_SELF . '?wabAction=areas&amp;wabSubAction=list';
        $var['county']['text'] = LAN_WAB_ADMIN_MENU9;
        $var['county']['link'] = e_SELF . '?wabAction=counties&amp;wabSubAction=list';
        $var['country']['text'] = LAN_WAB_ADMIN_MENU5;
        $var['country']['link'] = e_SELF . '?wabAction=country&amp;wabSubAction=list';

        $var['islands']['text'] = LAN_WAB_ADMIN_MENU7;
        $var['islands']['link'] = e_SELF . '?wabAction=islands&amp;subaction=list';

        $var['nets']['text'] = LAN_WAB_ADMIN_MENU8;
        $var['nets']['link'] = e_SELF . '?wabAction=nets&amp;wabSubAction=list';

        return $var;
    }

    /**
    * wabAdmin::savePrefs()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function prefsSave() {
        $this->prefs->set('wab_maxlogs', $_POST['wab_maxlogs']);
        $this->prefs->set('wab_viewclass', $_POST['wab_viewclass']);
        $this->prefs->set('wab_useclass', $_POST['wab_useclass']);
        $this->prefs->set('wab_netcontrolclass', $_POST['wab_netcontrolclass']);
        $this->prefs->set('wab_modclass', $_POST['wab_modclass']);
        $this->prefs->set('wab_netmenu', $_POST['wab_netmenu']);
        $this->prefs->set('wab_adminpp', $_POST['wab_adminpp']);
        $this->prefs->set('wab_userpp', $_POST['wab_userpp']);
        $this->prefs->set('wabIconFColour', $_POST['wabIconFColour']);
        $this->prefs->set('wabIconBColour', $_POST['wabIconBColour']);
        $this->prefs->set('wabIconSize', $_POST['wabIconSize']);

        $this->prefs->save(true, true);

        $this->mes->addSuccess();
        $this->current['action'] = 'prefs';
        $this->current['subAction'] = 'edit';
        $this->setSession();
        // $this->ns->tablerender($caption, $this->mes->render() . $text);
    }

    /**
    * wabAdmin::prefsEdit()
    */
    protected function prefsEdit() {
        $maxlogs = (int)$this->prefs->getPref('wab_maxlogs', 3);
        $text = $this->frm->open('adminprefs', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'prefs');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
		<table class='table adminform' style='width:" . ADMIN_WIDTH . ";'>
    	<colgroup span='2'>
    		<col class='col-label' />
    		<col class='col-control' />
    	</colgroup>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS04 . ":</td>
			<td>" . e107::getUserClass()->uc_dropdown('wab_viewclass', (int)$this->prefs->getPref('wab_viewclass', 255), 'public,member,admin,classes') . "<span class='field-help'>" . LAN_WAB_ADMIN_PREFS05 . "</span></td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS06 . ":</td>
			<td>" . e107::getUserClass()->uc_dropdown('wab_useclass', (int)$this->prefs->getPref('wab_useclass', 255), 'member,admin,classes') . "<span class='field-help'>" . LAN_WAB_ADMIN_PREFS07 . "</span></td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS08 . ":</td>
			<td>" . e107::getUserClass()->uc_dropdown('wab_modclass', (int)$this->prefs->getPref('wab_modclass', 255), 'member,admin,classes') . "<span class='field-help'>" . LAN_WAB_ADMIN_PREFS09 . "</span></td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS22 . ":</td>
			<td>" . e107::getUserClass()->uc_dropdown('wab_netcontrolclass', (int)$this->prefs->getPref('wab_netcontrolclass', 255), 'member,admin,classes') . "<span class='field-help'>" . LAN_WAB_ADMIN_PREFS23 . "</span></td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS02 . ":</td>
			<td>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['label'] = 'On - off';
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS03;
        $text .= $this->frm->checkbox('wab_netmenu', '1', $this->prefs->getPref('wab_netmenu'), $callOptions);
        $text .= "
			</td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS10 . ":</td>
			<td>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['min'] = 1;
        $callOptions['max'] = 5;
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS11;
        $text .= $this->frm->number('wab_maxlogs', $this->prefs->getPref('wab_maxlogs', 3), 3, $callOptions);
        $text .= "
			</td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS12 . ":</td>
			<td>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['min'] = 10;
        $callOptions['max'] = 100;
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS14;
        $text .= $this->frm->number('wab_adminpp', $this->prefs->getPref('wab_adminpp', 30), 3, $callOptions);
        $text .= "
			</td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS13 . ":</td>
			<td>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['min'] = 10;
        $callOptions['max'] = 100;
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS15;
        $text .= $this->frm->number('wab_userpp', $this->prefs->getPref('wab_userpp', 30), 3, $callOptions);
        $text .= "
			</td>
		</tr>
		<tr>
			<td>" . LAN_WAB_ADMIN_PREFS16 . ":</td>
			<td>";
        unset($callOptions);
        unset($optionsArray);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS17;
        $optionsArray['red'] = 'Red';
        $optionsArray['green'] = 'Green';
        $optionsArray['blue'] = 'Blue';
        $optionsArray['orange'] = 'Orange';
        $optionsArray['grey'] = 'Grey';
        $text .= $this->frm->select('wabIconFColour', $optionsArray, $this->prefs->getPref('wabIconFColour', 'blue'), $callOptions);
        $text .= "</td>
    	</tr>
    	<tr>
			<td>" . LAN_WAB_ADMIN_PREFS20 . ":</td>
			<td>";
        unset($callOptions);
        unset($optionsArray);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS21;
        $optionsArray['red'] = 'Red';
        $optionsArray['green'] = 'Green';
        $optionsArray['blue'] = 'Blue';
        $optionsArray['orange'] = 'Orange';
        $optionsArray['grey'] = 'Grey';
        $text .= $this->frm->select('wabIconBColour', $optionsArray, $this->prefs->getPref('wabIconBColour', 'orange'), $callOptions);
        $text .= "</td>
    	</tr>
    	<tr>
			<td>" . LAN_WAB_ADMIN_PREFS18 . ":</td>
			<td>";
        unset($callOptions);
        unset($optionsArray);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = LAN_WAB_ADMIN_PREFS19;
        $optionsArray[11] = '11 px';
        $optionsArray[16] = '16 px';
        $optionsArray[22] = '22 px';
        $optionsArray[32] = '32 px';
        $text .= $this->frm->select('wabIconSize', $optionsArray, $this->prefs->getPref('wabIconSize', 16), $callOptions);
        $text .= "</td>
    	</tr>

	</table>
		<div class='buttons-bar center'>" . $this->frm->admin_button('wab_updateprefs', LAN_UPDATE, 'update') . "</div>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/settings16.png' alt='Settings' /> " . LAN_WAB_ADMIN_PREFS01;
        $this->ns->tablerender($title, $this->mes->render() . $text);
    }
    /*
	   User methods
	*/
    /**
    * wabAdmin::userConfirmed()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function userConfirmed() {
        if (isset($_POST['userDelete'])) {
            // get rid of stuff
            if ($this->sql->update('wabuser', $update)) {
                // do all the necessary deletions
                $this->mes->addSuccess(LAN_DELETED);
            } else {
                $this->mes->addError(LAN_DELETED_FAILED);
            }
        } else {
            // deletion was cancelled
            $this->mes->addInfo(LAN_WAB_ADMIN_USER16);
        }
        // either way we now list users.
        $this->current['action'] = 'users';
        $this->current['subAction'] = 'list';
    }

    /**
    * wabAdmin::userDelete()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function userDelete() {
        $qry = "select
        			u.user_name,
        			u.user_customtitle,
        			u.user_join,
        			u.user_lastvisit,
        			u.user_visits,
        			wu.*
        			from #user as u left join #wabuser as wu on u.user_id=wabUserID where u.user_id={$this->current['id']}";
        $this->sql->gen($qry, false);
        $userRec = $this->sql->db_Fetch();

        if ((int)$userRec['wabUserID'] === 0) {
            // record doesnt exist so create a record
            $this->sql->gen("insert into #wabuser (wabUserID) value ({$this->current['id']})", false);
            $this->sql->gen($qry, false);
            $userRec = $this->sql->db_Fetch();
        }
        $text = $this->frm->open('wabuseredit', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'users');
        $text .= $this->frm->hidden('wabSubAction', 'confirm');
        $text .= $this->frm->hidden('wabId', $userRec['wabUserID']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
	<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER11 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER12 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER13 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER14 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER15 . "</td>
		</tr>
		<tr>
			<td style='width:20%'>" . $userRec['user_name'] . "</td>
			<td style='width:20%'>" . $userRec['user_customtitle'] . "</td>
			<td style='width:20%'>" . $this->gen->convert_date($userRec['user_join'], 'short') . "</td>
			<td style='width:20%'>" . $this->gen->convert_date($userRec['user_lastvisit'], 'short') . "</td>
			<td style='width:20%'>" . $userRec['user_visits'] . "</td>
		</tr>
				<tr>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER08 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER09 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER10 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER14 . "</td>
			<td style='width:20%'>" . LAN_WAB_ADMIN_USER15 . "</td>
		</tr>
				<tr>
			<td colspan='5' style='text-align:left'>&nbsp;</td>
		</tr>
		<tr>
			<td style='width:20%'>" . $userRec['wabUserCallsign'] . "</td>
			<td style='width:20%'>" . $userRec['wabUserHomeArea'] . "</td>
			<td colspan='3'>" . $userRec['wabUserHomeIARU'] . "</td>

		</tr>
		<tr>
			<td colspan='5' style='text-align:center'>" . $this->frm->admin_button('userDelete', LAN_DELETE, 'delete') . " &nbsp;&nbsp;&nbsp;" . $this->frm->admin_button('userCancel', LAN_CANCEL, 'cancel') . "</td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:left'></td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_USER17, $text);
    }
    /**
    * wabAdmin::userEdit()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function userEdit() {
        $currentOrder = 0;
        $qry = "select * from #wabCountry order by wabCountryOrder,wabCountryName";
        $this->sql->gen($qry, false);
        while ($row = $this->sql->fetch()) {
            $optionsArray[$row['wabCountryCode']] = $row['wabCountryName'];
        }

        $qry = "select
        			u.user_name,
        			u.user_customtitle,
        			u.user_join,
        			u.user_lastvisit,
        			u.user_visits,
        			wu.*
        			from #user as u left join #wabuser as wu on u.user_id=wabUserID where wabUserID={$this->current['id']}";
        $this->sql->gen($qry, false);
        $userRec = $this->sql->db_Fetch();

        if ((int)$userRec['wabUserID'] === 0) {
            // record doesnt exist so create a record
            $this->sql->gen("insert into #wabuser (wabUserID) value ({$this->current['id']})", false);
            $this->sql->gen($qry, false);
            $userRec = $this->sql->db_Fetch();
        }
        $text = $this->frm->open('wabuseredit', 'post', e_SELF . "?wabAction=users&amp;wabSubAction=save&amp;wabID={$this->current['id']}");
        $text .= $this->formHidden();
        $text .= "
	<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr>
			<th style='width:20%'>" . LAN_WAB_ADMIN_USER11 . "</td>
			<th style='width:20%'>" . LAN_WAB_ADMIN_USER12 . "</td>
			<th style='width:20%'>" . LAN_WAB_ADMIN_USER13 . "</td>
			<th style='width:20%'>" . LAN_WAB_ADMIN_USER14 . "</td>
			<th style='width:20%'>" . LAN_WAB_ADMIN_USER15 . "</td>
		</tr>
		<tr>
			<td style='width:20%'>" . $userRec['user_name'] . "</td>
			<td style='width:20%'>" . $userRec['user_customtitle'] . "</td>
			<td style='width:20%'>" . $this->gen->convert_date($userRec['user_join'], 'short') . "</td>
			<td style='width:20%'>" . $this->gen->convert_date($userRec['user_lastvisit'], 'short') . "</td>
			<td style='width:20%'>" . $userRec['user_visits'] . "</td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_USER08 . "</td>
			<td style='vertical-align:top;' colspan='4'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 30;
        $callOptions['title'] = 'Users primary callsign';
        $callOptions['required'] = true;
        $text .= $this->frm->text('wabUserCallsign', $userRec['wabUserCallsign'], 30, $callOptions);
        $text .= "</td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_USER09 . "</td>
			<td style='vertical-align:top;' colspan='4'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['title'] = 'Users home WAB area';
        $text .= $this->frm->text('wabUserHomeArea', $userRec['wabUserHomeArea'], 6, $callOptions);
        $text .= "</td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_USER10 . "</td>
			<td style='vertical-align:top;' colspan='4'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['title'] = 'Users home IARU locator area';
        $text .= $this->frm->text('wabUserHomeIARU', $userRec['wabUserHomeIARU'], 6, $callOptions);
        $text .= "</td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_USER19 . "</td>
			<td style='vertical-align:top;' colspan='4'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = 'Users country';
        $text .= $this->frm->select('wabUserCountryfk', $optionsArray, $userRec['wabUserCountryfk'], $callOptions);
        $text .= "</td>
		</tr>
	</table>
			<div class='buttons-bar center'>" . $this->frm->admin_button('save_users', LAN_SAVE, 'update') . "</div>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/users16.png' alt='Users' //> " . LAN_WAB_ADMIN_USER05;
        $this->ns->tablerender($title, $text);
    }
    /**
    * wabAdmin::userList()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function userList() {
        $direction[] = array();
        switch ($this->current['column']) {
            case 'name':
                $orderby = 'user_name ';
                $direction[1] = $this->current['direction'];
                break;
            case 'call':
                $orderby = 'wabUserCallsign ';
                $direction[2] = $this->current['direction'];;
                break;
            case 'last':
                $orderby = 'user_lastvisit ';
                $direction[3] = $this->current['direction'];;
                break;
            case 'join':
                $orderby = 'user_join ';
                $direction[4] = $this->current['direction'];;
                break;
            case 'country':
                $orderby = 'wabCountryName ';
                $direction[5] = $this->current['direction'];;
                break;
            default:
            case 'id':
                $orderby = 'wabUserID ';
                $direction[0] = $this->current['direction'];
        } // switch
        $orderby .= $this->current['direction'];
        $text = $this->frm->open('wabuserlist', 'post');
        $taction = "wabAction=users&amp;wabSubAction=list&amp;wabID=0&amp;wabFrom=0";
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr >
			<td colspan='7' style='text-align:left;' >" . $this->frm->search('userlook', '', 'filt') . " </td>
		</tr>
		<tr>
			<th style='text-align:right;'>" . LAN_ID . "<br />" . $this->iconSort($taction, 'id', $direction[0]) . "</td>
			<th>" . LAN_NAME . "<br />" . $this->iconSort($taction, 'name', $direction[1]) . "</td>
			<th>" . LAN_WAB_ADMIN_USER01 . "<br />" . $this->iconSort($taction, 'call', $direction[2]) . "</td>
			<th>" . LAN_WAB_ADMIN_USER03 . "<br />" . $this->iconSort($taction, 'last', $direction[3]) . "</td>
			<th>" . LAN_WAB_ADMIN_USER13 . "<br />" . $this->iconSort($taction, 'join', $direction[4]) . "</td>
			<th style='text-align:center;'>" . LAN_WAB_ADMIN_USER18 . "<br />" . $this->iconSort($taction, 'country', $direction[5]) . "</td>
			<th style='text-align:right;'>" . LAN_WAB_ADMIN_USER04 . "</td>
		</tr>
		";

        $query = "select u.user_id,u.user_name,u.user_join,u.user_lastvisit,
        	wu.wabUserID,wu.wabUserCallsign,wu.wabUserHomeArea,wu.wabUserCountryfk,
        	c.wabCountryCode,c.wabCountryName
        	from #user as u left join #wabuser as wu on u.user_id=wabUserID
        	left join #wabcountry as c on wu.wabUserCountryfk=c.wabCountryCode
        	order by {$orderby}
        	limit {$this->current['from']},{$this->maxlogs}";
        if ($this->sql->gen($query, false)) {
            $userList = $this->sql->db_getList();
            foreach($userList as $userRow) {
                $action = "&amp;wabAction=users&amp;wabID={$userRow['wabUserID']}&amp;wabSubAction=";
                $text .= "
				<tr>
					<td style='vertical-align:top;text-align:right;'>{$userRow['wabUserID']}</td>
					<td style='vertical-align:top;'>{$userRow['user_name']}</td>
					<td style='vertical-align:top;'>{$userRow['wabUserCallsign']}</td>
					<td style='vertical-align:top;'>" . $this->gen->convert($userRow['user_lastvisit'], 'short') . "</td>
					<td style='vertical-align:top;'>" . $this->gen->convert($userRow['user_join'], 'short') . "</td>
					<td style='vertical-align:top;text-align:center;'>" . $this->iconFlag($userRow['wabUserCountryfk'], $userRow['wabCountryName']) . "</td>
					<td style='vertical-align:top;; text-align:right'>
					<a href='" . e_SELF . "?{$action}edit' alt='Edit' title='Edit'>" . ADMIN_EDIT_ICON . "</a>
					<a href='" . e_SELF . "?{$action}delete' alt='Delete' title='Delete'>" . ADMIN_DELETE_ICON . "</a>
					</td>
				</tr>
				";
            }
            $text .= "
			<tr>
				<td colspan='7' style='text-align:left'>";
            $num_entry = $this->sql->count('user'); //number of records in user table
            $action = "wabAction=users&amp;wabSubAction=list&amp;wabID=0";
            $text .= $this->nextPrev($action, $num_entry);
            $text .= "	</td>
			</tr>";
        } else {
            $text .= "<tr><td colspan='7' style='text-align:center'>" . LAN_WAB_GEN01 . "</td>";
        }

        $text .= "
			</table>";
        $text .= $this->frm->close();

        $title = "<img src='images/menu/users16.png' alt='users' /> " . LAN_WAB_ADMIN_USER00;
        $this->ns->tablerender($title, $this->mes->render() . $text);
    }
    /**
    * wabAdmin::save_user()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function userSave() {
        unset($update);
        $update = array(
            'wabUserCallsign' => $this->tp->toDb(strtoupper ($_POST['wabUserCallsign'])),
            'wabUserHomeArea' => $this->tp->toDb(strtoupper ($_POST['wabUserHomeArea'])),
            'wabUserHomeIARU' => $this->tp->toDb($_POST['wabUserHomeIARU']),
            'wabUserCountryfk' => $this->tp->toDb($_POST['wabUserCountryfk']),
            'wabUserLastUpdate' => time(),
            'wabUserUpdater' => USERID . '.' . USERNAME,
            'WHERE' => 'wabUserID = ' . (int)$this->current['id']);

        if ($this->sql->update('wabuser', $update, false)) {
            $this->mes->addSuccess(LAN_UPDATED);
        } else {
            $this->mes->addError(LAN_UPDATED_FAILED);
        }
        $this->current['action'] = 'users';
        $this->current['subAction'] = 'list';
        $this->setSession();
    }
    /*
	   Books methods
	*/
    /**
    * wabAdmin::booksConfirmed()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper deletions
    */
    protected function booksConfirmed() {
        if (isset($_POST['booksDelete'])) {
            // get rid of stuff
            if ($this->sql->update('wabcountry', $update)) {
                // do all the necessary deletions
                $this->mes->addSuccess(LAN_DELETED);
            } else {
                $this->mes->addError(LAN_DELETED_FAILED);
            }
        } else {
            // deletion was cancelled
            $this->mes->addInfo(LAN_WAB_ADMIN_BOOKS18);
        }
        // either way we now list books.
        $this->current['action'] = 'books';
        $this->current['subAction'] = 'list';
    }

    /**
    * wabAdmin::booksDelete()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function booksDelete() {
        $qry = "select * from #wabBookList where wabBookNo={$this->current['id']}";
        $this->sql->gen($qry, false);
        $booksRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabbooksdelete', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'books');
        $text .= $this->frm->hidden('wabSubAction', 'confirm');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
			<tr>
				<td style='vertical-align:top;width:25%;'>" . LAN_WAB_ADMIN_BOOKS10 . " <b>{$booksRec['wabBookNo']}</b></td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS11 . " <b>{$booksRec['wabBookSeries']}</b></td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS12 . " <b>{$booksRec['wabBookDate']}</b></td>
			</tr>
			<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS13 . " <b>{$booksRec['wabBookHolder']}</b></td>
			</tr>

			<tr>
				<td colspan='5' style='text-align:center'>" . $this->frm->admin_button('booksDelete', LAN_DELETE, 'delete') . " &nbsp;&nbsp;&nbsp;" . $this->frm->admin_button('booksCancel', LAN_CANCEL, 'cancel') . "</td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:left'></td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_BOOKS16, $text);
    }
    /**
    * wabAdmin::booksEdit()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo date format if empty.
    * @todo add a cancel button.
    * @todo data validation before submit.
    */
    protected function booksEdit() {
        $qry = "select * from #wabBookList where wabBookNo={$this->current['id']}";
        $this->sql->gen($qry, false);
        $booksRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabcountryedit', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'books');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
			<tr>
				<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_BOOKS10 . " *</td>
				<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 6;
        $callOptions['min'] = 1;
        $callOptions['max'] = 30000;
        $callOptions['title'] = 'Book number';
        $callOptions['required'] = true;
        $text .= $this->frm->number('wabBookNo', $booksRec['wabBookNo'], 6, $callOptions);
        $text .= "</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS11 . "</td>
				<td style='vertical-align:top;'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 4;
        $callOptions['min'] = 1;
        $callOptions['max'] = 9;
        $callOptions['title'] = 'Book Series';
        $text .= $this->frm->number('wabBookSeries', $booksRec['wabBookSeries'], 4, $callOptions);
        $text .= "</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS12 . "</td>
				<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['type'] = 'date';
        $callOptions['title'] = "Date the book was issued";
        $text .= $this->frm->datepicker('wabBookDate', $booksRec['wabBookDate'], 6, $callOptions);
        $text .= "</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS13 . "</td>
				<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['title'] = 'Book holders name or callsign';
        $text .= $this->frm->text('wabBookHolder', $booksRec['wabBookHolder'], 6, $callOptions);
        $text .= "</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS14 . "</td>
				<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['label'] = 'Reissued';
        $callOptions['title'] = 'Check if the book is a reissue';
        $text .= $this->frm->checkbox('wabBookReissue', 1, ($booksRec['wabBookReissue'] == 1?true:false), $callOptions);
        $this->frm->text('wabBookReissue', $booksRec['wabBookReissue'], 6, $callOptions);
        $text .= "</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS15 . "</td>
				<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['label'] = "NLV";
        $callOptions['title'] = 'Check if the book is no longer valid';
        $text .= $this->frm->checkbox('wabBookNLV', 1, ($booksRec['wabBookNLV'] == 1?true:false), $callOptions);
        $text .= "</td>
			</tr>
	</table>
		<div class='buttons-bar center'>" . $this->frm->admin_button('save_books', LAN_SAVE, 'update') . "</div>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/book16.png' alt='books' /> " . LAN_WAB_ADMIN_BOOKS17;
        $this->ns->tablerender($title, $text);
    }
    /**
    * wabAdmin::booksList()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo add an add new book button.
    */
    protected function booksList() {
        $direction[] = array();

        switch ($this->current['column']) {
            case 'home':
                $orderby = 'wabUserHomeArea ';
                $direction[1] = $this->current['direction'];
                break;
            case 'owner':
                $orderby = 'wabBookHolder ';
                $direction[2] = $this->current['direction'];;
                break;
            case 'series':
                $orderby = 'wabBookSeries ';
                $direction[3] = $this->current['direction'];;
                break;
            case 'issued':
                $orderby = 'wabBookDate ';
                $direction[4] = $this->current['direction'];;
                break;
            case 'reissue':
                $orderby = 'wabBookReissue ';
                $direction[5] = $this->current['direction'];;
                break;
            default:
            case 'id':
                $orderby = 'wabBookNo ';
                $direction[0] = $this->current['direction'];
        } // switch
        $orderby .= $this->current['direction'];
        $text = $this->frm->open('wabbooklist', 'post');
        $action = "&amp;wabAction=books&amp;wabSubAction=list&amp;wabID=0&amp;wabFrom=0";
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr >
			<td colspan='5' style='text-align:left;' >" . $this->frm->search('booklook', '', 'filt') . " </td>
			<td colspan='2' style='text-align:right;' >" . $this->iconAdd() . " </td>
		</tr>
		<tr>
				<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_BOOKS02 . "<br />" . $this->iconSort($action, 'book', $direction[0]) . "</td>
				<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_BOOKS03 . "<br />" . $this->iconSort($action, 'owner', $direction[2]) . "</td>
				<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_BOOKS04 . "<br />" . $this->iconSort($action, 'series', $direction[3]) . "</td>
				<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_BOOKS05 . "<br />" . $this->iconSort($action, 'issued', $direction[4]) . "</td>
				<td style='width:10%;text-align:center;'>" . LAN_WAB_ADMIN_BOOKS06 . "<br />" . $this->iconSort($action, 'reissue', $direction[5]) . "</td>
				<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_BOOKS07 . "<br />" . $this->iconSort($action, 'home', $direction[1]) . "</td>
				<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_BOOKS08 . "</td>
			</tr>
			";
        $query = "select * from #wabbooklist as b left join #wabUser as u on u.wabUserCallsign like '%b.wabBookHolder%' order by {$orderby} limit {$this->current['from']},{$this->maxlogs}";
        if ($this->sql->gen($query, false)) {
            $booksRec = $this->sql->db_getList();
            foreach($booksRec as $booksRow) {
                $text .= "
					<tr>
						<td style='text-align:right;vertical-align:top;'>{$booksRow['wabBookNo']}</td>
						<td style='text-align:left;vertical-align:top;'>{$booksRow['wabBookHolder']}</td>
						<td style='text-align:right;vertical-align:top;'>{$booksRow['wabBookSeries']}</td>
						<td style='text-align:left;vertical-align:top;'>" . (empty($booksRow['wabBookDate'])?$booksRow['wabBookDate']:'&nbsp;') . "</td>
						<td style='text-align:center;vertical-align:top;'>" . ($booksRow['wabBookReissue']?ADMIN_TRUE_ICON:'') . "</td>
						<td style='text-align:left;vertical-align:top;'>{$booksRow['wabUserHomeArea']}</td>

						<td style='vertical-align:top;text-align:right;'>
						<a href='" . e_SELF . "?wabAction=books&amp;wabSubAction=edit&amp;wabID={$booksRow['wabBookNo']}&amp;wabFrom={$this->current['from']}' alt='Edit' title='Edit'>" . ADMIN_EDIT_ICON . "</a>
						<a href='" . e_SELF . "?wabAction=books&amp;wabSubAction=delete&amp;wabID={$booksRow['wabBookNo']}&amp;wabFrom={$this->current['from']}' alt='Delete' title='Delete'>" . ADMIN_DELETE_ICON . "</a>
						</td>
					</tr>
					";
            }
            $text .= "
				<tr>
					<td colspan='7' style='text-align:left'>";
            $num_entry = $this->sql->count('wabBookList'); //number of records in country table
            $action = "wabAction=books&amp;wabSubAction=list&amp;wabID=0";
            $text .= $this->nextPrev($action, $num_entry);
            $text .= "
					</td>
				</tr>";
        } else {
            $text .= "<tr><td colspan='7' style='text-align:center'>" . LAN_WAB_GEN01 . "</td>";
        }

        $text .= "
				</table>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/book16.png' alt='books' /> " . LAN_WAB_ADMIN_BOOKS01;
        $this->ns->tablerender($title, $this->mes->render() . $text);
    }
    /**
    * wabAdmin::booksSave()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper save with on duplicate update.
    * @todo date conversions .
    */
    protected function booksSave() {
        unset($update);
        $update = array(
            'wabBookNo' => (int)$_POST['wabBookNo'],
            'wabBookSeries' => (int)$_POST['wabBookSeries'],
            'wabBookDate' => $this->tp->toDb(strtoupper ($_POST['wabBookDate'])),
            'wabBookHolder' => $this->tp->toDb($_POST['wabBookHolder']),
            'wabBookHolderFK' => (int)$_POST['wabBookHolderFK'],
            'wabBookReissue' => (int)$_POST['wabBookReissue'],
            'wabBookNLV' => (int)$_POST['wabBookNLV'],
            'wabBookLastUpdate' => time(),
            'wabBookUpdater' => USERID . '.' . USERNAME,
            'WHERE' => 'wabBookNo = ' . (int)$_POST['wabBookNo']); // where must be in caps
        if ($this->sql->update('wabbooklist', $update, false)) {
            $this->mes->addSuccess(LAN_UPDATED);
        } else {
            $this->mes->addError(LAN_UPDATED_FAILED);
        }
        $this->current['action'] = 'books';
        $this->current['subAction'] = 'list';
        $this->setSession();
    }

    /*
	   Country methods
	*/
    /**
    * wabAdmin::countryConfirmed()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper deletions
    */
    protected function countryConfirmed() {
        if (isset($_POST['countryDelete'])) {
            // get rid of stuff
            if ($this->sql->update('wabcountry', $update)) {
                // do all the necessary deletions
                $this->mes->addSuccess(LAN_DELETED);
            } else {
                $this->mes->addError(LAN_DELETED_FAILED);
            }
        } else {
            // deletion was cancelled
            $this->mes->addInfo(LAN_WAB_ADMIN_COUNTRY18);
        }
        // either way we now list country.
        $this->current['action'] = 'country';
        $this->current['subAction'] = 'list';
    }

    /**
    * wabAdmin::countryDelete()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function countryDelete() {
        $qry = "select * from #wabCountry where wabCountryCode='{$this->current['id']}'";
        $this->sql->gen($qry, false);
        $countryRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabcountrydelete', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'country');
        $text .= $this->frm->hidden('wabSubAction', 'confirm');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
			<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
				<tr>
					<td style='vertical-align:top;width:25%;'>" . LAN_WAB_ADMIN_COUNTRY10 . " <b>{$countryRec['wabCountryCode']}</b></td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_COUNTRY11 . " <b>{$countryRec['wabCountryName']}</b></td>
				</tr>
				<tr>
					<td colspan='5' style='text-align:center'>" . $this->frm->admin_button('countryDelete', LAN_DELETE, 'delete') . " &nbsp;&nbsp;&nbsp;" . $this->frm->admin_button('countryCancel', LAN_CANCEL, 'cancel') . "</td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:left'></td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_COUNTRY16, $text);
    }
    /**
    * wabAdmin::countryEdit()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo date format if empty.
    * @todo add a cancel button.
    * @todo data validation before submit.
    */
    protected function countryEdit() {
        $qry = "select * from #wabcountry where wabCountryCode='{$this->current['id']}'";
        $this->sql->gen($qry, false);
        $countryRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabcountryedit', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'country');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
			<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
				<tr >
					<td colspan='2'  >Add </td>
				</tr>
				<tr>
					<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_COUNTRY10 . " *</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 6;
        $callOptions['title'] = 'Country code (Two letters only)';
        $callOptions['required'] = true;
        $text .= $this->frm->text('wabCountryCode', $countryRec['wabCountryCode'], 3, $callOptions);
        $text .= "</td>
				</tr>
								<tr>
					<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_COUNTRY11 . " *</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 30;
        $callOptions['title'] = 'Country name';
        $callOptions['required'] = true;
        $text .= $this->frm->text('wabCountryName', $countryRec['wabCountryName'], 30, $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_COUNTRY12 . " *</td>
					<td style='vertical-align:top;'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = 'Sort list into three parts, top of list, middle or bottom.';
        $optionsArray[0] = 'Top of List';
        $optionsArray[1] = 'middle of List';
        $optionsArray[2] = 'Bottom of List';

        $text .= $this->frm->select('wabCountryOrder', $optionsArray, $countryRec['wabCountryOrder'], $callOptions);
        $text .= "</td>
				</tr>
	</table>
		<div class='buttons-bar center'>" . $this->frm->admin_button('save_country', LAN_SAVE, 'update') . "</div>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_COUNTRY17, $text);
    }
    /**
    * wabAdmin::countryList()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo add an add new country button.
    */
    protected function countryList() {
        $direction[] = array();
        switch ($this->current['column']) {
            case 'cuname':
                $orderby = 'wabCountryName ';
                $direction[1] = $this->current['direction'];
                $orderby .= $this->current['direction'];
                break;
            case 'cucode':
                $orderby = 'wabCountryCode ';
                $direction[2] = $this->current['direction'];
                $orderby .= $this->current['direction'];
                break;
            default:
            case 'cuorder':
                $orderby = 'wabCountryOrder ';
                $direction[0] = $this->current['direction'];
                $orderby .= $this->current['direction'];
                $orderby .= ",wabCountryName asc";
        } // switch
        $action = "&amp;wabAction=country&amp;wabSubAction=list&amp;wabID=0&amp;wabFrom=0";
        $text = $this->frm->open('wabcountrylist', 'post');
        $text .= "
	<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr >
			<td colspan='2' style='text-align:left;' >" . $this->frm->search('countrylook', '', 'filt') . " </td>
			<td colspan='3' style='text-align:right;' >" . $this->iconAdd() . " </td>
		</tr>
			<tr>
				<th style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_COUNTRY02 . "<br />" . $this->iconSort($action, 'cucode', $direction[2]) . "</td>
				<th style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_COUNTRY03 . "<br />" . $this->iconSort($action, 'cuname', $direction[1]) . "</td>
				<th style='width:20%;text-align:center;'>" . LAN_WAB_ADMIN_COUNTRY04 . "</td>
				<th style='width:20%;text-align:center;'>" . LAN_WAB_ADMIN_COUNTRY05 . "<br />" . $this->iconSort($action, 'cuorder', $direction[0]) . "</td>
				<th style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_COUNTRY08 . "</td>
			</tr>";
        $qry = "select * from #wabCountry order by {$orderby} limit {$this->current['from']},{$this->maxlogs}";
        if ($this->sql->gen($qry, false)) {
            while ($countryRec = $this->sql->fetch()) {
                $action = "&amp;wabAction=country&amp;wabID={$countryRec['wabCountryCode']}&amp;wabSubAction=";
                $text .= "
						<tr>
							<td style='text-align:right;vertical-align:top;'>{$countryRec['wabCountryCode']}</td>
							<td style='text-align:left;vertical-align:top;'>{$countryRec['wabCountryName']}</td>
							<td style='text-align:center;vertical-align:top;'>" . $this->iconFlag($countryRec['wabCountryCode'], $countryRec['wabCountryName']) . "</td>
							<td style='text-align:center;vertical-align:top;'>{$countryRec['wabCountryOrder']}</td>
							<td style='vertical-align:top;text-align:right;'>
							<a href='" . e_SELF . "?{$action}edit' alt='Edit' title='Edit'>" . ADMIN_EDIT_ICON . "</a>
							<a href='" . e_SELF . "?{$action}delete' alt='Delete' title='Delete'>" . ADMIN_DELETE_ICON . "</a>
							</td>
						</tr>
						";
            }
            $text .= "
					<tr>
						<td colspan='5' style='text-align:left'>";
            $num_entry = $this->sql->count('wabcountry'); //number of records in country table
            $action = "action=country&amp;subaction=list&amp;id=0";
            $text .= $this->nextPrev($action, $num_entry);
            $text .= "<div class='nextprev-bar'>&nbsp;" . $this->tp->parseTemplate("{NEXTPREV={$parms}}") . "&nbsp;</div>
						</td>
					</tr>";
        } else {
            $text .= "<tr><td colspan='5' style='text-align:center'>" . LAN_WAB_GEN01 . "</td>";
        }

        $text .= "
					</table>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/globe16.png' alt='Countries' /> " . LAN_WAB_ADMIN_COUNTRY01;
        $this->ns->tablerender($title, $this->mes->render() . $text);
    }
    /**
    * wabAdmin::countrySave()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper save with on duplicate update.
    * @todo date conversions .
    */
    protected function countrySave() {
        unset($update);
        $update = array(
            'wabCountryCode' => $this->tp->toDb(strtolower ($_POST['wabCountryCode'])),
            'wabCountryName' => $this->tp->toDb(ucwords(strtolower ($_POST['wabCountryName']))),
            'wabCountryOrder' => (int)$_POST['wabCountryOrder'],
            'wabCountryLastUpdate' => time(),
            'wabCountryUpdater' => USERID . '.' . USERNAME,
            'WHERE' => 'wabCountryCode = ' . "'" . $_POST['wabCountryCode'] . "'"); // where must be in caps
        if ($this->sql->update('wabcountry', $update, false)) {
            $this->mes->addSuccess(LAN_UPDATED);
        } else {
            $this->mes->addError(LAN_UPDATED_FAILED);
        }
        $this->current['action'] = 'country';
        $this->current['subAction'] = 'list';
        $this->setSession();
    }

    /*
	   Areas methods
	*/
    /**
    * wabAdmin::areasConfirmed()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper deletions
    */
    protected function areasConfirmed() {
        if (isset($_POST['areasDelete'])) {
            // get rid of stuff
            if ($this->sql->update('wabcountry', $update)) {
                // do all the necessary deletions
                $this->mes->addSuccess(LAN_DELETED);
            } else {
                $this->mes->addError(LAN_DELETED_FAILED);
            }
        } else {
            // deletion was cancelled
            $this->mes->addInfo(LAN_WAB_ADMIN_BOOKS18);
        }
        // either way we now list areas.
        $this->current['action'] = 'areas';
        $this->current['subAction'] = 'list';
    }

    /**
    * wabAdmin::areasDelete()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function areasDelete() {
        $qry = "select * from #wabAreas where wabareaID={$this->current['id']}";
        $this->sql->gen($qry, false);
        $areasRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabareasdelete', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'areas');
        $text .= $this->frm->hidden('wabSubAction', 'confirm');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
			<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
				<tr>
					<td style='vertical-align:top;width:25%;'>" . LAN_WAB_ADMIN_AREAS03 . " <b>{$areasRec['wabSquare']}</b></td>
				</tr>
				<tr>
					<td colspan='5' style='text-align:center'>" . $this->frm->admin_button('areasDelete', LAN_DELETE, 'delete') . " &nbsp;&nbsp;&nbsp;" . $this->frm->admin_button('areasCancel', LAN_CANCEL, 'cancel') . "</td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:left'></td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_AREAS16, $text);
    }
    /**
    * wabAdmin::areasEdit()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo date format if empty.
    * @todo add a cancel button.
    * @todo data validation before submit.
    */
    protected function areasEdit() {
        $qry = "select * from #wabCountry order by wabCountryOrder,wabCountryName";
        $this->sql->gen($qry, false);
        while ($row = $this->sql->fetch()) {
            $optionsArray[$row['wabCountryCode']] = $row['wabCountryName'];
        }
        $qry = "select * from #wabAreas where wabAreaID={$this->current['id']}";
        $this->sql->gen($qry, false);
        $areasRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabareaedit', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'areas');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
			<tr>
				<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_AREAS03 . " *</td>
				<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 4;
        $callOptions['title'] = 'WAB Area';
        $callOptions['required'] = true;
        $text .= $this->frm->text('wabSquare', $areasRec['wabSquare'], 4, $callOptions);
        $text .= "</td>
		</tr>
		<tr>
			<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_AREAS04 . " *</td>
			<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = 'Area country';
        $text .= $this->frm->select('wabAreaCountryFK', $optionsArray, $areasRec['wabAreaCountryFK'], $callOptions);
        $text .= "</td>
			</tr>
		<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_AREAS05 . "</td>
			<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['label'] = 'Yes - No';
        $callOptions['title'] = LAN_WAB_ADMIN_AREAS06;
        $text .= $this->frm->checkbox('wabCoastal', '1', $areasRec['wabCoastal'], $callOptions);
        $text .= "
			</td>
		</tr>
	</table>
		<div class='buttons-bar center'>" . $this->frm->admin_button('save_areas', LAN_SAVE, 'update') . "</div>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/square16.png' alt='Areas' /> " . LAN_WAB_ADMIN_AREAS17;
        $this->ns->tablerender($title, $text);
    }
    /**
    * wabAdmin::areasList()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo add an add new area button.
    */
    protected function areasList() {
        $direction[] = array();
        $action = "&amp;wabAction=areas&amp;wabSubAction=list&amp;wabID=0&amp;wabFrom=0";
        switch ($this->current['column']) {
            case 'areaid':
                $orderby = 'wabareaID ';
                $direction[1] = $this->current['direction'];
                break;
            case 'country':
                $orderby = 'wabCountryName ';
                $direction[2] = $this->current['direction'];;
                break;
            case 'coast':
                $orderby = 'wabCoastal ';
                $direction[3] = $this->current['direction'];;
                break;
            default:
            case 'area':
                $orderby = 'wabSquare ';
                $direction[0] = $this->current['direction'];
        } // switch
        $orderby .= $this->current['direction'];

        $text = $this->frm->open('wabarea', 'post');
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr >
			<td colspan='2' style='text-align:left;' >" . $this->frm->search('arealook', '', 'filt') . " </td>
			<td colspan='3' style='text-align:right;' >" . $this->iconAdd() . " </td>
		</tr>
			<tr>
				<th style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_AREAS02 . "<br />" . $this->iconSort($action, 'areaid', $direction[1]) . "</td>
				<th style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_AREAS03 . "<br />" . $this->iconSort($action, 'area', $direction[0]) . "</td>
				<th style='width:10%;text-align:center;'>" . LAN_WAB_ADMIN_AREAS04 . "<br />" . $this->iconSort($action, 'country', $direction[2]) . "</td>
				<th style='width:10%;text-align:center;'>" . LAN_WAB_ADMIN_AREAS07 . "<br />" . $this->iconSort($action, 'coast', $direction[3]) . "</td>
				<th style='width:20%;text-align:right;'>" . LAN_WAB_ADMIN_AREAS08 . "</td>
			</tr>";

        $query = "select a.*,c.wabCountryCode,c.wabCountryName from #wabareas as a left join #wabcountry as c on a.wabAreaCountryFK = c.wabCountryCode order by {$orderby} limit {$this->current['from']},{$this->maxlogs}";
        if ($this->sql->gen($query, false)) {
            $areasRec = $this->sql->db_getList();
            foreach($areasRec as $areasRow) {
                $action = "wabAction=areas&amp;wabID={$areasRow['wabareaID']}&amp;wabSubAction=";
                $text .= "
			<tr>
				<td style='text-align:right;vertical-align:top;'>{$areasRow['wabareaID']}</td>
				<td style='text-align:left;vertical-align:top;'>{$areasRow['wabSquare']}</td>
				<td style='text-align:center;vertical-align:top;'>" . $this->iconFlag($areasRow['wabCountryCode'], $areasRow['wabCountryName']) . "</td>
				<td style='text-align:center;vertical-align:top;'>" . ($areasRow['wabCoastal']?ADMIN_TRUE_ICON:'') . "</td>
				<td style='vertical-align:top;text-align:right;'>
					<a href='" . e_SELF . "?{$action}edit' alt='Edit' title='Edit'>" . ADMIN_EDIT_ICON . "</a>
					<a href='" . e_SELF . "?{$action}delete' alt='Delete' title='Delete'>" . ADMIN_DELETE_ICON . "</a>
				</td>
			</tr>";
            }
            $text .= "
			<tr>
				<td colspan='5' style='text-align:left'>";
            $num_entry = $this->sql->count('wabAreas'); //number of records in areas table
            $action = "wabAction=areas&amp;wabSubAction=list&amp;wabID=0";
            $text .= $this->nextPrev($action, $num_entry);
            $text .= "
				</td>
			</tr>";
        } else {
            $text .= "<tr><td colspan='4' style='text-align:center'>" . LAN_WAB_GEN01 . "</td></tr>";
        }

        $text .= "
		</table>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/square16.png' alt='Areas' /> " . LAN_WAB_ADMIN_AREAS01;
        $this->ns->tablerender($title, $this->mes->render() . $text);
    }
    /**
    * wabAdmin::areasSave()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper save with on duplicate update.
    * @todo date conversions .
    */
    protected function areasSave() {
        unset($update);
        $update = array(
            'wabSquare' => $this->tp->toDb(strtoupper ($_POST['wabSquare'])),
            'wabAreaCountryFK' => $this->tp->toDb($_POST['wabAreaCountryFK']),
            'wabCoastal' => $_POST['wabCoastal'],
            'wabAreaLastUpdate' => time(),
            'wanAreaUpdater' => USERID . '.' . USERNAME,
            'WHERE' => 'wabareaID = ' . (int)$this->current['id']); // where must be in caps
        if ($this->sql->update('wabareas', $update, false)) {
            $this->mes->addSuccess(LAN_UPDATED);
        } else {
            $this->mes->addError(LAN_UPDATED_FAILED);
        }
        $this->current['action'] = 'areas';
        $this->current['subAction'] = 'list';
        $this->setSession();
    }
    /*
	   Islands methods
	*/
    /**
    * wabAdmin::islandsConfirmed()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper deletions
    */
    protected function islandsConfirmed() {
        if (isset($_POST['islandsDelete'])) {
            // get rid of stuff
            if ($this->sql->update('wabcountry', $update)) {
                // do all the necessary deletions
                $this->mes->addSuccess(LAN_DELETED);
            } else {
                $this->mes->addError(LAN_DELETED_FAILED);
            }
        } else {
            // deletion was cancelled
            $this->mes->addInfo(LAN_WAB_ADMIN_BOOKS18);
        }
        // either way we now list islands.
        $this->current['action'] = 'islands';
        $this->current['subAction'] = 'list';
    }

    /**
    * wabAdmin::islandsDelete()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function islandsDelete() {
        $qry = "select * from #wabIslandList where wabIslandNo={$this->current['id']}";
        $this->sql->gen($qry, false);
        $islandsRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabislandsdelete', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'islands');
        $text .= $this->frm->hidden('wabSubAction', 'confirm');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
			<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
				<tr>
					<td style='vertical-align:top;width:25%;'>" . LAN_WAB_ADMIN_BOOKS10 . " <b>{$islandsRec['wabIslandNo']}</b></td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS11 . " <b>{$islandsRec['wabIslandSeries']}</b></td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS12 . " <b>{$islandsRec['wabIslandDate']}</b></td>
				</tr>
				<tr>
				<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS13 . " <b>{$islandsRec['wabIslandHolder']}</b></td>
				</tr>

				<tr>
					<td colspan='5' style='text-align:center'>" . $this->frm->admin_button('islandsDelete', LAN_DELETE, 'delete') . " &nbsp;&nbsp;&nbsp;" . $this->frm->admin_button('islandsCancel', LAN_CANCEL, 'cancel') . "</td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:left'></td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_BOOKS16, $text);
    }
    /**
    * wabAdmin::islandsEdit()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo date format if empty.
    * @todo add a cancel button.
    * @todo data validation before submit.
    */
    protected function islandsEdit() {
        $qry = "select * from #wabIslandList where wabIslandNo={$this->current['id']}";
        $this->sql->gen($qry, false);
        $islandsRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabcountryedit', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'islands');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
			<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
				<tr >
					<td colspan='2'  >Add </td>
				</tr>
				<tr>
					<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_BOOKS10 . " *</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 6;
        $callOptions['min'] = 1;
        $callOptions['max'] = 30000;
        $callOptions['title'] = 'Island number';
        $callOptions['required'] = true;
        $text .= $this->frm->number('wabIslandNo', $islandsRec['wabIslandNo'], 6, $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS11 . "</td>
					<td style='vertical-align:top;'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 4;
        $callOptions['min'] = 1;
        $callOptions['max'] = 9;
        $callOptions['title'] = 'Island Series';
        $text .= $this->frm->number('wabIslandSeries', $islandsRec['wabIslandSeries'], 4, $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS12 . "</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['type'] = 'date';
        $callOptions['title'] = "Date the island was issued";
        $text .= $this->frm->datepicker('wabIslandDate', $islandsRec['wabIslandDate'], 6, $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS13 . "</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 10;
        $callOptions['title'] = 'Island holders name or callsign';
        $text .= $this->frm->text('wabIslandHolder', $islandsRec['wabIslandHolder'], 6, $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS14 . "</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['label'] = 'Reissued';
        $callOptions['title'] = 'Check if the island is a reissue';
        $text .= $this->frm->checkbox('wabIslandReissue', 1, ($islandsRec['wabIslandReissue'] == 1?true:false), $callOptions);
        $this->frm->text('wabIslandReissue', $islandsRec['wabIslandReissue'], 6, $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_BOOKS15 . "</td>
					<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['label'] = "NLV";
        $callOptions['title'] = 'Check if the island is no longer valid';
        $text .= $this->frm->checkbox('wabIslandNLV', 1, ($islandsRec['wabIslandNLV'] == 1?true:false), $callOptions);
        $text .= "</td>
				</tr>
				<tr>
					<td colspan='5' style='text-align:center'>" . $this->frm->admin_button('save_islands', LAN_SAVE, 'update') . " </td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:left'>&nbsp;</td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_BOOKS17, $text);
    }
    /**
    * wabAdmin::islandsList()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo add an add new island button.
    */
    protected function islandsList() {
        $maxlogs = $this->prefs->getPref('wab_adminpp', 30);
        // die("W");
        $text = $this->frm->open('wabcountrylist', 'post');
        $text .= "
	<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr >
			<td colspan='4'  >Add </td>
		</tr>
		<tr>
			<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_BOOKS02 . "</td>
					<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_BOOKS03 . "</td>
					<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_BOOKS04 . "</td>
					<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_BOOKS05 . "</td>
					<td style='width:10%;text-align:center;'>" . LAN_WAB_ADMIN_BOOKS06 . "</td>
					<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_BOOKS07 . "</td>
					<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_BOOKS08 . "</td>
				</tr>
				";
        $query = "select * from #wabislandlist as b left join #wabUser as u on u.wabUserCallsign like '%b.wabIslandHolder%' order by wabIslandNo limit {$this->current['from']},{$maxlogs}";
        if ($this->sql->gen($query, false)) {
            $islandsRec = $this->sql->db_getList();
            foreach($islandsRec as $islandsRow) {
                $text .= "
						<tr>
							<td style='text-align:right;vertical-align:top;'>{$islandsRow['wabIslandNo']}</td>
							<td style='text-align:left;vertical-align:top;'>{$islandsRow['wabIslandHolder']}</td>
							<td style='text-align:right;vertical-align:top;'>{$islandsRow['wabIslandSeries']}</td>
							<td style='text-align:left;vertical-align:top;'>" . (empty($islandsRow['wabIslandDate'])?$islandsRow['wabIslandDate']:'&nbsp;') . "</td>
							<td style='text-align:center;vertical-align:top;'>" . ($islandsRow['wabIslandReissue']?ADMIN_TRUE_ICON:'') . "</td>
							<td style='text-align:left;vertical-align:top;'>{$islandsRow['wabUserHomeArea']}</td>

							<td style='vertical-align:top;text-align:right;'>
							<a href='" . e_SELF . "?islands.edit.{$islandsRow['wabIslandNo']}.{$this->current['from']}' alt='Edit' title='Edit'>" . ADMIN_EDIT_ICON . "</a>
							<a href='" . e_SELF . "?islands.delete.{$islandsRow['wabIslandNo']}.{$this->current['from']}' alt='Delete' title='Delete'>" . ADMIN_DELETE_ICON . "</a>
							</td>
						</tr>
						";
            }
            $text .= "
					<tr>
						<td colspan='7' style='text-align:left'>";
            $num_entry = $this->sql->count('wabIslandList'); //number of records in country table
            $action = 'islands.list.0';
            $parms = "{$num_entry},{$maxlogs},{$this->current['from']}," . e_SELF . "?" . $action . ".[FROM]";
            $text .= "<div class='nextprev-bar'>&nbsp;" . $this->tp->parseTemplate("{NEXTPREV={$parms}}") . "&nbsp;</div>
						</td>
					</tr>";
        } else {
            $text .= "<tr><td colspan='7' style='text-align:center'>" . LAN_WAB_GEN01 . "</td>";
        }

        $text .= "
					</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_BOOKS01, $this->mes->render() . $text);
    }
    /**
    * wabAdmin::islandsSave()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper save with on duplicate update.
    * @todo date conversions .
    */
    protected function islandsSave() {
        unset($update);
        $update = array(
            'wabIslandNo' => (int)$_POST['wabIslandNo'],
            'wabIslandSeries' => (int)$_POST['wabIslandSeries'],
            'wabIslandDate' => $this->tp->toDb(strtoupper ($_POST['wabIslandDate'])),
            'wabIslandHolder' => $this->tp->toDb($_POST['wabIslandHolder']),
            'wabIslandHolderFK' => (int)$_POST['wabIslandHolderFK'],
            'wabIslandReissue' => (int)$_POST['wabIslandReissue'],
            'wabIslandNLV' => (int)$_POST['wabIslandNLV'],
            'wabIslandLastUpdate' => time(),
            'wabIslandUpdater' => USERID . '.' . USERNAME,
            'WHERE' => 'wabIslandNo = ' . (int)$_POST['wabIslandNo']); // where must be in caps
        if ($this->sql->update('wabislandlist', $update, false)) {
            $this->mes->addSuccess(LAN_UPDATED);
        } else {
            $this->mes->addError(LAN_UPDATED_FAILED);
        }
        $this->current['action'] = 'islands';
        $this->current['subAction'] = 'list';
    }

    /*
	   Counties methods
	*/
    /**
    * wabAdmin::countiesConfirmed()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper deletions
    */
    protected function countiesConfirmed() {
        if (isset($_POST['countiesDelete'])) {
            // get rid of stuff
            if ($this->sql->update('wabcountry', $update)) {
                // do all the necessary deletions
                $this->mes->addSuccess(LAN_DELETED);
            } else {
                $this->mes->addError(LAN_DELETED_FAILED);
            }
        } else {
            // deletion was cancelled
            $this->mes->addInfo(LAN_WAB_ADMIN_COUNTY18);
        }
        // either way we now list counties.
        $this->current['action'] = 'counties';
        $this->current['subAction'] = 'list';
    }

    /**
    * wabAdmin::countiesDelete()
    *
    * @version 1.0.1
    * @since 1.0.1
    */
    protected function countiesDelete() {
        $qry = "select * from #wabCounty where wabCountyID={$this->current['id']}";
        $this->sql->gen($qry, false);
        $countiesRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabcountiesdelete', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'counties');
        $text .= $this->frm->hidden('wabSubAction', 'confirm');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= $this->frm->hidden('wabFrom', $this->current['from']);
        $text .= '</div>';
        $text .= "
			<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
				<tr>
					<td style='vertical-align:top;width:25%;'>" . LAN_WAB_ADMIN_COUNTY03 . " <b>{$countiesRec['wabCountyName']}</b></td>
				</tr>
				<tr>
					<td  style='text-align:center'>" . $this->frm->admin_button('countiesDelete', LAN_DELETE, 'delete') . " &nbsp;&nbsp;&nbsp;" . $this->frm->admin_button('countiesCancel', LAN_CANCEL, 'cancel') . "</td>
		</tr>
		<tr>
			<td style='text-align:left'></td>
		</tr>
	</table>";
        $text .= $this->frm->close();
        $this->ns->tablerender(LAN_WAB_ADMIN_COUNTY16, $text);
    }
    /**
    * wabAdmin::countiesEdit()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo date format if empty.
    * @todo add a cancel button.
    * @todo data validation before submit.
    */
    protected function countiesEdit() {
        $qry = "select * from #wabCountry order by wabCountryOrder,wabCountryName";
        $this->sql->gen($qry, false);
        while ($row = $this->sql->fetch()) {
            $optionsArray[$row['wabCountryCode']] = $row['wabCountryName'];
        }
        $qry = "select * from #wabCounty where wabCountyID={$this->current['id']}";
        $this->sql->gen($qry, false);
        $countiesRec = $this->sql->db_Fetch();

        $text = $this->frm->open('wabcountryedit', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'counties');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= '</div>';
        $text .= "
	<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";' >
		<tr>
			<td style='vertical-align:top;width:20%;'>" . LAN_WAB_ADMIN_AREAS03 . " *</td>
			<td style='vertical-align:top;' >";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['size'] = 50;
        $callOptions['title'] = 'County Name';
        $callOptions['required'] = true;
        $text .= $this->frm->text('wabCountyName', $countiesRec['wabCountyName'], 50, $callOptions);
        $text .= "</td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>" . LAN_WAB_ADMIN_COUNTY04 . "</td>
			<td style='vertical-align:top;' colspan='4'>";
        unset($callOptions);
        $callOptions['class'] = 'tbox';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = 'Country';
        $text .= $this->frm->select('wabCountyfk', $optionsArray, $countiesRec['wabCountyfk'], $callOptions);
        $text .= "</td>
		</tr>
	</table>
	<div class='buttons-bar center'>" . $this->frm->admin_button('save_counties', LAN_SAVE, 'update') . "</div>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/uk16.png' alt='Counties' /> " . LAN_WAB_ADMIN_COUNTY17;
        $this->ns->tablerender($title, $text);
    }
    /**
    * wabAdmin::countiesList()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo add an add new book button.
    */
    protected function countiesList() {
        $direction[] = array();
        switch ($this->current['column']) {
            case 'cname':
                $orderby = 'wabCountyName ';
                $direction[1] = $this->current['direction'];
                break;
            case 'ccountry':
                $orderby = 'wabCountryName ';
                $direction[2] = $this->current['direction'];;
                break;
            default:
            case 'ccode':
                $orderby = 'wabCountyID ';
                $direction[0] = $this->current['direction'];
        } // switch
        $orderby .= $this->current['direction'];
        $action = "&wabAction=counties&wabSubAction=list&wabID=0&wabFrom=0";
        $text = $this->frm->open('wabcountieslist', 'post');
        $text .= '<div>';
        $text .= $this->frm->hidden('wabAction', 'counties');
        $text .= $this->frm->hidden('wabSubAction', 'save');
        $text .= $this->frm->hidden('wabId', $this->current['id']);
        $text .= '</div>';
        $text .= "
		<table class='table adminlist' style='width:" . ADMIN_WIDTH . ";'>
		<tr >
			<th colspan='2' style='text-align:left;' >" . $this->frm->search('countylook', '', 'filt') . " </td>
			<th colspan='2' style='text-align:right;' >" . $this->iconAdd() . " </td>
		</tr>
				<tr>
					<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_COUNTY02 . "<br />" . $this->iconSort($action, 'ccode', $direction[0]) . "</td>
					<td style='width:20%;text-align:left;'>" . LAN_WAB_ADMIN_COUNTY03 . "<br />" . $this->iconSort($action, 'cname', $direction[1]) . "</td>
					<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_COUNTY04 . "<br />" . $this->iconSort($action, 'ccountry', $direction[2]) . "</td>
					<td style='width:10%;text-align:right;'>" . LAN_WAB_ADMIN_COUNTY08 . "</td>
				</tr>
				";
        $query = "select * from #wabcounty as b left join #wabcountry as c on b.wabCountyfk = c.wabCountryCode order by {$orderby} limit {$this->current['from']},{$this->maxlogs}";
        if ($this->sql->gen($query, false)) {
            $countiesRec = $this->sql->db_getList();

            foreach($countiesRec as $countiesRow) {
                $action = "wabAction=counties&wabID={$countiesRow['wabCountyID']}&wabSubAction=";
                $text .= "
						<tr>
							<td style='text-align:right;vertical-align:top;'>{$countiesRow['wabCountyID']}</td>
							<td style='text-align:left;vertical-align:top;'>{$countiesRow['wabCountyName']}</td>
							<td style='text-align:right;vertical-align:top;'>" . $this->iconFlag($countiesRow['wabCountryCode'], $countiesRow['wabCountryName']) . "</td>
							<td style='vertical-align:top;text-align:right;' class='S16'>
							<a href='" . e_SELF . "?{$action}edit' alt='Edit' title='Edit'>" . ADMIN_EDIT_ICON . "</a>
							<a href='" . e_SELF . "?{$action}delete' alt='Delete' title='Delete'>" . ADMIN_DELETE_ICON . "</a>
							</td>
						</tr>
						";
            }
            $text .= "
					<tr>
						<td colspan='4' style='text-align:left'>";
            $num_entry = $this->sql->count('wabCounty'); //number of records in country table
            $action = "wabAction=counties&wabSubAction=list&wabID=0";
            $text .= $this->nextPrev($action, $num_entry);

            $text .= "
						</td>
					</tr>";
        } else {
            $text .= "<tr><td colspan='4' style='text-align:center'>" . LAN_WAB_GEN01 . "</td>";
        }

        $text .= "
					</table>";
        $text .= $this->frm->close();
        $title = "<img src='images/menu/uk16.png' alt='Counties' /> " . LAN_WAB_ADMIN_COUNTY01;
        $this->ns->tablerender($title, $this->mes->render() . $text);
    }
    /**
    * wabAdmin::countiesSave()
    *
    * @version 1.0.1
    * @since 1.0.1
    * @todo proper save with on duplicate update.
    * @todo date conversions .
    */
    protected function countiesSave() {
        unset($update);
        $update = array(
            'wabCountyName' => $this->tp->toDb(ucwords(strtolower($_POST['wabCountyName']))),
            'wabCountyfk' => $this->tp->toDb($_POST['wabCountyfk']),
            'wabCountyLastUpdate' => time(),
            'wabCountyUpdater' => USERID . '.' . USERNAME,
            'WHERE' => 'wabCountyID = ' . (int)$this->current['id']); // where must be in caps
        if ($this->sql->update('wabcounty', $update, false)) {
            $this->mes->addSuccess(LAN_UPDATED);
        } else {
            $this->mes->addError(LAN_UPDATED_FAILED);
        }
        $this->current['action'] = 'counties';
        $this->current['subAction'] = 'list';
        $this->setSession();
    }
}

class wabAjax extends wab {
    function __construct() {
        parent::__construct();
        // $this->processAjax();
    }
    function getFilterNumrecs($filterEle) {
        $where = $this->buildFilter($filterEle);
        $order = $this->buildOrder($filterEle);
        $limit = $this->buildLimit($filterEle);
        $qry = "SELECT count(*) as numrecs from #wablog
    	left join #wabmodes on wabLogModefk = wabModesID
    	left join #wabbands on wabLogBandfk = wabBandsID
    	left join #wabareas on wabLogAreaWorkedFK = wabareaID
    	left join #wabcountry on wabLogCountryfk = wabCountryCode
    	left join #wabloglist on wabLogMyLog = wabLogListID
    	left join #wabislands on wabIslandAreaFK = wabareaID
			{$where} {$order} {$limit}
    	";
        $this->sql->gen($qry, false);
        $row = $this->sql->fetch();
        return $row['numrecs'];
    }
    function processAjax() {
        // which logbook is it going in
        $insertLogRecord['wablogmylog'] = $_GET["wabquickmylog"];
        // make timestamp
        $datein = str_replace('/', '-', $_GET["wabquickstart"]);
        $date = strtotime($datein);

        $insertLogRecord['wabLogStartDate'] = $date;
        $insertLogRecord['wabLogCallsign'] = $_GET["wabquickcall"];
        // *
        // * look up callsign in table wabuser
        // *
        // *
        // *
        // *
        $insertLogRecord['wabLogFreq'] = $_GET["wabquickfreq"];
        $insertLogRecord['wabLogRSIn'] = $_GET["wabquickrrs"];
        $insertLogRecord['wabLogRSOut'] = $_GET["wabquicksrs"];
        $insertLogRecord['wabLogModefk'] = $_GET["wabquickmode"];
        // *
        // * get the fk for the area
        // *
        $insertLogRecord['wabLogAreaWorkedFK'] = $this->getAreaKey($_GET["wabquickwab"]);
        // *
        // * get the fk for the areaoperating from
        // *
        $insertLogRecord['wabLogAreaOpFromFK'] = $this->getAreaKey($_GET["wabquickmine"]);
        $tmp = $this->logInsert($insertLogRecord);
        echo JSON_encode($tmp);
    }
}

?>
