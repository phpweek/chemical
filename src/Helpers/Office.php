<?php
if(function_exists('generateExcelByTemplate')) {
    function generateExcelByTemplate($filename = '', $template = '', $data = [])
    {
        $writerType = $readerType = ucfirst(strtolower(substr($template, strrpos($template, ".") + 1)));
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($writerType);
        $spreadsheet = $reader->load($template);
        $flag = 0;
        foreach ($data as $r => $dataRow) {
            //如果数值是0
            if ($r == 0) {
                $flag = true;
            }
            if ($flag) {
                $r = $r + 1;
            }
            //如果数值是0 end
            foreach ($dataRow as $k => $v) {
                $spreadsheet->getActiveSheet()->setCellValue(_10to26($k) . $r, $v);
            }
        }
        $spreadsheet->setActiveSheetIndex(0);
        $writer = PHPExcelIOFactory::createWriter($spreadsheet, $writerType);
        $writer->save($filename);
        return $filename;
    }
}

if(function_exists('generateDocxByTemplate')) {
    /**
     * 通过模版来生成word
     * @param $fileName
     * @param $template
     * @param $datas
     * @return string
     */
    function generateDocxByTemplate($fileName, $template, $datas)
    {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template);
        foreach ($datas as $k => $item) {
            if (substr($k, 0, 3) == 'img') {
                $templateProcessor->setImageValue(substr($k, 3), $item);
            } else {
                if (is_array($item)) {
                    $templateProcessor->cloneRow($k, count($item));
                    foreach ($item as $rowKey => $rowData) {
                        $rowNumber = $rowKey + 1;
                        foreach ($rowData as $macro => $replace) {
                            if (substr($macro, 0, 3) == 'img') {
                                $templateProcessor->setImageValue(substr($macro, 3) . '#' . $rowNumber, $replace);
                            } else {
                                $templateProcessor->setValue($macro . '#' . $rowNumber, $replace);
                            }
                        }
                    }
                } elseif (is_scalar($item)) {
                    $templateProcessor->setValue($k, $item);
                }
            }
        }
        $templateProcessor->saveAs($fileName);
        return $fileName;
    }
}