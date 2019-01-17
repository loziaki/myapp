<?php declare(strict_types=1);
namespace Service\ExcelReader;

use SimpleXMLElement;
use ZipArchive;
use Exception;

abstract class XlsxReader implements FileReader
{
    //解析出来的字符串数组
    protected $values;
    //解析出来的工作表
    protected $xmlElementArr = [];
    //解析出来获得表明变成index的东西
    protected $sheetIndex2Name = [];
    //作为单个文件的处理对象
    private $zip;

    public function handleXlsxInZip(string $filePath): array
    {
        //打开文件
        $this->zip  = new ZipArchive();
        if (FALSE === $this->zip->open($filePath) || 0 == $this->zip->numFiles) {
            throw new Exception('某些原因不能读取文件',1001);
        }

        //读取ShareStrings.xml文件
        if (FALSE === $this->readShareString()) {
            throw new Exception('某些原因解析xml文件失败',1002);
        }
        //读取workbook.xml文件
        if (FALSE === $this->getsheetIndex2Name()) {
            throw new Exception('某些原因解析xml文件失败',1005);
        }

        $xmlElementArr = [];
        for($i = 0; $i < $this->zip->numFiles; $i++) { 
            $fileInZip = $this->zip->getNameIndex($i);
            if (preg_match('/^xl\/worksheets\/sheet([0-9]+)\.xml$/',$fileInZip,$match)) {
                //例子：xl/worksheets/sheet1.xml
                $sheetIndex = intval($match[1]);
                $sheetContent = $this->zip->getFromName($fileInZip);
                $sheetXmlObj = simplexml_load_string($sheetContent,'SimpleXMLElement',LIBXML_DTDLOAD | LIBXML_DTDATTR);

                $sheetName = $this->getSheeNameByIndex($sheetIndex);
                $xmlElementArr[$sheetName] = $sheetXmlObj;
            }
        }
        $this->zip->close();
        return $xmlElementArr;
    }

    public function readShareString(): bool
    {
        $xmlObj = simplexml_load_string($this->zip->getFromName('xl/sharedStrings.xml'));
    
        if (FALSE === $xmlObj) {
            return FALSE;
        }
        
        $n = 0;
        foreach ($xmlObj->si as $v) {
            if (isset($v->t)) {
                $str = trim($v->t->__toString());
            } else {
                $str = $this->getCombineTvalue($v);
                if (empty($str)) {
                    return false;
                }
            }
            $str = str_replace('&#10;','',$str);
            $this->values[$n] = $str;
            ++$n;
        }
        return TRUE;
    }

    public function getsheetIndex2Name()
    {
        $fileContent = $this->zip->getFromName('xl/workbook.xml');

        if (preg_match_all('/<sheet name="(\S+?)"[\s\S]+?\/>/',$fileContent,$match,PREG_SET_ORDER,250)) {
            if (count($match) == 0) {
                return false;
            }

            $index = 1;
            foreach ($match as $index => $item) {
                $this->sheetIndex2Name[$index+1] = $item[1];
            }
            return true;
        }
        return false;
    }

    public function getSheeNameByIndex(int $index)
    {
        if (isset($this->sheetIndex2Name[$index])) {
            return $this->sheetIndex2Name[$index];
        }
        return $index;
    }

    public function getAttribute(SimpleXMLElement $xmlObj,$att)
    {
        $arr = $xmlObj->attributes();
        return (isset($arr[$att]))? $arr[$att]->__toString():NULL;
    }

    public function getColumnNum(SimpleXMLElement $cell)
    {
        $cellPos = $this->getAttribute($cell, 'r');
        if (preg_match('/^([A-Z]+)[0-9]+$/', $cellPos, $match)) {
            return $match[1];
        }
        return null;
    }

    public function getCellValue(SimpleXMLElement $cell)
    {
        $cellValue = NULL;
        if (isset($cell->v)) {
            //读取单元格的值
            $cellValue = $cell->v->__toString();
            if ('s' == $this->getAttribute($cell,'t')) {
                //如果是字符串就替换一下值
                $cellValue = $this->getStringValue($cellValue);
            }
        }
        return $cellValue;
    }

    public function getStringValue($stringValue): string
    {
        return $this->values[$stringValue];
    }

    public function getCombineTvalue(SimpleXMLElement $xmlObj): string
    {
        if (preg_match_all('/<t(?:| \S+)>(.*?)<\/t>/',$xmlObj->asXML(),$match,PREG_PATTERN_ORDER)) {
            return implode('',$match[1]);
        }
        return '';
    }
}