<?php

class Materia {

	const AVAILABLE_INCLUSIONS = array(
		'phpPOO',
		'JSReady',
		'PDOdB'
	);
	const LIB_DIR = 'materia/lib/';
	const WB_PATH = './materia/workBench.txt';
	const PROJ_DIR = '../projects/';

	/*
	WORKBENCH
	*/
	//Setting workbench
	static function setWorkBench() {
		copy(self::LIB_DIR . 'empty.txt', self::WB_PATH);
		chmod(self::WB_PATH, 0777);
	}
	//Destroying workbench
	static function destroyWorkBench() {
		unlink(self::WB_PATH);
	}
	//Put in workbench
	static function addInFile(string $str) {
		$cursor = fopen(self::WB_PATH, 'a+');
		fputs($cursor, $str);
		fclose($cursor);
	}
	//Build workbench
	static function buildWorkBench(string $string, array $array) {
		foreach($array as $part) {
			if($part != $string) {
				self::addInFile(self::trueRead(self::LIB_DIR . $part . '.txt'));
			}
		}
	}
	//Export workbench
	static function exportWb(string $str) {
		copy(self::WB_PATH, $str);
	}
	//Create file
	static function createFile(string $proj, string $str, array $array) {
		self::setWorkBench();
		self::buildWorkBench($proj, $array);
		self::exportWb($str);
		self::destroyWorkBench();
	}

	/*
	PDO
	 */
	//Build db_config
	static function buildPdoConfig(string $str, array $array) {
		self::setWorkBench();
		self::addInFile(self::trueRead(self::LIB_DIR . 'pdo/db_host.txt'));
		self::addInFile($array['PDOdB_host']);
		self::addInFile(self::trueRead(self::LIB_DIR . 'pdo/db_user.txt'));
		self::addInFile($array['PDOdB_user']);
		self::addInFile(self::trueRead(self::LIB_DIR . 'pdo/db_pass.txt'));
		self::addInFile($array['PDOdB_pass']);
		self::addInFile(self::trueRead(self::LIB_DIR . 'pdo/db_define.txt'));
		self::exportWb($str);
		self::destroyWorkBench();
	}

	/*
	PROJECT BUILD
	 */
	//Build all project
	static function buildProject(string $str, array $array) {
		self::buildIndexPhp($str, $array);
		mkdir(self::PROJ_DIR . $str . '/res');
		chmod(self::PROJ_DIR . $str . '/res', 0777);
		self::buildIndexCss($str, $array);
		if(isset($array['JSReady'])) {
			mkdir(self::PROJ_DIR . $str . '/res/js');
			chmod(self::PROJ_DIR . $str . '/res/js', 0777);
			if(isset($array['jsObj_cookChief'])) {
				self::createFile($str, self::PROJ_DIR . $str . '/res/js/cookChief.js', array('js/cookChief'));
			}
			if(isset($array['jsObj_getJson'])) {
				self::createFile($str, self::PROJ_DIR . $str . '/res/js/getJson.js', array('js/getJson'));
			}
		}
		if(isset($array['phpPOO'])) {
			mkdir(self::PROJ_DIR . $str . '/res/class');
			chmod(self::PROJ_DIR . $str . '/res/class', 0777);
			if(isset($array['phpClass_dataBar'])) {
				self::createFile($str, self::PROJ_DIR . $str . '/res/class/dataBar.php', array('class/dataBar'));
			}
			if(isset($array['phpClass_rssBuilder'])) {
				self::createFile($str, self::PROJ_DIR . $str . '/res/class/rssBuilder.php', array('class/rssBuilder'));
			}
		}
		if(isset($array['PDOdB'])) {
			mkdir(self::PROJ_DIR . $str . '/res/dataBase');
			chmod(self::PROJ_DIR . $str . '/res/dataBase', 0777);
			self::buildPdoConfig(self::PROJ_DIR . $str . '/res/dataBase/db_config.php', $array);
			self::createFile($str, self::PROJ_DIR . $str . '/res/dataBase/db_init.php', array('pdo/dBinit'));
		}
		chmod(self::PROJ_DIR . $str, 0777);
	}
	//Build index.php
	static function buildIndexPhp(string $str, array $array) {

		$inIndex = [];

		$inIndex[] = 'php/openPhp';
		if(isset($array['phpPOO'])) {
			$inIndex[] = 'php/autoClassLoad';
		}
		if(isset($array['PDOdB'])) {
			$inIndex[] = 'pdo/dBrequires';
		}
		$inIndex[] = 'php/linePhp';
		$inIndex[] = 'php/closePhp';

		$inIndex[] = 'html/doctypeHtml';
		$inIndex[] = 'html/openHtml';

		$inIndex[] = 'html/openHead';
		$inIndex[] = 'html/openTitle';
		$inIndex[] = $str;
		$inIndex[] = 'html/closeTitle';
		$inIndex[] = 'html/metaUtf8';
		if(isset($array['JSReady'])) {
			if(isset($array['jsLib_jQuery'])) {
				$inIndex[] = 'js/jQueryGalLink';
			}
			if(isset($array['jsObj_cookChief'])) {
				$inIndex[] = 'js/cookChiefCall';
			}
			if(isset($array['jsObj_getJson'])) {
				$inIndex[] = 'js/getJsonCall';
			}
		}
		$inIndex[] = 'html/basicStylesheet';
		$inIndex[] = 'html/closeHead';

		$inIndex[] = 'html/openBody';
		$inIndex[] = 'html/closeBody';

		$inIndex[] = 'html/closeHtml';

		self::createFile($str, self::PROJ_DIR . $str . '/index.php', $inIndex);

	}
	//Build index.css
	static function buildIndexCss(string $str) {
		$inCss = [];
		$inCss[] = 'css/basicBody';
		self::createFile($str, self::PROJ_DIR . $str . '/res/main.css', $inCss);
	}

	/*
	UTILITIES
	*/
	static function trueRead(string $str) {
		$returned = '';
		foreach(file($str) as $line) {
			$returned = $returned . $line;
		}
		return $returned;
	}

}
