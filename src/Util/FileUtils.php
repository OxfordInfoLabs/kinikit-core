<?php

namespace Kinikit\Core\Util;


class FileUtils {

    /**
     * Add data to the end of a file.
     *
     * @param $filePath string
     * @param $data string
     * @param $newLine boolean
     */
    public static function appendDataToFile($filePath, $data, $newLine = false) {
        if ($newLine) {
            file_put_contents($filePath, $data . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents($filePath, $data, FILE_APPEND);
        }
    }

    public static function replaceFileData($filePath, $data) {
        file_put_contents($filePath, $data);
    }

    /**
     * Find matching line of text from file and remove it.
     *
     * @param $filePath string
     * @param $match string
     */
    public static function removeLineFromFile($filePath, $match) {
        $allLines = file($filePath);
        $matches = preg_grep('/' . $match . '/i', $allLines, PREG_GREP_INVERT);

        // This removes any lines with returns, so we avoid additional blank lines when we glue
        // back together again.
        $cleanMatches = array_map(function ($str) {
            return str_replace(PHP_EOL, '', $str);
        }, $matches);

        // Write each match back onto its own line.
        file_put_contents($filePath, implode(PHP_EOL, array_filter($cleanMatches)));
    }

    /**
     * Return lines matching the text supplied.
     *
     * @param $filePath
     * @param $textMatch
     * @return array
     */
    public static function returnMatchingLinesFromFile($filePath, $textMatch) {
        $allLines = file($filePath);
        $matches = preg_grep("/" . $textMatch . "/i", $allLines);

        $cleanMatches = array_map(function ($str) {
            return str_replace(PHP_EOL, '', $str);
        }, $matches);

        return $cleanMatches;
    }

    /**
     * @param $filePath
     * @return array
     */
    public static function returnAllLinesInFile($filePath) {
        if (!file_exists($filePath))
            file_put_contents($filePath, "");

        return file($filePath, FILE_IGNORE_NEW_LINES);
    }

    /**
     * Used to clear out the contents of a file.
     *
     * @param $filePath
     */
    public static function clearFileContents($filePath) {
        file_put_contents($filePath, "");
    }


    /**
     * Delete a directory recursively
     *
     * @param $dir
     * @param bool $recursive
     * @return bool
     */
    public static function deleteDirectory($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


    /**
     * Copy source to destination recursively.
     *
     * @param $src
     * @param $dst
     */
    public static function copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

}