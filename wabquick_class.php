<?php
if (!defined('e107_INIT')) {
	exit;
}
class wabQuickMenu extends wab {
	function __construct(){
		parent::__construct();
	}
	function showQuick() {
		$text = $this->frm->open('wabQuick', 'get', e_PLUGIN . 'wab/index.php');
		// my logbook
		$qry = "select * from #wabloglist where wabLogListWabUserfk=7 order by wabLogTitle";
		$this->sql->gen($qry, false);
		$mylogOptions['title'] = "Insert into my logbook";
		$tmp = $this->frm->select_open('wabQuickMyLog', $mylogOptions);
		while ($row = $this->sql->fetch()) {
			$tmp .= $this->frm->option($row['wabLogTitle'], $row[ 'wabLogListID']);
		}
		$tmp .= $this->frm->select_close();

		$this->sc->dataRow['wabQuickMyLog'] = $tmp;
		// date time start
		$startOptions['class'] = 'tbox';
		// $startOptions['required'] = 'required';
		$startOptions['type'] = 'datetime';
		$startOptions['format'] = 'dd-mm-yyyy hh:ii';
		$startOptions['size'] = 'small';
		$startOptions['title'] = 'QSO Start Time';
		$startOptions['firstDay'] = 1;

		$this->sc->dataRow['wabQuickStart'] = $this->frm->datepicker('wabQuickStart', time(), $startOptions);
		// callsign
		$callOptions['class'] = 'tbox';
		$callOptions['size'] = '10';
		$callOptions['type'] = 'text';
		$callOptions['required'] = 'required';
		$callOptions['title'] = 'Callsign';
		$this->sc->dataRow['wabQuickCall'] = $this->frm->text('wabQuickCall', '', 10, $callOptions);
		// Frequency
		$freqOptions['class'] = 'tbox';
		$freqOptions['required'] = 'required';
		$freqOptions['size'] = '20';
		$freqOptions['type'] = 'text';
		$freqOptions['title'] = 'Frequency';
		$this->sc->dataRow['wabQuickFreq'] = $this->frm->text('wabQuickFreq', '', 7 , $freqOptions);
		// received RST
		$rstOptions['class'] = 'tbox';
		$rstOptions['required'] = 'required';
		$rstOptions['size'] = '10';
		$rstOptions['type'] = 'text';
		$rstOptions['title'] = 'Received RS';
		$this->sc->dataRow['wabQuickRrs'] = $this->frm->text('wabQuickRrs', '', 7, $rstOptions);
		// Sent RST
		$rstOptions['class'] = 'tbox';
		$rstOptions['required'] = 'required';
		$rstOptions['size'] = '10';
		$rstOptions['type'] = 'text';
		$rstOptions['title'] = 'Sent RS';
		$this->sc->dataRow['wabQuickSrs'] = $this->frm->text('wabQuickSrs', '', 10, $rstOptions);
		// mode
		$qry = "select * from #wabmodes order by wabModesName";
		$this->sql->gen($qry, false);
		$modeOptions['title'] = "Mode";
		unset($tmp);
		$tmp = $this->frm->select_open('wabQuickMode', $modeOptions);
		while ($row = $this->sql->fetch()) {
			$tmp .= $this->frm->option($row['wabModesName'], $row[ 'wabModesID']);
		}
		$tmp .= $this->frm->select_close();
		$this->sc->dataRow['wabQuickMode'] = $tmp;
		// their square
		$areaOptions['class'] = 'tbox';
		$areaOptions['size'] = '15';
		$areaOptions['type'] = 'text';
		$areaOptions['title'] = 'WAB Square';
		$this->sc->dataRow['wabQuickWab'] = $this->frm->text('wabQuickWab', '', 8, $areaOptions);
		// my square
		$mineOptions['class'] = 'tbox';
		$mineOptions['size'] = '15';
		$mineOptions['type'] = 'text';
		$mineOptions['title'] = 'My Square';
		$this->sc->dataRow['wabQuickMine'] = $this->frm->text('wabQuickMine', '', 8, $mineOptions);
		// submit
		// $subOptions['class'] = 'tbox';
		// $subOptions['size'] = '15';
		$this->sc->dataRow['wabQuickSubmit'] = $this->frm->button('wabQuickSubmit', 'Submit', 'submit', 'Submit', $subOptions);

		$text .= $this->tp->parseTemplate($this->template->wabQuickMenu(), false, $this->sc);
		$text .= $this->frm->close();
		return $text;
	}
}

?>