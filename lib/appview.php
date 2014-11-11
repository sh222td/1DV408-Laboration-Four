<?php
/*
*	A class for rendering full HTML-pages with help of template-files.
*	Gives option to choose what HTML-standard to render: Strict, Transitional or HTML5.

*	TODO: Figure out how to add content, includes, css, javascript etc dynamically from view or controller classes.
*/
class AppView{
	protected $strHtmlStandard = APPLICATION_HTML_STANDARD;	
	protected $arrPageData;

	protected $arrCssFiles;
	protected $arrJavascriptFiles;
	protected $arrIncludeFiles = array();
	
	public function __construct(){
		$this->arrCssFiles = Config::$arrDefaultCssFiles;
		$this->arrJavascriptFiles = Config::$arrDefaultJavascriptFiles;
	}

	public function render($html){
		$strTemplate = $this->getApplicationTemplate();
		$this->buildIncludes($strTemplate);
		$this->buildCssTags();
		$this->buildJavascriptTags();
		$this->arrPageData['title'] = APPLICATION_TITLE;

		$strHtml = $strTemplate;
		foreach($this->arrPageData['includes'] as $arrInclude){
			$strHtml = trim(str_replace($arrInclude[0], $arrInclude[1], $strHtml));
		}
		$strHtml = str_replace('<!--{CSS}-->', trim($this->arrPageData['css']), $strHtml);
		$strHtml = str_replace('<!--{JAVASCRIPT}-->', trim($this->arrPageData['js']), $strHtml);
		$strHtml = str_replace('<!--{TITLE}-->', trim($this->arrPageData['title']), $strHtml);
		$strHtml = str_replace('<!--{HTMLBODY}-->', trim($html), $strHtml);

		echo $strHtml;
	}
	
	public function addCssFile($strFileName){
		$this->arrCssFiles[] = $strFileName;
	}

	private function buildCssTags(){
		$this->arrPageData['css'] = '';
		foreach($this->arrCssFiles as $strCssFileName){
			$this->arrPageData['css'] .= '<link href="' . CSS_PATH . $strCssFileName . '" media="all" rel="stylesheet" type="text/css" />' . "\n";
		}
		trim($this->arrPageData['css']);
	}
	
	public function addJavaScriptFile($strFileName){
		$this->arrJavascriptFiles[] = $strFileName;
	}

	private function buildJavascriptTags(){
		$this->arrPageData['js'] = '';
		foreach($this->arrJavascriptFiles as $strJavascriptFileName){
			$this->arrPageData['js'] .= '<script src="' . JS_PATH . $strJavascriptFileName . '" type="text/javascript"></script>' . "\n";
		}
		trim($this->arrPageData['js']);
	}

	private function getApplicationTemplate(){
		$strFileName = 'application-' . $this->strHtmlStandard . '.html.php';
		$strFullPath = APP_DIR . 'layout/' . $strFileName;
		$f = fopen($strFullPath, 'r');
		$strHTML = fread($f, filesize($strFullPath));
		fclose($f);
		return $strHTML;
	}

	private function buildIncludes($strHtml){
		preg_match_all('/<!--{Include:(.*)}-->/', $strHtml, $arrIncludes, PREG_PATTERN_ORDER);
		$this->arrPageData['includes'] = array();
		for($i = 0; $i < count($arrIncludes); $i++){
			$strFile = (isset($arrIncludes[$i][0])) ? $arrIncludes[$i][0] : '';
			if(file_exists(APP_DIR . 'layout/' . $strFile)){
				//$this->arrIncludeFiles[] = $strFile;
				$strFilePath = APP_DIR . 'layout' . DS . $strFile;
				$f = fopen($strFilePath, 'r');
				$strFileContent = fread($f, filesize($strFilePath));
				fclose($f);
				$this->arrPageData['includes'][] = array('<!--{Include:' . $strFile . '}-->', $strFileContent);
			}
		}
	}
}

?>