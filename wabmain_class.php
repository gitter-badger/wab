<?php
if (!defined('e107_INIT')) {
    exit;
}

/**
* wabMain
*
* @package
* @author Barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class wabMain extends wab {
    function __construct() {
        parent::__construct();
        $this->maxlogs = $this->prefs->getPref('wab_userpp', 20);

        $this->processMain();
        // print_a($this);
    }
    protected function processMain() {
        // Next Section runs through the possible actions and checks the sub action
        // the subAction is the things Order is important because some things
        // will follow on from one action to another.
        // thats why it is if then not switch or if then else.
        // print_a($this);
        switch ($this->current['action']) {
            case 'export':
                $this->processExport($this->subAction);
                break;
            case 'import':
                $this->processImport($this->subAction);
                break;
            case 'books':
                $this->processBooks($this->subAction);
                break;
            case 'islands':
                $this->processIslands($this->subAction);
                break;
            case 'settings':
                $this->processSettings($this->subAction);
                break;
            case 'users':
                $this->processUsers($this->subAction);
                break;
            case 'coastal':
                $this->processCoastal($this->subAction);
                break;
            case 'counties':
                $this->processCounties($this->subAction);
                break;
            case 'countries':
                $this->processCountries($this->subAction);
                break;
            case 'userlogs':
                $this->processLogList($this->subAction, false);
                break;
            default:
            case 'mylogs':
                $this->processLogList($this->subAction, true);
                break;
        }
    } // end processMain
    protected function processImport($subAction = '') {
        switch ($subAction) {
            case'save':
                break;
            case 'get':
            default:
                $this->importAdi();
                break;
        }
    }
    protected function processExport($subAction = '') {
        $this->exportAdi();
    }
    protected function processBooks($subAction = '') {
        switch ($subAction) {
            default:
            case 'list':
                $this->booksList();
                break;
        }
    }
    protected function processIslands($subAction = '') {
        switch ($subAction) {
            default:
            case 'list':
                $this->islandsList();
                break;
        }
    }
    protected function processSettings($subAction = '') {
        if (isset($_POST['wabLogSettingscreate'])) {
            $subAction = 'edit';
            $this->settingsCreate();
        }
        if (isset($_POST['wabLogSettingsSave'])) {
            $subAction = 'edit';
            $this->settingsUpdate();
        }

        switch ($subAction) {
            case'none':

                break;
            case 'edit':
            default:
                $this->settingsMain();
                break;
        }
    }
    protected function processUsers($subAction = '') {
        switch ($subAction) {
            default:
            case 'list':
                $this->userList();
                break;
        }
    }
    protected function processCoastal($subAction = '') {
        switch ($subAction) {
            default:
            case 'list':
                $this->coastalList();
                break;
        }
    }
    protected function processCounties($subAction = '') {
        switch ($subAction) {
            default:
            case 'list':
                $this->countiesList();
                break;
        }
    }
    protected function processCountries($subAction = '') {
        switch ($subAction) {
            default:
            case 'list':
                $this->countriesList();
                break;
        }
    }
    protected function processLogList($subAction = '', $mine = true) {
        switch ($subAction) {
            default:
            case 'list':
                $this->logsList($mine);
                break;
        }
    }
    /**
    * wabMain::userLogsList()
    *
    * @return
    */
    protected function logView() {
    }

    /**
    * wabMain::LogsList()
    *
    * @param mixed $mine
    * @return
    * @todo use filter and search to where clause
    */
    protected function logsList($mine = false) {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        /*
		   * Create drop down with all registered log users or set to user ID if my logs
		*/
        if ($mine) {
            // just using the logged in users log books
            $this->current['wabUserCallID'] = USERID;
            $userSel .= $this->frm->hidden('wabUserCallID', $this->current['wabUserCallID']);
            $wabusertitle = "<img src='images/menu/logbook24.png' alt='Users' /> My Log Books" ;
        } else {
            $wabusertitle = "<img src='images/menu/logbookusers24.png' alt='Users' /> Users Log Books" ;
            // get all the users who have one or more logbooks
            $listTitle = LAN_WAB_USER20;
            $userArray[0] = "--- Select User ---";
            unset($callOptions);
            $callOptions['class'] = 'tbox form-control';
            $callOptions['useValues'] = false;
            $callOptions['multiple'] = false;
            $callOptions['title'] = 'Users Callsign';
            $qry = "select wabUserID,wabUserCallsign,wabLogListID from #wabuser
    	left join #wabloglist on wabUserID=wabLogListWabUserfk
    	where wabLogListID>0
    	order by wabUserCallsign";
            $this->sql->gen($qry, false);
            while ($row = $this->sql->fetch()) {
                $userArray[$row['wabUserID']] = $row['wabUserCallsign'];
            } // end while
            $userSel = $this->frm->select('wabUserCallID', $userArray, $this->current['wabUserCallID'], $callOptions);
        } // end if $mine
        if ($this->current['wabUserCallID'] == 0) {
            $logsArray[0] = "--- Select User first ---";
        } else {
            $logsArray[0] = "--- Select Logbook ---";
            $qry = "select wabLogListID,wabLogTitle from #wabloglist
    	where wabLogListWabUserfk={$this->current['wabUserCallID']}
    	order by wabLogTitle";
            $this->sql->gen($qry, false);
            while ($row = $this->sql->fetch()) {
                $logsArray[$row['wabLogListID']] = $row['wabLogTitle'];
            }
        } // end $this->wabUserCallID == 0
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $callOptions['useValues'] = false;
        $callOptions['multiple'] = false;
        $callOptions['title'] = 'Active Logbook';
        $selLog = $this->frm->select('wabUserlogID', $logsArray, $this->current['wabUserlogID'], $callOptions);
        unset($callOptions);
        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }

        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabUserlogSelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter by Mode');
        $selectOptions .= $this->frm->option('FM', 'fm', $this->current['selection'] == 'fm');
        $selectOptions .= $this->frm->option('SSB', 'ssb', $this->current['selection'] == 'ssb');
        $selectOptions .= $this->frm->option('CW', 'cw', $this->current['selection'] == 'cw');
        $selectOptions .= $this->frm->option('RTTY', 'rtty', $this->current['selection'] == 'rtty');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter by WAB');
        $selectOptions .= $this->frm->option('WAB area only', 'wab', $this->current['selection'] == 'wab');
        $selectOptions .= $this->frm->option('Non WAB only', 'nowab', $this->current['selection'] == 'nowab');

        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter by Band');
        $selectOptions .= $this->frm->option('160&nbsp;m', '160', $this->current['selection'] == '160');
        $selectOptions .= $this->frm->option('&nbsp;80&nbsp;m', '80', $this->current['selection'] == '80');
        $selectOptions .= $this->frm->option('&nbsp;40&nbsp;m', '40', $this->current['selection'] == '40');
        $selectOptions .= $this->frm->option('&nbsp;30&nbsp;m', '30', $this->current['selection'] == '30');
        $selectOptions .= $this->frm->option('&nbsp;20&nbsp;m', '20', $this->current['selection'] == '20');
        $selectOptions .= $this->frm->option('&nbsp;17&nbsp;m', '17', $this->current['selection'] == '17');
        $selectOptions .= $this->frm->option('&nbsp;12&nbsp;m', '12', $this->current['selection'] == '12');
        $selectOptions .= $this->frm->option('&nbsp;10&nbsp;m', '10', $this->current['selection'] == '10');
        $selectOptions .= $this->frm->option('&nbsp;&nbsp;6&nbsp;&nbsp;m', ' 6', $this->current['selection'] == '6');
        $selectOptions .= $this->frm->option('&nbsp;&nbsp;4&nbsp;&nbsp;m', ' 4', $this->current['selection'] == '4');
        $selectOptions .= $this->frm->option('&nbsp;70&nbsp;cm', '70', $this->current['selection'] == '70');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter only');
        $selectOptions .= $this->frm->option('Coastal', 'coast', $this->current['selection'] == 'coast');
        $selectOptions .= $this->frm->option('Islands', 'island', $this->current['selection'] == 'island');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter on QSL');
        $selectOptions .= $this->frm->option('Sent', 'sent', $this->current['selection'] == 'sent');
        $selectOptions .= $this->frm->option('Received', 'received', $this->current['selection'] == 'received');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter on Matched');
        $selectOptions .= $this->frm->option('Matched', 'match', $this->current['selection'] == 'match');
        $selectOptions .= $this->frm->option('Not Matched', 'nomatch', $this->current['selection'] == 'nomatch');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabListFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabListHeader($this), false, $this->sc);
        // *
        // * display the logs if any
        // *
        if ($this->current['wabUserCallID'] > 0 && $this->current['wabUserlogID'] > 0) {
            $qry = "select * from #wablog
        left join #wabareas on wabLogAreaWorkedFK=wabareaID
        left join #wabbands on wabLogBandfk=wabBandsID
        left join #wabmodes on wabLogModefk=wabModesID
        left join #wabcountry on wabAreaCountryFK=wabCountryCode
        order by wabLogStartDate";
            if ($this->sql->gen($qry, false)) {
                while ($this->sc->dataRow = $this->sql->fetch()) {
                    $text .= $this->tp->parseTemplate($this->template->wabListDetail($this), false, $this->sc);
                }
            } else {
                $text .= $this->tp->parseTemplate($this->template->wabListNoDetail(), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabListNoDetail(), false, $this->sc);
        }
        $text .= $this->tp->parseTemplate($this->template->wabListFooter(), false, $this->sc);
        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }
    protected function booksList() {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }
        unset($callOptions);
        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabUserlogSelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter Series');
        $selectOptions .= $this->frm->option('Series 1', 'series1', $this->current['selection'] == 'series1');
        $selectOptions .= $this->frm->option('Series 2', 'series2', $this->current['selection'] == 'series2');
        $selectOptions .= $this->frm->option('Series 3', 'series3', $this->current['selection'] == 'series3');
        $selectOptions .= $this->frm->option('Series 4', 'series4', $this->current['selection'] == 'series4');
        $selectOptions .= $this->frm->option('Series 5', 'series5', $this->current['selection'] == 'series5');
        $selectOptions .= $this->frm->option('Series 6', 'series6', $this->current['selection'] == 'series6');

        $selectOptions .= $this->frm->optgroup_close();

        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabBooksFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabBooksHeader($this), false, $this->sc);
        // *
        // * display the books if any
        // *
        $qry = "select * from #wabbooklist
        left join #wabbookcalls on wabBookHolderFK=wabBookCallsID
        order by wabBookNo asc
        limit 0,25";
        if ($this->sql->gen($qry, false)) {
            while ($this->sc->dataRow = $this->sql->fetch()) {
                $text .= $this->tp->parseTemplate($this->template->wabBooksDetail(), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabBooksNoDetail(), false, $this->sc);
        }

        $text .= $this->tp->parseTemplate($this->template->wabBooksFooter(), false, $this->sc);

        $this->sc->dataRow['perpagefilter'] .= $this->frm->open('wabPerPageForm', 'post', e_SELF);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_open('wabPerPage');
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('10', '10', false, $this->current['wabPerPage'] == 10);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('25', '25', false, $this->current['wabPerPage'] == 25);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('50', '50', false, $this->current['wabPerPage'] == 50);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('100', '100', false, $this->current['wabPerPage'] == 100);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('250', '250', false, $this->current['wabPerPage'] == 250);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('500', '500', false, $this->current['wabPerPage'] == 500);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('All', 'all', false, $this->current['wabPerPage'] == 0);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_close();
        $this->sc->dataRow['perpagefilter'] .= $this->frm->close();

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/book24.png' alt='Users' /> Book List";
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }
    protected function countiesList() {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }
        unset($callOptions);
        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabUserlogSelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter by Country');
        $selectOptions .= $this->frm->option('England', 'en', $this->current['selection'] == 'en');
        $selectOptions .= $this->frm->option('Scotland', 'sw', $this->current['selection'] == 'sw');
        $selectOptions .= $this->frm->option('Wales', 'wa', $this->current['selection'] == 'wa');
        $selectOptions .= $this->frm->option('N Ireland', 'nd', $this->current['selection'] == 'nd');

        $selectOptions .= $this->frm->optgroup_close();

        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabCountiesFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabCountiesHeader($this), false, $this->sc);
        // *
        // * display the books if any
        // *
        $qry = "select * from #wabcounty
        left join #wabcountry on wabCountryfk=wabCountryCode
        order by wabCountyName asc
        limit 0,25";
        if ($this->sql->gen($qry, false)) {
            while ($this->sc->dataRow = $this->sql->fetch()) {
                $text .= $this->tp->parseTemplate($this->template->wabCountiesDetail(), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabCountiesNoDetail(), false, $this->sc);
        }

        $text .= $this->tp->parseTemplate($this->template->wabCountiesFooter(), false, $this->sc);

        $this->sc->dataRow['perpagefilter'] .= $this->frm->open('wabPerPageForm', 'post', e_SELF);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_open('wabPerPage');
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('10', '10', false, $this->current['wabPerPage'] == 10);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('25', '25', false, $this->current['wabPerPage'] == 25);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('50', '50', false, $this->current['wabPerPage'] == 50);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('100', '100', false, $this->current['wabPerPage'] == 100);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('250', '250', false, $this->current['wabPerPage'] == 250);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('500', '500', false, $this->current['wabPerPage'] == 500);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('All', 'all', false, $this->current['wabPerPage'] == 0);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_close();
        $this->sc->dataRow['perpagefilter'] .= $this->frm->close();

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/uk24.png' alt='Users' /> Counties" ;
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }

    protected function countriesList() {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }
        unset($callOptions);
        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabCountrySelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter Series');
        $selectOptions .= $this->frm->option('UK Only', '0', $this->current['selection'] == '0');
        $selectOptions .= $this->frm->option('Rest of the World', '1', $this->current['selection'] == '1');
        $selectOptions .= $this->frm->optgroup_close();

        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabCountriesFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabCountriesHeader($this), false, $this->sc);
        // *
        // * display the books if any
        // *
        $qry = "select * from #wabcountry
        order by wabCountryOrder,wabCountryName asc
        limit 0,25";
        if ($this->sql->gen($qry, false)) {
            while ($this->sc->dataRow = $this->sql->fetch()) {
                $text .= $this->tp->parseTemplate($this->template->wabCountriesDetail(), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabCountriesNoDetail(), false, $this->sc);
        }

        $text .= $this->tp->parseTemplate($this->template->wabCountriesFooter(), false, $this->sc);

        $this->sc->dataRow['perpagefilter'] .= $this->frm->open('wabPerPageForm', 'post', e_SELF);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_open('wabPerPage');
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('10', '10', false, $this->current['wabPerPage'] == 10);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('25', '25', false, $this->current['wabPerPage'] == 25);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('50', '50', false, $this->current['wabPerPage'] == 50);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('100', '100', false, $this->current['wabPerPage'] == 100);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('250', '250', false, $this->current['wabPerPage'] == 250);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('500', '500', false, $this->current['wabPerPage'] == 500);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('All', 'all', false, $this->current['wabPerPage'] == 0);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_close();
        $this->sc->dataRow['perpagefilter'] .= $this->frm->close();

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/globe24.png' alt='Users' /> Countries" ;
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }
    protected function coastalList() {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }
        unset($callOptions);
        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabUserlogSelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter Series');
        $selectOptions .= $this->frm->option('Series 1', 'series1', $this->current['selection'] == 'series1');
        $selectOptions .= $this->frm->option('Series 2', 'series2', $this->current['selection'] == 'series2');
        $selectOptions .= $this->frm->option('Series 3', 'series3', $this->current['selection'] == 'series3');
        $selectOptions .= $this->frm->option('Series 4', 'series4', $this->current['selection'] == 'series4');
        $selectOptions .= $this->frm->option('Series 5', 'series5', $this->current['selection'] == 'series5');
        $selectOptions .= $this->frm->option('Series 6', 'series6', $this->current['selection'] == 'series6');

        $selectOptions .= $this->frm->optgroup_close();

        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabCoastalFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabCoastalHeader($this), false, $this->sc);
        // *
        // * display the books if any
        // *
        $qry = "select * from #wabcoastal
		left join #wabareas on wabareaID=wabTidalAreafk
		left join #wabcountry on  wabCountryCode=wabAreaCountryFK
        order by wabSquare asc
        limit 0,25";
        if ($this->sql->gen($qry, false)) {
            while ($this->sc->dataRow = $this->sql->fetch()) {
                $text .= $this->tp->parseTemplate($this->template->wabCoastalDetail(), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabCoastalNoDetail(), false, $this->sc);
        }

        $text .= $this->tp->parseTemplate($this->template->wabCoastalFooter(), false, $this->sc);

        $this->sc->dataRow['perpagefilter'] .= $this->frm->open('wabPerPageForm', 'post', e_SELF);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_open('wabPerPage');
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('10', '10', false, $this->current['wabPerPage'] == 10);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('25', '25', false, $this->current['wabPerPage'] == 25);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('50', '50', false, $this->current['wabPerPage'] == 50);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('100', '100', false, $this->current['wabPerPage'] == 100);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('250', '250', false, $this->current['wabPerPage'] == 250);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('500', '500', false, $this->current['wabPerPage'] == 500);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('All', 'all', false, $this->current['wabPerPage'] == 0);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_close();
        $this->sc->dataRow['perpagefilter'] .= $this->frm->close();

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/coastal24.png' alt='Users' /> Coastal Areas" ;
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }

    /**
    * wabMain::LogsList()
    *
    * @param mixed $mine
    * @return
    * @todo use filter and search to where clause
    */
    protected function islandsList() {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }
        unset($callOptions);
        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabUserlogSelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter Country');
        $selectOptions .= $this->frm->option('England', 'en', $this->current['selection'] == 'fm');
        $selectOptions .= $this->frm->option('Scotland', 'ssb', $this->current['selection'] == 'ssb');
        $selectOptions .= $this->frm->option('Wales', 'cw', $this->current['selection'] == 'cw');
        $selectOptions .= $this->frm->option('N Ireland', 'rtty', $this->current['selection'] == 'rtty');
        $selectOptions .= $this->frm->option('Jersey', 'rtty', $this->current['selection'] == 'rtty');
        $selectOptions .= $this->frm->option('Gurnsey', 'rtty', $this->current['selection'] == 'rtty');
        $selectOptions .= $this->frm->option('Isle of Man', 'rtty', $this->current['selection'] == 'rtty');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter by WAB');
        $selectOptions .= $this->frm->option('WAB area only', 'wab', $this->current['selection'] == 'wab');
        $selectOptions .= $this->frm->option('Non WAB only', 'nowab', $this->current['selection'] == 'nowab');

        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter by Band');
        $selectOptions .= $this->frm->option('160&nbsp;m', '160', $this->current['selection'] == '160');
        $selectOptions .= $this->frm->option('&nbsp;80&nbsp;m', '80', $this->current['selection'] == '80');
        $selectOptions .= $this->frm->option('&nbsp;40&nbsp;m', '40', $this->current['selection'] == '40');
        $selectOptions .= $this->frm->option('&nbsp;30&nbsp;m', '30', $this->current['selection'] == '30');
        $selectOptions .= $this->frm->option('&nbsp;20&nbsp;m', '20', $this->current['selection'] == '20');
        $selectOptions .= $this->frm->option('&nbsp;17&nbsp;m', '17', $this->current['selection'] == '17');
        $selectOptions .= $this->frm->option('&nbsp;12&nbsp;m', '12', $this->current['selection'] == '12');
        $selectOptions .= $this->frm->option('&nbsp;10&nbsp;m', '10', $this->current['selection'] == '10');
        $selectOptions .= $this->frm->option('&nbsp;&nbsp;6&nbsp;&nbsp;m', ' 6', $this->current['selection'] == '6');
        $selectOptions .= $this->frm->option('&nbsp;&nbsp;4&nbsp;&nbsp;m', ' 4', $this->current['selection'] == '4');
        $selectOptions .= $this->frm->option('&nbsp;70&nbsp;cm', '70', $this->current['selection'] == '70');
        $selectOptions .= $this->frm->optgroup_close();
        $selectOptions .= $this->frm->optgroup_open('Filter only');
        $selectOptions .= $this->frm->option('Coastal', 'coast', $this->current['selection'] == 'coast');
        $selectOptions .= $this->frm->option('Islands', 'island', $this->current['selection'] == 'island');
        $selectOptions .= $this->frm->optgroup_close();

        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabIslandFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabIslandHeader($this), false, $this->sc);
        // *
        // * display the islands if any
        // *
        $qry = "select * from #wabislands
        left join #wabareas on wabIslandAreaFK=wabareaID
        left join #wabcountry on wabIslandCountryfk=wabCountryCode
        order by wabIsland
        limit 0,30";
        if ($this->sql->gen($qry, false)) {
            while ($this->sc->dataRow = $this->sql->fetch()) {
                $text .= $this->tp->parseTemplate($this->template->wabIslandDetail($this), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabIslandNoDetail(), false, $this->sc);
        }

        $text .= $this->tp->parseTemplate($this->template->wabIslandFooter(), false, $this->sc);

        $this->sc->dataRow['perpagefilter'] .= $this->frm->open('wabPerPageForm', 'post', e_SELF);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_open('wabPerPage');
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('10', '10', false, $this->current['wabPerPage'] == 10);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('25', '25', false, $this->current['wabPerPage'] == 25);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('50', '50', false, $this->current['wabPerPage'] == 50);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('100', '100', false, $this->current['wabPerPage'] == 100);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('250', '250', false, $this->current['wabPerPage'] == 250);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('500', '500', false, $this->current['wabPerPage'] == 500);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('All', 'all', false, $this->current['wabPerPage'] == 0);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_close();
        $this->sc->dataRow['perpagefilter'] .= $this->frm->close();

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/island24.png' alt='Users' /> Islands" ;
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }
    protected function userList() {
        global $userSel, $selLog, $selectOptions, $search, $wabRow;

        if (($this->current['selection']) == 'clear') {
            $this->current['search'] = '';
        }
        unset($callOptions);
        $callOptions['class'] = 'tbox typeahead';
        $callOptions['title'] = 'Search';
        $callOptions['typeahead'] = 'wab-search';
        $callOptions['data-source'] = "http://localhost/e107_plugins/wab/wab_search.php";
        $callOptions['data-provide'] = "typeahead";
        $search = $this->frm->text('wab-search', $this->current['search'], '', $callOptions);
        unset($callOptions);
        $callOptions['class'] = 'tbox form-control';
        $selectOptions = $this->frm->select_open('wabUserlogSelect', $callOptions);
        $selectOptions .= $this->frm->option('Display All', 'all', $this->current['selection'] == 'all');
        $selectOptions .= $this->frm->option('Clear filters', 'clear', false);
        $selectOptions .= $this->frm->optgroup_open('Filter Series');
        $selectOptions .= $this->frm->option('Series 1', 'series1', $this->current['selection'] == 'series1');
        $selectOptions .= $this->frm->option('Series 2', 'series2', $this->current['selection'] == 'series2');
        $selectOptions .= $this->frm->option('Series 3', 'series3', $this->current['selection'] == 'series3');
        $selectOptions .= $this->frm->option('Series 4', 'series4', $this->current['selection'] == 'series4');
        $selectOptions .= $this->frm->option('Series 5', 'series5', $this->current['selection'] == 'series5');
        $selectOptions .= $this->frm->option('Series 6', 'series6', $this->current['selection'] == 'series6');

        $selectOptions .= $this->frm->optgroup_close();

        $selectOptions .= $this->frm->select_close();
        // // *
        // * Get the filters and log books
        // *
        $taction = e_SELF ;
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
        $text .= $this->frm->open('wabloglist', 'get', $taction, $options);
        $text .= $this->frm->hidden('wabAction', $this->current['action']);
        $text .= $this->frm->hidden('wabSubAction', $this->current['subAction']);
        $text .= $this->frm->hidden('wabID', '0');
        $text .= $this->frm->hidden('wabFrom', '0');
        $text .= $this->tp->parseTemplate($this->template->wabBooksFilter($this), false, $this->sc);
        $text .= $this->frm->close();
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabUsersHeader($this), false, $this->sc);
        // *
        // * display the books if any
        // *
        $qry = "select u.user_id,u. user_name,u.user_lastvisit,
		wu.*,area.wabSquare,
		cont.wabCountryName,cont.wabCountryCode,
		cty.wabCountyID,cty.wabCountyName,
		count(wlog.wabLogListID) as numlogs,
		(select count(*) from #wabbooklist where wabUserBookHolderfk=wabBookHolderFK) as counted
		from #user as u
        left join #wabuser as wu on wabUserID=u.user_id
        left join #wabloglist as wlog on wlog.wabLogListWabUserfk=wu.wabUserID
        left join #wabareas as area on wu.wabUserAreafk=area.wabareaID
        left join #wabcounty as cty on wu.wabUserLogCountyfk=cty.wabCountyID
        left join #wabcountry as cont on area.wabAreaCountryFK=cont.wabCountryCode
        group by u.user_id
		order by wabUserCallsign asc
        limit 0,25 ";
        if ($this->sql->gen($qry, true)) {
            while ($this->sc->dataRow = $this->sql->fetch()) {
                $text .= $this->tp->parseTemplate($this->template->wabUsersDetail(), false, $this->sc);
            }
        } else {
            $text .= $this->tp->parseTemplate($this->template->wabUsersNoDetail(), false, $this->sc);
        }

        $text .= $this->tp->parseTemplate($this->template->wabUsersFooter(), false, $this->sc);

        $this->sc->dataRow['perpagefilter'] .= $this->frm->open('wabPerPageForm', 'post', e_SELF);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_open('wabPerPage');
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('10', '10', false, $this->current['wabPerPage'] == 10);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('25', '25', false, $this->current['wabPerPage'] == 25);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('50', '50', false, $this->current['wabPerPage'] == 50);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('100', '100', false, $this->current['wabPerPage'] == 100);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('250', '250', false, $this->current['wabPerPage'] == 250);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('500', '500', false, $this->current['wabPerPage'] == 500);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->option('All', 'all', false, $this->current['wabPerPage'] == 0);
        $this->sc->dataRow['perpagefilter'] .= $this->frm->select_close();
        $this->sc->dataRow['perpagefilter'] .= $this->frm->close();

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/users24.png' alt='Users' /> System Users";
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }
    protected function exportAdi() {
        $p = new WabAdifParser();
        // make select box from logs
        // get any post values if submit button pressed and stick them in current
        if (isset($_POST['wabLogExportDo'])) {
            foreach($_POST as $key => $value) {
                if (substr($key, 0, 9) == 'wabExport') {
                    $this->current[$key] = $value;
                }
            }
            unset($this->current['wabLogExportDo']);
            $this->setSession();
        }
        $logrows = $this->sql->retrieve('wabloglist', 'wabLogListID,wabLogTitle', 'wabLogListWabUserfk=' . USERID, true, null, false);
        $loglist[0] = '-- All -- ';
        foreach($logrows as $array) {
            $loglist[$array['wabLogListID']] = $array['wabLogTitle'];
        }
        $this->sc->dataRow['exportLogs'] = $this->frm->select('wabExportLogs', $loglist, $this->current['wabExportLogs']);
        $wab = array('wall' => '-- All --', 'wonly' => 'With WAB square Only', 'wnone' => 'Exclude with WAB square');
        $this->sc->dataRow['exportWab'] = $this->frm->select('wabExportWab', $wab, $this->current['wabExportWab']);

        $active = array('acall' => '-- All --', 'aconly' => 'Active Only', 'acnone' => 'Exclude Active');
        $this->sc->dataRow['exportActive'] = $this->frm->select('wabExportActive', $active, $this->current['wabExportActive']);

        $private = array('pall' => '-- All --', 'ponly' => 'Private Only', 'pnone' => 'Exclude Private');
        $this->sc->dataRow['exportPrivate'] = $this->frm->select('wabExportPrivate', $private, $this->current['wabExportPrivate']);

        $books = array('ball' => '-- All --', 'bonly' => 'Only Holders', 'bnone' => 'Exclude Holders');
        $this->sc->dataRow['exportBooks'] = $this->frm->select('wabExportBooks', $books, $this->current['wabExportBooks']);

        $country = array('ukall' => '-- All --', 'ukonly' => 'UK Only', 'uknone' => 'Exclude UK');
        // now do list of countries
        $this->sc->dataRow['exportCountry'] = $this->frm->select('wabExportCountry', $country, $this->current['wabExportCountry']);

        $coastal = array('coastall' => '-- All --', 'coastonly' => 'Coastal Only', 'coastnone' => 'Exclude Coastal');
        $this->sc->dataRow['exportCoastal'] = $this->frm->select('wabExportCoastal', $coastal, $this->current['wabExportCoastal']);

        $islands = array('iall' => '-- All --', 'ionly' => 'Only Islands', 'inone' => 'Exclude Islands');
        $this->sc->dataRow['exportIslands'] = $this->frm->select('wabExportIslands', $islands, $this->current['wabExportIslands']);
        // make select box from modes
        $moderows = $this->sql->retrieve('wabmodes', 'wabModesID,wabModesName', 'ORDER BY wabModesName', true, null, false);
        $modelist[0] = '-- Any -- ';
        foreach($moderows as $array) {
            $modelist[$array['wabModesID']] = $array['wabModesName'];
        }
        $this->sc->dataRow['exportModes'] = $this->frm->select('wabExportModes', $modelist, $this->current['wabExportModes']);
        // make select box from BANDS
        $bandrows = $this->sql->retrieve('wabbands', 'wabBandsID,wabBandsName', 'ORDER BY wabBandsName', true, null, false);
        $bandlist[0] = '-- Any -- ';
        foreach($bandrows as $array) {
            $bandlist[$array['wabBandsID']] = $array['wabBandsName'];
        }
        $this->sc->dataRow['exportBands'] = $this->frm->select('wabExportBands', $bandlist, $this->current['wabExportBands']);
        // make select box from continents
        $controws = $this->sql->retrieve('wabcontinent', 'wabContinentID,wabContinentName', 'ORDER BY wabContinentName', true, null, false);
        $contlist[0] = '-- Any -- ';
        foreach($controws as $array) {
            $contlist[$array['wabContinentID']] = $array['wabContinentName'];
        }
        $this->sc->dataRow['exportCont'] = $this->frm->select('wabExportCont', $contlist, $this->current['wabExportCont']);

        $qsls = array('qslsall' => '-- All --', 'qslsonly' => 'QSL Sent', 'qslsnone' => 'QSL Not Sent');
        $this->sc->dataRow['exportQslS'] = $this->frm->select('wabExportQslS', $qsls, $this->current['wabExportQslS']);

        $qslr = array('qslrall' => '-- All --', 'qslronly' => 'QSL Received', 'qslrnone' => 'QSL Not Received');
        $this->sc->dataRow['exportQslR'] = $this->frm->select('wabExportQslR', $qslr, $this->current['wabExportQslR']);
$limitArray=array('0'=>'-- All --','10'=>'10','25'=>'25','50'=>'50','100'=>'100','250'=>'250','500'=>'500','1000'=>'1,000');
    	$this->sc->dataRow['exportLimit'] = $this->frm->select('wabExportLimit', $limitArray, $this->current['wabExportLimit']);



        $order = array('bydate' => 'By Date', 'bycall' => 'By Callsign', 'byarea' => 'By Area', 'byctry' => 'By Country', 'bycalldate' => 'By Call,Date', 'byadate' => 'By Area,date');
        $this->sc->dataRow['exportOrder'] = $this->frm->select('wabExportOrder', $order, $this->current['wabExportOrder']);

        $form = array('formscreen' => 'On Screen', 'formcsv' => 'CSV .csv', 'formxls' => 'Excel .xls', 'formpdf' => 'PDF .pdf', 'formadif2' => 'ADI Version 2', 'formadif3' => 'ADI Version 3');
        $this->sc->dataRow['exportFormat'] = $this->frm->select('wabExportFormat', $form, $this->current['wabExportFormat']);
        $resetvalues = array('ddd' => 'All', 'Selections', 'Outputs');
        $this->sc->dataRow['exportReset'] = $this->frm->submit('wabLogExportReset', 'Reset');
        $this->sc->dataRow['exportDo'] = $this->frm->submit('wabLogExportDo', 'Export');
        $text .= $this->frm->open('wabExport', 'post', e_SELF);
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);
        $text .= $this->tp->parseTemplate($this->template->wabExport(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $text .= $this->frm->close();
        $filterEle = $_POST;
        $where = $this->buildFilter($filterEle);
        $order = $this->buildOrder($filterEle);
        $limit = $this->buildLimit($filterEle);
        $qry = "SELECT * from #wablog
    	left join #wabmodes on wabLogModefk = wabModesID
    	left join #wabbands on wabLogBandfk = wabBandsID
    	left join #wabareas on wabLogAreaWorkedFK = wabareaID
    	left join #wabcountry on wabLogCountryfk = wabCountryCode
    	left join #wabloglist on wabLogMyLog = wabLogListID
    	left join #wabislands on wabIslandAreaFK = wabareaID
			{$where} {$order} {$limit}
    	";
        $this->sql->gen($qry, true);
        $wabusertitle = "<img src='images/menu/export24.png' alt='Export' /> Export Records";
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
        // $p->adifExport();
    }
    protected function importAdi() {
        // include 'adif_parser.php';
        // $p = new WabAdifParser;
        // $p->loadFromFile("adifin/adif.adi");
        // $p->initialize();
        $this->sc->dataRow['doup'] = $this->frm->button("wabUp", "Upload");
        $this->sc->dataRow['doimp'] = $this->frm->button("wabDo", "Import");
        unset($callOptions);

        $callOptions['class'] = 'tbox ';
        $callOptions['title'] = 'Import Types';
        $selectOptions = $this->frm->select_open('wabAdiType', $callOptions);
        $qry = "SELECT wabAdiAppName,wabAdiApplication from #wabadifields order by wabAdiAppName";
        $selectOptions .= $this->frm->option('--- Select format ---', 'none');
        if ($this->sql->gen($qry, false)) {
            while ($row = $this->sql->fetch()) {
                $apps[$row['wabAdiApplication']] = $row['wabAdiAppName'];
            }
        }
        foreach($apps as $key => $value) {
            $selectOptions .= $this->frm->option($value, $key, $this->current['selection'] == $key);
        }
        $selectOptions .= $this->frm->select_close();
        $this->sc->dataRow['select'] = $selectOptions;
        $this->sc->dataRow['fileup'] = $this->frm->file('wabUpload');
        $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);
        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);
        $text .= $this->tp->parseTemplate($this->template->wabImportADIHeader(), false, $this->sc);

        $filelist = scandir('adifin');

        foreach($filelist as $file) {
            $owner = explode('_', $file);
            // print "<br /> $file | {$owner[0]} || {$owner[1]}  || ".USERID;
            if (intval($owner[0]) == USERID) {
                $this->sc->dataRow['file'] = $owner[1];
                // print $owner[1];
                $this->sc->dataRow['fileselect'] = $this->frm->radio('wabFile[]', $owner[1], false);
                $this->sc->dataRow['filedelete'] = $this->frm->checkbox('wabDelete[]', $owner[1], false);
                $text .= $this->tp->parseTemplate($this->template->wabImportADIFiles(), false, $this->sc);
            }
        }
        $this->sc->dataRow['filedelete'] = $this->frm->checkbox_toggle('wabDelete', 'wabdelete');

        $text .= $this->tp->parseTemplate($this->template->wabImportADIFooter(), false, $this->sc);

        $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);

        $wabusertitle = "<img src='images/menu/import24.png' alt='Import' /> Import ADI File";
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }

    protected function settingsMain() {
        // first get the users details then their logs
        $qry = "select * from #wabuser where wabUserID=" . USERID . "";
        $result = $this->sql->gen($qry, false);
        if (!$result) {
            // Name
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['required'] = 'required';
            $callOptions['title'] = 'My Name';
            $this->sc->dataRow['wabUserName'] = $this->frm->text('wabUserName', $row['wabUserName'], 10, $callOptions);
            // Callsign
            // Name
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['required'] = 'required';
            $callOptions['title'] = 'My Callsign';
            $this->sc->dataRow['wabUserCallsign'] = $this->frm->text('wabUserCallsign', $row['wabUserCallsign'], 10, $callOptions);
            // home area
            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'My Area';
            $this->sc->dataRow['wabUserHomeArea'] = $this->frm->text('wabUserHomeArea', $row['wabUserHomeArea'], 10, $callOptions);
            // IARU
            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'My IARU Locator';
            $this->sc->dataRow['wabUserHomeIARU'] = $this->frm->text('wabUserHomeIARU', $row['wabUserHomeIARU'], 10, $callOptions);
            // qrz login name
            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'QRZ Login name';
            $this->sc->dataRow['wabUserQrzLogin'] = "W" . $this->frm->text('wabUserQrzLogin', $row['wabUserQrzLogin'], 10, $callOptions);

            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'QRZ Password';
            $this->sc->dataRow['wabUserQrzPassword'] = $this->frm->text('wabUserQrzPassword', $row['wabUserQrzPassword'], 10, $callOptions);

            $this->sc->dataRow['wabUserCountyfk'] = $this->inputCounty('wabUserCountyfk', $row['wabUserCountyfk'], 'Select County');
            $this->sc->dataRow['wabUserCountryfk'] = $this->inputCountry('wabUserCountryfk', $row['wabUserCountryfk'], 'Select Country');

            $this->sc->dataRow['wabLogSettingscreate'] = $this->frm->submit('wabLogSettingscreate', 'Create');
            $text .= $this->frm->open('wabSettings', 'post', e_SELF);
            $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);
            $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
            $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);
            $text .= $this->tp->parseTemplate($this->template->wabSettingsNoAcc(), false, $this->sc);
            $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);
            $text .= $this->frm->close();
        } else {
            $row = $this->sql->Fetch();
            // print_a($row);
            $qry = "select * from #wabloglist where wabLogListWabUserfk=" . $row['wabUserID'] . "";
            $this->sql->gen($qry, false);
            $i = 1;
            while ($logrow = $this->sql->Fetch()) {
                $logs[$i] = $logrow;
                $i++;
            }
            $text .= $this->frm->open('wabSettings', 'post', e_SELF);
            $text .= $this->tp->parseTemplate($this->template->wabMainHeader(), false, $this->sc);
            $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuOpen(), false, $this->sc);
            $text .= $this->tp->parseTemplate($this->template->wabTemplateMenuClose(), false, $this->sc);
            // Name
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['required'] = 'required';
            $callOptions['title'] = 'My Name';
            $this->sc->dataRow['wabUserName'] = $this->frm->text('wabUserName', $row['wabUserName'], 10, $callOptions);
            // Callsign
            // Name
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['required'] = 'required';
            $callOptions['title'] = 'My Callsign';
            $this->sc->dataRow['wabUserCallsign'] = $this->frm->text('wabUserCallsign', $row['wabUserCallsign'], 10, $callOptions);
            // home area
            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'My Area';
            $this->sc->dataRow['wabUserHomeArea'] = $this->frm->text('wabUserHomeArea', $row['wabUserHomeArea'], 10, $callOptions);
            // IARU
            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'My IARU Locator';
            $this->sc->dataRow['wabUserHomeIARU'] = $this->frm->text('wabUserHomeIARU', $row['wabUserHomeIARU'], 10, $callOptions);
            // qrz login name
            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'QRZ Login name';
            $this->sc->dataRow['wabUserQrzLogin'] = $this->frm->text('wabUserQrzLogin', $row['wabUserQrzLogin'], 10, $callOptions);

            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'QRZ Password';
            $this->sc->dataRow['wabUserQrzPassword'] = $this->frm->text('wabUserQrzPassword', $row['wabUserQrzPassword'], 10, $callOptions);

            $this->sc->dataRow['wabUserCountyfk'] = $this->inputCounty('wabUserCountyfk', $row['wabUserCountyfk'], 'Select County');
            $this->sc->dataRow['wabUserCountryfk'] = $this->inputCountry('wabUserCountryfk', $row['wabUserCountryfk'], 'Select Country');

            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'Log Title';
            $this->sc->dataRow['wabLogTitle'][1] = $this->frm->text('wabLogTitle[1]', $logs[1]['wabLogTitle'], 10, $callOptions);
            $this->sc->dataRow['wabLogTitle'][2] = $this->frm->text('wabLogTitle[2]', $logs[2]['wabLogTitle'], 10, $callOptions);
            $this->sc->dataRow['wabLogTitle'][3] = $this->frm->text('wabLogTitle[3]', $logs[3]['wabLogTitle'], 10, $callOptions);
            $this->sc->dataRow['wabLogTitle'][4] = $this->frm->text('wabLogTitle[4]', $logs[4]['wabLogTitle'], 10, $callOptions);
            $this->sc->dataRow['wabLogTitle'][5] = $this->frm->text('wabLogTitle[5]', $logs[5]['wabLogTitle'], 10, $callOptions);

            $this->sc->dataRow['wabLogListLogType'][1] = $this->inputLogType('wabLogListLogType', $logs[1]['wabLogListLogType'], 'Select Log Type', 1);
            $this->sc->dataRow['wabLogListLogType'][2] = $this->inputLogType('wabLogListLogType', $logs[2]['wabLogListLogType'], 'Select Log Type', 2);
            $this->sc->dataRow['wabLogListLogType'][3] = $this->inputLogType('wabLogListLogType', $logs[3]['wabLogListLogType'], 'Select Log Type', 3);
            $this->sc->dataRow['wabLogListLogType'][4] = $this->inputLogType('wabLogListLogType', $logs[4]['wabLogListLogType'], 'Select Log Type', 4);
            $this->sc->dataRow['wabLogListLogType'][5] = $this->inputLogType('wabLogListLogType', $logs[5]['wabLogListLogType'], 'Select Log Type', 5);

            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'Hide log from others';
            $this->sc->dataRow['wabLogListPrivate'][1] = $this->frm->checkbox('wabLogListPrivate[1]', '1', $logs[1]['wabLogListPrivate'], $callOptions);
            $this->sc->dataRow['wabLogListPrivate'][2] = $this->frm->checkbox('wabLogListPrivate[2]', '1', $logs[2]['wabLogListPrivate'], $callOptions);
            $this->sc->dataRow['wabLogListPrivate'][3] = $this->frm->checkbox('wabLogListPrivate[3]', '1', $logs[3]['wabLogListPrivate'], $callOptions);
            $this->sc->dataRow['wabLogListPrivate'][4] = $this->frm->checkbox('wabLogListPrivate[4]', '1', $logs[4]['wabLogListPrivate'], $callOptions);
            $this->sc->dataRow['wabLogListPrivate'][5] = $this->frm->checkbox('wabLogListPrivate[5]', '1', $logs[5]['wabLogListPrivate'], $callOptions);

            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'Log is active';
            $this->sc->dataRow['wabLogListActive'][1] = $this->frm->checkbox('wabLogListActive[1]', '1', $logs[1]['wabLogListActive'], $callOptions);
            $this->sc->dataRow['wabLogListActive'][2] = $this->frm->checkbox('wabLogListActive[2]', '1', $logs[2]['wabLogListActive'], $callOptions);
            $this->sc->dataRow['wabLogListActive'][3] = $this->frm->checkbox('wabLogListActive[3]', '1', $logs[3]['wabLogListActive'], $callOptions);
            $this->sc->dataRow['wabLogListActive'][4] = $this->frm->checkbox('wabLogListActive[4]', '1', $logs[4]['wabLogListActive'], $callOptions);
            $this->sc->dataRow['wabLogListActive'][5] = $this->frm->checkbox('wabLogListActive[5]', '1', $logs[5]['wabLogListActive'], $callOptions);

            unset($callOptions);
            $callOptions['class'] = 'tbox';
            $callOptions['size'] = '10';
            $callOptions['type'] = 'text';
            $callOptions['title'] = 'Log is active';
            $this->sc->dataRow['wabLogListDefault'][1] = $this->frm->radio('wabLogListDefault[]', '1', $logs[1]['wabLogListDefault'], $callOptions);
            $this->sc->dataRow['wabLogListDefault'][2] = $this->frm->radio('wabLogListDefault[]', '2', $logs[2]['wabLogListDefault'], $callOptions);
            $this->sc->dataRow['wabLogListDefault'][3] = $this->frm->radio('wabLogListDefault[]', '3', $logs[3]['wabLogListDefault'], $callOptions);
            $this->sc->dataRow['wabLogListDefault'][4] = $this->frm->radio('wabLogListDefault[]', '4', $logs[4]['wabLogListDefault'], $callOptions);
            $this->sc->dataRow['wabLogListDefault'][5] = $this->frm->radio('wabLogListDefault[]', '5', $logs[5]['wabLogListDefault'], $callOptions);

            $this->sc->dataRow['wabLogSettingsSave'] = $this->frm->submit('wabLogSettingsSave', 'Save');

            $text .= $this->tp->parseTemplate($this->template->wabSettingsMain(), false, $this->sc);
            // $qry = "select * from #wabcoastal
            // left join #wabareas on wabareaID=wabTidalAreafk
            // left join #wabcountry on  wabCountryCode=wabAreaCountryFK
            // order by wabSquare asc
            // limit 0,25";
            // if ($this->sql->gen($qry, false)) {
            // while ($this->sc->dataRow = $this->sql->fetch()) {
            // $text .= $this->tp->parseTemplate($this->template->wabCoastalDetail(), false, $this->sc);
            // }
            // } else {
            // $text .= $this->tp->parseTemplate($this->template->wabCoastalNoDetail(), false, $this->sc);
            // }
            $text .= $this->tp->parseTemplate($this->template->wabMainFooter(), false, $this->sc);
            $text .= $this->frm->close();
        }
        $wabusertitle = "<img src='images/menu/mysettings24.png' alt='Users' /> My Settings" ;
        $this->ns->tablerender($wabusertitle, $this->mes->render() . $text);
    }
    protected function settingsUpdate() {
        $insert['WHERE'] = "wabUserID ='" . USERID . "' ";
        $insert['data'] = array(
            'wabUserName' => $_POST['wabUserName'],
            'wabUserCallsign' => $_POST['wabUserCallsign'],
            'wabUserHomeArea' => $_POST['wabUserHomeArea'],
            'wabUserCountyfk' => $_POST['wabUserHomeArea'],
            'wabUserCountryfk' => $_POST['wabUserCountryfk'],
            'wabUserHomeIARU' => $_POST['wabUserHomeIARU'],
            'wabUserQrzLogin' => $_POST['wabUserQrzLogin'],
            'wabUserQrzPassword' => $_POST['wabUserQrzPassword'],
            'wabUserUpdater' => USERID . "." . USERNAME);
        $result = $this->sql->update('wabuser', $insert, false); // update user entry
        if ($result === false) {
            $this->mes->addError("Error updating user log entries");
        } else {
            $this->mes->addSuccess('Account Updated');
        }
    }
    protected function settingsCreate() {
        $insert['data'] = array(
            'wabUserID' => USERID,
            'wabUserName' => $_POST['wabUserName'],
            'wabUserCallsign' => $_POST['wabUserCallsign'],
            'wabUserHomeArea' => $_POST['wabUserHomeArea'],
            'wabUserCountyfk' => $_POST['wabUserHomeArea'],
            'wabUserCountryfk' => $_POST['wabUserCountryfk'],
            'wabUserHomeIARU' => $_POST['wabUserHomeIARU'],
            'wabUserHomeIARU' => $_POST['wabUserHomeIARU'],
            'wabUserQrzPassword' => $_POST['wabUserQrzPassword'],
            'wabUserUpdater' => USERID . "." . USERNAME);
        $result = $this->sql->insert('wabuser', $insert, false); // create user entry
        if ($result !== false) {
            $this->mes->addSuccess('Account Created');
            // create log records
            $insertok = true;
            for($i = 1;$i < 6;$i++) {
                $insert['data'] = array('wabLogListWabUserfk' => $result, 'wabLogTitle' => "Log " . $i, 'wabLogListUpdater' => USERID . "." . USERNAME);
                $inserted = $this->sql->insert('wabloglist', $insert, false);
                if ($inserted === false) {
                    $insertok = false;
                }
            }
            if (!$insertok) {
                $this->mes->addError("Error creating user log entries");
            } else {
                $this->mes->addSuccess('Log entries Created');
            }
        } else {
            $this->mes->addError("Error creating user entry");
        }
    }
}

?>