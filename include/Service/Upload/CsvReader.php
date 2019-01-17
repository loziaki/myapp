<?php declare(strict_types=1);

namespace Service\ExcelReader;

abstract class CsvReader implements FileReader
{
    const UTF8_BOM = "\xef\xbb\xbf";
    const UTF16_LITTLE_ENDIAN_BOM = "\xff\xfe";

    public $seperator;
    public $eol;

    public function cookFileContent($content)
    {
        $first2 = substr($content, 0, 2);
        $first3 = substr($content, 0, 3);
        $content2 = NULL;
        if ($first3 == SELF::UTF8_BOM) {
            $content2 = substr($content,3);
            $this->seperator = "\t";
        } elseif ($first2 == SELF::UTF16_LITTLE_ENDIAN_BOM) {
            $content2 = substr($content,2);
            $content2 = iconv('UTF-16LE','UTF-8',$content2);
            $this->seperator = "\t";
        } elseif (mb_detect_encoding($content,'GBK',TRUE)) {
            $content2 = iconv('GBK','UTF-8',$content);
            $this->seperator = ",";
        } else {
            $content2 = iconv('UTF-16LE','UTF-8',$content);
            $this->seperator = "\t";
        }
        $this->getEOL($content2);
        return $content2;
    }

    public function getEOL($content)
    {
        if (strpos($content,"\r") > 0) {
            if (strpos($content,"\n") > 0) {
                $this->eol = "\r\n";
            } else {
                $this->eol = "\r";
            }
        } else {
            $this->eol = "\n";
        }
    }
}